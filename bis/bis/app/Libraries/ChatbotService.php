<?php

namespace App\Libraries;

use App\Models\ChatbotMessageModel;

class ChatbotService
{
    private ChatbotKnowledgeService $knowledgeService;
    private ChatbotMessageModel $messageModel;
    private ResidentUpdateRequestService $residentUpdateRequestService;
    private ChatbotAIService $aiService;

    private string $fallbackReply = 'Sorry, I do not have enough verified barangay information to answer that. Please contact the barangay office for confirmation.';

    public function __construct()
    {
        $this->knowledgeService = new ChatbotKnowledgeService();
        $this->messageModel = new ChatbotMessageModel();
        $this->residentUpdateRequestService = new ResidentUpdateRequestService();
        $this->aiService = new ChatbotAIService();
    }

    public function ask(
        string $message,
        string $channel = 'web',
        ?int $userId = null,
        ?string $messengerPsid = null
    ): array {
        $message = trim($message);

        if ($message === '') {
            return [
                'success' => false,
                'reply' => 'Please enter a message.',
                'matched_faq_id' => null,
                'confidence' => 0.00,
                'ai_used' => false,
                'source_type' => 'fallback',
            ];
        }

        /*
         * 1. First, check if the resident is trying to submit
         *    an information update request.
         *
         * Important:
         * The chatbot should NOT directly update official resident/census records.
         * It only creates a pending request for barangay staff verification.
         */
        $requestType = $this->residentUpdateRequestService->detectRequestType($message);

        if ($requestType !== null) {
            $request = $this->residentUpdateRequestService->createPendingRequest(
                requestType: $requestType,
                requestedData: $message,
                residentId: $userId,
                currentData: null,
                reason: null,
                sourceChannel: $channel,
                messengerPsid: $messengerPsid
            );

            $requestLabel = $this->residentUpdateRequestService->formatRequestTypeLabel($requestType);

            $reply = 'Thank you. I recorded your ' . $requestLabel . ' request as pending. Barangay staff will verify the information first before any official resident or census record is updated.';

            $this->messageModel->insert([
                'channel' => $channel,
                'user_id' => $userId,
                'messenger_psid' => $messengerPsid,
                'user_message' => $message,
                'bot_reply' => $reply,
                'matched_faq_id' => null,
                'confidence' => 1.00,
                'ai_used' => 0,
                'source_type' => 'resident_update_request',
            ]);

            return [
                'success' => true,
                'reply' => $reply,
                'matched_faq_id' => null,
                'confidence' => 1.00,
                'ai_used' => false,
                'source_type' => 'resident_update_request',
                'resident_update_request_id' => $request['request_id'] ?? null,
                'resident_update_request_type' => $requestType,
            ];
        }

        /*
         * 2. If it is not a resident update request,
         *    answer using verified barangay FAQ/context.
         *
         * AI rule:
         * The AI is only allowed to rewrite or clarify a matched FAQ answer.
         * If there is no matched FAQ, the chatbot uses the safe fallback.
         */
        $match = $this->knowledgeService->findBestFaq($message);

        $faq = $match['faq'];
        $confidence = (float) $match['confidence'];

        $aiUsed = 0;

        if ($faq !== null) {
            $reply = $faq['answer'];
            $matchedFaqId = (int) $faq['id'];
            $sourceType = 'faq';

            $verifiedContext = 'Question: ' . ($faq['question'] ?? '') . "\n"
                . 'Answer: ' . ($faq['answer'] ?? '') . "\n"
                . 'Category: ' . ($faq['category'] ?? '');

            $aiReply = $this->aiService->generateReply($message, $verifiedContext);

            if ($aiReply !== null) {
                $reply = $aiReply;
                $aiUsed = 1;
                $sourceType = 'ai_faq';
            }
        } else {
            $reply = $this->fallbackReply;
            $matchedFaqId = null;
            $sourceType = 'fallback';
        }

        $this->messageModel->insert([
            'channel' => $channel,
            'user_id' => $userId,
            'messenger_psid' => $messengerPsid,
            'user_message' => $message,
            'bot_reply' => $reply,
            'matched_faq_id' => $matchedFaqId,
            'confidence' => $confidence,
            'ai_used' => $aiUsed,
            'source_type' => $sourceType,
        ]);

        return [
            'success' => true,
            'reply' => $reply,
            'matched_faq_id' => $matchedFaqId,
            'confidence' => $confidence,
            'ai_used' => $aiUsed === 1,
            'source_type' => $sourceType,
        ];
    }
}