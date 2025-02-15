<?= $this->extend('Layouts/main'); ?>
<?= $this->section('title') ?>Home<?= $this->endSection() ?>
<?= $this->section('content') ?>
    <?php
        $loggedInUser = getCurrentUser();
    ?>
    <h1>Home</h1>
    <p><b>Hi</b>&emsp;<?= $loggedInUser->getFullName() ?></p>
    <p><b>Access:</b>&emsp;<?= implode("/", $loggedInUser->getUserGroups()) ?></p>
    <table class="plain">
        <tbody>
            <tr>
                <td>Bank</td>
                <td class="cell-text-right"><?= esc($loggedInUser->bank_name) ?></td>

            </tr>
            <tr>
                <td>Account name</td>
                <td class="cell-text-right"><?= esc($loggedInUser->bank_account_name) ?></td>
            </tr>
            <tr>
                <td>Account number</td>
                <td class="cell-text-right"><?= $loggedInUser->getFormattedAccountNumber() ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <a href='<?= url_to('App\Controllers\Bank::changeRequests', $loggedInUser->id) ?>'>My Bank Change Requests</a>
<?= $this->endSection() ?>