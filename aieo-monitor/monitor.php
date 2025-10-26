<?php
/**
 * AI Citation Monitor Dashboard - MySQL Version
 * 
 * This version connects to MySQL database on Bluehost
 * Compatible with the new schema that includes runs, queries, and models tables
 */

// Database configuration
// For Bluehost, use localhost and the credentials from cPanel
$host = getenv('MYSQL_HOST') ?: 'localhost';
$database = getenv('MYSQL_DATABASE') ?: 'darintec_monitor';
$username = getenv('MYSQL_USER') ?: 'darintec_monitor';
$password = getenv('MYSQL_PASSWORD') ?: 'your_password';

// Connect to MySQL database
try {
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get filter parameters
$filterModel = isset($_GET['model']) ? $_GET['model'] : '';
$filterQuery = isset($_GET['query']) ? $_GET['query'] : '';

// Build WHERE clause for filters
$whereConditions = [];
$params = [];

if ($filterModel) {
    $whereConditions[] = "m.name = :model";
    $params[':model'] = $filterModel;
}

if ($filterQuery) {
    $whereConditions[] = "r.query_text = :query";
    $params[':query'] = $filterQuery;
}

$whereClause = !empty($whereConditions) ? 'AND ' . implode(' AND ', $whereConditions) : '';

// Get all available models for dropdown
$stmt = $db->query("SELECT DISTINCT name FROM models WHERE active = 1 ORDER BY name");
$allModels = array_column($stmt->fetchAll(), 'name');

// Get all unique queries for dropdown
$stmt = $db->query("
    SELECT DISTINCT query_text 
    FROM responses 
    WHERE query_text IS NOT NULL 
    ORDER BY query_text
");
$allQueries = array_column($stmt->fetchAll(), 'query_text');

// Get summary statistics with filters
$sql = "SELECT COUNT(*) as count FROM responses r 
        JOIN models m ON r.model_id = m.id 
        WHERE r.paintballevents_referenced = 1 $whereClause";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$citedQueries = $stmt->fetch()['count'];

$sql = "SELECT COUNT(*) as total FROM responses r 
        JOIN models m ON r.model_id = m.id 
        WHERE 1=1 $whereClause";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$totalQueries = $stmt->fetch()['total'];

$citationRate = $totalQueries > 0 ? round(($citedQueries / $totalQueries) * 100, 1) : 0;

// Get model performance data with filters
$sql = "
    SELECT 
        m.name as model,
        m.provider,
        COALESCE(SUM(r.paintballevents_referenced), 0) as times_cited,
        COUNT(r.id) as times_tested,
        CASE 
            WHEN COUNT(r.id) > 0 
            THEN ROUND(SUM(r.paintballevents_referenced) / COUNT(r.id) * 100, 1)
            ELSE 0 
        END as citation_rate,
        ROUND(AVG(r.response_time_ms)) as avg_response_time
    FROM models m
    LEFT JOIN responses r ON m.id = r.model_id " . ($filterQuery ? "AND r.query_text = :query" : "") . "
    WHERE m.active = 1
    GROUP BY m.id, m.name, m.provider
    ORDER BY m.name
";
$stmt = $db->prepare($sql);
if ($filterQuery) {
    $stmt->bindParam(':query', $filterQuery);
}
$stmt->execute();
$modelData = $stmt->fetchAll();

// Get citations over time per model (with query info)
$sql = "
    SELECT 
        DATE(r.timestamp) as date,
        m.name as model,
        r.query_text as query,
        SUM(r.paintballevents_referenced) as citations,
        COUNT(*) as total_queries
    FROM responses r
    JOIN models m ON r.model_id = m.id
    WHERE 1=1 $whereClause
    GROUP BY DATE(r.timestamp), m.name, r.query_text
    ORDER BY date ASC, model, query
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$timeSeriesData = $stmt->fetchAll();

// Get recent citation events with filters
$sql = "
    SELECT 
        r.timestamp,
        m.name as model,
        r.query_text as query,
        r.cited_urls
    FROM responses r
    JOIN models m ON r.model_id = m.id
    WHERE r.paintballevents_referenced = 1 $whereClause
    ORDER BY r.timestamp DESC
    LIMIT 20
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$recentData = $stmt->fetchAll();

// Get query-specific stats when filtering by model
$queryStats = [];
if ($filterModel && !$filterQuery) {
    $sql = "
        SELECT 
            r.query_text,
            COUNT(*) as times_tested,
            SUM(r.paintballevents_referenced) as times_cited,
            CASE 
                WHEN COUNT(*) > 0 
                THEN ROUND(SUM(r.paintballevents_referenced) / COUNT(*) * 100, 1)
                ELSE 0 
            END as citation_rate
        FROM responses r
        JOIN models m ON r.model_id = m.id
        WHERE m.name = :model AND r.query_text IS NOT NULL
        GROUP BY r.query_text
        ORDER BY r.query_text
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':model', $filterModel);
    $stmt->execute();
    $queryStats = $stmt->fetchAll();
}

// Get latest run info
$stmt = $db->query("
    SELECT 
        run_id,
        started_at,
        completed_at,
        status,
        queries_executed,
        errors_count
    FROM runs
    ORDER BY started_at DESC
    LIMIT 1
");
$latestRun = $stmt->fetch();

$db = null; // Close connection
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
            background: rgba(0, 217, 255, 0.03);
            border: 2px solid #00d9ff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.2), inset 0 0 15px rgba(0, 217, 255, 0.03);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, transparent, rgba(0, 217, 255, 0.1), transparent);
            animation: border-flow 3s linear infinite;
        }
        
        @keyframes border-flow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .stat-card .label {
            font-size: 0.9em;
            opacity: 0.7;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            z-index: 1;
        }
        
        .stat-card .value {
            font-size: 2.5em;
            font-weight: 700;
            color: #00ff88;
            text-shadow: 0 0 15px rgba(0, 255, 136, 0.6);
            position: relative;
            z-index: 1;
        }
        
        .stat-card .subtext {
            font-size: 0.85em;
            opacity: 0.6;
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }
        
        .chart-container {
            background: rgba(0, 217, 255, 0.03);
            border: 2px solid #00d9ff;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.2), inset 0 0 15px rgba(0, 217, 255, 0.03);
            position: relative;
        }
        
        .chart-container::before {
            content: attr(data-title);
            position: absolute;
            top: -12px;
            left: 20px;
            background: #0a0a0a;
            padding: 0 10px;
            font-size: 0.85em;
            letter-spacing: 2px;
            text-shadow: 0 0 10px #00d9ff;
            text-transform: uppercase;
        }
        
        .chart-container canvas {
            max-height: 400px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        
        thead th {
            background: rgba(0, 217, 255, 0.1);
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #00d9ff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.85em;
        }
        
        tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(0, 217, 255, 0.2);
        }
        
        tbody tr {
            transition: all 0.2s;
        }
        
        tbody tr:hover {
            background: rgba(0, 217, 255, 0.05);
            box-shadow: inset 0 0 10px rgba(0, 217, 255, 0.1);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .badge.success {
            background: rgba(0, 255, 136, 0.2);
            border: 1px solid #00ff88;
            color: #00ff88;
            text-shadow: 0 0 5px rgba(0, 255, 136, 0.5);
        }
        
        .badge.warning {
            background: rgba(255, 136, 0, 0.2);
            border: 1px solid #ff8800;
            color: #ff8800;
            text-shadow: 0 0 5px rgba(255, 136, 0, 0.5);
        }
        
        .badge.error {
            background: rgba(255, 0, 85, 0.2);
            border: 1px solid #ff0055;
            color: #ff0055;
            text-shadow: 0 0 5px rgba(255, 0, 85, 0.5);
        }
        
        .url-list {
            font-size: 0.85em;
            opacity: 0.8;
            line-height: 1.6;
        }
        
        .url-list a {
            color: #00d9ff;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .url-list a:hover {
            text-shadow: 0 0 5px rgba(0, 217, 255, 0.8);
            text-decoration: underline;
        }
        
        .timestamp {
            font-family: 'Courier New', monospace;
            opacity: 0.7;
            font-size: 0.9em;
        }
        
        /* Terminal blinking cursor */
        .cursor {
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 49% { opacity: 1; }
            50%, 100% { opacity: 0; }
        }
        
        .run-info {
            background: rgba(0, 217, 255, 0.03);
            border: 1px solid rgba(0, 217, 255, 0.3);
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        
        .run-info .run-label {
            opacity: 0.7;
            margin-right: 10px;
        }
        
        .filter-container {
            background: rgba(0, 217, 255, 0.03);
            border: 2px solid #00d9ff;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.2), inset 0 0 15px rgba(0, 217, 255, 0.03);
            position: relative;
        }
        
        .filter-container::before {
            content: '> FILTER CONTROLS_';
            position: absolute;
            top: -12px;
            left: 20px;
            background: #0a0a0a;
            padding: 0 10px;
            font-size: 0.85em;
            letter-spacing: 2px;
            text-shadow: 0 0 10px #00d9ff;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        
        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .filter-group label {
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            opacity: 0.7;
        }
        
        .filter-group select {
            background: rgba(0, 217, 255, 0.05);
            border: 1px solid #00d9ff;
            color: #00d9ff;
            padding: 10px 15px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .filter-group select:hover,
        .filter-group select:focus {
            background: rgba(0, 217, 255, 0.1);
            box-shadow: 0 0 10px rgba(0, 217, 255, 0.3);
            outline: none;
        }
        
        .filter-group select option {
            background: #0a0a0a;
            color: #00d9ff;
        }
        
        .filter-btn {
            background: rgba(0, 255, 136, 0.1);
            border: 2px solid #00ff88;
            color: #00ff88;
            padding: 10px 20px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.2s;
            text-shadow: 0 0 5px rgba(0, 255, 136, 0.5);
            font-weight: 600;
        }
        
        .filter-btn:hover {
            background: rgba(0, 255, 136, 0.2);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.4);
            transform: translateY(-1px);
        }
        
        .filter-btn.clear {
            background: rgba(255, 136, 0, 0.1);
            border-color: #ff8800;
            color: #ff8800;
            text-shadow: 0 0 5px rgba(255, 136, 0, 0.5);
        }
        
        .filter-btn.clear:hover {
            background: rgba(255, 136, 0, 0.2);
            box-shadow: 0 0 15px rgba(255, 136, 0, 0.4);
        }
        
        .active-filters {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 217, 255, 0.2);
            font-size: 0.9em;
        }
        
        .active-filters .filter-tag {
            display: inline-block;
            background: rgba(0, 255, 136, 0.2);
            border: 1px solid #00ff88;
            color: #00ff88;
            padding: 5px 12px;
            margin-right: 10px;
            margin-top: 5px;
            border-radius: 3px;
            font-size: 0.85em;
        }
        
        .clickable-cell {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .clickable-cell:hover {
            color: #00ff88;
            text-shadow: 0 0 8px rgba(0, 255, 136, 0.6);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AI CITATION MONITOR<span class="cursor">_</span></h1>
            <p>Tracking paintballevents.net citations across AI models</p>
        </div>
        
        <!-- Filter Controls -->
        <div class="filter-container">
            <form method="GET" action="" id="filterForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="modelFilter">Filter by Model</label>
                        <select name="model" id="modelFilter">
                            <option value="">-- All Models --</option>
                            <?php foreach ($allModels as $model): ?>
                                <option value="<?php echo htmlspecialchars($model); ?>" 
                                    <?php echo $filterModel === $model ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($model); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="queryFilter">Filter by Query</label>
                        <select name="query" id="queryFilter">
                            <option value="">-- All Queries --</option>
                            <?php foreach ($allQueries as $query): ?>
                                <option value="<?php echo htmlspecialchars($query); ?>"
                                    <?php echo $filterQuery === $query ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(strlen($query) > 60 ? substr($query, 0, 60) . '...' : $query); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="filter-btn">Apply</button>
                        <?php if ($filterModel || $filterQuery): ?>
                            <button type="button" class="filter-btn clear" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">Clear</button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($filterModel || $filterQuery): ?>
                <div class="active-filters">
                    <strong>ACTIVE FILTERS:</strong>
                    <?php if ($filterModel): ?>
                        <span class="filter-tag">MODEL: <?php echo htmlspecialchars($filterModel); ?></span>
                    <?php endif; ?>
                    <?php if ($filterQuery): ?>
                        <span class="filter-tag">QUERY: <?php echo htmlspecialchars(strlen($filterQuery) > 50 ? substr($filterQuery, 0, 50) . '...' : $filterQuery); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if ($latestRun): ?>
        <div class="run-info">
            <span class="run-label">LATEST RUN:</span>
            <strong><?php echo htmlspecialchars($latestRun['run_id']); ?></strong>
            &nbsp;|&nbsp;
            <span class="run-label">STATUS:</span>
            <span class="badge <?php echo $latestRun['status'] === 'completed' ? 'success' : 'warning'; ?>">
                <?php echo htmlspecialchars($latestRun['status']); ?>
            </span>
            &nbsp;|&nbsp;
            <span class="run-label">QUERIES:</span>
            <?php echo $latestRun['queries_executed']; ?>
            &nbsp;|&nbsp;
            <span class="run-label">ERRORS:</span>
            <?php echo $latestRun['errors_count']; ?>
            &nbsp;|&nbsp;
            <span class="timestamp"><?php echo date('Y-m-d H:i:s', strtotime($latestRun['started_at'])); ?></span>
        </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Queries</div>
                <div class="value"><?php echo $totalQueries; ?></div>
                <div class="subtext"><?php 
                    if ($filterModel && $filterQuery) {
                        echo 'For selected model + query';
                    } elseif ($filterModel) {
                        echo 'For ' . htmlspecialchars($filterModel);
                    } elseif ($filterQuery) {
                        echo 'For selected query';
                    } else {
                        echo 'Across all models';
                    }
                ?></div>
            </div>
            
            <div class="stat-card">
                <div class="label">Citations Found</div>
                <div class="value"><?php echo $citedQueries; ?></div>
                <div class="subtext"><?php 
                    if ($filterModel || $filterQuery) {
                        echo 'In filtered results';
                    } else {
                        echo 'paintballevents.net mentioned';
                    }
                ?></div>
            </div>
            
            <div class="stat-card">
                <div class="label">Citation Rate</div>
                <div class="value"><?php echo $citationRate; ?>%</div>
                <div class="subtext"><?php 
                    if ($filterModel || $filterQuery) {
                        echo 'Filtered performance';
                    } else {
                        echo 'Overall performance';
                    }
                ?></div>
            </div>
        </div>
        
        <div class="chart-container" data-title="MODEL PERFORMANCE COMPARISON">
            <canvas id="modelChart"></canvas>
        </div>
        
        <div class="chart-container" data-title="<?php echo $filterQuery ? 'MODEL PERFORMANCE FOR SELECTED QUERY' : 'MODEL PERFORMANCE TABLE'; ?>">
            <table>
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Provider</th>
                        <th>Times Tested</th>
                        <th>Times Cited</th>
                        <th>Citation Rate</th>
                        <th>Avg Response Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modelData as $model): ?>
                    <tr>
                        <td>
                            <strong class="clickable-cell" onclick="filterByModel('<?php echo htmlspecialchars($model['model'], ENT_QUOTES); ?>')">
                                <?php echo htmlspecialchars($model['model']); ?>
                            </strong>
                        </td>
                        <td><?php echo htmlspecialchars($model['provider']); ?></td>
                        <td><?php echo $model['times_tested']; ?></td>
                        <td><?php echo $model['times_cited']; ?></td>
                        <td>
                            <span class="badge <?php echo $model['citation_rate'] > 0 ? 'success' : 'warning'; ?>">
                                <?php echo $model['citation_rate']; ?>%
                            </span>
                        </td>
                        <td>
                            <?php 
                            if ($model['avg_response_time']) {
                                echo number_format($model['avg_response_time']) . ' ms';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($filterModel && !$filterQuery && !empty($queryStats)): ?>
        <div class="chart-container" data-title="QUERIES TESTED WITH <?php echo strtoupper(htmlspecialchars($filterModel)); ?>">
            <table>
                <thead>
                    <tr>
                        <th>Query</th>
                        <th>Times Tested</th>
                        <th>Times Cited</th>
                        <th>Citation Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($queryStats as $stat): ?>
                    <tr>
                        <td>
                            <span class="clickable-cell" onclick="filterByQuery('<?php echo htmlspecialchars($stat['query_text'], ENT_QUOTES); ?>')">
                                <?php echo htmlspecialchars($stat['query_text']); ?>
                            </span>
                        </td>
                        <td><?php echo $stat['times_tested']; ?></td>
                        <td><?php echo $stat['times_cited']; ?></td>
                        <td>
                            <span class="badge <?php echo $stat['citation_rate'] > 0 ? 'success' : 'warning'; ?>">
                                <?php echo $stat['citation_rate']; ?>%
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="chart-container" data-title="<?php 
            if ($filterModel && $filterQuery) {
                echo 'CITATION TIMELINE: ' . strtoupper(htmlspecialchars($filterModel)) . ' + SPECIFIC QUERY';
            } elseif ($filterModel) {
                echo 'CITATION TIMELINE: ' . strtoupper(htmlspecialchars($filterModel));
            } elseif ($filterQuery) {
                echo 'CITATION TIMELINE: SELECTED QUERY';
            } else {
                echo 'CITATION TIMELINE';
            }
        ?>">
            <canvas id="timelineChart"></canvas>
        </div>
        
        <?php if (!empty($recentData)): ?>
        <div class="chart-container" data-title="RECENT CITATIONS<?php echo ($filterModel || $filterQuery) ? ' (FILTERED)' : ''; ?>">
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Model</th>
                        <th>Query</th>
                        <th>Cited URLs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentData as $citation): ?>
                    <tr>
                        <td class="timestamp"><?php echo date('Y-m-d H:i:s', strtotime($citation['timestamp'])); ?></td>
                        <td>
                            <span class="clickable-cell" onclick="filterByModel('<?php echo htmlspecialchars($citation['model'], ENT_QUOTES); ?>')">
                                <?php echo htmlspecialchars($citation['model']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="clickable-cell" onclick="filterByQuery('<?php echo htmlspecialchars($citation['query'], ENT_QUOTES); ?>')" title="<?php echo htmlspecialchars($citation['query']); ?>">
                                <?php echo htmlspecialchars(substr($citation['query'], 0, 60)) . (strlen($citation['query']) > 60 ? '...' : ''); ?>
                            </span>
                        </td>
                        <td class="url-list">
                            <?php 
                            $urls = json_decode($citation['cited_urls'], true);
                            if ($urls && is_array($urls)) {
                                foreach ($urls as $url) {
                                    echo '<a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a><br>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Filter helper functions
        function filterByModel(modelName) {
            const url = new URL(window.location.href);
            url.searchParams.set('model', modelName);
            // Preserve existing query filter if present
            window.location.href = url.toString();
        }
        
        function filterByQuery(queryText) {
            const url = new URL(window.location.href);
            url.searchParams.set('query', queryText);
            // Preserve existing model filter if present
            window.location.href = url.toString();
        }
        
        // Chart.js styling
        Chart.defaults.color = '#00d9ff';
        Chart.defaults.borderColor = 'rgba(0, 217, 255, 0.2)';
        
        // Model Performance Chart
        const modelCtx = document.getElementById('modelChart').getContext('2d');
        new Chart(modelCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($modelData, 'model')); ?>,
                datasets: [
                    {
                        label: 'Citation Rate (%)',
                        data: <?php echo json_encode(array_column($modelData, 'citation_rate')); ?>,
                        backgroundColor: 'rgba(0, 255, 136, 0.3)',
                        borderColor: '#00ff88',
                        borderWidth: 2
                    },
                    {
                        label: 'Times Tested',
                        data: <?php echo json_encode(array_column($modelData, 'times_tested')); ?>,
                        backgroundColor: 'rgba(0, 217, 255, 0.3)',
                        borderColor: '#00d9ff',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Citation Rate (%)'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Times Tested'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
        
        // Timeline Chart
        const timelineData = <?php echo json_encode($timeSeriesData); ?>;
        const dates = [...new Set(timelineData.map(d => d.date))];
        const models = [...new Set(timelineData.map(d => d.model))];
        
        const datasets = models.map((model, index) => {
            const colors = ['#00ff88', '#00d9ff', '#ff8800', '#ff0055', '#8800ff', '#00ffff'];
            const color = colors[index % colors.length];
            
            return {
                label: model,
                data: dates.map(date => {
                    const entries = timelineData.filter(d => d.date === date && d.model === model);
                    return entries.reduce((sum, e) => sum + parseInt(e.citations), 0);
                }),
                borderColor: color,
                backgroundColor: color + '40',
                borderWidth: 2,
                tension: 0.3
            };
        });
        
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Citations'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>

