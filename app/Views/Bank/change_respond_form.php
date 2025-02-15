<?= $this->extend('Layouts/main') ?>
<?= $this->section('title') ?>Bank change respond form<?= $this->endSection() ?>
<?= $this->section('content') ?>
    <?php
        helper(['form', 'bank', 'datetime']);

        $loggedInUser = getCurrentUser();
        
        /** @var App\Entities\BankChangeResponsesEntity $changeResponse */
        $changeResponse = $changeRequest->response;
        $formAction = esc($action) === 'approved' ? 'approve' : 'reject';
        
        /** @var App\Entities\BankChangeRequestsEntity $changeRequest */
    ?>
    <?php if(esc($action) !== 'view'): ?>
        <h1><?= ucfirst($formAction) ?> Request</h1>
    <?php else: ?>
        <!-- Change response info should already available by this time -->
        <h1>Request <?= $changeResponse->status ?></h1>     
    <?php endif; ?>        
    <div>
        <a href="<?= url_to('App\Controllers\Bank::changeRequests', $changeRequest->employee_id) ?>">
            <?= esc($action) !== 'view' ? 'Cancel Action' : 'Return to Requests List' ?>
        </a>
    </div>
    <?php if (session()->has('errors')): ?>
        <ul class="error">
            <?php foreach (session('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?= $this->include('Bank/Layouts/change_request_info') ?>
    <br>
    <?php if(isset($changeRequest->response) && esc($action) === 'view'): ?>
        <p><?= esc($changeResponse->status) ?>&ensp;<b><?= stringDateTimeToSemiLongDateTime(esc($changeResponse->created_at)) ?></b></p>
        <p>Status&ensp;<b style="color: <?= $changeResponse->isApproved() ? 'green' : 'red' ?>"><?= esc($changeResponse->status) ?></b></p>
        <div class="reason">
            <?= esc($changeResponse->comments) ?>
        </div>
    <?php endif; ?>
    <?php if(esc($action) !== 'view'): ?>
        <?= form_open("bank/$formAction/" . $changeRequest->id, ['id' => 'changeRespondForm']) ?>
            <label for='comments'>Comment</label>
            <textarea id='comments' name='comments'><?= old('comments') ?></textarea>
            <br>
            <input type='hidden' id='status' name='status' value='<?= ucfirst(esc($action)) ?>' />
            <div class="formButtons">
                <button id="submitBtn"><?= ucfirst(esc($formAction)) ?></button>
            </div>
        </form>
    <?php endif; ?>
    <script>
        document.getElementById("changeRespondForm").addEventListener("submit", function() {
            document.getElementById("submitBtn").disabled = true;
        });    
    </script>
<?= $this->endSection() ?>