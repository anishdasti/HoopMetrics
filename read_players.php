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

    // Prepare the SQL statement to fetch player data
    $sqlQuery = "SELECT * FROM Players WHERE PlayerID = :playerID";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<p>Player ID: " . htmlspecialchars($row['PlayerID']) . "</p>";
                echo "<p>First Name: " . htmlspecialchars($row['FirstName']) . "</p>";
                echo "<p>Last Name: " . htmlspecialchars($row['LastName']) . "</p>";
                echo "<p>Team ID: " . htmlspecialchars($row['TeamID']) . "</p>";
                echo "<p>Height: " . htmlspecialchars($row['Height']) . " cm</p>";
                echo "<p>Weight: " . htmlspecialchars($row['Weight']) . " kg</p>";
                echo "<p>Position: " . htmlspecialchars($row['Position']) . "</p>";
                echo "<p>Year: " . htmlspecialchars($row['Year']) . "</p><hr>";
            }
        } else {
            echo "<p>No player found with the specified ID.</p>";
        }
    } else {
        echo "Error fetching player data: " . $stmt->errorInfo()[2];
    }
} else {
    echo "<p>Invalid request or Player ID not provided.</p>";
}

echo "<a href='players.html'><button>Back to Player Management</button></a>";
?>