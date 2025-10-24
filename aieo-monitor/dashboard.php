<?php
// Connect to SQLite database
$db = new SQLite3('query_responses.db');

// Get summary statistics
$citedCount = $db->querySingle('SELECT COUNT(*) as count FROM responses WHERE paintballevents_referenced = 1', true);
$citedQueries = $citedCount['count'];

$totalCount = $db->querySingle('SELECT COUNT(*) as total FROM responses', true);
$totalQueries = $totalCount['total'];

$citationRate = $totalQueries > 0 ? round(($citedQueries / $totalQueries) * 100, 1) : 0;

// Get model performance data
$modelQuery = "
    SELECT 
        model,
        SUM(paintballevents_referenced) as times_cited,
        COUNT(*) as times_tested,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate
    FROM responses
    WHERE model IS NOT NULL
    GROUP BY model
    ORDER BY model
";
$modelResults = $db->query($modelQuery);
$modelData = [];
while ($row = $modelResults->fetchArray(SQLITE3_ASSOC)) {
    $modelData[] = $row;
}

// Get citations over time per model
$timeSeriesQuery = "
    SELECT 
        DATE(timestamp) as date,
        model,
        SUM(paintballevents_referenced) as citations,
        COUNT(*) as total_queries
    FROM responses
    WHERE model IS NOT NULL
    GROUP BY DATE(timestamp), model
    ORDER BY date ASC, model
";
$timeSeriesResults = $db->query($timeSeriesQuery);
$timeSeriesData = [];
while ($row = $timeSeriesResults->fetchArray(SQLITE3_ASSOC)) {
    $timeSeriesData[] = $row;
}

