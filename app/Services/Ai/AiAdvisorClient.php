<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAdvisorClient
{
    /**
     * @param  array<int, array{role:string,text:string}>  $messages
     */
    public function reply(array $messages): string
    {
        $provider = config('services.ai_advisor.provider', 'mock');
        if ($provider !== 'openai') {
            return $this->mock('');
        }

        $baseUrl = (string) config('services.ai_advisor.openai.base_url', 'https://api.openai.com/v1');
        $apiKey = (string) config('services.ai_advisor.openai.api_key');
        $model = (string) config('services.ai_advisor.openai.model', 'gpt-4o-mini');

        if ($apiKey === '') {
            return "ยังไม่ได้ตั้งค่า OPENAI_API_KEY ครับ\n\nถ้าต้องการใช้โหมด AI จริง ให้ใส่คีย์ในไฟล์ .env แล้วรัน php artisan config:clear";
        }

        $system = $this->systemPrompt();
        $payloadMessages = [];
        $payloadMessages[] = ['role' => 'system', 'content' => $system];

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

            if (! $res->ok()) {
                $error = $res->json('error');
                $errorCode = is_array($error) ? ($error['code'] ?? null) : null;
                $errorType = is_array($error) ? ($error['type'] ?? null) : null;

                Log::warning('AI advisor OpenAI error', [
                    'status' => $res->status(),
                    'error_code' => $errorCode,
                    'error_type' => $errorType,
                    'body' => $res->body(),
                ]);

                $status = $res->status();

                if ($status === 401) {
                    return "ระบบเชื่อมต่อ AI ไม่สำเร็จ: คีย์ไม่ถูกต้อง (401)\n\nตรวจสอบ OPENAI_API_KEY ในไฟล์ .env แล้วรัน php artisan config:clear";
                }

                if ($status === 404) {
                    return "ระบบเชื่อมต่อ AI ไม่สำเร็จ: URL ไม่ถูกต้อง (404)\n\nตรวจสอบ OPENAI_BASE_URL ให้เป็น https://api.openai.com/v1 แล้วรัน php artisan config:clear";
                }

                if ($status === 429 && $errorCode === 'insufficient_quota') {
                    return "ตอนนี้คีย์ OpenAI ของระบบไม่มีโควต้า/ยังไม่ได้เปิด Billing (429: insufficient_quota)\n\nวิธีแก้: เติมเครดิต/ตั้งค่า Billing ใน OpenAI แล้วลองใหม่อีกครั้ง หรือสลับกลับไปใช้โหมด mock ชั่วคราว";
                }

                if ($status === 429) {
                    return "ตอนนี้ระบบโดนจำกัดการใช้งานชั่วคราว (429)\n\nลองรอ 10–30 วินาทีแล้วส่งใหม่อีกครั้งนะครับ";
                }

                return "ตอนนี้ระบบเชื่อมต่อ AI ไม่สำเร็จชั่วคราว (HTTP {$status})\n\nลองใหม่อีกครั้ง หรือเช็คค่า OPENAI_* ใน .env แล้วรัน php artisan config:clear";
            }

            $data = $res->json();
            $text = $data['choices'][0]['message']['content'] ?? null;
            $text = is_string($text) ? trim($text) : '';

            return $text !== '' ? $text : "ผมพร้อมช่วยครับ ลองพิมพ์รายละเอียดเพิ่มอีกนิดได้ไหม";
        } catch (\Throwable $e) {
            Log::warning('AI advisor OpenAI exception', ['error' => $e->getMessage()]);
            return "ตอนนี้ระบบมีปัญหาชั่วคราว ลองใหม่อีกครั้งได้ไหมครับ";
        }
    }

    private function systemPrompt(): string
    {
        return implode("\n", [
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
    }

    private function mock(string $userText): string
    {
        // Should never be used directly in production path; controller has richer mock.
        return "ได้ครับ เล่าเพิ่มอีกนิดว่าเป็นเรื่องเรียน/สอบ/แรงจูงใจ หรือเลือกทางไหนดีครับ";
    }
}
