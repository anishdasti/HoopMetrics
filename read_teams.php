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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['teamID'])) {
    $teamID = (int)$_POST['teamID'];

    // Prepare the SQL statement to fetch team data
    $sqlQuery = "SELECT * FROM Teams WHERE TeamID = :teamID";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->bindParam(':teamID', $teamID, PDO::PARAM_INT);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<p>Team ID: " . htmlspecialchars($row['TeamID']) . "</p>";
                echo "<p>Team Name: " . htmlspecialchars($row['TeamName']) . "</p>";
                echo "<p>Coach Name: " . htmlspecialchars($row['CoachName']) . "</p>";
                echo "<p>Wins: " . htmlspecialchars($row['Win']) . "</p>";
                echo "<p>Losses: " . htmlspecialchars($row['Loss']) . "</p><hr>";
            }
        } else {
            echo "<p>No team found with the specified ID.</p>";
        }
    } else {
        echo "Error reading team data: " . $stmt->errorInfo()[2];
    }
} else {
    echo "<p>Invalid request or Team ID not provided.</p>";
}

echo "<a href='teams.html'><button>Back to Team Management</button></a>";
?>