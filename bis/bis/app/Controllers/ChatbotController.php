<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ChatbotService;
use CodeIgniter\HTTP\ResponseInterface;

class ChatbotController extends BaseController
{
    public function ask(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (! is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $message = trim((string) ($payload['message'] ?? ''));

        if ($message === '') {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'reply' => 'Please enter a message.',
                ]);
        }

        $userId = $this->getCurrentUserId();

        $chatbotService = new ChatbotService();

        $result = $chatbotService->ask(
            message: $message,
            channel: 'web',
            userId: $userId,
            messengerPsid: null
        );

        return $this->response->setJSON($result);
    }

    private function getCurrentUserId(): ?int
    {
        $session = session();

        $possibleKeys = [
            'user_id',
            'id',
            'resident_id',
            'admin_id',
        ];

        foreach ($possibleKeys as $key) {
            $value = $session->get($key);

            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }
}