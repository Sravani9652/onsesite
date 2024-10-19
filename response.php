<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sanitize the incoming query parameters
$responses = [];
foreach ($_GET as $key => $value) {
    $responses[$key] = htmlspecialchars($value);
}

function sanitizeResponse($response) {
    // Remove special characters and symbols
    return preg_replace('/[^A-Za-z0-9\s]/', '', $response);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response</title>
</head>
<body>
    <h1>Response</h1>
    <ul>
        <?php
        // Display responses in the requested format
        foreach ($responses as $key => $response) {
            $formattedResponse = sanitizeResponse($response);
            echo "<li>" . ucfirst(strtolower($key)) . ": $formattedResponse</li>";
        }
        ?>
    </ul>
</body>
</html>
