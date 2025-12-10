<?php
require_once 'db.php';

$guest = null;

// --- Fetch the data for editing ---
if (isset($_GET['id'])) {
    // Sanitize the ID to ensure it's a valid integer
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    try {
        // Prepare and execute a SELECT statement to get the data
        $sql = "SELECT * FROM members WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $guest = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no one is found with that ID, redirect back to the main page
        if (!$guest) {
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    }
}

// --- Handle the form submission  ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_EMAIL);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_EMAIL);
    $age = filter_var($_POST['age'], FILTER_SANITIZE_EMAIL);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    try {
        $sql = "UPDATE members SET name=?, age=?, email=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $age, $email, $id]);
        
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<h2>Edit Member Information</h2>
<?php if ($guest): ?>
<form method="post" action="edit.php">
    <input type="hidden" name="id" value="<?= htmlspecialchars($guest['id']); ?>">
    <label for="name">Name:</label>
    <br>
    <input type="text" name="name" value="<?= htmlspecialchars($guest['name']); ?>" required>
    <br><br>
    <label for="age">Age:</label>
    <br>
    <input type="text" name="age" value="<?= htmlspecialchars($guest['age']); ?>" required>
    <br><br>
    <label for="email">Email:</label>
    <br>
    <input type="email" name="email" value="<?= htmlspecialchars($guest['email']); ?>" required>
    <br><br>
    <button type="submit">Update Member</button>
</form>
<?php endif; ?>