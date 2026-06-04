<?php

require_once 'utils.php';

$inData = getRequestInfo();

$contactId = $inData['contactId'] ?? 0;
$userId = $inData['userId'] ?? 0;
$firstName = $inData['firstName'] ?? '';
$lastName = $inData['lastName'] ?? '';
$phone = $inData['phone'] ?? '';
$email = $inData['email'] ?? '';

if ($contactId == 0 || $userId == 0 || $firstName == '' || $lastName == '' || $phone == '' || $email == '') {
    returnWithError('Missing required update fields.');
    exit;
}

$conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');
if ($conn->connect_error) {
    returnWithError('Database connection failed: ' . $conn->connect_error);
    exit;
}

$stmt = $conn->prepare(
    'UPDATE Contacts
     SET FirstName = ?, LastName = ?, Phone = ?, Email = ?
     WHERE ID = ? AND UserID = ?'
);

$stmt->bind_param('ssssii', $firstName, $lastName, $phone, $email, $contactId, $userId);
$stmt->execute();

sendResultInfoAsJson([
    'message' => 'Contact updated',
    'error' => ''
]);

$stmt->close();
$conn->close();

?>
