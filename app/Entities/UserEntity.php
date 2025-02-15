<?php

namespace App\Entities;

use CodeIgniter\Shield\Entities\User;
use App\Entities\BankChangeRequestsEntity;

class UserEntity extends User
{
    protected $attributes = [
        'email' => null
    ];

    public function isActive(): string
    {
        return $this->active ? 'Yes' : 'No';
    }

    public function isSuperAdmin(): bool
    {
        return $this->inGroup('superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->inGroup('admin');
    }

    public function getFormattedAccountNumber()
    {
        helper('bank');

        return getFormattedAccountNumber(esc($this->bank_account_number));
    }

    public function getFullName(): string
    {
        $firstName = esc($this->first_name);
        $lastName = esc($this->last_name);

        return "$firstName $lastName";
    }

    public function getUserGroups(): array
    {
        /** @var Array $authGroups */
        $authGroups = setting('AuthGroups.groups');
        $userGroups = $this->getGroups();

        return array_map(function ($group) use ($authGroups) {
            $authGrp = array_values(array_filter($authGroups, function ($authGrp, $key) use ($group) {
                return $key === $group;
            }, ARRAY_FILTER_USE_BOTH));

            if ($authGrp) {
                return $authGrp[0]['title'];
            }

            return 'Unknown';
        }, $userGroups);
    }

    public function cannotAccessOtherRecord(int $ownerId): bool
    {
        return !$this->can('admin.access') && $ownerId !== $this->id;
    }

    public function canRespondToBankChangeRequest(): bool
    {
        return $this->can('bank.change-respond');
    }

    public function canApproveBankChangeRequest(): bool
    {
        return $this->can('bank.change-request-approve');
    }

    public function canRejectBankChangeRequest(): bool
    {
        return $this->can('bank.change-request-reject');
    }

    public function setBankDetailsFromChangeRequest(BankChangeRequestsEntity $changeRequest, bool $getOld = false)
    {
        $this->bank_name = $getOld ? $changeRequest->old_bank_name : $changeRequest->new_bank_name;
        $this->bank_account_name = $getOld ? $changeRequest->old_account_name : $changeRequest->new_account_name;
        $this->bank_account_number = $getOld ? $changeRequest->old_account_no : $changeRequest->new_account_no; 
    }
}
