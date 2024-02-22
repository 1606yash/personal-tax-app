<?php

require_once 'db_connection/DB.class.php';
require_once 'cross-origin.php';
require __DIR__ . '/vendor/autoload.php';

use \Firebase\JWT\JWT;

class UserManager
{

    private $db;

    # constructor for the DB class
    public function __construct()
    {
        $this->db = new DB();
    }

    # create token function starts here
    public function createToken($email, $pin)
    {
        $length = 32;
        $randomBytes = random_bytes($length);
        $secretKey = base64_encode($randomBytes);

        $payload = array(
            "email" => $email,
            "pin" => $pin
        );

        $expirationTime = time() + 3600;
        $algorithm = 'HS256';

        $token = JWT::encode([
            'exp' => $expirationTime,
            'data' => $payload
        ], $secretKey, $algorithm);

        return $token;
    }
    # create token function ends here

    public function saveToken($token, $userId)
    {
        $tokenData = array(
            'token' => $token,
            'user_id' => $userId
        );

        $token = $this->db->insert('tokens', $tokenData);
        if ($token) {
            return true;
        } else {
            return false;
        }
    }

    # get Token from Request function starts here
    public function getTokenFromRequest()
    {
        $headers = getallheaders();
        $tokenHeaderName = 'authorization';
        // Check if the Authorization header exists
        if (isset($headers[$tokenHeaderName])) {
            // Extract the token from the header value
            $authorizationHeader = $headers[$tokenHeaderName];
            
            // Check if the header value starts with "Bearer "
            if (strpos($authorizationHeader, 'Bearer ') === 0) {
                // Remove "Bearer " from the beginning of the header value
                $token = trim(substr($authorizationHeader, 7));
                
                // Return the extracted token
                return $token;
            } 
        } else {
            // If the Authorization header is missing, return false
            return false;
        }
    }
    #get Token from Request function ends here

    public function verifyToken($token)
    {
        $conditions = array(
            'where' => array(
                'token' => $token
            ),
            'return_type' => 'single',
            'order_by' => 'id DESC'
        );

        try {
            $verifyToken = $this->db->getRows('tokens', $conditions);

            if ($verifyToken) {
                return true;
            } else {
                $response = array('status' => 401, 'message' => 'Unauthorized!');
                http_response_code(401);
                return $response;
            }
        } catch (PDOException $e) {
            // Handle database query error
            $response = array('status' => 500, 'message' => 'Internal Server Error');
            http_response_code(500);
            return $response;
        }
    }


