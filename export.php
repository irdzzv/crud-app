<?php
// Call Python script
header('Content-Type: text/plain');

// Call Python script directly
$output = shell_exec('python export_csv.py 2>&1');

// Check if file was created
if (file_exists('members_export.csv')) {
    $rowCount = count(file('members_export.csv')) - 1;
    
    echo "Success! Exported {$rowCount} members to CSV.\n";
    echo "<a href='members_export.csv' download>Download members_export.csv</a>";
} else {
    echo "Export failed!\n";
    echo "Error details:\n" . htmlspecialchars($output);
}
?>