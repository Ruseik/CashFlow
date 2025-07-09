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

<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Monthly Cash Flow</h5>
                <div class="chart-container">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Monthly Fees & Profit</h5>
                <div class="chart-container">
                    <canvas id="feesProfitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Summary Table</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Currency</th>
                                <th>Total Amount</th>
                                <th>Total Fees</th>
                                <th>Total Profit</th>
                                <th>Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analytics as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['month']) ?></td>
                                    <td><?= htmlspecialchars($row['currency']) ?></td>
                                    <td><?= number_format($row['total_amount'], 2) ?></td>
                                    <td><?= number_format($row['total_fees'], 2) ?></td>
                                    <td><?= number_format($row['total_profit'], 2) ?></td>
                                    <td><?= (int)$row['transaction_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($analytics)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
</script>
