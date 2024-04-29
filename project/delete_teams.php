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

    try {
        // Check if the team is linked to players or games
        $checkQuery = "SELECT * FROM Players WHERE TeamID = :teamID LIMIT 1";
        $stmtCheck = $pdo->prepare($checkQuery);
        $stmtCheck->bindParam(':teamID', $teamID, PDO::PARAM_INT);
        $stmtCheck->execute();
        if ($stmtCheck->rowCount() > 0) {
            $message = "Cannot delete team: Team has linked players. Please reassign or remove players first.";
        } else {
            // Prepare the SQL statement to delete team
            $sqlQuery = "DELETE FROM Teams WHERE TeamID = :teamID";
            $stmt = $pdo->prepare($sqlQuery);
            $stmt->bindParam(':teamID', $teamID, PDO::PARAM_INT);
    
            // Execute the statement and check if it runs successfully
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $message = "Team deleted successfully!";
                } else {
                    $message = "No team found with the specified ID.";
                }
            } else {
                $message = "Error deleting team: " . $stmt->errorInfo()[2]; // Show specific SQL error
            }
        }
    } catch (PDOException $e) {
        $message = "Error in operation: " . $e->getMessage();
    }
}

echo "<a href='teams.html'><button>Back to Team Management</button></a>";

if (!empty($message)) {
    echo "<p><strong>Results:</strong><br>" . $message . "</p>";
}
?>