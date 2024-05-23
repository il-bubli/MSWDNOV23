<?php


require 'com/icemalta/kahuna/util/ApiUtil.php';
require 'com/icemalta/kahuna/model/Product.php';
require 'com/icemalta/kahuna/model/User.php';
require 'com/icemalta/kahuna/model/AccessToken.php';



use com\icemalta\kahuna\util\ApiUtil;
use com\icemalta\kahuna\model\{Product, User, AccessToken, registeredProduct};


cors();

$endPoints = [];
$requestData = [];
header("Content-Type: application/json; charset=UTF-8");

/* BASE URI */
$BASE_URI = '/kahuna/api/';

function sendResponse(mixed $data = null, int $code = 200, mixed $error = null): void
{
    if (!is_null($data)) {
        $response['data'] = $data;
    }
    if (!is_null($error)) {
        $response['error'] = $error;
    }
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    http_response_code($code);
}

function checkToken(array $requestData): bool
{
    if (!isset($requestData['token']) || !isset($requestData['user'])) {
        return false;
    }
    $token = new AccessToken($requestData['user'], $requestData['token']);
    return AccessToken::verify($token);
}

/* Get Request Data */
$requestMethod = $_SERVER['REQUEST_METHOD'];
switch ($requestMethod) {
    case 'GET':
        $requestData = $_GET;
        break;
    case 'POST':
        $requestData = $_POST;
        break;
    case 'PATCH':
        parse_str(file_get_contents('php://input'), $requestData);
        ApiUtil::parse_raw_http_request($requestData);
        $requestData = is_array($requestData) ? $requestData : [];
        break;
    case 'DELETE':
        break;
    default:
        sendResponse(null, 405, 'Method not allowed.');
}

/* Extract EndPoint */
$parsedURI = parse_url($_SERVER["REQUEST_URI"]);
$path = explode('/', str_replace($BASE_URI, "", $parsedURI["path"]));
$endPoint = $path[0];
$requestData['dataId'] = isset($path[1]) ? $path[1] : null;
if (empty($endPoint)) {
    $endPoint = "/";
}

/* Extract Token */
if (isset($_SERVER["HTTP_X_API_KEY"])) {
    $requestData["user"] = $_SERVER["HTTP_X_API_USER"];
}
if (isset($_SERVER["HTTP_X_API_KEY"])) {
    $requestData["token"] = $_SERVER["HTTP_X_API_KEY"];
}

/* EndPoint Handlers */
$endpoints["/"] = function (string $requestMethod, array $requestData): void {
    sendResponse('Welcome to Kahuna API!');
};

$endpoints["404"] = function (string $requestMethod, array $requestData): void {
    sendResponse(null, 404, "Endpoint " . $requestData["endPoint"] . " not found.");
};

/* This will handle /user */
$endpoints["user"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'POST') {
        $email = $requestData['email'];
        $password = $requestData['password'];
        $user = new User($email, $password);
        $user = User::save($user);
        sendResponse($user, 201);
    } else if ($requestMethod === 'PATCH') {
        sendResponse(null, 501, 'Updating a user has not yet been implemented.');
    } else if ($requestMethod === 'DELETE') {
        sendResponse(null, 501, 'Deleting a user has not yet been implemented.');
    } else {
        sendResponse(null, 405, 'Method not allowed.');
    }
};

// login code from todopal
$endpoints["login"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'POST') {
        $email = $requestData['email'];
        $password = $requestData['password'];
        $user = new User($email, $password);
        $user = User::authenticate($user);
        if ($user) {
            $token = new AccessToken($user->getId());
            $token = AccessToken::save($token);
            sendResponse(['user' => $user->getId(), 'token' => $token->getToken()]);
        } else {
            sendResponse(null, 401, 'Login failed.');
        }
    } else {
        sendResponse(null, 405, 'Method not allowed.');
    }
};

// logout code from todopal
$endpoints["logout"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'POST') {
        if (checkToken($requestData)) {
            $userId = $requestData['user'];
            $token = new AccessToken($userId);
            $token = AccessToken::delete($token);
            sendResponse('You are logged out', 200,);
        } else {
            sendResponse(null, 403, 'Missing, invalid or expired token.');
        }
    } else {
        sendResponse(null, 405, 'Method not allowed.');
    }
};

// token code from todopal
$endpoints["token"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'GET') {
        if (checkToken($requestData)) {
            sendResponse(['valid' => true, 'token' => $requestData['token']]);
        } else {
            sendResponse(['valid' => false, 'token' => $requestData['token']]);
        }
    } else {
        sendResponse(null, 405, 'Method not allowed.');
    }
};

/* This will handle /product */
$endpoints["product"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'GET') {
        $products = Product::loadAll();
        sendResponse($products);
    } else if ($requestMethod === 'POST') {
        $serial = $requestData['serial'];
        $name = $requestData['name'];
        $warrantyLength = $requestData['warrantyLength'];
        $product = new Product($serial, $name, $warrantyLength);
        $product = Product::save($product);
        sendResponse($product, 201);
    } else {
        sendResponse(null, 404, 'Method not allowed');
    }
};

$endpoints["allRegisteredProducts"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'GET') {
        $products = RegisteredProduct::loadallRegisteredProducts();
        sendResponse($products);
    } else {
        sendResponse(null, 404, 'Method not allowed');
    }
};

$endpoints["registerProduct"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'POST') {
        
        $productList = new registeredProduct();
        $productList = RegisteredProduct::loadallRegisteredProducts();
        if (checkToken($requestData)) {
            $productSerial = $requestData['serialNumber'];
            if (in_array($productSerial, $productList)) {
                sendResponse(1, 403, 'Product already registered.');
            } else {
                $userId = $requestData['user'];
                $product = Product::load($productSerial);
                $registerProduct = new RegisteredProduct(id: $product->getId(), serial: $product->getserial(), name: $product->getname(), warrantyLength: $product->getwarrantyLength(), userId: $userId);
                $registerProduct = RegisteredProduct::saveRegisteredProduct($registerProduct);
                sendResponse($registerProduct, 201);
            }
        } else {
        sendResponse(0, 403, 'Missing, invalid or expired token.');
        }
        
    } else if ($requestMethod === 'GET') {
            if (checkToken($requestData)) {
                $userId = $requestData['user'];
                $user = new RegisteredProduct(userId: $userId);
                $registeredProducts = $user::loadRegisteredProducts($user);
                sendResponse($registeredProducts);
            } else {
                sendResponse(null, 403, 'Missing, invalid or expired token.');
            }
    }    
    else {
        // If the request method is not POST, return a "Method Not Allowed" response
        sendResponse(null, 405, 'Method not allowed');
    }
};

function cors()
{
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, DELETE");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}

try {
    if (isset($endpoints[$endPoint])) {
        $endpoints[$endPoint]($requestMethod, $requestData);
    } else {
        $endpoints["404"]($requestMethod, array("endPoint" => $endPoint));
    }
} catch (Exception $e) {
    sendResponse(null, 500, $e->getMessage());
} catch (Error $e) {
    sendResponse(null, 500, $e->getMessage());
}