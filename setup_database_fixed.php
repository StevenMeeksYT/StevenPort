<?php
/**
 * Fixed Database Setup Script for StevenPort
 * This script properly handles database creation and table setup
 */

// Enable output buffering and real-time updates
ob_start();
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);

// Send headers for real-time updates
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no'); // Disable nginx buffering

require_once("db.php");
require_once("func.php");

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Database Setup</title>";
echo "<link rel='stylesheet' href='master.css'>";
echo "</head><body>";
echo "<div class='page-container'>";
echo "<h1 class='page-title'>Fixed Database Setup for StevenPort</h1>";
echo "<div id='progress' class='progress-container'></div>";
echo "<script>function addProgress(msg) { document.getElementById('progress').innerHTML += '<div class=\"progress-item\">' + msg + '</div>'; }</script>";

// Flush output immediately
if (ob_get_level()) {
    ob_flush();
}
flush();

try {
    // First, connect without specifying a database
    $conn = new mysqli("localhost", "root", "");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<script>addProgress('✅ MySQL connection successful');</script>";
    flush();
    
    // Read and execute the fixed SQL file
    $sqlFile = __DIR__ . '/create_database_fixed.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: " . $sqlFile);
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Execute the entire SQL script at once
    echo "<script>addProgress('🔄 Executing SQL script...');</script>";
    flush();
    
    if ($conn->multi_query($sql)) {
        echo "<script>addProgress('✅ Database and tables created successfully!');</script>";
        flush();
        
        // Process all results
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        
        echo "<script>addProgress('🔍 Testing Database Connection...');</script>";
        flush();
        
        // Test the connection with the new database
        $db = new DBConn();
        $testConn = $db->getConnection();
        
        if ($testConn) {
            echo "<script>addProgress('✅ Database connection with stevenport database successful');</script>";
            flush();
            
            // Test if tables exist
            $tables = ['users', 'tcdatabase', 'projects', 'user_sessions', 'user_activity_logs'];
            foreach ($tables as $table) {
                $result = $testConn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    echo "<script>addProgress('✅ Table $table exists');</script>";
                } else {
                    echo "<script>addProgress('❌ Table $table not found');</script>";
                }
                flush();
            }
            
            // Test if we can query users
            $result = $testConn->query("SELECT COUNT(*) as count FROM users");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<script>addProgress('✅ Users table has {$row['count']} records');</script>";
            }
            flush();
            
            // Test if we can query tropical cyclones
            $result = $testConn->query("SELECT COUNT(*) as count FROM tcdatabase");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<script>addProgress('✅ Tropical Cyclone database has {$row['count']} records');</script>";
            }
            flush();
            
            // Test if we can query projects
            $result = $testConn->query("SELECT COUNT(*) as count FROM projects");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<script>addProgress('✅ Projects table has {$row['count']} records');</script>";
            }
            flush();
            
        } else {
            echo "<script>addProgress('❌ Failed to connect to stevenport database');</script>";
            flush();
        }
        
        echo "<script>addProgress('🔍 Testing New Functions...');</script>";
        flush();
        
        $func = new DBFunc();
        
        // Test if we can get a user
        try {
            $testUser = $func->getUserByUsername('admin');
            if ($testUser) {
                echo "<script>addProgress('✅ getUserByUsername function working - Found admin user');</script>";
            } else {
                echo "<script>addProgress('⚠️ getUserByUsername function working but no admin user found');</script>";
            }
        } catch (Exception $e) {
            echo "<script>addProgress('❌ getUserByUsername function error: " . addslashes($e->getMessage()) . "');</script>";
        }
        flush();
        
        echo "<script>addProgress('🎉 Setup Complete! Your database is ready to use.');</script>";
        echo "<script>addProgress('Default Admin Account:');</script>";
        echo "<script>addProgress('Username: admin');</script>";
        echo "<script>addProgress('Password: password');</script>";
        flush();
        
    } else {
        throw new Exception("Failed to execute SQL: " . $conn->error);
    }
    
} catch (Exception $e) {
    echo "<script>addProgress('❌ Setup failed: " . addslashes($e->getMessage()) . "');</script>";
    echo "<script>addProgress('Please check your MySQL configuration and try again.');</script>";
    flush();
}

// Add navigation links
echo "<script>addProgress('');</script>";
echo "<script>addProgress('🔗 Navigation Links:');</script>";
echo "<script>addProgress('<a href=\"dashboard.php\">Go to Dashboard</a>');</script>";
echo "<script>addProgress('<a href=\"account_settings.php\">Go to Account Settings</a>');</script>";
echo "<script>addProgress('<a href=\"login.php\">Go to Login</a>');</script>";
flush();

echo "</div></body></html>";
?>
