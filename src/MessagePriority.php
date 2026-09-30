<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

enum MessagePriority: int
{
    case Marketing = 1;
    case Notification = 2;
    case Transactional = 3;
    // Needs to be enabled for the project, otherwise it goes as transactional.
    case HighPriority = 4;

    public function description(): string
    {
        return match ($this) {
            self::Marketing => 'Marketing',
            self::Notification => 'Notification',
            self::Transactional => 'Transactional',
            self::HighPriority => 'High Priority',
        };
    }
}
