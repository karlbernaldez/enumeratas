<?php

namespace App\Libraries;

use App\Models\ChatbotFaqModel;

class ChatbotKnowledgeService
{
    private ChatbotFaqModel $faqModel;

    public function __construct()
    {
        $this->faqModel = new ChatbotFaqModel();
    }

    public function findBestFaq(string $message): array
    {
        $message = trim($message);

        if ($message === '') {
            return [
                'faq' => null,
                'confidence' => 0.00,
            ];
        }

        $faqs = $this->faqModel
            ->where('is_active', 1)
            ->findAll();

        $bestFaq = null;
        $bestScore = 0.00;

        foreach ($faqs as $faq) {
            $score = $this->calculateScore($message, $faq);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFaq = $faq;
            }
        }

        if ($bestScore < 0.20) {
            return [
                'faq' => null,
                'confidence' => $bestScore,
            ];
        }

        return [
            'faq' => $bestFaq,
            'confidence' => min($bestScore, 1.00),
        ];
    }

    private function calculateScore(string $message, array $faq): float
    {
        $normalizedMessage = $this->normalize($message);

        $question = $this->normalize($faq['question'] ?? '');
        $keywords = $this->normalize($faq['keywords'] ?? '');
        $category = $this->normalize($faq['category'] ?? '');

        $score = 0.00;

        if ($question !== '' && str_contains($normalizedMessage, $question)) {
            $score += 0.50;
        }

        $keywordList = array_filter(array_map('trim', explode(',', $keywords)));

        foreach ($keywordList as $keyword) {
            if ($keyword === '') {
                continue;
            }

            if (str_contains($normalizedMessage, $keyword)) {
                $score += 0.25;
                continue;
            }

            $keywordWords = explode(' ', $keyword);
            foreach ($keywordWords as $word) {
                if (strlen($word) >= 4 && str_contains($normalizedMessage, $word)) {
                    $score += 0.08;
                }
            }
        }

        if ($category !== '') {
            $categoryWords = explode('_', $category);

            foreach ($categoryWords as $word) {
                if (strlen($word) >= 4 && str_contains($normalizedMessage, $word)) {
                    $score += 0.10;
                }
            }
        }

        return round($score, 2);
    }

    private function normalize(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9\s,_-]/', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }
}