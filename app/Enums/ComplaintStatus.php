<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Done = 'done';
    case Rejected = 'rejected';

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Done,
            self::Rejected,
        ]);
    }
}
