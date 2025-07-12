<?php include_once __DIR__ . '/../partials/amount_format.php'; ?>

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
                                        <?= amount_format($b['balance']) ?>
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
    <div class="col">
        <h3>Expenditure Analytics</h3>
        <a href="/analytics/expenditure" class="btn btn-outline-primary mb-3">Go to Expenditure Analytics</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <h3>Crypto Analytics</h3>
        <a href="/analytics/crypto" class="btn btn-outline-primary mb-3">Go to Crypto Analytics</a>
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
