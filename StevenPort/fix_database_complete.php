<?php
/**
 * Complete Database Fix Script for StevenPort
 * This script fixes all missing columns including updated_at
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

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Database Fix</title>";
echo "<link rel='stylesheet' href='master.css'>";
echo "</head><body>";
echo "<div class='page-container'>";
echo "<h1 class='page-title'>Complete Database Fix for StevenPort</h1>";
echo "<div id='progress' class='progress-container'></div>";
echo "<script>function addProgress(msg) { document.getElementById('progress').innerHTML += '<div class=\"progress-item\">' + msg + '</div>'; }</script>";

// Flush output immediately
if (ob_get_level()) {
    ob_flush();
}
flush();

try {
    $db = new DBConn();
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    echo "<script>addProgress('✅ Database connection successful');</script>";
    flush();
    
    // First, let's check what columns exist
    echo "<script>addProgress('🔍 Checking Current Table Structure...');</script>";
    flush();
    
    $result = $conn->query("DESCRIBE users");
    if ($result) {
        $existingColumns = [];
        while ($row = $result->fetch_assoc()) {
            $existingColumns[] = $row['Field'];
        }
        echo "<script>addProgress('Current columns: " . implode(', ', $existingColumns) . "');</script>";
        flush();
    }
    
    // Read and execute the complete fix SQL file
    $sqlFile = __DIR__ . '/fix_database_complete.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: " . $sqlFile);
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    echo "<script>addProgress('🔄 Executing SQL statements...');</script>";
    flush();
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        try {
            if ($conn->query($statement)) {
                $successCount++;
                echo "<script>addProgress('✅ Executed: " . addslashes(substr($statement, 0, 60)) . "...');</script>";
            } else {
                $errorCount++;
                echo "<script>addProgress('❌ Error: " . addslashes($conn->error) . "');</script>";
            }
        } catch (Exception $e) {
            $errorCount++;
            echo "<script>addProgress('❌ Exception: " . addslashes($e->getMessage()) . "');</script>";
        }
        flush();
    }
    
    echo "<script>addProgress('📊 Fix Complete');</script>";
    echo "<script>addProgress('✅ Successful statements: $successCount');</script>";
    echo "<script>addProgress('❌ Failed statements: $errorCount');</script>";
    flush();
    
    // Check the final table structure
    echo "<script>addProgress('🔍 Checking Final Table Structure...');</script>";
    flush();
    
    $result = $conn->query("DESCRIBE users");
    if ($result) {
        $finalColumns = [];
        while ($row = $result->fetch_assoc()) {
            $finalColumns[] = $row['Field'];
        }
        echo "<script>addProgress('Final columns: " . implode(', ', $finalColumns) . "');</script>";
        flush();
        
        // Check for required columns
        $requiredColumns = ['updated_at', 'theme', 'language', 'timezone', 'notifications', 'email_notifications', 'first_name', 'last_name', 'bio'];
        
        echo "<script>addProgress('🔍 Required Columns Check:');</script>";
        flush();
        
        foreach ($requiredColumns as $col) {
            if (in_array($col, $finalColumns)) {
                echo "<script>addProgress('✅ Column $col exists');</script>";
            } else {
                echo "<script>addProgress('❌ Column $col missing');</script>";
            }
            flush();
        }
    }
    
    if ($errorCount === 0) {
        echo "<script>addProgress('🎉 Database fix completed successfully!');</script>";
        flush();
        
        // Test the new functions
        echo "<script>addProgress('🔍 Testing Functions...');</script>";
        flush();
        
        $func = new DBFunc();
        
        try {
            // Test if we can get a user
            $testUser = $func->getUserByUsername('admin');
            if ($testUser) {
                echo "<script>addProgress('✅ getUserByUsername function working');</script>";
            } else {
                echo "<script>addProgress('⚠️ getUserByUsername function working but no admin user found');</script>";
            }
        } catch (Exception $e) {
            echo "<script>addProgress('❌ getUserByUsername function error: " . addslashes($e->getMessage()) . "');</script>";
        }
        flush();
        
        echo "<script>addProgress('🎉 Fix Complete! Try the account settings now.');</script>";
        flush();
        
    } else {
        echo "<script>addProgress('⚠️ Fix completed with some errors. Check the output above.');</script>";
        flush();
    }
    
} catch (Exception $e) {
    echo "<script>addProgress('❌ Fix failed: " . addslashes($e->getMessage()) . "');</script>";
    echo "<script>addProgress('Please check your database configuration and try again.');</script>";
    flush();
}

// Add navigation links
echo "<script>addProgress('');</script>";
echo "<script>addProgress('🔗 Navigation Links:');</script>";
echo "<script>addProgress('<a href=\"account_settings.php\">Go to Account Settings</a>');</script>";
echo "<script>addProgress('<a href=\"login.php\">Go to Login</a>');</script>";
flush();

echo "</div></body></html>";
?>