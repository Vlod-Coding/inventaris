<?php
/**
 * DEBUG SCRIPT - Test Database Connection
 * Upload file ini ke htdocs dan akses via browser
 * Setelah selesai test, HAPUS file ini untuk keamanan!
 */

echo "<h2>Testing InfinityFree Database Connection</h2>";
echo "<hr>";

// Database credentials
$db_host = 'sql100.infinityfree.com';
$db_user = 'if0_40771316';
$db_pass = 'Bajudit0k0';
$db_name = 'if0_40771316_inventaris';

echo "<h3>1. Testing Connection Parameters:</h3>";
echo "Host: " . $db_host . "<br>";
echo "User: " . $db_user . "<br>";
echo "Pass: " . str_repeat('*', strlen($db_pass)) . "<br>";
echo "Database: " . $db_name . "<br>";
echo "<hr>";

echo "<h3>2. Testing Connection (without database):</h3>";
$conn_test = @mysqli_connect($db_host, $db_user, $db_pass);
if ($conn_test) {
    echo "✅ <strong style='color:green'>Connection to MySQL server SUCCESS!</strong><br>";
    mysqli_close($conn_test);
} else {
    echo "❌ <strong style='color:red'>Connection FAILED!</strong><br>";
    echo "Error: " . mysqli_connect_error() . "<br>";
    echo "Error Code: " . mysqli_connect_errno() . "<br>";
}
echo "<hr>";

echo "<h3>3. Testing Connection (with database):</h3>";
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if ($conn) {
    echo "✅ <strong style='color:green'>Connection to database SUCCESS!</strong><br>";
    
    // Test query
    echo "<h3>4. Testing Query:</h3>";
    $result = mysqli_query($conn, "SHOW TABLES");
    if ($result) {
        echo "✅ <strong style='color:green'>Query SUCCESS!</strong><br>";
        echo "Tables in database:<br><ul>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ <strong style='color:red'>Query FAILED!</strong><br>";
        echo "Error: " . mysqli_error($conn) . "<br>";
    }
    
    mysqli_close($conn);
} else {
    echo "❌ <strong style='color:red'>Connection to database FAILED!</strong><br>";
    echo "Error: " . mysqli_connect_error() . "<br>";
    echo "Error Code: " . mysqli_connect_errno() . "<br>";
    echo "<br><strong>Possible issues:</strong><ul>";
    echo "<li>Database name salah</li>";
    echo "<li>Database belum dibuat di InfinityFree</li>";
    echo "<li>User tidak punya akses ke database</li>";
    echo "</ul>";
}
echo "<hr>";

echo "<h3>5. PHP Info:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQL Extension: " . (extension_loaded('mysqli') ? '✅ Loaded' : '❌ Not Loaded') . "<br>";

echo "<hr>";
echo "<p style='color:red'><strong>⚠️ PENTING: Hapus file ini setelah selesai testing untuk keamanan!</strong></p>";
?>
