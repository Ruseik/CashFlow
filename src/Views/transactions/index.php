<div class="row mb-4">
    <div class="col-md-6">
        <h2>Transactions</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <div class="btn-group">
            <a href="/transactions/create?mode=basic" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Quick Add
            </a>
            <a href="/transactions/create?mode=full" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle-dotted"></i> Full Add
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>From</th>
                        <th>Amount</th>
                        <th>To</th>
                        <th>Amount</th>
                        <th>Purpose</th>
                        <th>Mode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($t['date']))) ?></td>
                            <td><?= htmlspecialchars($t['name']) ?></td>
                            <td><?= htmlspecialchars($t['start_entity_name']) ?></td>
                            <td>
                                <?= htmlspecialchars($t['start_currency_code']) ?> 
                                <?= number_format($t['start_amount'], 2) ?>
                            </td>
                            <td><?= htmlspecialchars($t['dest_entity_name']) ?></td>
                            <td>
                                <?= htmlspecialchars($t['dest_currency_code']) ?> 
                                <?= number_format($t['dest_amount'], 2) ?>
                            </td>
                            <td><?= htmlspecialchars($t['purpose_name']) ?></td>
                            <td><?= htmlspecialchars($t['mode_name']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/transactions/<?= $t['id'] ?>" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/transactions/<?= $t['id'] ?>/edit" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="/transactions/<?= $t['id'] ?>/delete" method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this transaction?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox-fill display-4 d-block mb-3"></i>
                                    No transactions found
                                </div>
                                <a href="/transactions/create?mode=basic" class="btn btn-primary mt-2">
                                    Create your first transaction
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