    # register user function starts here
    public function register($email, $pin, $firstName, $lastName, $clientId)
    {
        try {
            // Validate $pin contains only digits
            if (!ctype_digit($pin)) {
                http_response_code(400);
                echo json_encode(array('status' => 400, 'message' => 'Pin must contain only digits!'));
                return;
            }

            if (strlen($pin) < 4) {
                http_response_code(400);
                echo json_encode(array('status' => 400, 'message' => 'Pin should be minimum 4 digits long!'));
                return;
            }elseif(strlen($pin) > 4){
                http_response_code(400);
                echo json_encode(array('status' => 400, 'message' => 'Pin should not exceeds more than 4 digits!'));
                return;
            }

            $userData = array(
                'password' => password_hash($pin, PASSWORD_BCRYPT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'client_id' => $clientId,
                'email' => $email,
            );

            $conditions = array(
                'where' => array(
                    'email' => $email
                ),
                'return_type' => 'single'
            );

            $user = $this->db->getRows('users', $conditions);
            if ($user) {
                http_response_code(422);
                echo json_encode(array('status' => 422, 'message' => 'User Already Exist!'));
            } else {
                $insert = $this->db->insert('users', $userData);
                if ($insert) {
                    http_response_code(200);
                    echo json_encode(array('status' => 200, 'message' => 'User Registered Successfully!'));
                } else {
                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
                }
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    #register user function ends here

    # login user function starts here
    public function login($username, $pin)
    {
        try {
            // Validate $pin contains only digits
            if (!ctype_digit($pin)) {
                http_response_code(400);
                echo json_encode(array('status' => 400, 'message' => 'Pin must contain only digits!'));
                return;
            }

            if (strlen($pin) < 4) {
                http_response_code(400);
                echo json_encode(array('status' => 400, 'message' => 'Pin should be minimum 4 digits long!'));
                return;
            } elseif (strlen($pin) > 4) {
                http_response_code(400);
                echo json_encode(array('status' => 400, 'message' => 'Pin should not exceeds more than 4 digits!'));
                return;
            }

            $conditions = array(
                'where' => array(
                    'email' => $username
                ),
                'return_type' => 'single'
            );
            $user = $this->db->getRows('users', $conditions);

            if ($user) {
                $storedHashedPin = $user['password'];

                if (password_verify($pin, $storedHashedPin)) {

                    $token = $this->createToken($username, $pin);
                    $saveToken = $this->saveToken($token, $user['id']);

                    $finalArray = array('status' => 200, 'message' => 'Login successful!','userId' => $user['id'], 'clientId' => $user['client_id'], 'firstName' => $user['first_name'], 'lastName' => $user['last_name'], 'email' => $user['email'], 'profilePath' => $user['profile_path'], 'token' => $token);

                    http_response_code(200);
                    echo json_encode($finalArray);
                } else {

                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Invalid Pin!'));
                }
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 404, 'message' => 'User Not Found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    # login user function ends here

    # logout function starts here
    public function logout($token, $userId)
    {
        try {
            $conditions = array('token' => $token);
            $verifyToken = $this->db->delete('tokens', $conditions);
            if ($verifyToken) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'Logout Successfully!'));
            } else {
                http_response_code(401);
                echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    #logout function ends here

    # post general comment function starts here
    public function postGeneralComment($userId, $comment, $categoryId)
    {
        try {
            $userData = array(
                'comment' => $comment,
                'user_id' => $userId,
                'category_id' => $categoryId
            );
            $insert = $this->db->insert('general_comments', $userData);
            if ($insert) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'General Comment Added Successfully!'));
            } else {
                http_response_code(401);
                echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    # post general comment function ends here

    public function postCommentDocument($userId, $commentFile, $categoryId){
        try {
            $folder = 'assets/upload/';
            $categoryName = $this->getCategoryNameById($categoryId);
            $categoryName = str_replace(' ', '_', $categoryName);
            $filenamePrefix = $categoryName . '_' . date('Y-m-d') . '_' . uniqid();
            $filename = $this->uploadDocument($folder, $commentFile, $filenamePrefix);
            $documentData = array(
                'category_id' => $categoryId,
                'user_id' => $userId,
                'comment_file_name' => $filenamePrefix . '.' . pathinfo($commentFile["name"], PATHINFO_EXTENSION),
                'comment_file_path' => $filename,
            );
            if ($filename) {
                $insert = $this->db->insert('general_comments', $documentData);
                if ($insert) {
                    http_response_code(200);
                    echo json_encode(array('status' => 200, 'message' => 'General Comment Document Uploaded Successfully!'));
                } else {
                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
                }
            } else {
                http_response_code(401);
                echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }

    public function deleteGeneralComments($commentId)
    {
        try {
            $conditions = array('id' => $commentId);
            $deleteComment = $this->db->delete('general_comments', $conditions);
            if ($deleteComment) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'Comment Deleted Successfully!'));
            } else {
                http_response_code(401);
                echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }

    public function deleteCommentDocument($commentId){
        try {

            $conditions = array(
                'select' => 'comment_file_path,comment_file_name',
                'where' => array(
                    'id' => $commentId
                ),
                'return_type' => 'single'
            );

            $document = $this->db->getRows('general_comments', $conditions);

            if ($document) {
                $folder = 'assets/upload/';
                $filePath = $folder . $document['comment_file_name'];
                $filename = $this->unsetDocument($filePath);

                if ($filename) {
                    $conditions = array('id' => $commentId);

                    $deletefile = $this->db->delete('general_comments', $conditions);

                    if ($deletefile) {
                        http_response_code(200);
                        echo json_encode(array('status' => 200, 'message' => 'Comment document deleted Successfully!'));
                    } else {
                        http_response_code(401);
                        echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Comment document not deleted!'));
                }
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 401, 'message' => 'Comment document not found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }

    # get general comment function starts here
    public function getGeneralComments($userId = '', $categoryId = '',$year = '')
    {
        try {
            $conditions = array(
                'select' => 'id,user_id,comment_file_path,created_at',
                'where' => array(
                    'user_id' => $userId,
                    //'category_id' => $categoryId
                ),
                'return_type' => 'all'
            );
            
            if (!empty($categoryId)) {
                $conditions['where']['category_id'] = $categoryId;
            }
            if (!empty($year)) {
                $conditions['where']['YEAR(created_at)'] = $year;
            }

            $comments = $this->db->getRows('general_comments', $conditions);

            if ($comments) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'General Comments fetched Successfully!', 'data' => $comments));
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 404, 'message' => 'General Comments not Found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    # get general comment function ends here

    public function getCommentDocument($userId = '', $categoryId = '', $year = ''){
        try {
            $conditions = array(
                'where' => array(
                    'category_id' => $categoryId,
                    'user_id' => $userId,
                    'YEAR(created_at)' => $year
                ),
                'return_type' => 'all'
            );
            $document = $this->db->getRows('general_comments', $conditions);
            if ($document) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'Comment documents fetched Successfully!', 'data' => $document));
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 404, 'message' => 'Comment documents not Found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }

    # create category function starts here
    public function createCategory($category_name, $categoryIcon)
    {
        try {
            if (!empty($categoryIcon)) {
                $folder = 'assets/upload/icon/';
                $categoryName = str_replace(' ', '_', $category_name);
                $filenamePrefix = $categoryName . '_' . date('Y-m-d') . '_' . uniqid();
                $filename = $this->uploadDocument($folder, $categoryIcon, $filenamePrefix);
            } else {
                $filename = '';
            }

            $categoryData = array(
                'category_name' => $category_name,
                'icon' => $filename,
            );

            $conditions = array(
                'where' => array(
                    'category_name' => $category_name
                ),
                'return_type' => 'single'
            );

            $category = $this->db->getRows('categories', $conditions);
            if ($category) {
                http_response_code(422);
                echo json_encode(array('status' => 422, 'message' => 'Category Already Exist!'));
            } else {
                $insert = $this->db->insert('categories', $categoryData);
                if ($insert) {
                    http_response_code(200);
                    echo json_encode(array('status' => 200, 'message' => 'Category Added Successfully!'));
                } else {
                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
                }
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    # create category function ends here

    # get category function starts here
    public function getCategory()
    {
        try {
            $conditions = array(
                'select' => 'id,category_name,icon',
                'return_type' => 'all'
            );
            $menu = $this->db->getRows('categories', $conditions);
            if ($menu) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'Categories fetched Successfully!', 'data' => $menu));
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 404, 'message' => 'Category not Found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    # get category function ends here

    # create menu function starts here
    public function createMenu($menu_name, $menuIcon)
    {
        try {
            if(!empty($menuIcon)){
                $folder = 'assets/upload/icon/';
                $menuName = str_replace(' ', '_', $menu_name);
                $filenamePrefix = $menuName . '_' . date('Y-m-d') . '_' . uniqid();
                $filename = $this->uploadDocument($folder, $menuIcon, $filenamePrefix);
            }else{
                $filename = '';
            }
           
            $menuData = array(
                'menu_name' => $menu_name,
                'menu_icon' => $filename,
            );

            $conditions = array(
                'where' => array(
                    'menu_name' => $menu_name
                ),
                'return_type' => 'single'
            );

            $menu = $this->db->getRows('menus', $conditions);
            if ($menu) {
                http_response_code(422);
                echo json_encode(array('status' => 422, 'message' => 'Sidebar Menu Already Exist!'));
            } else {
                $insert = $this->db->insert('menus', $menuData);
                if ($insert) {
                    http_response_code(200);
                    echo json_encode(array('status' => 200, 'message' => 'Sidebar Menu Added Successfully!'));
                } else {
                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
                }
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    #create menu function ends here

    #get menu function starts here
    public function getMenu()
    {
        try {
            $conditions = array(
                'select' => 'id,menu_name,menu_icon',
                'return_type' => 'all'
            );
            $menu = $this->db->getRows('menus', $conditions);
            if ($menu) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'Sidebar Menus fetched Successfully!', 'data' => $menu));
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 404, 'message' => 'Sidebar Menus not Found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    #get menu function ends here

    #get document by category function starts here
    public function getDocumentsByCategory($categoryId, $userId, $year)
    {
        try {
            $conditions = array(
                'select' => 'id,category_id,document_name,document_url,comments,created_at',
                'where' => array(
                    'category_id' => $categoryId,
                    'user_id' => $userId,
                    'YEAR(created_at)' => $year
                ),
                'return_type' => 'all'
            );
            $document = $this->db->getRows('documents', $conditions);
            if ($document) {
                http_response_code(200);
                echo json_encode(array('status' => 200, 'message' => 'Documents fetched Successfully!', 'data' => $document));
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 404, 'message' => 'Documents not Found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    # get document document by category function ends here

    #post document by category function starts here
    public function postDocumentsByCategory($file, $categoryId, $userId, $comments)
    {
        try {
            $folder = 'assets/upload/';
            $categoryName = $this->getCategoryNameById($categoryId);
            $categoryName = str_replace(' ', '_', $categoryName);
            $filenamePrefix = $categoryName . '_' . date('Y-m-d') . '_' . uniqid();
            $filename = $this->uploadDocument($folder, $file, $filenamePrefix);
            $documentData = array(
                'category_id' => $categoryId,
                'user_id' => $userId,
                'document_name' => $filenamePrefix . '.' . pathinfo($file["name"], PATHINFO_EXTENSION),
                'document_url' => $filename,
                'comments' => $comments,
            );
            if ($filename) {
                $insert = $this->db->insert('documents', $documentData);
                if ($insert) {
                    http_response_code(200);
                    echo json_encode(array('status' => 200, 'message' => 'Document Uploaded Successfully!'));
                } else {
                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
                }
            } else {
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }
    #post post document by category function ends here


    public function uploadDocument($folder, $file, $filename)
    {
        ignore_user_abort(true);
        set_time_limit(0);
        @ini_set('upload_max_size', '4000M');
        @ini_set('post_max_size', '4500M');
        @ini_set('max_execution_time', '30000000');
        @ini_set('memory_limit', '15288M');

        // Get the protocol
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";

        // Get the host (domain name)
        $host = $_SERVER['HTTP_HOST'];

        // Get the base URL with path and query parameters
        $base_url = trim($protocol) . trim($host);

        $target_dir = $folder;

        // Create the target directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate a new filename using the provided $filename parameter
        $new_filename = $filename . '.' . pathinfo($file["name"], PATHINFO_EXTENSION);

        // Set the target path with the new filename
        $target_dir = $target_dir . "/" . $new_filename;

        $url = trim($base_url) . '/' . $folder . $new_filename;

        // Move the uploaded file to the target directory with the new filename
        if (move_uploaded_file($file["tmp_name"], $target_dir)) {
            return $url;
        } else {
            return $file["name"];
        }
    }

    public function unsetDocument($file_path)
    {
        // echo $file_path;die;
        if (file_exists($file_path)) {
            unlink($file_path);
            return true; // File deleted successfully
        } else {
            return false; // File not found or unable to delete
        }
    }

    public function deleteDocument($userId = '', $categoryId = '', $fileId = '')
    {
        try {

            $conditions = array(
                'select' => 'document_url,document_name',
                'where' => array(
                    'category_id' => $categoryId,
                    'user_id' => $userId,
                    'id' => $fileId
                ),
                'return_type' => 'single'
            );

            $document = $this->db->getRows('documents', $conditions);

            if ($document) {
                $folder = 'assets/upload/';
                $filePath = $folder . $document['document_name'];
                $filename = $this->unsetDocument($filePath);

                if ($filename) {
                    $conditions = array('category_id' => $categoryId, 'user_id' => $userId, 'id' => $fileId);

                    $deletefile = $this->db->delete('documents', $conditions);

                    if ($deletefile) {
                        http_response_code(200);
                        echo json_encode(array('status' => 200, 'message' => 'Document deleted Successfully!'));
                    } else {
                        http_response_code(401);
                        echo json_encode(array('status' => 401, 'message' => 'Something went wrong!'));
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(array('status' => 401, 'message' => 'Document not deleted!'));
                }
            } else {
                http_response_code(404);
                echo json_encode(array('status' => 401, 'message' => 'Document not Found!'));
            }
        } catch (Exception $e) {
            http_response_code(405);
            echo json_encode(array('status' => 405, 'message' => $e->getMessage()));
        }
    }

    public function getCategoryNameById($categoryId)
    {
        $conditions = array(
            'select' => 'category_name',
            'where' => array(
                'id' => $categoryId
            ),
            'return_type' => 'single'
        );

        $category = $this->db->getRows('categories', $conditions);
        return $category['category_name'];
    }
}
