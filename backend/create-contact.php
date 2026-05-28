<?php
require_once "utils.php";

$inData = getRequestInfo();

$userId = $inData["userId"] ?? $inData["UserID"] ?? getUserIdFromToken();
$firstName = $inData["firstName"] ?? $inData["FirstName"] ?? "";
$lastName = $inData["lastName"] ?? $inData["LastName"] ?? "";
$phone = $inData["phone"] ?? $inData["Phone"] ?? $inData["phoneNumber"] ?? "";
$email = $inData["email"] ?? $inData["Email"] ?? "";

if ($userId == 0 || $firstName === "" || $lastName === "" || $phone === "" || $email === "") {
    returnWithError("Missing required contact fields", 400);
}

$conn = getDatabaseConnection();

if ($conn == null) {
    returnWithError("Database connection failed", 500);
}

$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    sendJson(array(
        "id" => intval($stmt->insert_id),
        "firstName" => $firstName,
        "lastName" => $lastName,
        "phone" => $phone,
        "email" => $email,
        "userId" => intval($userId),
        "error" => ""
    ));
} else {
    returnWithError("Failed to create contact", 500);
}

$stmt->close();
$conn->close();
?>
