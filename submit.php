<?php
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING); // Sanitize inputs

    // Include the saveToDatabase.php to handle the form submission
    ob_start();
    include 'saveToDatabase.php'; // Ensure this file only returns JSON and has no echo statements
    $response = ob_get_clean(); // Capture the output

    // Decode the response to access individual fields
    $decodedResponse = json_decode($response, true);

    // Validate and store response data in session
    if ($decodedResponse) {
        $_SESSION['company_name_response'] = $decodedResponse['company_name_response'] ?? 'N/A';
        $_SESSION['year_established_response'] = $decodedResponse['year_established_response'] ?? 'N/A';
        $_SESSION['logo_response'] = $decodedResponse['logo_response'] ?? 'N/A';
        $_SESSION['phone_number_response'] = $decodedResponse['phone_number_response'] ?? 'N/A';
        $_SESSION['description_response'] = $decodedResponse['description_response'] ?? 'N/A';
        $_SESSION['industry_response'] = $decodedResponse['industry_response'] ?? 'N/A';
        $_SESSION['location_response'] = $decodedResponse['location_response'] ?? 'N/A';
    } else {
        // Handle the error appropriately
        $_SESSION['error'] = 'Error: Invalid response from database or API.';
    }

    // Redirect to the response page
    header('Location: response.php');
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit;
?>
