<?php
/**
 * Database Population Script for Real Weather Data
 * Run this script to populate your StevenPort database with real tropical cyclone and tornado data
 */

// Database connection
require_once 'db.php';

try {
    $db = new DBConn();
    $conn = $db->getConnection();
    
    echo "Starting database population with real weather data...\n";
    
    // Read the SQL file
    $sqlFile = __DIR__ . '/real_weather_data.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements - handle multiline statements better
    $statements = [];
    $current_statement = '';
    $lines = explode("\n", $sql);

    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip empty lines and comments
        if (empty($line) || strpos($line, '--') === 0) {
            continue;
        }
        
        $current_statement .= $line . " ";
        
        // If line ends with semicolon, it's a complete statement
        if (substr($line, -1) === ';') {
            $statements[] = trim($current_statement);
            $current_statement = '';
        }
    }

    // Add any remaining statement
    if (!empty(trim($current_statement))) {
        $statements[] = trim($current_statement);
    }
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    $tc_count = 0;
    $tornado_count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || 
            strpos($statement, '--') === 0 || 
            strpos($statement, '/*') === 0 ||
            strpos($statement, 'DROP') === 0 ||
            strpos($statement, 'CREATE DATABASE') === 0 ||
            strpos($statement, 'USE') === 0) {
            continue; // Skip comments, database creation, and USE statements
        }
        
        try {
            if ($conn->query($statement)) {
                $successCount++;
                if (preg_match('/^INSERT INTO tcdatabase/i', $statement)) {
                    $tc_count++;
                } elseif (preg_match('/^INSERT INTO tornado_db/i', $statement)) {
                    $tornado_count++;
                }
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            $errorCount++;
            $errors[] = "Error executing: " . substr($statement, 0, 100) . "... - " . $e->getMessage();
        }
    }
    
    echo "Database population completed!\n";
    echo "Successful statements: $successCount\n";
    echo "Failed statements: $errorCount\n";
    
    if ($errorCount > 0) {
        echo "\nErrors encountered:\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
    // Show summary of data inserted
    echo "\nData Summary:\n";
    
    // Count tropical cyclones
    $tcResult = $conn->query("SELECT COUNT(*) as count FROM tcdatabase");
    if ($tcResult) {
        $tcCount = $tcResult->fetch_assoc()['count'];
        echo "Tropical cyclones: $tcCount records\n";
    }
    
    // Count tornadoes
    $tornadoResult = $conn->query("SELECT COUNT(*) as count FROM tornado_db");
    if ($tornadoResult) {
        $tornadoCount = $tornadoResult->fetch_assoc()['count'];
        echo "Tornadoes: $tornadoCount records\n";
    }
    
    // Show recent additions
    echo "\nRecent tropical cyclones added:\n";
    $recentTC = $conn->query("SELECT name, basin, msw, formed FROM tcdatabase ORDER BY formed DESC LIMIT 5");
    if ($recentTC && $recentTC->num_rows > 0) {
        while ($row = $recentTC->fetch_assoc()) {
            echo "- {$row['name']} ({$row['basin']}) - {$row['msw']} mph - {$row['formed']}\n";
        }
    }
    
    echo "\nRecent tornadoes added:\n";
    $recentTornado = $conn->query("SELECT name, state, intensity_scale, date FROM tornado_db ORDER BY date DESC LIMIT 5");
    if ($recentTornado && $recentTornado->num_rows > 0) {
        while ($row = $recentTornado->fetch_assoc()) {
            echo "- {$row['name']} ({$row['state']}) - {$row['intensity_scale']} - {$row['date']}\n";
        }
    }
    
    echo "\nDatabase population completed successfully!\n";
    echo "You can now view the data in your StevenPort weather applications.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>StevenPort Weather Data Population</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; margin-bottom: 30px; }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; }
        .summary { background: #ecf0f1; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .data-list { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #2980b9; }
        pre { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌪️ StevenPort Weather Data Population</h1>
        
        <div class="summary">
            <h3>Database Population Status</h3>
            <p>This script has populated your StevenPort database with real weather data including:</p>
            <ul>
                <li><strong>Real Tropical Cyclone Data:</strong> Major hurricanes, typhoons, and cyclones from 2020-2024</li>
                <li><strong>Real Tornado Data:</strong> Significant tornado events from 2020-2024</li>
                <li><strong>Historical Events:</strong> Notable historical tornadoes for reference</li>
            </ul>
        </div>
        
        <div class="data-list">
            <h3>📊 Data Coverage</h3>
            <p><strong>Tropical Cyclones:</strong> Atlantic hurricanes, Pacific typhoons, Indian Ocean cyclones</p>
            <p><strong>Tornadoes:</strong> EF0-EF5 tornadoes across the United States</p>
            <p><strong>Time Period:</strong> 2020-2024 with some historical reference events</p>
            <p><strong>Geographic Coverage:</strong> North America, Asia, Indian Ocean region</p>
        </div>
        
        <div class="data-list">
            <h3>🔧 Next Steps</h3>
            <ol>
                <li>Visit your <a href="tc_database.php" class="btn">Tropical Cyclone Database</a></li>
                <li>Check out the <a href="tornado_db.php" class="btn">Tornado Database</a></li>
                <li>Test the improved AJAX functionality</li>
                <li>Explore the data with the advanced search and filtering features</li>
            </ol>
        </div>
        
        <div class="data-list">
            <h3>📋 Database Schema</h3>
            <p>The following tables have been populated with real data:</p>
            <ul>
                <li><code>tcdatabase</code> - Tropical cyclone records with storm details, images, and tracks</li>
                <li><code>tornado_db</code> - Tornado records with intensity, damage, and location data</li>
            </ul>
        </div>
        
        <?php if (isset($successCount) && $successCount > 0): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>✅ Success!</strong> Database populated with <?php echo $successCount; ?> successful operations.
        </div>
        <?php endif; ?>
        
        <?php if (isset($errorCount) && $errorCount > 0): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>⚠️ Warnings:</strong> <?php echo $errorCount; ?> operations encountered issues. Check the console output for details.
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="dashboard.php" class="btn">🏠 Back to Dashboard</a>
            <a href="tc_database.php" class="btn">🌀 Tropical Cyclone Database</a>
            <a href="tornado_db.php" class="btn">🌪️ Tornado Database</a>
        </div>
    </div>
</body>
</html>