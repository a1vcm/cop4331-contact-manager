<?php

// I still have not found a use for this file, its incomplete, dont worry about it much

$host = "localhost";
$user = "root";
$pass = "";
$db   = "ContactManager";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

?>
