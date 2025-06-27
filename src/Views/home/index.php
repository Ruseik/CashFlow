<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mb-4 mt-4">
                <div class="card-body">
                    <h2 class="card-title">Welcome, <?= htmlspecialchars($username) ?>!</h2>
                    <p class="card-text">This is your cash flow dashboard. Here are your 5 most recent transactions:</p>
                    <?php if (!empty($recentTransactions)): ?>
                        <ul class="list-group">
                            <?php foreach ($recentTransactions as $tx): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($tx['description'] ?? $tx['purpose_name'] ?? 'Transaction') ?></span>
                                    <span class="badge bg-<?= $tx['amount'] >= 0 ? 'success' : 'danger' ?>">
                                        <?= number_format($tx['amount'], 2) ?>
                                        <?= htmlspecialchars($tx['currency_code'] ?? '') ?>
                                    </span>
                                    <span class="text-muted small ms-2">
                                        <?= htmlspecialchars($tx['date'] ?? '') ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No transactions found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
