<?php

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = filter_var($_POST['id'], FILTER_SANITIZE_EMAIL);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_EMAIL);
    $age = filter_var($_POST['age'], FILTER_SANITIZE_EMAIL);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!empty($id) && !empty($name) && !empty($age) && !empty($email)) {
        try {
            $sql = "INSERT INTO members (id, name, age, email) VALUES (?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([$id, $name, $age, $email]);

            header("Location: index.php");
            exit();

        } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please fill in all the required fields.";
    }
}
?>

<form method="post" action="index.php">
    <h3>Add a New Member</h3>
    <input type="text" name="id" placeholder="Member ID">
    <br><br>
    <input type="text" name="name" placeholder="Name" required>
    <br><br>
    <input type="text" name="age" placeholder="Age" required>
    <br><br>
    <input type="email" name="email" placeholder="Email" required>
    <br><br>
    <button type="submit">Add Member</button>
</form>


<!-- Read Operation -->
<h2>Member List</h2>

<!-- Search box -->
<div style="margin: 20px 0;">
    <input type="text" id="searchBox" placeholder="Search" 
           style="padding: 8px; width: 300px;">
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody id="memberTable">
        <?php
        // Read all guests from the database
        $stmt = $pdo->query("SELECT id, name, age, email FROM members ORDER BY id DESC");
        
        // Loop through the results and display each guest
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['age']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        
        echo "<td><a href='edit.php?id=" . $row['id'] . "'>Edit</a> | <a href='delete.php?id=" . $row['id'] . "'>Delete</a></td>";
        echo "</tr>";
        }
        ?>
    </tbody>
</table>

<!-- Export Button -->
<div style="margin: 20px 0;">
    <button onclick="runExport()" 
            style="padding: 10px; background: #4CAF50; color: white; border: none; cursor: pointer;">
        Export to CSV
    </button>
    <span id="exportResult"></span>
</div>

<script>
// JavaScript for real-time search
function setupSearch() {
    const searchInput = document.getElementById('searchBox');
    const memberTable = document.getElementById('memberTable');
    const originalHTML = memberTable.innerHTML; // To save original table
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all members
            memberTable.innerHTML = originalHTML;
            return;
        }
        
        // Filter table rows
        const rows = memberTable.getElementsByTagName('tr');
        let hasResults = false;
        
        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            if (cells.length > 0) {
                const name = cells[1].textContent.toLowerCase();
                const email = cells[3].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                    hasResults = true;
                } else {
                    row.style.display = 'none';
                }
            }
        }
        
        // Show "no results" message
        const noResultsRow = document.getElementById('noResults');
        if (!hasResults) {
            if (!noResultsRow) {
                const newRow = memberTable.insertRow();
                newRow.id = 'noResults';
                const cell = newRow.insertCell();
                cell.colSpan = 5;
                cell.textContent = 'No members found';
                cell.style.textAlign = 'center';
                cell.style.color = '#666';
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', setupSearch);

// Export
function runExport() {
    const resultDiv = document.getElementById('exportResult');
    resultDiv.innerHTML = 'Exporting...';
    
    fetch('export.php')
        .then(response => response.text())
        .then(text => {
            resultDiv.innerHTML = text;
            
            // If success, show download link
            if (text.includes('Success')) {
                setTimeout(() => {
                    window.location.href = 'members_export.csv';
                }, 1000);
            }
        })
        .catch(error => {
            resultDiv.innerHTML = 'Error: ' + error;
        });
}
</script>