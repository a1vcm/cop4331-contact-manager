<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit();
}

function getRequestInfo()
{
    $data = json_decode(file_get_contents("php://input"), true);
    return $data === null ? array() : $data;
}

function getDatabaseConnection()
{
    $conn = new mysqli("localhost", "admin", "adminCOP", "COP4331");

    if ($conn->connect_error) {
        return null;
    }

    return $conn;
}

function sendJson($obj, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($obj);
    exit();
}

function returnWithError($message, $statusCode = 400)
{
    sendJson(array(
        "message" => $message,
        "error" => $message
    ), $statusCode);
}

function getUserIdFromToken()
{
    $headers = getallheaders();
    $auth = $headers["Authorization"] ?? $headers["authorization"] ?? "";
    $prefix = "Bearer user-";

    if (substr($auth, 0, strlen($prefix)) === $prefix) {
        return intval(substr($auth, strlen($prefix)));
    }

    return 0;
}
?>
