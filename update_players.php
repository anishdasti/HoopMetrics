<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "HoopMetrics";

try {
    $pdo = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error in connection: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['playerID'])) {
    $playerID = (int)$_POST['playerID'];
    $newFirstName = htmlspecialchars($_POST['newFirstName']);
    $newLastName = htmlspecialchars($_POST['newLastName']);
    $newTeamID = (int)$_POST['newTeamID'];
    $newHeight = (int)$_POST['newHeight'];
    $newWeight = (int)$_POST['newWeight'];
    $newPosition = htmlspecialchars($_POST['newPosition']);
    $newYear = htmlspecialchars($_POST['newYear']);

    // Prepare the SQL statement to update player details
    $sqlQuery = "UPDATE Players SET FirstName = :newFirstName, LastName = :newLastName, TeamID = :newTeamID, Height = :newHeight, Weight = :newWeight, Position = :newPosition, Year = :newYear WHERE PlayerID = :playerID";
    $stmt = $pdo->prepare($sqlQuery);

    $stmt->bindParam(':newFirstName', $newFirstName);
    $stmt->bindParam(':newLastName', $newLastName);
    $stmt->bindParam(':newTeamID', $newTeamID);
    $stmt->bindParam(':newHeight', $newHeight);
    $stmt->bindParam(':newWeight', $newWeight);
    $stmt->bindParam(':newPosition', $newPosition);
    $stmt->bindParam(':newYear', $newYear);
    $stmt->bindParam(':playerID', $playerID);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "<p>Player updated successfully!</p>";
        } else {
            echo "<p>No changes made or player not found.</p>";
        }
    } else {
        echo "<p>Error updating player: " . $stmt->errorInfo()[2] . "</p>"; // Show specific SQL error
    }
} else {
    echo "<p>Invalid request or Player ID not provided.</p>";
}

echo "<a href='players.html'><button>Back to Player Management</button></a>";
?>