<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\Ai\AiAdvisorClient;

class AdvisorController extends Controller
{
    public function index(Request $request)
    {
        $messages = $request->session()->get('advisor.messages');

        if (!is_array($messages) || count($messages) === 0) {
            $messages = [
                [
                    'id' => (string) Str::uuid(),
                    'role' => 'ai',
                    'text' => "สวัสดี เราคือ AI Advisor ของชุมชนนี้\n\nเล่าให้ฟังได้นะ: ตอนนี้คุณอยากปรึกษาเรื่องการเรียน/การสอบ/การจัดเวลา/แรงจูงใจ หรือการเลือกเส้นทางอะไรเป็นพิเศษ?",
                    'ts' => now()->toIso8601String(),
                ],
            ];
            $request->session()->put('advisor.messages', $messages);
        }

        return view('advisor.index', [
            'messages' => $messages,
        ]);
    }

    public function message(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $userText = trim($data['message']);

        $messages = $request->session()->get('advisor.messages', []);
        if (!is_array($messages)) {
            $messages = [];
        }

        $userMsg = [
            'id' => (string) Str::uuid(),
            'role' => 'user',
            'text' => $userText,
            'ts' => now()->toIso8601String(),
        ];

        $messages[] = $userMsg;

        $provider = config('services.ai_advisor.provider', 'mock');
        if ($provider === 'openai') {
            $aiText = app(AiAdvisorClient::class)->reply(array_map(function ($m) {
                return [
                    'role' => (string) ($m['role'] ?? 'user'),
                    'text' => (string) ($m['text'] ?? ''),
                ];
            }, $messages));
        } else {
            $aiText = $this->mockAdvisorReply($userText);
        }
        $aiMsg = [
            'id' => (string) Str::uuid(),
            'role' => 'ai',
            'text' => $aiText,
            'ts' => now()->toIso8601String(),
        ];
        $messages[] = $aiMsg;

        // Keep session small
        if (count($messages) > 50) {
            $messages = array_slice($messages, -50);
        }

        $request->session()->put('advisor.messages', $messages);

        return response()->json([
            'ok' => true,
            'user' => $userMsg,
            'ai' => $aiMsg,
        ]);
    }

    private function mockAdvisorReply(string $userText): string
    {
        // Concise placeholder: direct, simple, non-absolute; still ends with a gentle follow-up question.
        $t = mb_strtolower(trim($userText));

        $topic = 'general';
        if (str_contains($t, 'ง่วง') || str_contains($t, 'เพลีย') || str_contains($t, 'ง่วงนอน') || str_contains($t, 'sleepy')) {
            $topic = 'sleepy';
        } elseif (str_contains($t, 'หมดไฟ') || str_contains($t, 'ไม่อยากเรียน') || str_contains($t, 'ขี้เกียจ')) {
            $topic = 'motivation';
        } elseif (str_contains($t, 'สอบ') || str_contains($t, 'คะแนน') || str_contains($t, 'ข้อสอบ')) {
            $topic = 'exam';
        } elseif (str_contains($t, 'ไม่ทัน') || str_contains($t, 'ตามไม่ทัน') || str_contains($t, 'พื้นฐาน')) {
            $topic = 'catchup';
        } elseif (str_contains($t, 'คณะ') || str_contains($t, 'สาย') || str_contains($t, 'อาชีพ') || str_contains($t, 'งาน')) {
            $topic = 'career';
        }

        return match ($topic) {
            'sleepy' =>
                "ได้ครับ นี่คือวิธีแก้ง่วงแบบเร็ว ๆ ที่มักช่วยได้:\n\n" .
                "- ลุกเดิน/ยืดตัว 2–3 นาที หรือวิดพื้นเบา ๆ 10 ครั้ง\n" .
                "- ล้างหน้าหรือใช้น้ำเย็นที่ข้อมือ/หลังคอ\n" .
                "- ดื่มน้ำ 1 แก้ว (บางทีง่วงเพราะขาดน้ำ)\n" .
                "- ถ้าไหว: งีบ 10–20 นาที (ตั้งปลุก)\n" .
                "- กาแฟ/ชา: ดื่มแล้วรอ 15–20 นาทีถึงจะออกฤทธิ์\n\n" .
                "ถ้าง่วงบ่อยหลายวันติด ลองเช็คเวลานอน + แสงหน้าจอก่อนนอนด้วยนะ",
            'motivation' =>
                "เข้าใจเลยครับ—หมดไฟเกิดได้กับทุกคน โดยเฉพาะช่วงที่เหนื่อยสะสมหรือเป้าหมายไม่ชัด\n\n" .
                "ลองเลือกทำแค่ 1 ข้อนี้ก่อน (สั้น ๆ แต่ได้ผล):\n" .
                "- ตั้งเวลา 10 นาที แล้วทำงานชิ้นเล็กที่สุด (เช่นโจทย์ 3 ข้อ/สรุป 5 บรรทัด)\n" .
                "- ถ้าทำได้ ค่อยเพิ่มอีก 5 นาที",
            'exam' =>
                "ถ้าโฟกัสให้ถูกจุด การอ่านสอบจะเบาขึ้นครับ\n\n" .
                "ทางเลือกที่ทำได้ทันที:\n" .
                "- ไล่หัวข้อที่ออกบ่อยก่อน\n" .
                "- อ่านสั้น ๆ แล้วทำโจทย์ทันทีเพื่อเช็คจุดอ่อน\n" .
                "- จด ‘ข้อที่พลาด’ ไว้ทวนซ้ำ",
            'catchup' =>
                "ตามไม่ทันไม่ได้แปลว่าไม่เก่งนะ มักเป็นแค่พื้นฐานบางจุดยังไม่แน่น\n\n" .
                "ลองเลือก 1 วิธี:\n" .
                "- เติมพื้นฐาน 1–2 หัวข้อที่จำเป็นที่สุดก่อน\n" .
                "- ทำแบบฝึกหัดง่าย→กลางแบบไล่ระดับ",
            'career' =>
                "การเลือกคณะ/สายงาน เราทำให้ ‘ชัดขึ้น’ ได้ แม้ยังไม่ฟันธง\n\n" .
                "ลองเทียบ 2–3 มุมนี้:\n" .
                "- ชอบทำอะไรแล้วลืมเวลา\n" .
                "- ทำอะไรแล้วพอฝึกแล้วดีขึ้นจริง\n" .
                "- อยากมีชีวิตแบบไหน (เวลา/รายได้/ความมั่นคง)",
            default =>
                "โอเคครับ ผมช่วยคิดให้แบบตรงคำถามและกระชับได้\n\n" .
                "เพื่อให้ตอบได้แม่นขึ้น ขอแค่ 1 อย่าง: เรื่องนี้เกี่ยวกับ ‘เรียน/สอบ/แรงจูงใจ/เลือกทาง’ แบบไหน และคุณอยากได้คำแนะนำในกรอบเวลาเท่าไหร่ (เช่น 1 สัปดาห์/1 เดือน)?",
        };
    }
}
