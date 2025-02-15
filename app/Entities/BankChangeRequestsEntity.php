<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Entities\UserEntity;

class BankChangeRequestsEntity extends Entity
{   
    protected $attributes = [
        'response' => null
    ];

    public function decryptAccountNumber(bool $old = false): string
    {
        helper('bank');

        if ($old && !$this->old_account_no) {
            return "";
        }

        return decryptAccountNumber(esc($old ? $this->old_account_no : $this->new_account_no));
    }

    public function getFormattedAccountNumber(bool $old = false): string
    {
        helper('bank');

        if ($old && !$this->old_account_no) {
            return "";
        }

        return getFormattedAccountNumber(esc($old ? $this->old_account_no : $this->new_account_no));
    }

    public function setOldAccountDetailsFromUser(UserEntity $user)
    {
        $this->old_bank_name = $user->bank_name;
        $this->old_account_name = $user->bank_account_name;
        $this->old_account_no = $user->bank_account_number;
    }
}
