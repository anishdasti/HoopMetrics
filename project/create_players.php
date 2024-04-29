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
    // Collect and sanitize input data
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $teamID = isset($_POST['teamID']) ? (int)$_POST['teamID'] : null;
    $height = isset($_POST['height']) ? (int)$_POST['height'] : null;
    $weight = isset($_POST['weight']) ? (int)$_POST['weight'] : null;
    $position = htmlspecialchars($_POST['position']);
    $year = htmlspecialchars($_POST['year']);
    $address = htmlspecialchars($_POST['address']); // Added address handling
    $gpa = isset($_POST['gpa']) ? (float)$_POST['gpa'] : null; // Added GPA handling as a float

    // Check if the provided TeamID exists in the database
    $sqlCheckTeam = "SELECT 1 FROM Teams WHERE TeamID = :teamID";
    $stmtCheckTeam = $pdo->prepare($sqlCheckTeam);
    $stmtCheckTeam->bindParam(':teamID', $teamID, PDO::PARAM_INT);
    $stmtCheckTeam->execute();

    if ($stmtCheckTeam->rowCount() > 0) {
        // Team exists, proceed with creating the player
        $sqlInsert = "INSERT INTO Players (FirstName, LastName, TeamID, Height, Weight, Position, Year, Address, GPA) VALUES (:firstName, :lastName, :teamID, :height, :weight, :position, :year, :address, :gpa)";
        $stmt = $pdo->prepare($sqlInsert);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':teamID', $teamID);
        $stmt->bindParam(':height', $height);
        $stmt->bindParam(':weight', $weight);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':address', $address); // Bind address
        $stmt->bindParam(':gpa', $gpa); // Bind GPA

        if ($stmt->execute()) {
            echo "<p>Player created successfully! </p>";
        } else {
            echo "Error creating player: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "<p>Error: The specified TeamID does not exist.</p>";
    }
} else {
    echo "Invalid request method.";
}

echo "<a href='players.html'><button>Back to Player Management</button></a>";
?>
