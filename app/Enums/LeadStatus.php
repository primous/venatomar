<?php

namespace App\Enums;

enum LeadStatus: string
{
    case Received = 'received';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Funded = 'funded';

    public function label(): string
    {
        return __('status.'.$this->value);
    }

    /** Full Tailwind class strings (never concatenated). */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Received, self::Sent => 'bg-surface-2 text-ink-700 ring-1 ring-line',
            self::Accepted, self::Funded => 'bg-brand-100 text-brand-700',
            self::Rejected => 'bg-danger-100 text-danger-600',
        };
    }
}
