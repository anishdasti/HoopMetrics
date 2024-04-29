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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['playerID'], $_POST['password'])) {
    $playerID = (int)$_POST['playerID'];
    $password = $_POST['password'];

    // Secure SQL statement using prepared statements
    $sqlQuery = "SELECT * FROM Players WHERE PlayerID = :playerID AND Password = :password";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->bindParam(':playerID', $playerID);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<p>Player ID: " . htmlspecialchars($row['PlayerID']) . "</p>";
            echo "<p>First Name: " . htmlspecialchars($row['FirstName']) . "</p>";
            echo "<p>Last Name: " . htmlspecialchars($row['LastName']) . "</p>";
            echo "<p>Team ID: " . htmlspecialchars($row['TeamID']) . "</p>";
            echo "<p>Height: " . htmlspecialchars($row['Height']) . " cm</p>";
            echo "<p>Weight: " . htmlspecialchars($row['Weight']) . " kg</p>";
            echo "<p>Position: " . htmlspecialchars($row['Position']) . "</p>";
            echo "<p>Year: " . htmlspecialchars($row['Year']) . "</p>";
            echo "<p>Address: " . htmlspecialchars($row['Address']) . "</p>";
            echo "<p>GPA: " . htmlspecialchars($row['GPA']) . "</p><hr>";
        }
    } else {
        echo "<p>No player found with the specified ID or password.</p>";
    }
} else {
    echo "<p>Invalid request or Player ID not provided.</p>";
}

echo "<a href='players.html'><button>Back to Player Management</button></a>";
?>
