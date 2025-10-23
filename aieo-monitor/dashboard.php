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

// Get detailed query breakdown
$detailedQuery = "
    SELECT 
        query,
        model,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_cited,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate,
        GROUP_CONCAT(id) as response_ids
    FROM responses
    GROUP BY query, model
    ORDER BY query, model
";
$detailedResults = $db->query($detailedQuery);
$detailedData = [];
while ($row = $detailedResults->fetchArray(SQLITE3_ASSOC)) {
    $query = $row['query'];
    if (!isset($detailedData[$query])) {
        $detailedData[$query] = [
            'total_tests' => 0,
            'total_citations' => 0,
            'models' => []
        ];
    }
    $detailedData[$query]['total_tests'] += $row['times_tested'];
    $detailedData[$query]['total_citations'] += $row['times_cited'];
    $detailedData[$query]['models'][] = $row;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
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
        
        .query-breakdown {
            margin-bottom: 30px;
        }
        
        .query-item {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00d9ff;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .query-item:hover {
            box-shadow: 0 0 25px rgba(0, 217, 255, 0.5);
        }
        
        .query-header {
            padding: 20px 25px;
            background: rgba(0, 217, 255, 0.05);
            color: #00d9ff;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid rgba(0, 217, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .query-header:hover {
            background: rgba(0, 217, 255, 0.1);
            text-shadow: 0 0 8px rgba(0, 217, 255, 0.8);
        }
        
        .query-title {
            font-size: 0.95em;
            font-weight: 600;
            flex: 1;
            letter-spacing: 0.5px;
        }
        
        .query-stats {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .query-stat {
            text-align: center;
            padding: 5px 15px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 217, 255, 0.3);
            font-size: 0.85em;
        }
        
        .query-stat-value {
            font-weight: 700;
            font-size: 1.3em;
            text-shadow: 0 0 8px rgba(0, 217, 255, 0.6);
        }
        
        .query-stat-label {
            font-size: 0.75em;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .toggle-icon {
            font-size: 1.5em;
            transition: transform 0.3s ease;
            text-shadow: 0 0 8px rgba(0, 217, 255, 0.6);
        }
        
        .query-item.active .toggle-icon {
            transform: rotate(180deg);
        }
        
        .query-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .query-item.active .query-content {
            max-height: 1000px;
        }
        
        .query-models {
            padding: 25px;
            background: rgba(0, 0, 0, 0.4);
        }
        
        .model-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 15px;
            padding: 15px;
            margin-bottom: 10px;
            background: rgba(0, 217, 255, 0.05);
            border: 1px solid rgba(0, 217, 255, 0.2);
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .model-row:hover {
            background: rgba(0, 217, 255, 0.1);
            border-color: #00d9ff;
            box-shadow: 0 0 10px rgba(0, 217, 255, 0.3);
        }
        
        .model-name {
            font-weight: 600;
            color: #00d9ff;
            text-shadow: 0 0 5px rgba(0, 217, 255, 0.5);
        }
        
        .model-metric {
            text-align: center;
        }
        
        .model-metric-value {
            font-size: 1.3em;
            font-weight: 700;
            color: #00d9ff;
            text-shadow: 0 0 8px rgba(0, 217, 255, 0.6);
        }
        
        .model-metric-label {
            font-size: 0.7em;
            color: #0af;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 217, 255, 0.3);
            overflow: hidden;
            margin-top: 8px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00d9ff 0%, #0af 100%);
            transition: width 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 217, 255, 0.8);
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
            
            .model-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .query-stat {
                font-size: 0.75em;
                padding: 4px 10px;
            }
            
            .query-stats {
                gap: 10px;
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
                <h3>TOTAL_QUERIES</h3>
                <div class="value"><?php echo $totalQueries; ?></div>
                <div class="label">:: API CALLS EXECUTED</div>
            </div>
            
            <div class="stat-card">
                <h3>CITATIONS_DETECTED</h3>
                <div class="value"><?php echo $citedQueries; ?></div>
                <div class="label">:: PAINTBALLEVENTS.NET REFS</div>
            </div>
            
            <div class="stat-card">
                <h3>CITATION_RATE</h3>
                <div class="value"><?php echo $citationRate; ?>%</div>
                <div class="label">:: SYSTEM PERFORMANCE</div>
            </div>
            
            <div class="stat-card">
                <h3>MODELS_ACTIVE</h3>
                <div class="value"><?php echo count($modelData); ?></div>
                <div class="label">:: AI MODELS TRACKED</div>
            </div>
        </div>
        
        <div class="chart-grid">
            <div class="chart-card">
                <h2>> MODEL_PERFORMANCE_ANALYSIS</h2>
                <div class="chart-container">
                    <canvas id="modelChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h2>> CITATION_RATE_METRICS</h2>
                <div class="chart-container">
                    <canvas id="citationRateChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card full-width">
                <h2>> QUERY_PATTERN_ANALYSIS</h2>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="queryChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="query-breakdown">
            <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #00d9ff; padding: 25px; box-shadow: 0 0 15px rgba(0, 217, 255, 0.3); margin-bottom: 20px; position: relative;">
                <div style="content: ''; position: absolute; top: -12px; left: 20px; background: #0a0a0a; padding: 0 10px; font-size: 0.7em; letter-spacing: 2px; color: #0af; text-shadow: 0 0 5px rgba(0, 217, 255, 0.5);">[ DETAILED ANALYSIS ]</div>
                <h2 style="margin-bottom: 10px; color: #00d9ff; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 0 8px rgba(0, 217, 255, 0.6); margin-top: 10px;">> QUERY_BREAKDOWN_MATRIX</h2>
                <p style="color: #0af; margin-bottom: 20px; font-size: 0.9em; letter-spacing: 0.5px;">// Expand query records for model-specific performance data</p>
            </div>
            
            <?php foreach ($detailedData as $query => $data): 
                $overallRate = $data['total_tests'] > 0 ? round(($data['total_citations'] / $data['total_tests']) * 100, 1) : 0;
            ?>
            <div class="query-item">
                <div class="query-header" onclick="toggleQuery(this)">
                    <div class="query-title"><?php echo htmlspecialchars($query); ?></div>
                    <div class="query-stats">
                        <div class="query-stat">
                            <div class="query-stat-value"><?php echo $data['total_tests']; ?></div>
                            <div class="query-stat-label">Tests</div>
                        </div>
                        <div class="query-stat">
                            <div class="query-stat-value"><?php echo $data['total_citations']; ?></div>
                            <div class="query-stat-label">Citations</div>
                        </div>
                        <div class="query-stat">
                            <div class="query-stat-value"><?php echo $overallRate; ?>%</div>
                            <div class="query-stat-label">Rate</div>
                        </div>
                        <div class="toggle-icon">▼</div>
                    </div>
                </div>
                <div class="query-content">
                    <div class="query-models">
                        <?php foreach ($data['models'] as $model): ?>
                        <div class="model-row">
                            <div class="model-name">
                                <?php 
                                $modelName = $model['model'] ?? 'Unknown';
                                $displayName = str_replace(
                                    ['claude-3-7-sonnet-20250219', 'gpt-4o'],
                                    ['Claude 3.7 Sonnet', 'GPT-4o'],
                                    $modelName
                                );
                                echo htmlspecialchars($displayName);
                                ?>
                            </div>
                            <div class="model-metric">
                                <div class="model-metric-value"><?php echo $model['times_tested']; ?></div>
                                <div class="model-metric-label">Tests</div>
                            </div>
                            <div class="model-metric">
                                <div class="model-metric-value"><?php echo $model['times_cited']; ?></div>
                                <div class="model-metric-label">Citations</div>
                            </div>
                            <div class="model-metric">
                                <div class="model-metric-value"><?php echo $model['citation_rate']; ?>%</div>
                                <div class="model-metric-label">Rate</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $model['citation_rate']; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="table-container">
            <h2 style="margin-bottom: 20px; margin-top: 10px; color: #00d9ff; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 0 8px rgba(0, 217, 255, 0.6);">> RECENT_ACTIVITY_LOG</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>TIMESTAMP</th>
                        <th>QUERY_STRING</th>
                        <th>MODEL_ID</th>
                        <th>STATUS</th>
                        <th>URLS</th>
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
        // Toggle query expansion
        function toggleQuery(header) {
            const queryItem = header.parentElement;
            queryItem.classList.toggle('active');
        }
        
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
                        backgroundColor: 'rgba(0, 217, 255, 0.3)',
                        borderColor: 'rgba(0, 217, 255, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    },
                    {
                        label: 'Citations Found',
                        data: modelCitations,
                        backgroundColor: 'rgba(0, 255, 136, 0.3)',
                        borderColor: 'rgba(0, 255, 136, 1)',
                        borderWidth: 2,
                        borderRadius: 5
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
                            font: { size: 11, weight: '600', family: "'IBM Plex Mono', monospace" },
                            color: '#00d9ff'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af'
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
        const citationRates = modelData.map(m => m.citation_rate);
        
        new Chart(document.getElementById('citationRateChart'), {
            type: 'doughnut',
            data: {
                labels: modelLabels,
                datasets: [{
                    data: citationRates,
                    backgroundColor: [
                        'rgba(0, 217, 255, 0.6)',
                        'rgba(0, 170, 255, 0.6)',
                        'rgba(0, 255, 136, 0.6)',
                        'rgba(0, 200, 200, 0.6)'
                    ],
                    borderWidth: 2,
                    borderColor: '#0a0a0a',
                    hoverBorderColor: '#00d9ff',
                    hoverBorderWidth: 3
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
                            font: { size: 11, weight: '600', family: "'IBM Plex Mono', monospace" },
                            color: '#00d9ff'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        borderColor: '#00d9ff',
                        borderWidth: 1,
                        titleFont: { family: "'IBM Plex Mono', monospace" },
                        bodyFont: { family: "'IBM Plex Mono', monospace" },
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
                        backgroundColor: 'rgba(0, 217, 255, 0.3)',
                        borderColor: 'rgba(0, 217, 255, 1)',
                        borderWidth: 2,
                        borderRadius: 4
                    },
                    {
                        label: 'Citations Found',
                        data: queryCitations,
                        backgroundColor: 'rgba(0, 255, 136, 0.3)',
                        borderColor: 'rgba(0, 255, 136, 1)',
                        borderWidth: 2,
                        borderRadius: 4
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
                    x: {
                        beginAtZero: true,
                        ticks: { 
                            font: { size: 10, family: "'IBM Plex Mono', monospace" },
                            color: '#0af'
                        },
                        grid: { color: 'rgba(0, 217, 255, 0.1)' },
                        border: { color: '#00d9ff' }
                    },
                    y: {
                        ticks: { 
                            font: { size: 9, family: "'IBM Plex Mono', monospace" },
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

