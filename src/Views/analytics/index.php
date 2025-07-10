<div class="row mb-4">
    <div class="col">
        <h2>Analytics Dashboard</h2>
    </div>
</div>

<div class="row mb-3">
    <div class="col">
        <div class="card border-primary shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">Balance (LKR)</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Entity</th>
                                <th class="text-end">Balance (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($balances as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['name']) ?></td>
                                    <td class="text-end fw-bold <?= $b['balance'] < 0 ? 'text-danger' : 'text-success' ?>">
                                        <?= number_format($b['balance'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($balances)): ?>
                                <tr><td colspan="2" class="text-center">No entities found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expenditure Analytics Section -->
<div class="row mb-4">
    <div class="col">
        <h3>Expenditure Analytics</h3>
        <form id="expenditure-filters" class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="expDateFrom" class="form-label">From</label>
                <input type="date" class="form-control" id="expDateFrom" name="date_from" value="<?= htmlspecialchars($exp_date_from ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="expDateTo" class="form-label">To</label>
                <input type="date" class="form-control" id="expDateTo" name="date_to" value="<?= htmlspecialchars($exp_date_to ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="expCurrency" class="form-label">Currency</label>
                <select class="form-select" id="expCurrency" name="currency">
                    <option value="">All</option>
                    <?php foreach (($currencies ?? []) as $cur): ?>
                        <option value="<?= htmlspecialchars($cur['code']) ?>" <?= (isset($exp_currency) && $exp_currency == $cur['code']) ? 'selected' : '' ?>><?= htmlspecialchars($cur['code']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </form>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card text-bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Total Expenditure</h6>
                        <p class="card-text fs-4 fw-bold"><?= number_format($exp_total ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Average Expenditure</h6>
                        <p class="card-text fs-4 fw-bold"><?= number_format($exp_avg ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Number of Transactions</h6>
                        <p class="card-text fs-4 fw-bold"><?= (int)($exp_count ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Expenditure by Month</h6>
                        <div class="chart-container">
                            <canvas id="expenditureMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Expenditure Transactions</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Currency</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($expenditures ?? []) as $exp): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($exp['date']) ?></td>
                                            <td><?= number_format($exp['amount'], 2) ?></td>
                                            <td><?= htmlspecialchars($exp['currency']) ?></td>
                                            <td><?= htmlspecialchars($exp['description']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($expenditures)): ?>
                                        <tr><td colspan="4" class="text-center">No expenditure data</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Expenditure by Entity</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead><tr><th>Entity</th><th>Amount</th></tr></thead>
                                <tbody>
                                <?php $total_entity = 0; foreach (($exp_by_entity ?? []) as $row): $total_entity += $row['amount']; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['entity']) ?></td>
                                        <td><?= number_format($row['amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($exp_by_entity)): ?>
                                    <tr><td colspan="2" class="text-center">No data</td></tr>
                                <?php else: ?>
                                    <tr class="fw-bold"><td>Total</td><td><?= number_format($total_entity, 2) ?></td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Expenditure by Purpose</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead><tr><th>Purpose</th><th>Amount</th></tr></thead>
                                <tbody>
                                <?php $total_purpose = 0; foreach (($exp_by_purpose ?? []) as $row): $total_purpose += $row['amount']; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                                        <td><?= number_format($row['amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($exp_by_purpose)): ?>
                                    <tr><td colspan="2" class="text-center">No data</td></tr>
                                <?php else: ?>
                                    <tr class="fw-bold"><td>Total</td><td><?= number_format($total_purpose, 2) ?></td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Expenditure by Mode</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead><tr><th>Mode</th><th>Amount</th></tr></thead>
                                <tbody>
                                <?php $total_mode = 0; foreach (($exp_by_mode ?? []) as $row): $total_mode += $row['amount']; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['mode']) ?></td>
                                        <td><?= number_format($row['amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($exp_by_mode)): ?>
                                    <tr><td colspan="2" class="text-center">No data</td></tr>
                                <?php else: ?>
                                    <tr class="fw-bold"><td>Total</td><td><?= number_format($total_mode, 2) ?></td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prepare data for charts
const analytics = <?= json_encode($analytics) ?>;
const months = [...new Set(analytics.map(row => row.month))];
const currencies = [...new Set(analytics.map(row => row.currency))];

// Cash Flow Data
const cashFlowDatasets = currencies.map(currency => ({
    label: currency,
    data: months.map(month => {
        const row = analytics.find(r => r.month === month && r.currency === currency);
        return row ? parseFloat(row.total_amount) : 0;
    }),
    fill: false,
    borderColor: '#' + Math.floor(Math.random()*16777215).toString(16),
    tension: 0.1
}));

// Fees & Profit Data
const feesDatasets = currencies.map(currency => ({
    label: currency + ' Fees',
    data: months.map(month => {
        const row = analytics.find(r => r.month === month && r.currency === currency);
        return row ? parseFloat(row.total_fees) : 0;
    }),
    backgroundColor: 'rgba(255,99,132,0.5)'
}));
const profitDatasets = currencies.map(currency => ({
    label: currency + ' Profit',
    data: months.map(month => {
        const row = analytics.find(r => r.month === month && r.currency === currency);
        return row ? parseFloat(row.total_profit) : 0;
    }),
    backgroundColor: 'rgba(54,162,235,0.5)'
}));

// Cash Flow Chart
const ctx1 = document.getElementById('cashFlowChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: months,
        datasets: cashFlowDatasets
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Monthly Cash Flow' }
        }
    }
});

// Fees & Profit Chart
const ctx2 = document.getElementById('feesProfitChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [...feesDatasets, ...profitDatasets]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Monthly Fees & Profit' }
        }
    }
});

// Expenditure Chart Data
const expenditureByMonth = <?= json_encode($exp_by_month ?? []) ?>;
const expMonths = expenditureByMonth.map(row => row.month);
const expAmounts = expenditureByMonth.map(row => parseFloat(row.amount));

const ctxExp = document.getElementById('expenditureMonthChart').getContext('2d');
new Chart(ctxExp, {
    type: 'bar',
    data: {
        labels: expMonths,
        datasets: [{
            label: 'Expenditure',
            data: expAmounts,
            backgroundColor: 'rgba(255, 99, 132, 0.5)'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Monthly Expenditure' }
        }
    }
});
</script>
