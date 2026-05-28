<?php
require_once "utils.php";

$inData = getRequestInfo();

$username = $inData["username"] ?? $inData["login"] ?? "";
$password = $inData["password"] ?? "";

if ($username === "" || $password === "") {
    returnWithError("Missing username or password", 400);
}

$conn = getDatabaseConnection();

if ($conn == null) {
    returnWithError("Database connection failed", 500);
}

$stmt = $conn->prepare("SELECT ID, FirstName, LastName, Login FROM Users WHERE Login=? AND Password=?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    sendJson(array(
        "id" => intval($row["ID"]),
        "firstName" => $row["FirstName"],
        "lastName" => $row["LastName"],
        "username" => $row["Login"],
        "token" => "user-" . $row["ID"],
        "error" => ""
    ));
} else {
    returnWithError("No Records Found", 401);
}

$stmt->close();
$conn->close();
?>
