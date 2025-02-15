<?= $this->extend('Layouts/main') ?>
<?= $this->section('title') ?>Bank change request form<?= $this->endSection() ?>
<?= $this->section('content') ?>
    <?php
        helper('form');

        $loggedInUser = getCurrentUser();

        /** @var App\Entities\BankChangeRequestsEntity $changeRequest */
        $isEditChangeRequest = isset($changeRequest);
        $formUrl = $isEditChangeRequest ? 'bank/update_change_request/' . $changeRequest->id : 'bank/create_change_request';
    ?>
    <h1><?= isset($changeRequest) ? "Update Change Request" : "New Change Request" ?></h1>
    <div>
        <a href="<?= url_to('App\Controllers\Bank::changeRequests', $isEditChangeRequest ? $changeRequest->employee_id : $loggedInUser->id) ?>">
            Cancel Change Request
        </a>
    </div>
    <br>
    <?php if (session()->has('errors')): ?>
        <ul class="error">
            <?php foreach (session('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?= form_open($formUrl, ['id' => 'changeRequestForm']) ?>
        <?php if ($isEditChangeRequest): ?>
            <input type="hidden" name="_method" value="PATCH" />
        <?php endif; ?>
        
        <div class="fieldSetGroup">
            <fieldset>
                <legend>Bank name</legend>
                <label for='old_bank_name'>Old bank name</label>
                <div class="oldValue" id="old_bank_name">
                    <?= esc($isEditChangeRequest ? $changeRequest->old_bank_name : $loggedInUser->bank_name) ?>
                </div>
                
                <label for='new_bank_name'>New bank name</label>
                <input type='text' id='new_bank_name'
                    name='new_bank_name'
                    value='<?= old('new_bank_name', esc($isEditChangeRequest ? $changeRequest->new_bank_name : '')) ?>'>
            </fieldset>

            <fieldset>
                <legend>Account name</legend>
                <label for='old_account_name'>Old account name</label>
                <div class="oldValue" id="old_account_name">
                    <?= esc($isEditChangeRequest ? $changeRequest->old_account_name : $loggedInUser->bank_account_name) ?>
                </div>

                <label for='new_account_name'>New account name</label>
                <input type='text' id='new_account_name'
                    name='new_account_name'
                    value='<?= old('new_account_name', esc($isEditChangeRequest ? $changeRequest->new_account_name : '')) ?>'>
            </fieldset>

            <fieldset>
                <legend>Account number</legend>
                <label for='old_account_no'>Old account number</label>
                <div class="oldValue" id="old_account_no">
                    <?= esc($isEditChangeRequest ? $changeRequest->getFormattedAccountNumber(true) : $loggedInUser->getFormattedAccountNumber()) ?>
                </div>

                <label for='new_account_no'>New account number</label>
                <input type='password' id='new_account_no'
                    name='new_account_no'
                    value='<?= old('new_account_no', $isEditChangeRequest ? $changeRequest->decryptAccountNumber() : '') ?>'>
                <div onclick="toggleAccountNumberVisibility()" class="asButton">👁 <span id="caption">Show</span></div>
            </fieldset>
        </div>
        <br>
        <label for='reason'>Reason</label>
        <textarea id='reason' name='reason'><?= old('reason', esc($isEditChangeRequest ? $changeRequest->reason : '')) ?></textarea>
        <br>   
        <div class="formButtons">             
            <button id="submitBtn">Save</button>
        </div>    
    </form>
    <script>
        document.getElementById("changeRequestForm").addEventListener("submit", function() {
            document.getElementById("submitBtn").disabled = true;
        });  

        function toggleAccountNumberVisibility() {
            const input = document.getElementById('new_account_no');
            const caption = document.getElementById('caption');

            input.type = input.type === 'text' ? 'password' : 'text';
            caption.innerHTML = caption.innerHTML === 'Show' ? 'Hide' : 'Show';
        }
    </script>
<?= $this->endSection() ?>