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

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['teamID'])) {
    $teamID = (int)$_POST['teamID'];
    $newTeamName = htmlspecialchars($_POST['newTeamName']);
    $newCoachName = htmlspecialchars($_POST['newCoachName']);

    // Prepare the SQL statement to update team name and coach name
    $sqlQuery = "UPDATE Teams SET TeamName = :newTeamName, CoachName = :newCoachName WHERE TeamID = :teamID";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->bindParam(':newTeamName', $newTeamName);
    $stmt->bindParam(':newCoachName', $newCoachName);
    $stmt->bindParam(':teamID', $teamID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $message = "Team updated successfully!";
        } else {
            $message = "No changes made or team not found.";
        }
    } else {
        $message = "Error updating team: " . $stmt->errorInfo()[2]; // Show specific SQL error
    }
}

echo "<a href='teams.html'><button>Back to Team Management</button></a>";

if (!empty($message)) {
    echo "<p><strong>Results:</strong><br>" . $message . "</p>";
}
?>