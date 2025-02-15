<?php /** @var App\Entities\BankChangeRequestsEntity $changeRequest */ ?>
<p>Submitted at&ensp;<b><?= stringDateTimeToSemiLongDateTime(esc($changeRequest->submitted_at)) ?></b></p>
<?php
    $oldBankName = esc($changeRequest->old_bank_name);
    if (empty($oldBankName)) $oldBankName = 'Empty';

    $oldAccountName = esc($changeRequest->old_account_name);
    if (empty($oldAccountName)) $oldAccountName = 'Empty';

    $oldAccountNumber = $changeRequest->getFormattedAccountNumber(true);
    if (empty($oldAccountNumber)) $oldAccountNumber = maskAccountNumber('####');
?>
<p>Bank name from&ensp;<b><?= $oldBankName ?></b>&ensp;to&ensp;<b><?= esc($changeRequest->new_bank_name) ?></b></p>
<p>Account name from&ensp;<b><?= $oldAccountName ?></b>&ensp;to&ensp;<b><?= esc($changeRequest->new_account_name) ?></b></p>
<p>Account number from&ensp;<b><?= $oldAccountNumber ?></b>&ensp;to&ensp;<b><?= $changeRequest->getFormattedAccountNumber() ?></b></p>
<p>Reason</p>
<div class="reason">
    <?= esc($changeRequest->reason) ?>
</div>