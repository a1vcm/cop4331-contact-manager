<?php
require_once "utils.php";

$inData = getRequestInfo();

$contactId = $inData["contactId"] ?? $inData["id"] ?? $inData["ID"] ?? 0;
$userId = $inData["userId"] ?? $inData["UserID"] ?? getUserIdFromToken();
$firstName = $inData["firstName"] ?? $inData["FirstName"] ?? "";
$lastName = $inData["lastName"] ?? $inData["LastName"] ?? "";
$phone = $inData["phone"] ?? $inData["Phone"] ?? $inData["phoneNumber"] ?? "";
$email = $inData["email"] ?? $inData["Email"] ?? "";

if ($contactId == 0 || $userId == 0 || $firstName === "" || $lastName === "" || $phone === "" || $email === "") {
    returnWithError("Missing required update fields", 400);
}

$conn = getDatabaseConnection();

if ($conn == null) {
    returnWithError("Database connection failed", 500);
}

$stmt = $conn->prepare(
    "UPDATE Contacts
     SET FirstName=?, LastName=?, Phone=?, Email=?
     WHERE ID=? AND UserID=?"
);

$stmt->bind_param("ssssii", $firstName, $lastName, $phone, $email, $contactId, $userId);
$stmt->execute();

if ($stmt->affected_rows >= 0) {
    sendJson(array(
        "message" => "Contact updated",
        "error" => ""
    ));
} else {
    returnWithError("Failed to update contact", 500);
}

$stmt->close();
$conn->close();
?>
