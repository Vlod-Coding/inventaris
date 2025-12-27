<?php
/**
 * ========================================
 * CHECK SESSION DEBUG
 * ========================================
 * File: debug_session.php
 * Fungsi: Untuk mengecek isi session (debugging)
 */

session_start();

echo "<h2>Session Debug Information</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";
echo "<h3>Checklist:</h3>";
echo "<ul>";
echo "<li>Login status: " . (isset($_SESSION['login']) && $_SESSION['login'] ? '✅ Logged in' : '❌ Not logged in') . "</li>";
echo "<li>User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '❌ Not set') . "</li>";
echo "<li>Username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : '❌ Not set') . "</li>";
echo "<li>Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : '❌ NOT SET - This is the problem!') . "</li>";
echo "</ul>";

if (!isset($_SESSION['role'])) {
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
    echo "<h4>⚠️ Role tidak ada di session!</h4>";
    echo "<p><strong>Solusi:</strong></p>";
    echo "<ol>";
    echo "<li>Pastikan sudah menjalankan migration SQL (add_user_roles.sql)</li>";
    echo "<li>Logout dari sistem</li>";
    echo "<li>Login kembali</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Kembali ke Dashboard</a></p>";
?>
