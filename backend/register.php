<?php

require_once 'utils.php';

$inData = getRequestInfo();

// ── Validate required fields are present ─────────────────────────────────────
if (
    empty($inData['firstName']) ||
    empty($inData['lastName'])  ||
    empty($inData['login'])     ||
    empty($inData['password'])
) {
    returnWithError('Missing required fields.');
    exit;
}

// ── Connect ───────────────────────────────────────────────────────────────────
// Use the same credentials as login.php (localhost / admin / adminCOP / COP4331)
$conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');
if ($conn->connect_error) {
    returnWithError('Database connection failed: ' . $conn->connect_error);
    exit;
}

// ── Check for duplicate login ─────────────────────────────────────────────────
$stmt = $conn->prepare('SELECT ID FROM Users WHERE Login = ?');
$stmt->bind_param('s', $inData['login']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    returnWithError('Username already exists.');
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// ── Hash the password (NEVER store plain text) ────────────────────────────────
$hashedPassword = password_hash($inData['password'], PASSWORD_DEFAULT);

// ── Insert new user ───────────────────────────────────────────────────────────
// Column names are NOT quoted — single-quoting them treats them as string
// literals in MySQL, which causes the INSERT to fail.
$stmt = $conn->prepare(
    'INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)'
);
$stmt->bind_param(
    'ssss',
    $inData['firstName'],
    $inData['lastName'],
    $inData['login'],
    $hashedPassword
);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    returnWithRegistrationInfo($stmt->insert_id);
} else {
    returnWithError('Registration failed. Please try again.');
}

$stmt->close();
$conn->close();

?>
