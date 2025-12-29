<?php
/**
 * Run Migration: Add batch_id to stok_keluar
 */

require_once __DIR__ . '/../config/koneksi.php';

echo "Running migration: Add batch_id to stok_keluar\n\n";

// Add batch_id column
$sql1 = "ALTER TABLE stok_keluar ADD COLUMN batch_id VARCHAR(50) NULL AFTER id";
if (mysqli_query($conn, $sql1)) {
    echo "✓ Added batch_id column\n";
} else {
    if (mysqli_errno($conn) == 1060) { // Duplicate column
        echo "✓ batch_id column already exists\n";
    } else {
        die("✗ Error adding batch_id: " . mysqli_error($conn) . "\n");
    }
}

// Add index
$sql2 = "ALTER TABLE stok_keluar ADD INDEX idx_batch_id (batch_id)";
if (mysqli_query($conn, $sql2)) {
    echo "✓ Added index on batch_id\n";
} else {
    if (mysqli_errno($conn) == 1061) { // Duplicate key name
        echo "✓ Index idx_batch_id already exists\n";
    } else {
        die("✗ Error adding index: " . mysqli_error($conn) . "\n");
    }
}

// Verify
$result = mysqli_query($conn, "DESCRIBE stok_keluar");
echo "\n✓ Migration completed successfully!\n\n";
echo "Current stok_keluar structure:\n";
echo str_repeat("-", 80) . "\n";
printf("%-20s %-15s %-10s %-10s\n", "Field", "Type", "Null", "Key");
echo str_repeat("-", 80) . "\n";
while ($row = mysqli_fetch_assoc($result)) {
    printf("%-20s %-15s %-10s %-10s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Null'], 
        $row['Key']
    );
}

mysqli_close($conn);
?>
