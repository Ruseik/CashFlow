<div class="row mb-4">
    <div class="col">
        <h2>Transaction Details</h2>
    </div>
    <div class="col text-end">
        <div class="btn-group">
            <a href="/transactions/<?= $transaction['id'] ?>/edit" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash"></i> Delete
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Main Transaction Details -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Transaction Information</h5>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Description:</strong>
                    </div>
                    <div class="col-md-9">
                        <?= htmlspecialchars($transaction['name']) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Date:</strong>
                    </div>
                    <div class="col-md-9">
                        <?= date('F j, Y', strtotime($transaction['date'])) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>From:</strong>
                    </div>
                    <div class="col-md-9">
                        <?= htmlspecialchars($startEntity['name']) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Amount:</strong>
                    </div>
                    <div class="col-md-9">
                        <?= htmlspecialchars($startCurrency['code']) ?> 
                        <?= number_format($transaction['start_amount'], 2) ?>
                    </div>
                </div>

                <?php if ($transaction['dest_entity_id'] != 1 || $transaction['dest_amount'] != 0): ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>To:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= htmlspecialchars($destEntity['name']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Destination Amount:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= htmlspecialchars($destCurrency['code']) ?> 
                            <?= number_format($transaction['dest_amount'], 2) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($transaction['fee_amount'] > 0): ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Fee:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= htmlspecialchars($feeCurrency['code']) ?> 
                            <?= number_format($transaction['fee_amount'], 2) ?>
                            (<?= htmlspecialchars($feeEntity['name']) ?>)
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($transaction['grid_profit'] != 0): ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Grid Profit:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= number_format($transaction['grid_profit'], 2) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Additional Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Purpose:</strong>
                    </div>
                    <div class="col-md-9">
                        <?= htmlspecialchars($purpose['name']) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Mode:</strong>
                    </div>
                    <div class="col-md-9">
                        <?= htmlspecialchars($mode['name']) ?>
                    </div>
                </div>

                <?php if (!empty($transaction['remarks'])): ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Remarks:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= nl2br(htmlspecialchars($transaction['remarks'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-9">
                        <?= date('F j, Y H:i:s', strtotime($transaction['created_at'])) ?>
                    </div>
                </div>

                <?php if ($transaction['updated_at'] != $transaction['created_at']): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Last Updated:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= date('F j, Y H:i:s', strtotime($transaction['updated_at'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="/transactions/create?mode=basic" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> New Quick Transaction
                    </a>
                    <a href="/transactions" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> View All Transactions
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Information -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Related Information</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-wallet2"></i>
                        <a href="#" class="text-decoration-none">
                            View <?= htmlspecialchars($startEntity['name']) ?> Transactions
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-tag"></i>
                        <a href="#" class="text-decoration-none">
                            View <?= htmlspecialchars($purpose['name']) ?> Transactions
                        </a>
                    </li>
                    <li>
                        <i class="bi bi-gear"></i>
                        <a href="#" class="text-decoration-none">
                            View <?= htmlspecialchars($mode['name']) ?> Transactions
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this transaction?
                <br><br>
                <strong><?= htmlspecialchars($transaction['name']) ?></strong>
                <br>
                <?= date('Y-m-d', strtotime($transaction['date'])) ?>
            </div>
            <div class="modal-footer">
                <form action="/transactions/<?= $transaction['id'] ?>/delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
