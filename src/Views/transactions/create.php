<div class="row mb-4">
    <div class="col">
        <h2><?= $isBasicMode ? 'Quick Add Transaction' : 'New Transaction' ?></h2>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="/transactions?mode=<?= $isBasicMode ? 'basic' : 'full' ?>" class="needs-validation" novalidate id="transactionForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="mode" value="<?= $isBasicMode ? 'basic' : 'full' ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="modeToggle" <?= $isBasicMode ? '' : 'checked' ?>>
                        <label class="form-check-label" for="modeToggle">
                            Full Mode
                        </label>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Description</label>
                    <input type="text" class="form-control <?= isset($_SESSION['errors']['name']) ? 'is-invalid' : '' ?>"
                           id="name" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>" required>
                    <?php if (isset($_SESSION['errors']['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control <?= isset($_SESSION['errors']['date']) ? 'is-invalid' : '' ?>"
                           id="date" name="date" value="<?= htmlspecialchars($_SESSION['old']['date'] ?? date('Y-m-d')) ?>" required>
                    <?php if (isset($_SESSION['errors']['date'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['date']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Source Information -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="start_entity_id" class="form-label">From</label>
                    <select class="form-select <?= isset($_SESSION['errors']['start_entity_id']) ? 'is-invalid' : '' ?>"
                            id="start_entity_id" name="start_entity_id" required>
                        <option value="">Select source</option>
                        <?php foreach ($entities as $entity): ?>
                            <option value="<?= $entity['id'] ?>" 
                                    <?= ($_SESSION['old']['start_entity_id'] ?? '') == $entity['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($entity['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($_SESSION['errors']['start_entity_id'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['start_entity_id']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label for="start_amount" class="form-label">Amount</label>
                    <input type="number" step="0.00000001" class="form-control <?= isset($_SESSION['errors']['start_amount']) ? 'is-invalid' : '' ?>"
                           id="start_amount" name="start_amount" value="<?= htmlspecialchars($_SESSION['old']['start_amount'] ?? '0') ?>"
                           required>
                    <?php if (isset($_SESSION['errors']['start_amount'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['start_amount']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label for="start_currency_id" class="form-label">Currency</label>
                    <select class="form-select <?= isset($_SESSION['errors']['start_currency_id']) ? 'is-invalid' : '' ?>"
                            id="start_currency_id" name="start_currency_id" required>
                        <option value="">Select currency</option>
                        <?php foreach ($currencies as $currency): ?>
                            <option value="<?= $currency['id'] ?>"
                                <?php
                                    $old = $_SESSION['old']['start_currency_id'] ?? null;
                                    if ($old !== null) {
                                        echo ($old == $currency['id']) ? 'selected' : '';
                                    } else {
                                        echo ($defaultCurrencyId == $currency['id']) ? 'selected' : '';
                                    }
                                ?>>
                                <?= htmlspecialchars($currency['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($_SESSION['errors']['start_currency_id'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['start_currency_id']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Full Mode Fields -->
            <div class="full-mode-fields" style="display: <?= $isBasicMode ? 'none' : 'block' ?>;">
                <!-- Destination Information -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="dest_entity_id" class="form-label">To</label>
                        <select class="form-select <?= isset($_SESSION['errors']['dest_entity_id']) ? 'is-invalid' : '' ?>"
                                id="dest_entity_id" name="dest_entity_id" <?= $isBasicMode ? '' : 'required' ?>>
                            <option value="">Select destination</option>
                            <?php
                            $voidEntityId = null;
                            foreach ($entities as $entity) {
                                if (strtolower($entity['name']) === 'void') {
                                    $voidEntityId = $entity['id'];
                                    break;
                                }
                            }
                            foreach ($entities as $entity): ?>
                                <option value="<?= $entity['id'] ?>"
                                    <?php
                                        $old = $_SESSION['old']['dest_entity_id'] ?? null;
                                        if ($old !== null) {
                                            echo ($old == $entity['id']) ? 'selected' : '';
                                        } else if ($voidEntityId !== null && $voidEntityId == $entity['id']) {
                                            echo 'selected';
                                        }
                                    ?>>
                                    <?= htmlspecialchars($entity['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errors']['dest_entity_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['dest_entity_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label for="dest_amount" class="form-label">Amount</label>
                        <input type="number" step="0.00000001" class="form-control <?= isset($_SESSION['errors']['dest_amount']) ? 'is-invalid' : '' ?>"
                               id="dest_amount" name="dest_amount" value="<?= htmlspecialchars($_SESSION['old']['dest_amount'] ?? '0') ?>"
                               required>
                        <?php if (isset($_SESSION['errors']['dest_amount'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['dest_amount']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label for="dest_currency_id" class="form-label">Currency</label>
                        <select class="form-select <?= isset($_SESSION['errors']['dest_currency_id']) ? 'is-invalid' : '' ?>"
                                id="dest_currency_id" name="dest_currency_id" <?= $isBasicMode ? '' : 'required' ?>>
                            <option value="">Select currency</option>
                            <?php foreach ($currencies as $currency): ?>
                                <option value="<?= $currency['id'] ?>"
                                    <?php
                                        $old = $_SESSION['old']['dest_currency_id'] ?? null;
                                        if ($old !== null) {
                                            echo ($old == $currency['id']) ? 'selected' : '';
                                        } else {
                                            echo ($defaultCurrencyId == $currency['id']) ? 'selected' : '';
                                        }
                                    ?>>
                                    <?= htmlspecialchars($currency['code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errors']['dest_currency_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['dest_currency_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Fee Information -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fee_entity_id" class="form-label">Fee Entity</label>
                        <select class="form-select <?= isset($_SESSION['errors']['fee_entity_id']) ? 'is-invalid' : '' ?>"
                                id="fee_entity_id" name="fee_entity_id" <?= $isBasicMode ? '' : 'required' ?>>
                            <option value="">Select fee entity</option>
                            <?php foreach ($entities as $entity): ?>
                                <option value="<?= $entity['id'] ?>"
                                        <?= ($_SESSION['old']['fee_entity_id'] ?? '') == $entity['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($entity['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errors']['fee_entity_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['fee_entity_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label for="fee_amount" class="form-label">Fee Amount</label>
                        <input type="number" step="0.00000001" class="form-control <?= isset($_SESSION['errors']['fee_amount']) ? 'is-invalid' : '' ?>"
                               id="fee_amount" name="fee_amount" value="<?= htmlspecialchars($_SESSION['old']['fee_amount'] ?? '0') ?>"
                               <?= $isBasicMode ? '' : 'required' ?>>
                        <?php if (isset($_SESSION['errors']['fee_amount'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['fee_amount']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label for="fee_currency_id" class="form-label">Fee Currency</label>
                        <select class="form-select <?= isset($_SESSION['errors']['fee_currency_id']) ? 'is-invalid' : '' ?>"
                                id="fee_currency_id" name="fee_currency_id" <?= $isBasicMode ? '' : 'required' ?>>
                            <option value="">Select currency</option>
                            <?php foreach ($currencies as $currency): ?>
                                <option value="<?= $currency['id'] ?>"
                                    <?php
                                        $old = $_SESSION['old']['fee_currency_id'] ?? null;
                                        if ($old !== null) {
                                            echo ($old == $currency['id']) ? 'selected' : '';
                                        } else {
                                            echo ($defaultCurrencyId == $currency['id']) ? 'selected' : '';
                                        }
                                    ?>>
                                    <?= htmlspecialchars($currency['code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errors']['fee_currency_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['fee_currency_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="grid_profit" class="form-label">Grid Profit</label>
                        <input type="number" step="0.01" class="form-control <?= isset($_SESSION['errors']['grid_profit']) ? 'is-invalid' : '' ?>"
                               id="grid_profit" name="grid_profit" value="<?= htmlspecialchars($_SESSION['old']['grid_profit'] ?? '0') ?>">
                        <?php if (isset($_SESSION['errors']['grid_profit'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['grid_profit']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Categorization -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="purpose_id" class="form-label">Purpose</label>
                    <select class="form-select <?= isset($_SESSION['errors']['purpose_id']) ? 'is-invalid' : '' ?>"
                            id="purpose_id" name="purpose_id" required>
                        <option value="">Select purpose</option>
                        <?php foreach ($purposes as $purpose): ?>
                            <option value="<?= $purpose['id'] ?>"
                                    <?= ($_SESSION['old']['purpose_id'] ?? '') == $purpose['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($purpose['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($_SESSION['errors']['purpose_id'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['purpose_id']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="mode_id" class="form-label">Mode</label>
                    <select class="form-select <?= isset($_SESSION['errors']['mode_id']) ? 'is-invalid' : '' ?>"
                            id="mode_id" name="mode_id" required>
                        <option value="">Select mode</option>
                        <?php foreach ($modes as $mode): ?>
                            <option value="<?= $mode['id'] ?>"
                                    <?= ($_SESSION['old']['mode_id'] ?? '') == $mode['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mode['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($_SESSION['errors']['mode_id'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($_SESSION['errors']['mode_id']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3"><?= htmlspecialchars($_SESSION['old']['remarks'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                    <a href="/transactions" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeToggle = document.getElementById('modeToggle');
    if (modeToggle) {
        modeToggle.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('mode', this.checked ? 'full' : 'basic');
            window.location.href = url.toString();
        });
    }

    // --- Auto-select Fee Entity based on From Entity ---
    const fromEntity = document.getElementById('start_entity_id');
    const feeEntity = document.getElementById('fee_entity_id');
    let feeEntityManuallyChanged = false;

    if (fromEntity && feeEntity) {
        // When user changes Fee Entity, mark as manually changed
        feeEntity.addEventListener('change', function() {
            feeEntityManuallyChanged = true;
        });

        // When user changes From Entity, auto-set Fee Entity if not manually changed
        fromEntity.addEventListener('change', function() {
            if (!feeEntityManuallyChanged) {
                feeEntity.value = fromEntity.value;
                // Optionally, trigger change event if needed
                feeEntity.dispatchEvent(new Event('change'));
            }
        });

        // Reset manual override if From Entity changes back to original
        fromEntity.addEventListener('change', function() {
            if (feeEntity.value === fromEntity.value) {
                feeEntityManuallyChanged = false;
            }
        });
    }

    // --- Auto-fill To Amount from From Amount ---
    const fromAmount = document.getElementById('start_amount');
    const toAmount = document.getElementById('dest_amount');
    let toAmountManuallyChanged = false;

    if (fromAmount && toAmount) {
        // On page load: if toAmount is 0 or empty and fromAmount has a value, set toAmount to fromAmount
        if ((toAmount.value === '' || toAmount.value === '0' || toAmount.value === 0) && fromAmount.value !== '') {
            toAmount.value = fromAmount.value;
        }
        toAmount.addEventListener('input', function() {
            toAmountManuallyChanged = true;
        });
        fromAmount.addEventListener('input', function() {
            if (!toAmountManuallyChanged) {
                toAmount.value = fromAmount.value;
                toAmount.dispatchEvent(new Event('input'));
            }
        });
        // Reset manual override if values match
        fromAmount.addEventListener('input', function() {
            if (toAmount.value === fromAmount.value) {
                toAmountManuallyChanged = false;
            }
        });
    }
});
</script>

<?php
// Clear session data
unset($_SESSION['errors']);
unset($_SESSION['old']);
?>
