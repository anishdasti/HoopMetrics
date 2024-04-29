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

    // Prepare the SQL statement to fetch player stats data
    $sqlQuery = "SELECT * FROM Player_Stats WHERE StatID = :statID";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->bindParam(':statID', $statID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<p>Stat ID: " . htmlspecialchars($row['StatID']) . "</p>";
                echo "<p>Player ID: " . htmlspecialchars($row['PlayerID']) . "</p>";
                echo "<p>Game ID: " . htmlspecialchars($row['GameID']) . "</p>";
                echo "<p>Points: " . htmlspecialchars($row['Points']) . "</p>";
                echo "<p>Assists: " . htmlspecialchars($row['Assists']) . "</p>";
                echo "<p>Rebounds: " . htmlspecialchars($row['Rebounds']) . "</p>";
                echo "<p>Steals: " . htmlspecialchars($row['Steals']) . "</p>";
                echo "<p>Blocks: " . htmlspecialchars($row['Blocks']) . "</p>";
                echo "<p>Turnovers: " . htmlspecialchars($row['Turnovers']) . "</p><hr>";
            }
        } else {
            echo "<p>No stats found with the specified Stat ID.</p>";
        }
    } else {
        echo "Error fetching stats data: " . $stmt->errorInfo()[2];
    }
} else {
    echo "<p>Invalid request or Stat ID not provided.</p>";
}

echo "<a href='player_stats.html'><button>Back to Stats Management</button></a>";
?>