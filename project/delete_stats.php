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

    // Prepare the SQL statement to delete the player stats
    $sqlQuery = "DELETE FROM Player_Stats WHERE StatID = :statID";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->bindParam(':statID', $statID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "<p>Player stats deleted successfully!</p>";
        } else {
            echo "<p>No stats found with the specified Stat ID, or stats could not be deleted.</p>";
        }
    } else {
        echo "<p>Error deleting stats: " . $stmt->errorInfo()[2] . "</p>"; // Show specific SQL error
    }
} else {
    echo "<p>Invalid request or Stat ID not provided.</p>";
}

echo "<a href='player_stats.html'><button>Back to Stats Management</button></a>";
?>