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
    // Collect and sanitize input data
    $teamName = htmlspecialchars($_POST['teamName']);
    $coachName = htmlspecialchars($_POST['coachName']);

    // Prepare the SQL statement to insert new team
    $sqlQuery = "INSERT INTO Teams (TeamName, CoachName) VALUES (:teamName, :coachName)";
    $stmt = $pdo->prepare($sqlQuery);

    // Bind parameters to statement
    $stmt->bindParam(':teamName', $teamName);
    $stmt->bindParam(':coachName', $coachName);

    // Execute the statement and check if it runs successfully
    if ($stmt->execute()) {
        $message = "Team created successfully!";
    } else {
        $message = "Error creating team: " . $stmt->errorInfo()[2]; // Show specific SQL error
    }
}

// Provide a link back to the team creation page
echo "<a href='teams.html'><button>Back</button></a>";

// Display the SQL query and results message
if (!empty($sqlQuery)) {
    echo "<p><strong>SQL Query:</strong> " . htmlspecialchars($sqlQuery) . "</p>";
}

if (!empty($message)) {
    echo "<p><strong>Results:</strong><br> " . $message . "</p>";
}
?>