<?php

require_once 'utils.php';

$inData = getRequestInfo();

// ── Validate required fields ──────────────────────────────────────────────────
if (empty($inData['username']) || empty($inData['password'])) {
    returnWithError('Username and password are required.');
    exit;
}

// ── Connect ───────────────────────────────────────────────────────────────────
$conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');
if ($conn->connect_error) {
    returnWithError('Database connection failed: ' . $conn->connect_error);
    exit;
}

// ── Look up user by Login only, then verify the hashed password ───────────────
// We fetch the stored hash first, then use password_verify() to check it.
// This is the correct approach — you cannot use WHERE Password=? with bcrypt.
$stmt = $conn->prepare(
    'SELECT ID, FirstName, LastName, Password FROM Users WHERE Login = ?'
);
$stmt->bind_param('s', $inData['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // password_verify compares the plain-text attempt against the stored hash
    if (password_verify($inData['password'], $row['Password'])) {
        returnWithLoginInfo($row['FirstName'], $row['LastName'], $row['ID']);
    } else {
        returnWithError('Invalid username or password.');
    }
} else {
    returnWithError('Invalid username or password.');
}

$stmt->close();
$conn->close();

?>
