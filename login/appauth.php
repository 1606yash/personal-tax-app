<?php
require_once '../config.php';
$usermanager = new UserManager();



#  login code starts
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['email']) && isset($_POST['pin']) ||
        isset($_REQUEST['email']) && isset($_REQUEST['pin'])
    ) {

        // cleaning variables
        $username = isset($_POST['email']) ? $_POST['email'] : $_REQUEST['email'];
        $pin = isset($_POST['pin']) ? $_POST['pin'] : $_REQUEST['pin'];

        echo $usermanager->login($username, $pin);
        exit();
    } else {
        http_response_code(422);
        echo json_encode(array('message' => 'Parameters missing!'));
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(array('status' => 400, 'message' => 'Invalid request method!'));
    exit();
}

# login code ends
