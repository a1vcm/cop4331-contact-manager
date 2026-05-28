<?php
require_once "utils.php";

$inData = getRequestInfo();

$userId = $inData["userId"] ?? $inData["UserID"] ?? getUserIdFromToken();
$search = $inData["search"] ?? $inData["Search"] ?? "";
$page = intval($inData["page"] ?? 1);
$limit = intval($inData["limit"] ?? 10);

if ($page < 1) {
    $page = 1;
}

if ($limit < 1 || $limit > 100) {
    $limit = 10;
}

$offset = ($page - 1) * $limit;

if ($userId == 0) {
    returnWithError("Missing userId", 400);
}

$conn = getDatabaseConnection();

if ($conn == null) {
    returnWithError("Database connection failed", 500);
}

$searchTerm = "%" . $search . "%";

$countStmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Contacts
     WHERE UserID = ?
     AND (
        FirstName LIKE ?
        OR LastName LIKE ?
        OR Phone LIKE ?
        OR Email LIKE ?
     )"
);
$countStmt->bind_param("issss", $userId, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$countStmt->execute();
$countResult = $countStmt->get_result();
$total = 0;

if ($countRow = $countResult->fetch_assoc()) {
    $total = intval($countRow["total"]);
}

$countStmt->close();

$stmt = $conn->prepare(
    "SELECT ID, FirstName, LastName, Phone, Email, UserID
     FROM Contacts
     WHERE UserID = ?
     AND (
        FirstName LIKE ?
        OR LastName LIKE ?
        OR Phone LIKE ?
        OR Email LIKE ?
     )
     ORDER BY LastName, FirstName
     LIMIT ? OFFSET ?"
);

$stmt->bind_param("issssii", $userId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
$stmt->execute();

$result = $stmt->get_result();

$contacts = array();

while ($row = $result->fetch_assoc()) {
    $contacts[] = array(
        "id" => intval($row["ID"]),
        "firstName" => $row["FirstName"],
        "lastName" => $row["LastName"],
        "phone" => $row["Phone"],
        "email" => $row["Email"],
        "userId" => intval($row["UserID"]),
        "createdAt" => null
    );
}

sendJson(array(
    "contacts" => $contacts,
    "results" => $contacts,
    "total" => $total,
    "page" => $page,
    "limit" => $limit,
    "error" => ""
));

$stmt->close();
$conn->close();
?>
