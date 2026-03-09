<?php
require_once __DIR__ . '/includes/db.php';

echo "<h2>Cleanup: Deleting Unused Categories</h2>";

// Find categories not used in any 'aspirasi'
$query = "DELETE FROM kategori 
          WHERE id_kategori NOT IN (
              SELECT DISTINCT id_kategori FROM aspirasi WHERE id_kategori IS NOT NULL
          )";

if (mysqli_query($conn, $query)) {
    $deleted_count = mysqli_affected_rows($conn);
    echo "<p style='color:green;'>Success! $deleted_count unused categories have been deleted.</p>";
    echo "<p>You can now delete this file (<code>cleanup_categories.php</code>).</p>";
}
else {
    echo "<p style='color:red;'>Error cleaning up categories: " . mysqli_error($conn) . "</p>";
}
?>
