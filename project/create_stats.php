<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "HoopMetrics";

try {
    $pdo = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gameID = isset($_POST['gameID']) ? (int)$_POST['gameID'] : null;
    $playerID = isset($_POST['playerID']) ? (int)$_POST['playerID'] : null;

    // Check if both game and player IDs are valid
    $validGame = $pdo->prepare("SELECT 1 FROM Games WHERE GameID = :gameID");
    $validGame->bindParam(':gameID', $gameID, PDO::PARAM_INT);
    $validGame->execute();

    $validPlayer = $pdo->prepare("SELECT 1 FROM Players WHERE PlayerID = :playerID");
    $validPlayer->bindParam(':playerID', $playerID, PDO::PARAM_INT);
    $validPlayer->execute();

    if ($validGame->rowCount() == 0 || $validPlayer->rowCount() == 0) {
        if ($validGame->rowCount() == 0) {
            echo "<p>No game found with ID: $gameID. </p>";
        }
        if ($validPlayer->rowCount() == 0) {
            echo "<p>No player found with ID: $playerID. </p>";
        }
    } else {
        // Proceed to insert stats if both IDs are valid
        $points = (int)$_POST['points'];
        $assists = (int)$_POST['assists'];
        $rebounds = (int)$_POST['rebounds'];
        $steals = (int)$_POST['steals'];
        $blocks = (int)$_POST['blocks'];
        $turnovers = (int)$_POST['turnovers'];

        $sqlInsert = "INSERT INTO Player_Stats (GameID, PlayerID, Points, Assists, Rebounds, Steals, Blocks, Turnovers) VALUES (:gameID, :playerID, :points, :assists, :rebounds, :steals, :blocks, :turnovers)";
        $stmt = $pdo->prepare($sqlInsert);
        $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
        $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
        $stmt->bindParam(':points', $points, PDO::PARAM_INT);
        $stmt->bindParam(':assists', $assists, PDO::PARAM_INT);
        $stmt->bindParam(':rebounds', $rebounds, PDO::PARAM_INT);
        $stmt->bindParam(':steals', $steals, PDO::PARAM_INT);
        $stmt->bindParam(':blocks', $blocks, PDO::PARAM_INT);
        $stmt->bindParam(':turnovers', $turnovers, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<p>Player stats created successfully! </p>";
        } else {
            echo "Error creating player stats: " . $stmt->errorInfo()[2];
        }
    }
} else {
    echo "Invalid request method.";
}

echo "<a href='player_stats.html'><button>Back to Stats Management</button></a>";
?>