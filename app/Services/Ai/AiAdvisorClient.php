<?php

namespace App\Services\Ai;

use App\Models\Prompt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAdvisorClient
{
    /**
     * @param  array<int, array{role:string,text:string}>  $messages
     */
    public function reply(array $messages): string
    {
        $result = $this->replyDetailed($messages);

        return (string) ($result['text'] ?? '');
    }

    /**
     * @param  array<int, array{role:string,text:string}>  $messages
     * @return array{text:string,status:string,provider:?string,model:?string,http_status:?int,error_code:?string,error_message:?string,prompt_version_id:?int,latency_ms:?int}
     */
    public function replyDetailed(array $messages): array
    {
        $provider = config('services.ai_advisor.provider', 'mock');
        if ($provider !== 'openai') {
            return [
                'text' => $this->mock(''),
                'status' => 'ok',
                'provider' => $provider,
                'model' => null,
                'http_status' => null,
                'error_code' => null,
                'error_message' => null,
                'prompt_version_id' => null,
                'latency_ms' => null,
            ];
        }

        $baseUrl = (string) config('services.ai_advisor.openai.base_url', 'https://api.openai.com/v1');
        $apiKey = (string) config('services.ai_advisor.openai.api_key');
        $model = (string) config('services.ai_advisor.openai.model', 'gpt-4o-mini');

        if ($apiKey === '') {
            return [
                'text' => "ยังไม่ได้ตั้งค่า OPENAI_API_KEY ครับ\n\nถ้าต้องการใช้โหมด AI จริง ให้ใส่คีย์ในไฟล์ .env แล้วรัน php artisan config:clear",
                'status' => 'error',
                'provider' => 'openai',
                'model' => $model,
                'http_status' => null,
                'error_code' => 'missing_api_key',
                'error_message' => 'OPENAI_API_KEY missing',
                'prompt_version_id' => null,
                'latency_ms' => null,
            ];
        }

        $systemData = $this->systemPromptData();
        $payloadMessages = [];
        $payloadMessages[] = ['role' => 'system', 'content' => $systemData['content']];

        // Keep the prompt short: last 12 turns max
        $tail = array_slice($messages, -24);
        foreach ($tail as $m) {
            $role = ($m['role'] ?? 'user') === 'ai' ? 'assistant' : 'user';
            $text = (string) ($m['text'] ?? '');
            if ($text === '') {
                continue;
            }
            $payloadMessages[] = ['role' => $role, 'content' => $text];
        }

        $startMs = (int) round(microtime(true) * 1000);

        try {
            $res = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(25)
                ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                    'model' => $model,
                    'messages' => $payloadMessages,
                    'temperature' => 0.4,
                    'max_tokens' => 280,
                ]);

            $latencyMs = max(0, (int) round(microtime(true) * 1000) - $startMs);

            if (! $res->ok()) {
                $error = $res->json('error');
                $errorCode = is_array($error) ? ($error['code'] ?? null) : null;
                $errorType = is_array($error) ? ($error['type'] ?? null) : null;
                $errorMessage = is_array($error) ? ($error['message'] ?? null) : null;
                $status = $res->status();

                Log::warning('AI advisor OpenAI error', [
                    'status' => $status,
                    'error_code' => $errorCode,
                    'error_type' => $errorType,
                    'body' => $res->body(),
                ]);

                $text = match (true) {
                    $status === 401 => "ระบบเชื่อมต่อ AI ไม่สำเร็จ: คีย์ไม่ถูกต้อง (401)\n\nตรวจสอบ OPENAI_API_KEY ในไฟล์ .env แล้วรัน php artisan config:clear",
                    $status === 404 => "ระบบเชื่อมต่อ AI ไม่สำเร็จ: URL ไม่ถูกต้อง (404)\n\nตรวจสอบ OPENAI_BASE_URL ให้เป็น https://api.openai.com/v1 แล้วรัน php artisan config:clear",
                    $status === 429 && $errorCode === 'insufficient_quota' => "ตอนนี้คีย์ OpenAI ของระบบไม่มีโควต้า/ยังไม่ได้เปิด Billing (429: insufficient_quota)\n\nวิธีแก้: เติมเครดิต/ตั้งค่า Billing ใน OpenAI แล้วลองใหม่อีกครั้ง หรือสลับกลับไปใช้โหมด mock ชั่วคราว",
                    $status === 429 => "ตอนนี้ระบบโดนจำกัดการใช้งานชั่วคราว (429)\n\nลองรอ 10–30 วินาทีแล้วส่งใหม่อีกครั้งนะครับ",
                    default => "ตอนนี้ระบบเชื่อมต่อ AI ไม่สำเร็จชั่วคราว (HTTP {$status})\n\nลองใหม่อีกครั้ง หรือเช็คค่า OPENAI_* ใน .env แล้วรัน php artisan config:clear",
                };

                return [
                    'text' => $text,
                    'status' => 'error',
                    'provider' => 'openai',
                    'model' => $model,
                    'http_status' => $status,
                    'error_code' => is_string($errorCode) ? $errorCode : (is_string($errorType) ? $errorType : null),
                    'error_message' => is_string($errorMessage) ? $errorMessage : $res->body(),
                    'prompt_version_id' => $systemData['prompt_version_id'],
                    'latency_ms' => $latencyMs,
                ];
            }

            $data = $res->json();
            $text = $data['choices'][0]['message']['content'] ?? null;
            $text = is_string($text) ? trim($text) : '';

            return [
                'text' => $text !== '' ? $text : 'ผมพร้อมช่วยครับ ลองพิมพ์รายละเอียดเพิ่มอีกนิดได้ไหม',
                'status' => 'ok',
                'provider' => 'openai',
                'model' => $model,
                'http_status' => (int) $res->status(),
                'error_code' => null,
                'error_message' => null,
                'prompt_version_id' => $systemData['prompt_version_id'],
                'latency_ms' => $latencyMs,
            ];
        } catch (\Throwable $e) {
            $latencyMs = max(0, (int) round(microtime(true) * 1000) - $startMs);
            Log::warning('AI advisor OpenAI exception', ['error' => $e->getMessage()]);

            return [
                'text' => 'ตอนนี้ระบบมีปัญหาชั่วคราว ลองใหม่อีกครั้งได้ไหมครับ',
                'status' => 'error',
                'provider' => 'openai',
                'model' => $model,
                'http_status' => null,
                'error_code' => 'exception',
                'error_message' => $e->getMessage(),
                'prompt_version_id' => $systemData['prompt_version_id'],
                'latency_ms' => $latencyMs,
            ];
        }
    }

    private function systemPromptData(): array
    {
        $fallback = implode("\n", [
            'You are an Education Advisor AI for an online learning community.',
            'Act as a friendly, safe, thoughtful educational advisor for all ages: students, parents, and lifelong learners.',
            '',
            'Style:',
            '- Respond naturally like a conversational chat assistant.',
            '- Be clear and structured when the question is complex; keep it short when the question is simple.',
            '- Use age-appropriate, respectful, non-judgmental language.',
            '- Encourage critical thinking and self-reflection rather than giving absolute commands.',
            '',
            'Scope (education-related): studying, school life, exams, university choices, career exploration, learning skills, motivation and burnout.',
            '',
            'Follow-ups:',
            '- If age/context is unclear AND it affects the advice, ask a gentle follow-up question before giving deep advice.',
            '- Otherwise, answer directly without unnecessary questions.',
            '',
            'Safety:',
            '- Do not shame, pressure, or compare the user to others.',
            '- You are not a therapist or doctor.',
            '- If the user shows strong emotional distress or risk of harm, respond calmly and encourage seeking help from trusted adults or professionals.',
            '',
            'Language: Respond in Thai by default unless the user writes in another language.',
        ]);

        $prompt = Prompt::query()->where('key', 'advisor.system')->with('activeVersion')->first();
        $content = $prompt?->activeVersion?->content;

        if (! is_string($content) || trim($content) === '') {
            return [
                'content' => $fallback,
                'prompt_version_id' => null,
            ];
        }

        return [
            'content' => $content,
            'prompt_version_id' => $prompt?->active_prompt_version_id,
        ];
    }

    private function mock(string $userText): string
    {
        // Should never be used directly in production path; controller has richer mock.
        return "ได้ครับ เล่าเพิ่มอีกนิดว่าเป็นเรื่องเรียน/สอบ/แรงจูงใจ หรือเลือกทางไหนดีครับ";
    }
}
