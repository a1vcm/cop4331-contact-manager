<?php
require_once "utils.php";

$inData = getRequestInfo();

$contactId = $inData["contactId"] ?? $inData["id"] ?? $inData["ID"] ?? 0;
$userId = $inData["userId"] ?? $inData["UserID"] ?? getUserIdFromToken();

if ($contactId == 0 || $userId == 0) {
    returnWithError("Missing contactId or userId", 400);
}

$conn = getDatabaseConnection();

if ($conn == null) {
    returnWithError("Database connection failed", 500);
}

$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=? AND UserID=?");
$stmt->bind_param("ii", $contactId, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    sendJson(array(
        "message" => "Contact deleted",
        "error" => ""
    ));
} else {
    returnWithError("Contact not found", 404);
}

$stmt->close();
$conn->close();
?>
