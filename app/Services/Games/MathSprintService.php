<?php

namespace App\Services\Games;

use Illuminate\Support\Str;

class MathSprintService
{
    /**
     * Generate a list of math questions for a sprint.
     *
     * @return array<int, array{id:string,prompt:string,choices:array<int,int>,answer:int}>
     */
    public function generateQuestions(int $count = 50): array
    {
        $questions = [];

        for ($i = 0; $i < $count; $i++) {
            $questions[] = $this->generateQuestion();
        }

        return $questions;
    }

    /**
     * @return array{id:string,prompt:string,choices:array<int,int>,answer:int}
     */
    public function generateQuestion(): array
    {
        $ops = ['+', '-', '×'];
        $op = $ops[random_int(0, count($ops) - 1)];

        [$a, $b] = $this->generateOperands($op);
        $answer = $this->computeAnswer($a, $b, $op);

        $choices = $this->generateChoices($answer, $op);

        return [
            'id' => (string) Str::uuid(),
            'prompt' => sprintf('%d %s %d = ?', $a, $op, $b),
            'choices' => $choices,
            'answer' => $answer,
        ];
    }

    /**
     * @return array{0:int,1:int}
     */
    private function generateOperands(string $op): array
    {
        if ($op === '×') {
            return [random_int(2, 12), random_int(2, 12)];
        }

        if ($op === '-') {
            $a = random_int(0, 50);
            $b = random_int(0, 50);

            if ($b > $a) {
                [$a, $b] = [$b, $a];
            }

            return [$a, $b];
        }

        // +
        return [random_int(0, 50), random_int(0, 50)];
    }

    private function computeAnswer(int $a, int $b, string $op): int
    {
        return match ($op) {
            '+' => $a + $b,
            '-' => $a - $b,
            '×' => $a * $b,
            default => $a + $b,
        };
    }

    /**
     * @return array<int,int>
     */
    private function generateChoices(int $answer, string $op): array
    {
        $set = [$answer => true];

        while (count($set) < 4) {
            $delta = match ($op) {
                '×' => random_int(-12, 12),
                default => random_int(-10, 10),
            };

            if ($delta === 0) {
                continue;
            }

            $candidate = $answer + $delta;
            if ($candidate < 0) {
                continue;
            }

            $set[$candidate] = true;
        }

        $choices = array_map('intval', array_keys($set));
        shuffle($choices);

        return $choices;
    }
}
