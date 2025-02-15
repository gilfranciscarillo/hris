<?= $this->extend('Layouts/main') ?>
<?= $this->section('title') ?>Change Requests<?= $this->endSection(); ?>
<?= $this->section('content') ?>
    <?php
        helper('datetime');

        $loggedInUser = getCurrentUser();
    ?>
    <h1>Bank Change Requests</h1>
    <?php if($loggedInUser->id !== $user->id): ?>
        <p><b>Employee:</b>&emsp;<?= $user->getFullName() ?></p>
    <?php endif; ?>
    <?php if($loggedInUser->id === $user->id): ?>
        <div class="top-actions">
            <button><a href="<?= url_to('App\Controllers\Bank::newChangeRequest') ?>" class="link-button plain">New bank change request</a></button>
        </div>
    <?php endif; ?>
    <table>
        <thead>
            <th>Old</th>
            <th>New</th>
            <th>Reason</th>
            <th>Submitted at</th>
            <th colspan="2">Status</th>
        </thead>
        <tbody>
            <?php $latestChangeRequestId = false ?>
            <?php /** @var App\Entities\BankChangeRequestsEntity $chRequest */ ?>
            <?php foreach ($changeRequests as $chRequest): ?>
                <?php 
                    if(!$latestChangeRequestId) $latestChangeRequestId = $chRequest->id; 
                    $statusColor = 'olive';
                    if (isset($chRequest->response)) $statusColor = $chRequest->response->isApproved() ? 'green' : 'red';
                ?>
                <tr>
                    <td>
                        <?php
                        $oldBankName = esc($chRequest->old_bank_name);
                        $oldAccountName = esc($chRequest->old_account_name);
                        $oldAccountNumber = $chRequest->getFormattedAccountNumber(true);

                        echo "$oldBankName<br>$oldAccountName<br>$oldAccountNumber";
                        ?>
                    </td>
                    <td>
                        <?php
                        $newBankName = esc($chRequest->new_bank_name);
                        $newAccountName = esc($chRequest->new_account_name);
                        $newAccountNumber = $chRequest->getFormattedAccountNumber();

                        echo "$newBankName<br>$newAccountName<br>$newAccountNumber";
                        ?>
                    </td>
                    <td><?= esc($chRequest->reason) ?></td>
                    <td><?= stringDateTimeToSemiLongDateTime(esc($chRequest->submitted_at)) ?></td>
                    <td style="color: <?= $statusColor ?>"><?= !$chRequest->response ? 'Pending' : esc($chRequest->response->status) ?></td>
                    <td>
                        <?php /** @var App\Entities\BankChangeResponsesEntity $changeResponse */ ?>
                        <?php $changeResponse = $chRequest->response ?>
                        <a href="<?= url_to('App\Controllers\Bank::changeResponses', $chRequest->id) ?>">View Responses</a><br>
                        <!-- 
                            1. Request should not be editable if a response already made 
                            2. Only the owner of the request should edit the request
                        -->
                        <?php if ($loggedInUser->id === $chRequest->employee_id && !isset($changeResponse)): ?>
                            <a href="<?= url_to('App\Controllers\Bank::editChangeRequest', $chRequest->id) ?>">Edit</a><br>
                        <?php endif; ?>
                        <!-- Old requests cannot be approved or rejected anymore -->
                        <?php if($latestChangeRequestId === $chRequest->id): ?>
                            <?php if (($loggedInUser->canApproveBankChangeRequest() && !isset($changeResponse)) || 
                                    ($loggedInUser->canApproveBankChangeRequest() && isset($changeResponse) && $changeResponse->isRejected())): 
                            ?>
                                <a href="<?= url_to('App\Controllers\Bank::newChangeResponse', $chRequest->id, 'approved') ?>">Approve</a><br>
                            <?php endif; ?>
                            <?php if (($loggedInUser->canRejectBankChangeRequest() && !isset($changeResponse)) || 
                                    ($loggedInUser->canRejectBankChangeRequest() && isset($changeResponse) && $changeResponse->isApproved())): 
                            ?>
                                <a href="<?= url_to('App\Controllers\Bank::newChangeResponse', $chRequest->id, 'rejected') ?>">Reject</a><br>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?= $this->endSection() ?>