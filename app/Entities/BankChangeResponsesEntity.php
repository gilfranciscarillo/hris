<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class BankChangeResponsesEntity extends Entity
{
    protected $attributes = [
        'approver' => null
    ];

    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
