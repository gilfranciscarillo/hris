<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\BankChangeRequestsEntity;
use App\Models\BankChangeResponsesModel;

class BankChangeRequestsModel extends Model
{
    protected $table            = 'bank_change_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = BankChangeRequestsEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'old_bank_name',
        'new_bank_name',
        'old_account_name',
        'new_account_name',
        'old_account_no',
        'new_account_no',
        'reason',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'employee_id' => 'integer'
    ];

    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'new_bank_name' => 'required|min_length[3]|alpha_numeric_space',
        'new_account_name' => 'required|min_length[12]|alpha_space',
        'new_account_no' => 'required|min_length[4]|numeric',
        'reason' => 'required|min_length[20]'
    ];

    protected $validationMessages   = [
        'new_bank_name' => [
            'required' => 'Bank name is required.',
            'min_length' => 'Bank name must be at least {param} characters',
            'alpha_numeric_space' => 'Bank name may only contain letters, numbers and spaces'
        ],
        'new_account_name' => [
            'required' => 'Account name is required',
            'min_length' => 'Account name must be at least {param} characters',
            'alpha_space' => 'Account name may only contain letters and spaces'
        ],
        'new_account_no' => [
            'required' => 'Account number is required',
            'min_length' => 'Account number must be at least {param} digits',
            'numeric' => 'Account number may only contain numbers'
        ],
        'reason' => [
            'required' => 'Reason is required',
            'min_length' => 'Reason must be at least {param} characters'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeInsertHook'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['beforeUpdateHook'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['attachResponseIfAny'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function beforeUpdateHook(array $data)
    {
        helper('bank');

        $data['data']['new_account_no'] = encryptAccountNumber($data['data']['new_account_no']);

        return $data;
    }

    protected function beforeInsertHook(array $data)
    {
        helper('bank');

        $loggedInUser = getCurrentUser();

        if ($loggedInUser->bank_account_number) {
            $data['data']['old_account_no'] = $loggedInUser->bank_account_number;
        }

        $data['data']['new_account_no'] = encryptAccountNumber($data['data']['new_account_no']);
        $data['data']['employee_id'] = $loggedInUser->id;
        $data['data']['submitted_at'] = date('Y-m-d H:i:s');

        return $data;
    }

    protected function attachResponseIfAny(array $data)
    {
        if ($data['data']) {
            $responseModel = model(BankChangeResponsesModel::class);

            if (!is_array($data['data'])) {
                $data['data']->response = $responseModel
                    ->where('request_id', $data['data']->id)
                    ->orderBy('created_at', 'DESC') //Let's get the latest response
                    ->first();
            } else {
                foreach ($data['data'] as &$row) {
                    if ($row instanceof BankChangeRequestsEntity) {
                        $row->response = $responseModel
                            ->where('request_id', $row->id)
                            ->orderBy('created_at', 'DESC') //Let's get the latest response
                            ->first();
                    }
                }
            }
        }

        return $data;
    }
}
