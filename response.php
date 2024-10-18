<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Response</title>
</head>
<body>

<?php
// Database connection settings
$host = 'localhost';
$dbname = 'company_info';
$username = 'root';
$password = '';

// Establish a database connection using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Fetch the latest API response from the database
$sql = "SELECT api_response FROM api_responses ORDER BY id DESC LIMIT 1"; // Adjust this query based on your needs

try {
    $stmt = $pdo->query($sql);
    $response = $stmt->fetchColumn(); // Fetch the first column of the first row

    if ($response) {
        echo htmlspecialchars($response); // Display the API response
    } else {
        echo 'No response available.';
    }
} catch (PDOException $e) {
    echo "Error fetching response: " . $e->getMessage();
}
?>

<br>
<a href="onesite.html">Go Back</a> <!-- Link to go back to the form page -->

</body>
</html>
