<?php
// Include the database connection file
require_once 'db.php';

// Check 'id'
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    try {
        $sql = "DELETE FROM members WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no ID is provided, redirect back to the main page
    header("Location: index.php");
    exit();
}
?>