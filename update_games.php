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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gameID'])) {
    $gameID = (int)$_POST['gameID'];
    $newScore_Home = (int)$_POST['newScore_Home'];
    $newScore_Away = (int)$_POST['newScore_Away'];

    // Prepare the SQL statement to update game scores
    $sqlQuery = "UPDATE Games SET Score_Home = :newScore_Home, Score_Away = :newScore_Away WHERE GameID = :gameID";
    $stmt = $pdo->prepare($sqlQuery);

    $stmt->bindParam(':newScore_Home', $newScore_Home, PDO::PARAM_INT);
    $stmt->bindParam(':newScore_Away', $newScore_Away, PDO::PARAM_INT);
    $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "<p>Game updated successfully!</p>";
        } else {
            echo "<p>No changes made or game not found.</p>";
        }
    } else {
        echo "<p>Error updating game: " . $stmt->errorInfo()[2] . "</p>"; // Show specific SQL error
    }
} else {
    echo "<p>Invalid request or Game ID not provided.</p>";
}

echo "<a href='games.html'><button>Back to Game Management</button></a>";
?>