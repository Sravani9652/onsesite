<?php
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formData = $_POST;

    // Send the form data to saveToDatabase.php
    $ch = curl_init('saveToDatabase.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);

    $response = curl_exec($ch);
    curl_close($ch);

    // Store the response in a session variable
    $_SESSION['api_response'] = $response;

    // Redirect to the response page
    header('Location: response.html');
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit;
