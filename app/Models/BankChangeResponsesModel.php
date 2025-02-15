<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\BankChangeResponsesEntity;

class BankChangeResponsesModel extends Model
{
    protected $table            = 'bank_change_responses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = BankChangeResponsesEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'request_id',
        'approver_id',
        'status',
        'comments',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'request_id' => 'integer',
        'approver_id' => 'integer'
    ];

    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'comments' => 'permit_empty|min_length[20]'
    ];
    protected $validationMessages   = [
        'comments' => [
            'min_length' => 'Comments should be at least {param} characters'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setApproverId'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setApproverId'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['attachApproverIfAny'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function setApproverId(array $data)
    {
        $data['data']['approver_id'] = auth()->user()->id;

        return $data;
    }

    protected function attachApproverIfAny(array $data)
    {
        if ($data['data']) {
            $user = model(\App\Models\UserModel::class);

            foreach ($data['data'] as &$row) {
                if ($row instanceof BankChangeResponsesEntity) {
                    $row->approver = $user->find($row->approver_id);
                }
            }
        }

        return $data;
    }
}
