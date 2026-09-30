<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Exception\GuzzleException;

class Message extends Service
{
    /**
     * @param array{phone_number?: string, message?: string, priority?: MessagePriority|int} $options e.g. ['phone_number' => '0700100100', 'message' => 'sample message', 'priority' => MessagePriority::Transactional]
     */
    public function send(array $options = []): array
    {
        if (empty($options['phone_number']) || empty($options['message'])) {
            return $this->error(7, 'phone number and message must be defined.');
        }

        $params = [
            'message' => $options['message'],
            'phone_number' => $options['phone_number'],
        ];
        if (isset($options['priority'])) {
            $params['priority'] = $this->priority($options['priority']);
        }

        try {
            $response = $this->client->post('message/send', [
                'form_params' => $params,
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * Send to many recipients at once: every number of `phone_number`, `contacts` and the contacts of `labels`, once each.
     * Each field takes a string or a list of them, at most 1000 numbers per request.
     *
     * A list of messages gives each recipient its own: it pairs by position with a list of `phone_number`
     * or of `contacts` of the same length, and cannot go to labels.
     *
     * Without a `priority` a send to contacts or labels goes as marketing, otherwise as a notification.
     *
     * The result carries the created messages as `data`, plus the server's `message`, a `summary` and the `failed` recipients.
     *
     * @param array{message?: string|list<string>, phone_number?: string|list<string>, contacts?: string|list<string>, labels?: string|list<string>, priority?: MessagePriority|int} $options
     */
    public function sendMany(array $options = []): array
    {
        if (empty($options['message'])) {
            return $this->error(7, 'message must be defined.');
        }
        if (empty($options['phone_number']) && empty($options['contacts']) && empty($options['labels'])) {
            return $this->error(7, 'at least one of phone number, contacts or labels must be defined.');
        }

        $params = array_intersect_key($options, array_flip(['message', 'phone_number', 'contacts', 'labels', 'priority']));
        if (isset($params['priority'])) {
            $params['priority'] = $this->priority($params['priority']);
        }

        try {
            $response = $this->client->post('v2/message/send', [
                'json' => $params,
                'timeout' => 30,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response, ['message', 'summary', 'failed']);
    }

    /**
     * Send to many recipients at once.
     *
     * @see Message::sendMany()
     */
    public function bulk(array $options = []): array
    {
        return $this->sendMany($options);
    }

    /**
     * Get message details using a message
     */
    public function getMessage(string $messageId): array
    {
        try {
            $response = $this->client->get('message/' . rawurlencode($messageId));
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * Get message details using a message
     */
    public function messageDetails(string $messageId): array
    {
        return $this->getMessage($messageId);
    }

    /**
     * Get message details using a message
     */
    public function fetchMessage(string $messageId): array
    {
        return $this->getMessage($messageId);
    }

    protected function priority(MessagePriority|int $priority): int
    {
        return $priority instanceof MessagePriority ? $priority->value : $priority;
    }
}
