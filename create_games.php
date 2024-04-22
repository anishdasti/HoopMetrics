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
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $teamID_Home = (int)$_POST['teamID_Home'];
    $teamID_Away = (int)$_POST['teamID_Away'];
    $score_Home = (int)$_POST['score_Home'];
    $score_Away = (int)$_POST['score_Away'];

    try {
        $pdo->beginTransaction();

        // Check if the home and away team IDs exist
        $checkTeamExistenceQuery = "SELECT COUNT(*) as count FROM Teams WHERE TeamID IN (?, ?)";
        $stmtExistence = $pdo->prepare($checkTeamExistenceQuery);
        $stmtExistence->execute([$teamID_Home, $teamID_Away]);
        $teamExistenceResults = $stmtExistence->fetchAll(PDO::FETCH_ASSOC);

        // Check if both teams exist
        if ($teamExistenceResults[0]['count'] == 2) {

            // Insert game details into the Games table
            $sqlQuery = "INSERT INTO Games (Date, TeamID_Home, TeamID_Away, Score_Home, Score_Away) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sqlQuery);
            $stmt->execute([$date, $teamID_Home, $teamID_Away, $score_Home, $score_Away]);

            // Determine the winner and loser
            if ($score_Home > $score_Away) {
                $winTeam = $teamID_Home;
                $loseTeam = $teamID_Away;
            } else if ($score_Home < $score_Away) {
                $winTeam = $teamID_Away;
                $loseTeam = $teamID_Home;
            }

            // Update win/loss records for the teams
            if (isset($winTeam) && isset($loseTeam)) { // Ensure there was a decisive outcome
                $sqlUpdateWin = "UPDATE Teams SET Win = Win + 1 WHERE TeamID = ?";
                $stmtWin = $pdo->prepare($sqlUpdateWin);
                $stmtWin->execute([$winTeam]);

                $sqlUpdateLoss = "UPDATE Teams SET Loss = Loss + 1 WHERE TeamID = ?";
                $stmtLoss = $pdo->prepare($sqlUpdateLoss);
                $stmtLoss->execute([$loseTeam]);
            }

            $pdo->commit();
            $message = "<p>Game recorded successfully and team records updated.</p>";
        } else {
            $message = "<p>Error: One or both of the Team IDs do not exist.</p>";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to record game: " . $e->getMessage();
    }
}

// Provide a link back to the game creation page
echo "<a href='games.html'><button>Back</button></a>";

// Display the SQL query and results message
if (!empty($sqlQuery)) {
    echo "<p><strong>SQL Query:</strong> " . htmlspecialchars($sqlQuery) . "</p>";
}

if (!empty($message)) {
    echo "<p><strong>Results:</strong><br> " . $message . "</p>";
}
?>
