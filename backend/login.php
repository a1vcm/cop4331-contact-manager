<?php

require_once 'utils.php';

$inData = getRequestInfo();

// API connects to database
$conn = new mysqli("localhost", "admin", "adminCOP", "COP4331");
if ($conn->connect_error)
{
    returnWithError($conn->connect_error);
    exit;
}

// FIX: Fetch the hashed password too so we can verify it with password_verify()
// Previously compared plain-text passwords directly in SQL — insecure and broken with hashing
$stmt = $conn->prepare("SELECT ID, FirstName, LastName, Password FROM Users WHERE Login = ?");
$stmt->bind_param("s", $inData["username"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && password_verify($inData["password"], $row['Password']))
{
    returnWithLoginInfo($row['FirstName'], $row['LastName'], $row['ID']);
}
else
{
    returnWithError("Invalid username or password");
}

$stmt->close();
$conn->close();

?>