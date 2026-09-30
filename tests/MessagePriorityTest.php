<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use RazorInformatics\RiNotifierPhp\MessagePriority;

class MessagePriorityTest extends TestCase
{
    #[TestWith([MessagePriority::Marketing, 1, 'Marketing'])]
    #[TestWith([MessagePriority::Notification, 2, 'Notification'])]
    #[TestWith([MessagePriority::Transactional, 3, 'Transactional'])]
    #[TestWith([MessagePriority::HighPriority, 4, 'High Priority'])]
    public function testValueAndDescription(MessagePriority $priority, int $value, string $description): void
    {
        $this->assertSame($value, $priority->value);
        $this->assertSame($priority, MessagePriority::from($value));
        $this->assertSame($description, $priority->description());
    }
}
