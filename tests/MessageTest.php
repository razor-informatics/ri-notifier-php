<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use RazorInformatics\RiNotifierPhp\Constants;
use RazorInformatics\RiNotifierPhp\Message;
use RazorInformatics\RiNotifierPhp\MessagePriority;

class MessageTest extends TestCase
{
    public function testSendsAMessage(): void
    {
        $message = new Message($this->client($this->json(['data' => ['id' => 'msg-1']])));

        $result = $message->send(['phone_number' => '0700100100', 'message' => 'Hello']);

        $this->assertRequest('POST', 'message/send');
        $this->assertSame(10, $this->lastOptions()['timeout']);
        $this->assertSame(['message' => 'Hello', 'phone_number' => '0700100100'], $this->formBody());
        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertSame('msg-1', $result['data']->id);
    }

    #[TestWith([MessagePriority::Transactional, '3'])]
    #[TestWith([MessagePriority::HighPriority, '4'])]
    #[TestWith([1, '1'])]
    public function testSendsThePriority(MessagePriority|int $priority, string $expected): void
    {
        $message = new Message($this->client($this->json(['data' => []])));

        $message->send(['phone_number' => '0700100100', 'message' => 'Hello', 'priority' => $priority]);

        $this->assertSame($expected, $this->formBody()['priority']);
    }

    #[TestWith([[]])]
    #[TestWith([['phone_number' => '0700100100']])]
    #[TestWith([['message' => 'Hello']])]
    #[TestWith([['phone_number' => '', 'message' => 'Hello']])]
    public function testSendNeedsAPhoneNumberAndMessage(array $options): void
    {
        $result = (new Message($this->client()))->send($options);

        $this->assertNoRequest();
        $this->assertSame([
            'status' => Constants::STATUS_ERROR,
            'message' => 'phone number and message must be defined.',
            'data' => [],
        ], $result);
    }

    #[TestWith(['sendMany'])]
    #[TestWith(['bulk'])]
    public function testSendsToMany(string $method): void
    {
        $message = new Message($this->client($this->json([
            'message' => '2 messages queued.',
            'data' => [['id' => 'msg-1'], ['id' => 'msg-2']],
            'summary' => ['sent' => 2, 'failed' => 1],
            'failed' => [['phone_number' => '123', 'reason' => 'invalid']],
        ])));

        $result = $message->{$method}([
            'message' => 'Closed on Friday',
            'phone_number' => ['0712345678', '+254722000111'],
            'contacts' => 'CONTACT',
            'labels' => ['LABEL'],
            'priority' => MessagePriority::Marketing,
            'unknown' => 'dropped',
        ]);

        $this->assertRequest('POST', 'v2/message/send');
        $this->assertSame(30, $this->lastOptions()['timeout']);
        $this->assertSame([
            'message' => 'Closed on Friday',
            'phone_number' => ['0712345678', '+254722000111'],
            'contacts' => 'CONTACT',
            'labels' => ['LABEL'],
            'priority' => 1,
        ], $this->jsonBody());

        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertCount(2, $result['data']);
        $this->assertSame('2 messages queued.', $result['message']);
        $this->assertSame(2, $result['summary']->sent);
        $this->assertSame('123', $result['failed'][0]->phone_number);
    }

    public function testSendManyLeavesOutAMissingPriority(): void
    {
        $message = new Message($this->client($this->json(['data' => []])));

        $message->sendMany(['message' => ['Hi A', 'Hi B'], 'phone_number' => ['0700000001', '0700000002']]);

        $this->assertSame(['message' => ['Hi A', 'Hi B'], 'phone_number' => ['0700000001', '0700000002']], $this->jsonBody());
    }

    public function testSendManyNeedsAMessage(): void
    {
        $result = (new Message($this->client()))->sendMany(['phone_number' => '0700100100']);

        $this->assertNoRequest();
        $this->assertSame('message must be defined.', $result['message']);
    }

    public function testSendManyNeedsARecipient(): void
    {
        $result = (new Message($this->client()))->sendMany(['message' => 'Hello', 'phone_number' => [], 'labels' => '']);

        $this->assertNoRequest();
        $this->assertSame(Constants::STATUS_ERROR, $result['status']);
        $this->assertSame('at least one of phone number, contacts or labels must be defined.', $result['message']);
    }

    public function testSendManyReturnsValidationErrors(): void
    {
        $message = new Message($this->client($this->json([
            'message' => 'Too many recipients.',
            'errors' => ['phone_number' => ['At most 1000 numbers.']],
        ], 422)));

        $result = $message->sendMany(['message' => 'Hello', 'phone_number' => '0700100100']);

        $this->assertSame([
            'status' => Constants::STATUS_ERROR,
            'message' => 'Too many recipients.',
            'data' => ['phone_number' => ['At most 1000 numbers.']],
        ], $result);
    }

    #[TestWith(['getMessage'])]
    #[TestWith(['messageDetails'])]
    #[TestWith(['fetchMessage'])]
    public function testFetchesAMessage(string $method): void
    {
        $message = new Message($this->client($this->json(['data' => ['id' => 'a/b', 'status' => 'delivered']])));

        $result = $message->{$method}('a/b');

        $this->assertRequest('GET', 'message/a%2Fb');
        $this->assertSame('delivered', $result['data']->status);
    }
}
