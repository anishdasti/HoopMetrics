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

    // Prepare the SQL statement to fetch game data
    $sqlQuery = "SELECT * FROM Games WHERE GameID = :gameID";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<p>Game ID: " . htmlspecialchars($row['GameID']) . "</p>";
                echo "<p>Date: " . htmlspecialchars($row['Date']) . "</p>";
                echo "<p>Home Team ID: " . htmlspecialchars($row['TeamID_Home']) . "</p>";
                echo "<p>Away Team ID: " . htmlspecialchars($row['TeamID_Away']) . "</p>";
                echo "<p>Home Score: " . htmlspecialchars($row['Score_Home']) . "</p>";
                echo "<p>Away Score: " . htmlspecialchars($row['Score_Away']) . "</p><hr>";
            }
        } else {
            echo "<p>No game found with the specified ID.</p>";
        }
    } else {
        echo "Error fetching game data: " . $stmt->errorInfo()[2];
    }
} else {
    echo "<p>Invalid request or Game ID not provided.</p>";
}

echo "<a href='games.html'><button>Back to Game Management</button></a>";
?>