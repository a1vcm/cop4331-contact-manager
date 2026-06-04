<?php

require_once 'utils.php';

$inData = getRequestInfo();

// ── Validate ──────────────────────────────────────────────────────────────────
if (
    empty($inData['userId'])    ||
    empty($inData['firstName']) ||
    empty($inData['lastName'])  ||
    empty($inData['phone'])     ||
    empty($inData['email'])
) {
    returnWithError('All contact fields are required.');
    exit;
}

// ── Connect ───────────────────────────────────────────────────────────────────
$conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');
if ($conn->connect_error) {
    returnWithError('Database connection failed: ' . $conn->connect_error);
    exit;
}

// ── Insert (include UserID so contacts can be retrieved per-user) ─────────────
$stmt = $conn->prepare(
    'INSERT INTO Contacts (UserID, FirstName, LastName, Phone, Email) VALUES (?, ?, ?, ?, ?)'
);
$stmt->bind_param(
    'issss',
    $inData['userId'],
    $inData['firstName'],
    $inData['lastName'],
    $inData['phone'],
    $inData['email']
);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    sendResultInfoAsJson(['id' => (int)$stmt->insert_id, 'error' => '']);
} else {
    returnWithError('Failed to create contact.');
}

$stmt->close();
$conn->close();