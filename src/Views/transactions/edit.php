<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h2>Edit Transaction</h2>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="/transactions/<?= $transaction['id'] ?>" class="needs-validation" novalidate id="transactionForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="mode" value="<?= $isBasicMode ? 'basic' : 'full' ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Description</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($transaction['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($transaction['date']) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="start_entity_id" class="form-label">From</label>
                    <select class="form-select" id="start_entity_id" name="start_entity_id" required>
                        <?php foreach ($entities as $entity): ?>
                            <option value="<?= $entity['id'] ?>" <?= $entity['id'] == $transaction['start_entity_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($entity['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="start_amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="start_amount" name="start_amount" value="<?= $transaction['start_amount'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="start_currency_id" class="form-label">Currency</label>
                    <select class="form-select" id="start_currency_id" name="start_currency_id" required>
                        <?php foreach ($currencies as $currency): ?>
                            <option value="<?= $currency['id'] ?>" <?= $currency['id'] == $transaction['start_currency_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($currency['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if (!$isBasicMode): ?>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="dest_entity_id" class="form-label">To</label>
                    <select class="form-select" id="dest_entity_id" name="dest_entity_id" required>
                        <?php foreach ($entities as $entity): ?>
                            <option value="<?= $entity['id'] ?>" <?= $entity['id'] == $transaction['dest_entity_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($entity['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="dest_amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="dest_amount" name="dest_amount" value="<?= $transaction['dest_amount'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="dest_currency_id" class="form-label">Currency</label>
                    <select class="form-select" id="dest_currency_id" name="dest_currency_id" required>
                        <?php foreach ($currencies as $currency): ?>
                            <option value="<?= $currency['id'] ?>" <?= $currency['id'] == $transaction['dest_currency_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($currency['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="fee_entity_id" class="form-label">Fee Entity</label>
                    <select class="form-select" id="fee_entity_id" name="fee_entity_id" required>
                        <?php foreach ($entities as $entity): ?>
                            <option value="<?= $entity['id'] ?>" <?= $entity['id'] == $transaction['fee_entity_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($entity['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="fee_amount" class="form-label">Fee Amount</label>
                    <input type="number" step="0.01" class="form-control" id="fee_amount" name="fee_amount" value="<?= $transaction['fee_amount'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="fee_currency_id" class="form-label">Fee Currency</label>
                    <select class="form-select" id="fee_currency_id" name="fee_currency_id" required>
                        <?php foreach ($currencies as $currency): ?>
                            <option value="<?= $currency['id'] ?>" <?= $currency['id'] == $transaction['fee_currency_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($currency['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="grid_profit" class="form-label">Grid Profit</label>
                    <input type="number" step="0.01" class="form-control" id="grid_profit" name="grid_profit" value="<?= $transaction['grid_profit'] ?>">
                </div>
            </div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="purpose_id" class="form-label">Purpose</label>
                    <select class="form-select" id="purpose_id" name="purpose_id" required>
                        <?php foreach ($purposes as $purpose): ?>
                            <option value="<?= $purpose['id'] ?>" <?= $purpose['id'] == $transaction['purpose_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($purpose['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="mode_id" class="form-label">Mode</label>
                    <select class="form-select" id="mode_id" name="mode_id" required>
                        <?php foreach ($modes as $mode): ?>
                            <option value="<?= $mode['id'] ?>" <?= $mode['id'] == $transaction['mode_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mode['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3"><?= htmlspecialchars($transaction['remarks']) ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="/transactions" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>