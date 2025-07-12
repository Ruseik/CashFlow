<?php include_once __DIR__ . '/../partials/amount_format.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h2>Crypto Analytics</h2>
    </div>
</div>

<form id="crypto-filters" class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="cryptoDateFrom" class="form-label">From</label>
        <input type="date" class="form-control" id="cryptoDateFrom" name="date_from" value="<?= htmlspecialchars($date_from ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label for="cryptoDateTo" class="form-label">To</label>
        <input type="date" class="form-control" id="cryptoDateTo" name="date_to" value="<?= htmlspecialchars($date_to ?? '') ?>">
    </div>
    <div class="col-md-4 align-self-end">
        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
    </div>
</form>

<div class="row mb-4">
    <?php if (empty($cryptoBalances)): ?>
        <div class="col">
            <div class="alert alert-info">
                No crypto balances found for the selected period.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($cryptoBalances as $entity): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><?= htmlspecialchars($entity['entity_name']) ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Currency</th>
                                        <th class="text-end">Inflow</th>
                                        <th class="text-end">Outflow</th>
                                        <th class="text-end">Net Flow</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($entity['currencies'] as $currency): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($currency['currency_code']) ?></td>
                                            <td class="text-end"><?= amount_format($currency['inflow']) ?></td>
                                            <td class="text-end"><?= amount_format($currency['outflow']) ?></td>
                                            <td class="text-end <?= $currency['netflow'] < 0 ? 'text-danger' : 'text-success' ?>"><?= amount_format($currency['netflow']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Crypto Balance Visualization</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="cryptoBalanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prepare data for chart
const cryptoBalances = <?= json_encode($cryptoBalances ?? []) ?>;
const entities = [];
const datasets = [];
const colors = [
    'rgba(255, 99, 132, 0.7)',
    'rgba(54, 162, 235, 0.7)',
    'rgba(255, 206, 86, 0.7)',
    'rgba(75, 192, 192, 0.7)',
    'rgba(153, 102, 255, 0.7)',
    'rgba(255, 159, 64, 0.7)'
];

// Process data for chart
cryptoBalances.forEach((entity, entityIndex) => {
    entities.push(entity.entity_name);
    
    entity.currencies.forEach((currency, currencyIndex) => {
        // Check if dataset for this currency exists
        let datasetIndex = datasets.findIndex(d => d.label === currency.currency_code);
        
        if (datasetIndex === -1) {
            // Create new dataset for this currency
            datasetIndex = datasets.length;
            datasets.push({
                label: currency.currency_code,
                data: Array(cryptoBalances.length).fill(0),
                backgroundColor: colors[datasets.length % colors.length]
            });
        }
        
        // Set the netflow value for this entity and currency
        datasets[datasetIndex].data[entityIndex] = currency.netflow;
    });
});

// Create the chart
const ctx = document.getElementById('cryptoBalanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: entities,
        datasets: datasets
    },
    options: {
        responsive: true,
        scales: {
            x: {
                stacked: false
            },
            y: {
                stacked: false
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Crypto Balance by Entity and Currency'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat('en-US', {
                                style: 'decimal',
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 8
                            }).format(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        }
    }
});
</script>
