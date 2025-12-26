<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Chat\Http\Requests\GetChatRequest;
use App\Modules\Chat\Http\Requests\GetMessagesRequest;
use App\Modules\Chat\Http\Requests\ReadMessageRequest;
use App\Modules\Chat\Http\Requests\StoreMessageFromESBRequest;
use App\Modules\Chat\Http\Requests\StoreMessageRequest;
use App\Modules\Chat\Services\ChatService;
use Exception;
use Illuminate\Http\JsonResponse;

class ChatController extends BaseController
{
    protected ChatService $service;

    public function __construct(ChatService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws Exception
     */
    public function index(GetChatRequest $request): JsonResponse
    {
        $chats = $this->service->getChats($request->validated());

        if (array_key_exists('errors', $chats)) {
            return $this->sendErrorResponse($chats);
        }

        return $this->sendResponse($chats);
    }

    /**
     * @throws Exception
     */
    public function getMessages(GetMessagesRequest $request, int $recipientId): JsonResponse
    {
        $messages = $this->service->getMessages($recipientId, $request->validated());

        if (array_key_exists('errors', $messages)) {
            return $this->sendErrorResponse($messages);
        }

        return $this->sendResponse($messages);
    }

    /**
     * @throws Exception
     */
    public function sendMessage(StoreMessageRequest $request, int $recipientId): JsonResponse
    {
        $message = $this->service->sendMessages($request->validated(), $recipientId);

        if (array_key_exists('errors', $message)) {
            return $this->sendErrorResponse($message);
        }

        return $this->sendResponse($message);
    }

    /**
     * @throws Exception
     */
    public function readMessages(ReadMessageRequest $request): JsonResponse
    {
        $messages = $this->service->markAsRead($request->validated());

        if (is_array($messages) && array_key_exists('errors', $messages)) {
            return $this->sendErrorResponse($messages);
        }

        return $this->sendResponse($messages);
    }

    /**
     * @throws Exception
     */
    public function getMessageFromESB(StoreMessageFromESBRequest $request): JsonResponse
    {
        $message = $this->service->getMessageFromEsb($request->validated());
        return $this->sendResponse($message);
    }
}
