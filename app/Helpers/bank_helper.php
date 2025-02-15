<?php

use App\Entities\BankChangeResponsesEntity;
use App\Models\BankChangeRequestsModel;
use App\Models\UserModel;

if (!function_exists('formatAccountNumber')) {
    function maskAccountNumber(string $accountNumber): string
    {
        return str_repeat('*', 8) . substr($accountNumber, -4);
    }
}

if (!function_exists('encryptAccountNumber')) {
    function encryptAccountNumber(?string $accountNumber): string
    {
        if (!$accountNumber) {
            return $accountNumber;
        }

        $encrypter = \Config\Services::encrypter();

        return base64_encode($encrypter->encrypt($accountNumber));
    }
}

if (!function_exists('decryptAccountNumber')) {
    function decryptAccountNumber(?string $encryptedAccountNumber): string
    {
        if (!$encryptedAccountNumber) {
            return $encryptedAccountNumber;
        }

        $decrypter = \Config\Services::encrypter();

        return $decrypter->decrypt(base64_decode($encryptedAccountNumber));
    }
}

if (!function_exists('getFormattedAccountNumber')) {
    function getFormattedAccountNumber(?string $encryptedAccountNumber): string
    {
        if (!$encryptedAccountNumber) {
            return $encryptedAccountNumber;
        }

        return maskAccountNumber(decryptAccountNumber($encryptedAccountNumber));
    }
}

if (!function_exists('createViewToken')) {
    function createViewToken(int $userId, int $reqId)
    {
        $emailSecretKey = getenv('EMAIL_SECRET_KEY');
        if (!$emailSecretKey) {
            $emailSecretKey = 'hr1s3m@1l';
        }

        $encrypter = \Config\Services::encrypter();
        $emailSecretKey = $userId . "," . $reqId . ",$emailSecretKey";
        return bin2hex($encrypter->encrypt($emailSecretKey));
    }
}

if (!function_exists('emailBankChangeResponse')) {
    function emailBankChangeResponse(BankChangeResponsesEntity $changeResponse, string $token): bool
    {
        $requestModel = model(BankChangeRequestsModel::class);
        $userModel = model(UserModel::class);

        $changeRequest = $requestModel->find($changeResponse->request_id);
        /** @var App\Entities\UserEntity $user */
        $user = $userModel->find($changeRequest->employee_id);

        /** @var CodeIgniter\Email\Email $email */
        $email = \Config\Services::email();
        $email->setTo($user->getEmail());
        $email->setSubject('Bank change requests status update');
        
        $fullName = $user->getFullName();
        $status = $changeResponse->status;
        $statusColor = $changeResponse->isApproved() ? 'green' : 'red';

        /** 
         * We pass the id of the change request instead of the change response since the viewChangeResponse mainly needs the request info
         * but no worries cause on find of request, corresponding latest response will already be attached
         */ 
        $token = createViewToken($user->id, $changeResponse->request_id);
        $responseLink = base_url("bank/view/$token");
        $message = "
            <h3>Good day $fullName</h3>
            <p>This email is to inform you that your request for changing bank details has been <span style='color:$statusColor'>$status</span></p>
            <p>Below is the link for more info about the response to your request</p>
            <p><a href='$responseLink'>Response Info</a></p> 
        ";

        $email->setMessage($message);

        return $email->send();
    }
}
