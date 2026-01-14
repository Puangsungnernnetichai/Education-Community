<?php

namespace Database\Seeders;

use App\Models\Prompt;
use App\Models\PromptVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromptSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $prompt = Prompt::updateOrCreate(
                ['key' => 'advisor.system'],
                [
                    'name' => 'Advisor System Prompt',
                    'description' => 'System prompt for AI Advisor chat (OpenAI).',
                ]
            );

            $hasAnyVersion = PromptVersion::query()->where('prompt_id', $prompt->id)->exists();

            if (! $hasAnyVersion) {
                $v = PromptVersion::create([
                    'prompt_id' => $prompt->id,
                    'version' => 1,
                    'content' => $this->defaultAdvisorSystemPrompt(),
                    'created_by' => null,
                ]);

                $prompt->update(['active_prompt_version_id' => $v->id]);
            } elseif ($prompt->active_prompt_version_id === null) {
                $latest = PromptVersion::query()
                    ->where('prompt_id', $prompt->id)
                    ->orderByDesc('version')
                    ->first();

                if ($latest) {
                    $prompt->update(['active_prompt_version_id' => $latest->id]);
                }
            }
        });
    }

    private function defaultAdvisorSystemPrompt(): string
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
}
