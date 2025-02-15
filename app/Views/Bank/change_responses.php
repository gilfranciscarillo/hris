<?= $this->extend('Layouts/main') ?>
<?= $this->section('title') ?>Change Responses<?= $this->endSection() ?>
<?= $this->section('content') ?>
    <?php
        helper('datetime');

        $loggedInUser = getCurrentUser();
        /**
         * @var App\Entities\BankChangeRequestsEntity $changeRequest
         * @var App\Entities\UserEntity $user
         */
    ?>
    <h1>Bank Change Responses</h1>
    <div>
        <a href="<?= url_to('App\Controllers\Bank::changeRequests', $changeRequest->employee_id) ?>">
            Return to Requests List
        </a>
    </div>
    <?php if($loggedInUser->id !== $user->id): ?>
        <p><b>Employee:</b>&emsp;<?= $user->getFullName() ?></p>
    <?php endif; ?>
    <?= $this->include('Bank/Layouts/change_request_info') ?>
    <br>
    <table>
        <thead>
            <th>Status</th>
            <th>Comment</th>
            <th>Responded at</th>
            <th>Responded by</th>
        </thead>
        <tbody>
            <?php /** @var App\Entities\BankChangeResponsesEntity $changeResponse */ ?>
            <?php foreach($changeResponses as $changeResponse): ?>
                <tr>
                    <?php $statusColor = $changeResponse->isApproved() ? 'green' : 'red' ?>
                    <td style="color: <?= $statusColor ?>"><?= esc($changeResponse->status) ?></td>
                    <td><?= esc($changeResponse->comments) ?></td>
                    <td><?= stringDateTimeToSemiLongDateTime(esc($changeResponse->created_at)) ?></td>
                    <td><?= $changeResponse->approver->getFullName() ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?= $this->endSection() ?>