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

    try {
        // Begin transaction to ensure both deletes happen or none
        $pdo->beginTransaction();

        // First, delete player stats associated with the player
        $sqlDeleteStats = "DELETE FROM Player_Stats WHERE PlayerID = :playerID";
        $stmtStats = $pdo->prepare($sqlDeleteStats);
        $stmtStats->bindParam(':playerID', $playerID, PDO::PARAM_INT);
        $stmtStats->execute();

        // Next, delete the player
        $sqlDeletePlayer = "DELETE FROM Players WHERE PlayerID = :playerID";
        $stmtPlayer = $pdo->prepare($sqlDeletePlayer);
        $stmtPlayer->bindParam(':playerID', $playerID, PDO::PARAM_INT);

        if ($stmtPlayer->execute() && $stmtPlayer->rowCount() > 0) {
            $pdo->commit(); // Commit the transaction if both deletes were successful
            echo "<p>Player and associated stats deleted successfully!</p>";
        } else {
            echo "<p>No player found with the specified ID, or player could not be deleted.</p>";
            $pdo->rollBack(); // Roll back the transaction if the player delete failed
        }
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction in case of any error
        echo "<p>Error deleting player or player stats: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Invalid request or Player ID not provided.</p>";
}

echo "<a href='players.html'><button>Back to Player Management</button></a>";
?>