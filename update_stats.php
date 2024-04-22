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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['statID'])) {
    $statID = (int)$_POST['statID'];
    $newPoints = isset($_POST['newPoints']) ? (int)$_POST['newPoints'] : null;
    $newAssists = isset($_POST['newAssists']) ? (int)$_POST['newAssists'] : null;
    $newRebounds = isset($_POST['newRebounds']) ? (int)$_POST['newRebounds'] : null;
    $newSteals = isset($_POST['newSteals']) ? (int)$_POST['newSteals'] : null;
    $newBlocks = isset($_POST['newBlocks']) ? (int)$_POST['newBlocks'] : null;
    $newTurnovers = isset($_POST['newTurnovers']) ? (int)$_POST['newTurnovers'] : null;

    // Prepare the SQL statement to update player stats
    $sqlQuery = "UPDATE Player_Stats SET 
        Points = COALESCE(:newPoints, Points), 
        Assists = COALESCE(:newAssists, Assists), 
        Rebounds = COALESCE(:newRebounds, Rebounds), 
        Steals = COALESCE(:newSteals, Steals), 
        Blocks = COALESCE(:newBlocks, Blocks), 
        Turnovers = COALESCE(:newTurnovers, Turnovers) 
        WHERE StatID = :statID";

    $stmt = $pdo->prepare($sqlQuery);

    $stmt->bindParam(':newPoints', $newPoints, PDO::PARAM_INT);
    $stmt->bindParam(':newAssists', $newAssists, PDO::PARAM_INT);
    $stmt->bindParam(':newRebounds', $newRebounds, PDO::PARAM_INT);
    $stmt->bindParam(':newSteals', $newSteals, PDO::PARAM_INT);
    $stmt->bindParam(':newBlocks', $newBlocks, PDO::PARAM_INT);
    $stmt->bindParam(':newTurnovers', $newTurnovers, PDO::PARAM_INT);
    $stmt->bindParam(':statID', $statID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "<p>Player stats updated successfully!</p>";
        } else {
            echo "<p>No changes made or stats not found.</p>";
        }
    } else {
        echo "<p>Error updating stats: " . $stmt->errorInfo()[2] . "</p>"; // Show specific SQL error
    }
} else {
    echo "<p>Invalid request or Stat ID not provided.</p>";
}

echo "<a href='player_stats.html'><button>Back to Stats Management</button></a>";
?>