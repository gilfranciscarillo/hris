<?= $this->extend('Layouts/main') ?>
<?= $this->section('title') ?>Users<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1>Users</h1>
<?php 
    $loggedInUser = getCurrentUser();   
?>
<table>
    <thead>
        <th>Name</th>
        <th>Active</th>
        <th>Bank</th>
        <th>Account name</th>
        <th rowspan="3">Account number</th>
    </thead>
    <tbody>
        <?php helper('form') ?>
        <?php /** @var App\Entities\UserEntity $user */ ?>
        <?php foreach ($users as $user): ?>
            <?php
                $formAction = $user->isAdmin() ? 'remove_from_group' : 'add_to_group';
                $buttonCaption = 'Set as ' . ($user->isAdmin() ? 'User' : 'Admin');
            ?>
            <tr>
                <td><?= $user->getFullName() ?></td>
                <td><?= $user->isActive() ?></td>
                <td><?= esc($user->bank_name) ?></td>
                <td><?= esc($user->bank_account_name) ?></td>
                <td><?= $user->getFormattedAccountNumber() ?></td>
                <td>
                    <?php if($loggedInUser->id !== $user->id && !$user->isSuperAdmin()): ?>
                    <?= form_open("admin/users/$formAction/" . $user->id . "/admin", ['class' => 'formSetUserGroup']) ?>
                        <button class="rowButton submitBtn"><?= $buttonCaption ?></button>
                        <input type="hidden" name="_method" value="PATCH" />
                    </form>
                    <?php endif; ?>
                </td>
                <td><a href="<?= url_to('\App\Controllers\Bank::changeRequests', $user->id) ?>">Bank change requests</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    document.querySelectorAll(".formSetUserGroup").forEach(form => {
        form.addEventListener("submit", function() {
            this.querySelector(".submitBtn").disabled = true;
        });
    });
</script>
<?= $this->endSection() ?>