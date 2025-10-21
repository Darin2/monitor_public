<?php
// Connect to SQLite database
$db = new SQLite3('query_responses.db');

// Get summary statistics
$summary = $db->querySingle('SELECT COUNT(*) as total FROM responses', true);
$totalQueries = $summary['total'];

$citedCount = $db->querySingle('SELECT COUNT(*) as count FROM responses WHERE paintballevents_referenced = 1', true);
$citedQueries = $citedCount['count'];

$citationRate = $totalQueries > 0 ? round(($citedQueries / $totalQueries) * 100, 1) : 0;

// Get model performance data
$modelQuery = "
    SELECT 
        model,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_cited,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate
    FROM responses
    WHERE model IS NOT NULL
    GROUP BY model
    ORDER BY times_tested DESC
";
$modelResults = $db->query($modelQuery);
$modelData = [];
while ($row = $modelResults->fetchArray(SQLITE3_ASSOC)) {
    $modelData[] = $row;
}

// Get query pattern data
$queryPattern = "
    SELECT 
        query,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_cited,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate
    FROM responses
    GROUP BY query
    ORDER BY times_tested DESC
    LIMIT 10
";
$queryResults = $db->query($queryPattern);
$queryData = [];
while ($row = $queryResults->fetchArray(SQLITE3_ASSOC)) {
    $queryData[] = $row;
}

// Get query + model combination data
$combinationQuery = "
    SELECT 
        query,
        model,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_cited,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate
    FROM responses
    WHERE model IS NOT NULL
    GROUP BY query, model
    ORDER BY query, model
";
$combinationResults = $db->query($combinationQuery);
$combinationData = [];
while ($row = $combinationResults->fetchArray(SQLITE3_ASSOC)) {
    $combinationData[] = $row;
}

// Get recent responses
$recentQuery = "
    SELECT 
        id,
        timestamp,
        query,
        model,
        paintballevents_referenced,
        cited_urls
    FROM responses
    ORDER BY timestamp DESC
    LIMIT 10
";
$recentResults = $db->query($recentQuery);
$recentData = [];
while ($row = $recentResults->fetchArray(SQLITE3_ASSOC)) {
    $recentData[] = $row;
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIEO Monitor Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            padding: 20px;
        }
        
        .header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .stat-card h3 {
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #667eea;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .stat-card .value {
            font-size: 2.5em;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            font-size: 0.9em;
            color: #666;
        }
        
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .chart-card h2 {
            font-size: 1.3em;
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f8f9ff;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .model-tag {
            background: #e7f3ff;
            color: #0066cc;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        
        .timestamp {
            color: #666;
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }
            
            .chart-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card .value {
                font-size: 2em;
            }
            
            .table-container {
                padding: 15px;
            }
            
            th, td {
                padding: 8px;
                font-size: 0.9em;
            }
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 AIEO Monitor Dashboard</h1>
            <p>PaintballEvents.net Citation Analysis</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Queries</h3>
                <div class="value"><?php echo $totalQueries; ?></div>
                <div class="label">API Calls Made</div>
            </div>
            
            <div class="stat-card">
                <h3>Citations Found</h3>
                <div class="value"><?php echo $citedQueries; ?></div>
                <div class="label">Times PaintballEvents.net Cited</div>
            </div>
            
            <div class="stat-card">
                <h3>Citation Rate</h3>
                <div class="value"><?php echo $citationRate; ?>%</div>
                <div class="label">Overall Performance</div>
            </div>
            
            <div class="stat-card">
                <h3>Models Tested</h3>
                <div class="value"><?php echo count($modelData); ?></div>
                <div class="label">AI Models Compared</div>
            </div>
        </div>
        
        <div class="chart-grid">
            <div class="chart-card">
                <h2>📊 Model Performance</h2>
                <div class="chart-container">
                    <canvas id="modelChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h2>🎯 Citation Rate by Model</h2>
                <div class="chart-container">
                    <canvas id="citationRateChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card full-width">
                <h2>📝 Top Query Patterns</h2>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="queryChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="table-container">
            <h2 style="margin-bottom: 20px;">📋 Recent Queries</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>Query</th>
                        <th>Model</th>
                        <th>CITED?</th>
                        <th>URLs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentData as $row): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td class="timestamp"><?php echo date('M j, Y g:i A', strtotime($row['timestamp'])); ?></td>
                        <td><?php echo htmlspecialchars(substr($row['query'], 0, 60)) . (strlen($row['query']) > 60 ? '...' : ''); ?></td>
                        <td><span class="model-tag"><?php echo htmlspecialchars($row['model'] ?? 'N/A'); ?></span></td>
                        <td>
                            <?php if ($row['paintballevents_referenced']): ?>
                                <span class="badge badge-success">✓ YES</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ NO</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['cited_urls'] ? count(json_decode($row['cited_urls'])) : 0; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Model Performance Chart
        const modelData = <?php echo json_encode($modelData); ?>;
        const modelLabels = modelData.map(m => m.model ? m.model.replace('claude-3-7-sonnet-20250219', 'Claude 3.7').replace('gpt-4o', 'GPT-4o') : 'Unknown');
        const modelTests = modelData.map(m => m.times_tested);
        const modelCitations = modelData.map(m => m.times_cited);
        
        new Chart(document.getElementById('modelChart'), {
            type: 'bar',
            data: {
                labels: modelLabels,
                datasets: [
                    {
                        label: 'Tests Run',
                        data: modelTests,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    },
                    {
                        label: 'Citations Found',
                        data: modelCitations,
                        backgroundColor: 'rgba(46, 213, 115, 0.8)',
                        borderColor: 'rgba(46, 213, 115, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '600' }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { font: { size: 11 } },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
        
        // Citation Rate Chart
        const citationRates = modelData.map(m => m.citation_rate);
        
        new Chart(document.getElementById('citationRateChart'), {
            type: 'doughnut',
            data: {
                labels: modelLabels,
                datasets: [{
                    data: citationRates,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(46, 213, 115, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Query Pattern Chart
        const queryData = <?php echo json_encode($queryData); ?>;
        const queryLabels = queryData.map(q => {
            const query = q.query || '';
            return query.length > 40 ? query.substring(0, 40) + '...' : query;
        });
        const queryTests = queryData.map(q => q.times_tested);
        const queryCitations = queryData.map(q => q.times_cited);
        
        new Chart(document.getElementById('queryChart'), {
            type: 'bar',
            data: {
                labels: queryLabels,
                datasets: [
                    {
                        label: 'Tests Run',
                        data: queryTests,
                        backgroundColor: 'rgba(102, 126, 234, 0.6)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    },
                    {
                        label: 'Citations Found',
                        data: queryCitations,
                        backgroundColor: 'rgba(46, 213, 115, 0.6)',
                        borderColor: 'rgba(46, 213, 115, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '600' }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { font: { size: 11 } },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>

