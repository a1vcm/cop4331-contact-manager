<?php

require_once 'utils.php';

$inData = getRequestInfo();

$conn = new mysqli("206.81.15.115", "TheBeast", "WeLoveCOP4331", "Tables_in_COP4331");
if ($conn->connect_error)
{
    returnWithErrorRegistration($conn->connect_error);
    exit;
}

// Check if login already exists
$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login = ?");
$stmt->bind_param("s", $inData["Login"]);
$stmt->execute();
$result = $stmt->get_result();

if ($result->fetch_assoc())
{
    returnWithErrorRegistration("Login already exists");
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

// Hash the password before storing
$hashedPassword = password_hash($inData["Password"], PASSWORD_DEFAULT);

// FIX 1: Removed single quotes around column names (was 'Login', 'Password')
// FIX 2: Changed 5 placeholders (?,?,?,?,?) to 4 to match 4 bound values
$stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $inData["FirstName"], $inData["LastName"], $inData["Login"], $hashedPassword);
$stmt->execute();

if ($stmt->affected_rows > 0)
{
    returnWithRegistrationInfo($stmt->insert_id);
}
else
{
    returnWithErrorRegistration("Registration failed");
}

$stmt->close();
$conn->close();

?>