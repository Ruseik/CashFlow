<div class="row mb-4">
    <div class="col-md-6">
        <h2>Currencies</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="/currencies/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Currency
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Symbol</th>
                        <th>Show in Basic Mode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currencies as $currency): ?>
                        <tr>
                            <td><?= htmlspecialchars($currency['code']) ?></td>
                            <td><?= htmlspecialchars($currency['name']) ?></td>
                            <td><?= htmlspecialchars($currency['symbol']) ?></td>
                            <td>
                                <?php if ($currency['show_in_basic_mode']): ?>
                                    <span class="badge bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/currencies/<?= $currency['id'] ?>/edit" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="/currencies/<?= $currency['id'] ?>/delete" method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this currency?');">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($currencies)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox-fill display-4 d-block mb-3"></i>
                                    No currencies found
                                </div>
                                <a href="/currencies/create" class="btn btn-primary mt-2">
                                    Add your first currency
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