// Get recent citation events
$recentQuery = "
    SELECT 
        timestamp,
        model,
        query,
        cited_urls
    FROM responses
    WHERE paintballevents_referenced = 1
    ORDER BY timestamp DESC
    LIMIT 20
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
    <title>MONITOR // SYSTEM TERMINAL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'IBM Plex Mono', 'Courier New', monospace;
            background: #0a0a0a;
            min-height: 100vh;
            padding: 20px;
            color: #00d9ff;
            letter-spacing: 0.5px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* CRT Scanline Effect */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 217, 255, 0.03) 0px,
                rgba(0, 0, 0, 0.05) 1px,
                rgba(0, 0, 0, 0.05) 2px,
                rgba(0, 217, 255, 0.03) 3px
            );
            pointer-events: none;
            z-index: 9999;
            animation: scanlines 8s linear infinite;
        }
        
        @keyframes scanlines {
            0% { transform: translateY(0); }
            100% { transform: translateY(4px); }
        }
        
        /* CRT Flicker */
        @keyframes flicker {
            0%, 100% { opacity: 1; }
            41.99% { opacity: 1; }
            42% { opacity: 0.8; }
            43% { opacity: 1; }
            45.99% { opacity: 1; }
            46% { opacity: 0.9; }
            46.5% { opacity: 1; }
        }
        
        body {
            animation: flicker 5s infinite;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .header {
            text-align: left;
            color: #00d9ff;
            margin-bottom: 30px;
            padding: 25px;
            border: 2px solid #00d9ff;
            background: rgba(0, 217, 255, 0.02);
            box-shadow: 0 0 20px rgba(0, 217, 255, 0.3), inset 0 0 20px rgba(0, 217, 255, 0.05);
            position: relative;
        }
        
        .header::before {
            content: '> SYSTEM ACTIVE_';
            position: absolute;
            top: -12px;
            left: 20px;
            background: #0a0a0a;
            padding: 0 10px;
            font-size: 0.85em;
            letter-spacing: 2px;
            text-shadow: 0 0 10px #00d9ff;
        }
        
        .header h1 {
            font-size: 2.2em;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 0 10px rgba(0, 217, 255, 0.8), 0 0 20px rgba(0, 217, 255, 0.4);
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        .header p {
            font-size: 0.95em;
            opacity: 0.8;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .stat-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00d9ff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.3), inset 0 0 15px rgba(0, 217, 255, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 217, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .stat-card:hover::before {
            left: 100%;
        }
        
        .stat-card:hover {
            box-shadow: 0 0 25px rgba(0, 217, 255, 0.5), inset 0 0 25px rgba(0, 217, 255, 0.1);
            border-color: #0af;
        }
        
        .stat-card h3 {
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #0af;
            margin-bottom: 15px;
            font-weight: 600;
            text-shadow: 0 0 5px rgba(0, 217, 255, 0.5);
        }
        
        .stat-card .value {
            font-size: 2.8em;
            font-weight: 700;
            color: #00d9ff;
            margin-bottom: 8px;
            text-shadow: 0 0 10px rgba(0, 217, 255, 0.8);
            font-family: 'IBM Plex Mono', monospace;
        }
        
        .stat-card .label {
            font-size: 0.8em;
            color: #0af;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00d9ff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.3), inset 0 0 15px rgba(0, 217, 255, 0.05);
            position: relative;
        }
        
        .chart-card::before {
            content: '[ DATA VISUALIZATION ]';
            position: absolute;
            top: -12px;
            left: 20px;
            background: #0a0a0a;
            padding: 0 10px;
            font-size: 0.7em;
            letter-spacing: 2px;
            color: #0af;
            text-shadow: 0 0 5px rgba(0, 217, 255, 0.5);
        }
        
        .chart-card h2 {
            font-size: 1.1em;
            margin-bottom: 20px;
            color: #00d9ff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 8px rgba(0, 217, 255, 0.6);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .table-container {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00d9ff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.3), inset 0 0 15px rgba(0, 217, 255, 0.05);
            overflow-x: auto;
            position: relative;
        }
        
        .table-container::before {
            content: '[ QUERY LOG ]';
            position: absolute;
            top: -12px;
            left: 20px;
            background: #0a0a0a;
            padding: 0 10px;
            font-size: 0.7em;
            letter-spacing: 2px;
            color: #0af;
            text-shadow: 0 0 5px rgba(0, 217, 255, 0.5);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: rgba(0, 217, 255, 0.1);
            color: #00d9ff;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.8em;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 2px solid #00d9ff;
            text-shadow: 0 0 5px rgba(0, 217, 255, 0.5);
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid rgba(0, 217, 255, 0.2);
            color: #0af;
        }
        
        tr:hover {
            background: rgba(0, 217, 255, 0.05);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .badge-success {
            background: rgba(0, 217, 255, 0.2);
            color: #00ff88;
            border: 1px solid #00ff88;
            text-shadow: 0 0 5px rgba(0, 255, 136, 0.5);
        }
        
        .badge-danger {
            background: rgba(255, 0, 100, 0.1);
            color: #ff0064;
            border: 1px solid #ff0064;
            text-shadow: 0 0 5px rgba(255, 0, 100, 0.5);
        }
        
        .model-tag {
            background: rgba(0, 217, 255, 0.15);
            color: #00d9ff;
            padding: 3px 10px;
            font-size: 0.75em;
            font-weight: 500;
            border: 1px solid #00d9ff;
            text-shadow: 0 0 5px rgba(0, 217, 255, 0.3);
        }
        
        .timestamp {
            color: #0af;
            font-size: 0.85em;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.6em;
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
                font-size: 0.85em;
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
            
            .header::before {
                font-size: 0.75em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>/// MONITOR TERMINAL v2.1</h1>
            <p>LLM Citation Tracking System :: paintballevents.net</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>TOTAL_CITATIONS</h3>
                <div class="value"><?php echo $citedQueries; ?></div>
                <div class="label">:: PAINTBALLEVENTS.NET REFERENCES</div>
            </div>
            
            <div class="stat-card">
                <h3>CITATION_RATE</h3>
                <div class="value"><?php echo $citationRate; ?>%</div>
                <div class="label">:: <?php echo $citedQueries; ?> / <?php echo $totalQueries; ?> QUERIES</div>
            </div>
            
            <div class="stat-card">
                <h3>MODELS_TRACKED</h3>
                <div class="value"><?php echo count($modelData); ?></div>
                <div class="label">:: ACTIVE LLM SYSTEMS</div>
            </div>
        </div>
        
        <div class="chart-grid">
            <div class="chart-card full-width">
                <h2>> CITATIONS_OVER_TIME_PER_LLM</h2>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="timeSeriesChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h2>> TOTAL_CITATIONS_BY_MODEL</h2>
                <div class="chart-container">
                    <canvas id="modelChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h2>> CITATION_RATE_BY_MODEL</h2>
                <div class="chart-container">
                    <canvas id="citationRateChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="table-container">
            <h2 style="margin-bottom: 20px; margin-top: 10px; color: #00d9ff; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 0 8px rgba(0, 217, 255, 0.6);">> RECENT_CITATIONS_LOG</h2>
            <table>
                <thead>
                    <tr>
                        <th>TIMESTAMP</th>
                        <th>MODEL</th>
                        <th>QUERY</th>
                        <th>URLS_CITED</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentData as $row): ?>
                    <tr>
                        <td class="timestamp"><?php echo date('M j, g:i A', strtotime($row['timestamp'])); ?></td>
                        <td><span class="model-tag"><?php 
                            $modelName = $row['model'] ?? 'N/A';
                            $displayName = str_replace(
                                ['claude-3-7-sonnet-20250219', 'gpt-4o'],
                                ['Claude 3.7', 'GPT-4o'],
                                $modelName
                            );
                            echo htmlspecialchars($displayName);
                        ?></span></td>
                        <td><?php echo htmlspecialchars(substr($row['query'], 0, 80)) . (strlen($row['query']) > 80 ? '...' : ''); ?></td>
                        <td style="text-align: center;"><span class="badge badge-success"><?php echo $row['cited_urls'] ? count(json_decode($row['cited_urls'])) : 0; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Prepare data
        const modelData = <?php echo json_encode($modelData); ?>;
        const timeSeriesData = <?php echo json_encode($timeSeriesData); ?>;
        
        const modelLabels = modelData.map(m => m.model ? m.model.replace('claude-3-7-sonnet-20250219', 'Claude 3.7').replace('gpt-4o', 'GPT-4o') : 'Unknown');
        const modelCitations = modelData.map(m => m.times_cited);
        const citationRates = modelData.map(m => m.citation_rate);
        
        // Process time series data per model
        const uniqueDates = [...new Set(timeSeriesData.map(d => d.date))].sort();
        const uniqueModels = [...new Set(timeSeriesData.map(d => d.model))];
        
        const colors = [
            { bg: 'rgba(0, 217, 255, 0.3)', border: 'rgba(0, 217, 255, 1)' },
            { bg: 'rgba(0, 255, 136, 0.3)', border: 'rgba(0, 255, 136, 1)' },
            { bg: 'rgba(255, 170, 0, 0.3)', border: 'rgba(255, 170, 0, 1)' },
            { bg: 'rgba(170, 0, 255, 0.3)', border: 'rgba(170, 0, 255, 1)' }
        ];
        
        const timeSeriesDatasets = uniqueModels.map((model, idx) => {
            const modelName = model.replace('claude-3-7-sonnet-20250219', 'Claude 3.7').replace('gpt-4o', 'GPT-4o');
            const data = uniqueDates.map(date => {
                const entry = timeSeriesData.find(d => d.date === date && d.model === model);
                return entry ? entry.citations : 0;
            });
            
            return {
                label: modelName,
                data: data,
                backgroundColor: colors[idx % colors.length].bg,
                borderColor: colors[idx % colors.length].border,
                borderWidth: 2,
                fill: false,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6
            };
        });
        
        // Time Series Chart
        new Chart(document.getElementById('timeSeriesChart'), {
            type: 'line',
            data: {
                labels: uniqueDates.map(d => {
                    const date = new Date(d + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }),
                datasets: timeSeriesDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 11, weight: '600', family: "'IBM Plex Mono', monospace" },
                            color: '#00d9ff'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        borderColor: '#00d9ff',
                        borderWidth: 1,
                        titleFont: { family: "'IBM Plex Mono', monospace" },
                        bodyFont: { family: "'IBM Plex Mono', monospace" }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af',
                            stepSize: 1
                        },
                        grid: { color: 'rgba(0, 217, 255, 0.1)' },
                        border: { color: '#00d9ff' },
                        title: {
                            display: true,
                            text: 'Citations',
                            color: '#00d9ff',
                            font: { size: 11, family: "'IBM Plex Mono', monospace" }
                        }
                    },
                    x: {
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af'
                        },
                        grid: { display: false },
                        border: { color: '#00d9ff' }
                    }
                }
            }
        });
        
        // Total Citations by Model Chart
        new Chart(document.getElementById('modelChart'), {
            type: 'bar',
            data: {
                labels: modelLabels,
                datasets: [{
                    label: 'Total Citations',
                    data: modelCitations,
                    backgroundColor: 'rgba(0, 255, 136, 0.3)',
                    borderColor: 'rgba(0, 255, 136, 1)',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af',
                            stepSize: 1
                        },
                        grid: { color: 'rgba(0, 217, 255, 0.1)' },
                        border: { color: '#00d9ff' }
                    },
                    x: {
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af'
                        },
                        grid: { display: false },
                        border: { color: '#00d9ff' }
                    }
                }
            }
        });
        
        // Citation Rate Chart
        new Chart(document.getElementById('citationRateChart'), {
            type: 'bar',
            data: {
                labels: modelLabels,
                datasets: [{
                    label: 'Citation Rate (%)',
                    data: citationRates,
                    backgroundColor: 'rgba(0, 217, 255, 0.3)',
                    borderColor: 'rgba(0, 217, 255, 1)',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        borderColor: '#00d9ff',
                        borderWidth: 1,
                        titleFont: { family: "'IBM Plex Mono', monospace" },
                        bodyFont: { family: "'IBM Plex Mono', monospace" },
                        callbacks: {
                            label: function(context) {
                                return 'Rate: ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af',
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: { color: 'rgba(0, 217, 255, 0.1)' },
                        border: { color: '#00d9ff' }
                    },
                    x: {
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af'
                        },
                        grid: { display: false },
                        border: { color: '#00d9ff' }
                    }
                }
            }
        });
    </script>
</body>
</html>

