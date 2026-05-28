<?php
require_once "utils.php";

$inData = getRequestInfo();

$firstName = $inData["firstName"] ?? $inData["FirstName"] ?? "";
$lastName = $inData["lastName"] ?? $inData["LastName"] ?? "";
$username = $inData["username"] ?? $inData["login"] ?? $inData["Login"] ?? "";
$password = $inData["password"] ?? $inData["Password"] ?? "";

if ($firstName === "" || $lastName === "" || $username === "" || $password === "") {
    returnWithError("Missing required fields", 400);
}

$conn = getDatabaseConnection();

if ($conn == null) {
    returnWithError("Database connection failed", 500);
}

$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->fetch_assoc()) {
    $stmt->close();
    $conn->close();
    returnWithError("Username already exists", 409);
}

$stmt->close();

$stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstName, $lastName, $username, $password);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    sendJson(array(
        "id" => intval($stmt->insert_id),
        "firstName" => $firstName,
        "lastName" => $lastName,
        "username" => $username,
        "token" => "user-" . $stmt->insert_id,
        "error" => ""
    ));
} else {
    returnWithError("Registration failed", 500);
}

$stmt->close();
$conn->close();
?>
