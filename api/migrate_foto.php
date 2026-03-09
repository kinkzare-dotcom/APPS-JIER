<?php
require_once __DIR__ . '/includes/db.php';

echo "<h2>Database Migration: Updating 'foto' column to LONGTEXT</h2>";

$query = "ALTER TABLE aspirasi MODIFY COLUMN foto LONGTEXT";

if (mysqli_query($conn, $query)) {
    echo "<p style='color:green;'>Success! The 'foto' column has been updated to LONGTEXT.</p>";
    echo "<p>You can now delete this file (<code>migrate_foto.php</code>).</p>";
}
else {
    echo "<p style='color:red;'>Error updating database: " . mysqli_error($conn) . "</p>";
}
?>
