<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BankChangeRequestsModel;
use App\Models\BankChangeResponsesModel;
use App\Models\UserModel;
use App\Entities\BankChangeRequestsEntity;
use App\Entities\BankChangeResponsesEntity;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class Bank extends BaseController
{
    protected BankChangeRequestsModel $changeRequestsModel;
    protected BankChangeResponsesModel $changeResponsesModel;
    protected UserModel $userModel;

    public function __construct()
    {
        helper('bank');

        $this->changeRequestsModel = model(BankChangeRequestsModel::class);
        $this->changeResponsesModel = model(BankChangeResponsesModel::class);
        $this->userModel = model(UserModel::class);
    }

    public function index()
    {
        //
    }

    public function changeRequests(int $userId)
    {
        pageExists($userId);

        $changeRequests = $this->changeRequestsModel
            ->where('employee_id', $userId)
            ->orderBy('submitted_at', 'DESC')
            ->findAll();

        return view('Bank/change_requests', [
            'changeRequests' => $changeRequests,
            'user' => $this->userModel->find($userId)
        ]);
    }

    public function newChangeRequest()
    {
        return view('Bank/change_request_form');
    }

    public function editChangeRequest(?int $req_id = null)
    {
        $changeRequest = $this->changeRequestsModel->find($req_id);
        if (!$changeRequest) pageExists(false);

        pageExists($changeRequest->employee_id);

        return view('Bank/change_request_form', ['changeRequest' => $changeRequest]);
    }

    public function createChangeRequest()
    {
        $newChangeRequest = new BankChangeRequestsEntity();
        $newChangeRequest->fill($this->request->getPost());
        $newChangeRequest->setOldAccountDetailsFromUser(getCurrentUser());
        
        if ($this->changeRequestsModel->save($newChangeRequest) === false) {
            return redirect()->back()
                ->with('errors', $this->changeRequestsModel->errors())
                ->withInput();
        }

        $newChangeRequest = $this->changeRequestsModel->find($this->changeRequestsModel->getInsertID());
        return redirect()->to('bank/change_requests/' . $newChangeRequest->employee_id)
            ->with('message', 'Change request successfully created');
    }

    public function updateChangeRequest(int $req_id)
    {
        $editChangeRequest = $this->changeRequestsModel->find($req_id);
        $editChangeRequest->fill($this->request->getPost());

        if ($this->changeRequestsModel->save($editChangeRequest) === false) {
            return redirect()->back()
                ->with('errors', $this->changeRequestsModel->errors())
                ->withInput();
        }

        return redirect()->to('bank/change_requests/' . $editChangeRequest->employee_id)
            ->with('message', 'Change request successfully updated');
    }

    public function changeResponses(int $req_id)
    {
        $changeRequest = $this->changeRequestsModel->find($req_id);
        if (!$changeRequest) pageExists(false);

        pageExists($changeRequest->employee_id);

        $changeResponses = $this->changeResponsesModel->where('request_id', $req_id)->findAll();

        return view('Bank/change_responses', [
            'changeRequest' => $changeRequest,
            'changeResponses' => $changeResponses,
            'user' => $this->userModel->find($changeRequest->employee_id)
        ]);
    }

    /**
     * New response will always be created 
     */
    public function newChangeResponse(int $req_id, string $action)
    {
        $changeRequest = $this->changeRequestsModel->find($req_id);
        if (!$changeRequest) pageExists(false);

        pageExists($changeRequest->employee_id);

        return view('Bank/change_respond_form', ['changeRequest' => $changeRequest, 'action' => $action]);
    }

    public function viewChangeResponse(string $token)
    {   
        $decrypter = Services::encrypter();
        $dataInfo = explode(',', $decrypter->decrypt(hex2bin($token)));
        $emailSecretKey = getenv('EMAIL_SECRET_KEY');
        if (!$emailSecretKey) {
            $emailSecretKey = 'hr1s3m@1l';
        }
        $loggedInUser = getCurrentUser();
        if ($loggedInUser->cannotAccessOtherRecord((int)$dataInfo[0]) && $emailSecretKey !== $dataInfo[2]) {
            pageExists(false);
        }

        /** @var BankChangeRequestsEntity $changeRequest */
        $changeRequest = $this->changeRequestsModel->find((int)$dataInfo[1]);
        if (!$changeRequest) pageExists(false);

        pageExists($changeRequest->employee_id);

        return view('Bank/change_respond_form', ['changeRequest' => $changeRequest, 'action' => 'view']);
    }

    /**
     * @return BankChangeResponsesEntity | bool
     */
    private function createChangeResponse(int $req_id): BankChangeResponsesEntity | bool
    {
        $newChangeResponse = new BankChangeResponsesEntity();
        $newChangeResponse->fill($this->request->getPost());
        $newChangeResponse->request_id = $req_id;

        if ($this->changeResponsesModel->save($newChangeResponse) === false) {
            return false;    
        }

        return $newChangeResponse;
    }

    private function createViewToken(int $userId, int $reqId): string
    {
        return createViewToken($userId, $reqId);
    }

    public function approveChangeRequest(int $req_id) {
        $newChangeResponse = $this->createChangeResponse($req_id);
        if (!$newChangeResponse) {
            return redirect()->back()
                ->with('errors', $this->changeResponsesModel->errors())
                ->withInput();
        }

        if ($newChangeResponse->isApproved()) {
            /** @var BankChangeRequestsEntity $changeRequest */
            $changeRequest = $this->changeRequestsModel->find($req_id);

            /** @var \App\Entities\UserEntity $user */
            $user = $this->userModel->find($changeRequest->employee_id);
            $user->setBankDetailsFromChangeRequest($changeRequest); 

            if ($this->userModel->save($user) === false) {
                return redirect()->back()
                    ->with('errors', $this->userModel->errors())
                    ->withInput();
            }

            $token = $this->createViewToken($user->id, $req_id);
            if (emailBankChangeResponse($newChangeResponse, $token)) {
                return redirect()->to("bank/view/$token")
                    ->with('message', 'Change request successfully ' . $newChangeResponse->status);
            }
        }

        return redirect()->back()
            ->with('errors', ['Unexpected change response status or Unknown error'])
            ->withInput();
    }

    public function rejectChangeRequest(int $req_id) {
        $newChangeResponse = $this->createChangeResponse($req_id);
        if (!$newChangeResponse) {
            return redirect()->back()
                ->with('errors', $this->changeResponsesModel->errors())
                ->withInput();
        }

        if ($newChangeResponse->isRejected()) {
            /** @var BankChangeRequestsEntity $changeRequest */
            $changeRequest = $this->changeRequestsModel->find($req_id);

            /** @var \App\Entities\UserEntity $user */
            $user = $this->userModel->find($changeRequest->employee_id);
            $user->setBankDetailsFromChangeRequest($changeRequest, true); 

            if ($this->userModel->save($user) === false) {
                return redirect()->back()
                    ->with('errors', $this->userModel->errors())
                    ->withInput();
            }

            $token = $this->createViewToken($user->id, $req_id);
            if (emailBankChangeResponse($newChangeResponse, $token)) {
                return redirect()->to("bank/view/$token")
                    ->with('message', 'Change request successfully ' . $newChangeResponse->status);
            }
        }

        return redirect()->back()
            ->with('errors', ['Unexpected change response status or Unknown error'])
            ->withInput();
    }
}
