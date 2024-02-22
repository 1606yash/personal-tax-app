<?php
require_once 'config.php';
$usermanager = new UserManager();


$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if (empty($action)) {
    http_response_code(422);
    echo json_encode(array('message' => 'Action parameter missing!'));
    exit();
}

$token = $usermanager->getTokenFromRequest();
$verifyToken = $usermanager->verifyToken($token);

if ($verifyToken !== true) {
    exit();
}

switch ($action) {
    case 'post-comment':
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
        $file = isset($_FILES['document']) ? $_FILES['document'] : null;
        $category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';

        if (empty($user_id) || empty($file) || empty($category_id)) {
            http_response_code(422);
            echo json_encode(array('message' => 'Parameter missing!'));
            exit();
        }

        $usermanager->postCommentDocument($user_id, $file, $category_id);
        break;

    case 'get-comments':
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
        $category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : '';
        $usermanager->getCommentDocument($user_id, $category_id, $year);
        break;

    case 'delete-comment':
        $commentId = isset($_REQUEST['comment_id']) ? $_REQUEST['comment_id'] : '';
        $usermanager->deleteCommentDocument($commentId);
        break;

    case 'add-category':
        $category = isset($_REQUEST['category']) ? $_REQUEST['category'] : '';
        $categoryIcon = isset($_FILES['category_icon']) ? $_FILES['category_icon'] : '';
        $usermanager->createCategory($category, $categoryIcon);
        break;

    case 'get-category':
        $usermanager->getCategory();
        break;

    case 'add-menu':
        $menu = isset($_REQUEST['menu_name']) ? $_REQUEST['menu_name'] : '';
        $menuIcon = isset($_FILES['menu_icon']) ? $_FILES['menu_icon'] : null;
        $usermanager->createMenu($menu,$menuIcon);
        break;

    case 'get-menu':
        $usermanager->getMenu();
        break;

    case 'get-document-list-by-category-id':
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
        $category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : '';
        $usermanager->getDocumentsByCategory($category_id, $user_id, $year);
        break;

    case 'post-document':
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
        $category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';
        $comments = isset($_REQUEST['comments']) ? $_REQUEST['comments'] : '';
        $file = isset($_FILES['document']) ? $_FILES['document'] : null;
        $usermanager->postDocumentsByCategory($file,$category_id, $user_id,$comments);
        break;

    case 'logout':
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
        $usermanager->logout($token, $user_id);
        break;

    case 'delete-document':
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
        $category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';
        $document_id = isset($_REQUEST['document_id']) ? $_REQUEST['document_id'] : '';
        
        $usermanager->deleteDocument($user_id, $category_id, $document_id);
        break;

    case 'register-user':
        $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
        $pin = isset($_REQUEST['pin']) ? $_REQUEST['pin'] : '';
        $firstName = isset($_REQUEST['first_name']) ? $_REQUEST['first_name'] : '';
        $lastName = isset($_REQUEST['last_name']) ? $_REQUEST['last_name'] : '';
        $clientId = isset($_REQUEST['client_id']) ? $_REQUEST['client_id'] : '';
        $usermanager->register($email, $pin, $firstName, $lastName, $clientId);
        break;

    default:
        http_response_code(422);
        echo json_encode(array('message' => 'Invalid action!'));
        exit();
}
