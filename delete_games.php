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

    try {
        // Begin transaction to ensure all related actions are done together
        $pdo->beginTransaction();

        // First, get the scores to determine which team won and which lost
        $sqlFetchGame = "SELECT TeamID_Home, TeamID_Away, Score_Home, Score_Away FROM Games WHERE GameID = :gameID";
        $stmtFetchGame = $pdo->prepare($sqlFetchGame);
        $stmtFetchGame->bindParam(':gameID', $gameID, PDO::PARAM_INT);
        $stmtFetchGame->execute();
        $gameData = $stmtFetchGame->fetch(PDO::FETCH_ASSOC);

        if ($gameData) {
            $homeTeam = $gameData['TeamID_Home'];
            $awayTeam = $gameData['TeamID_Away'];
            $homeScore = $gameData['Score_Home'];
            $awayScore = $gameData['Score_Away'];

            // Determine winner and loser
            if ($homeScore > $awayScore) {
                $winTeam = $homeTeam;
                $loseTeam = $awayTeam;
            } elseif ($homeScore < $awayScore) {
                $winTeam = $awayTeam;
                $loseTeam = $homeTeam;
            } else {
                // In case of a tie, no need to adjust win/loss
                $winTeam = null;
                $loseTeam = null;
            }

            // Adjust win/loss records if there was a definitive result
            if ($winTeam && $loseTeam) {
                $sqlUpdateWin = "UPDATE Teams SET Win = Win - 1 WHERE TeamID = :winTeam";
                $stmtUpdateWin = $pdo->prepare($sqlUpdateWin);
                $stmtUpdateWin->bindParam(':winTeam', $winTeam, PDO::PARAM_INT);
                $stmtUpdateWin->execute();

                $sqlUpdateLoss = "UPDATE Teams SET Loss = Loss - 1 WHERE TeamID = :loseTeam";
                $stmtUpdateLoss = $pdo->prepare($sqlUpdateLoss);
                $stmtUpdateLoss->bindParam(':loseTeam', $loseTeam, PDO::PARAM_INT);
                $stmtUpdateLoss->execute();
            }

            // Delete player stats associated with the game
            $sqlDeleteStats = "DELETE FROM Player_Stats WHERE GameID = :gameID";
            $stmtStats = $pdo->prepare($sqlDeleteStats);
            $stmtStats->bindParam(':gameID', $gameID, PDO::PARAM_INT);
            $stmtStats->execute();

            // Finally, delete the game
            $sqlDeleteGame = "DELETE FROM Games WHERE GameID = :gameID";
            $stmtGame = $pdo->prepare($sqlDeleteGame);
            $stmtGame->bindParam(':gameID', $gameID, PDO::PARAM_INT);
            if ($stmtGame->execute() && $stmtGame->rowCount() > 0) {
                $pdo->commit(); // Commit the transaction if all operations were successful
                echo "<p>Game and associated stats deleted successfully!</p>";
            } else {
                echo "<p>Failed to delete the game or game not found.</p>";
                $pdo->rollBack();
            }
        } else {
            echo "<p>Game not found or unable to fetch game details.</p>";
            $pdo->rollBack();
        }
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction in case of any error
        echo "<p>Error during operation: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Invalid request or Game ID not provided.</p>";
}

echo "<a href='games.html'><button>Back to Game Management</button></a>";
?>