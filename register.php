<?php
require_once 'config.php';
$usermanager = new UserManager();


#  register code starts
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requiredFields = ['email', 'password', 'first_name', 'last_name'];
    $missingFields = [];

    // Check if all required fields are set and not empty
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) && !isset($_REQUEST[$field])) {
            $missingFields[] = $field;
        } elseif (empty($_POST[$field] ?? $_REQUEST[$field])) {
            $missingFields[] = $field;
        }
    }

    if (empty($missingFields)) {
        // cleaning variables
        $email = $_POST['email'] ?? $_REQUEST['email'];
        $password = $_POST['password'] ?? $_REQUEST['password'];
        $firstName = $_POST['first_name'] ?? $_REQUEST['first_name'];
        $lastName = $_POST['last_name'] ?? $_REQUEST['last_name'];
        $clientId = $_POST['client_id'] ?? $_REQUEST['client_id'];

        echo $usermanager->register($email, $password, $firstName, $lastName, $clientId);
        exit();
    } else {
        http_response_code(422);
        echo json_encode(array('message' => 'Required parameters missing: ' . implode(', ', $missingFields)));
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(array('status' => 400, 'message' => 'Invalid request method!'));
    exit();
}



# register code ends