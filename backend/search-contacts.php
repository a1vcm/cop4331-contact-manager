<?php

require_once 'utils.php';

$inData = getRequestInfo();

$userId = $inData['userId'] ?? 0;
$search = $inData['search'] ?? '';
$page   = max(1, (int)($inData['page']  ?? 1));
$limit  = (int)($inData['limit'] ?? 10);

// Clamp limit to allowed values: 5, 10, 25
if (!in_array($limit, [5, 10, 25])) $limit = 10;

$offset = ($page - 1) * $limit;

if ($userId == 0) {
    returnWithError('Missing userId.');
    exit;
}

$conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');
if ($conn->connect_error) {
    returnWithError('Database connection failed: ' . $conn->connect_error);
    exit;
}

$searchTerm = '%' . $search . '%';

// First get the total count for pagination
$countStmt = $conn->prepare(
    'SELECT COUNT(*) AS total
     FROM Contacts
     WHERE UserID = ?
     AND (
         FirstName LIKE ?
         OR LastName LIKE ?
         OR Phone LIKE ?
         OR Email LIKE ?
     )'
);
$countStmt->bind_param('issss', $userId, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$countStmt->execute();
$totalCount = (int)$countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();

// Then fetch only the current page of results
$stmt = $conn->prepare(
    'SELECT ID, FirstName, LastName, Phone, Email, UserID, CreatedDate
     FROM Contacts
     WHERE UserID = ?
     AND (
         FirstName LIKE ?
         OR LastName LIKE ?
         OR Phone LIKE ?
         OR Email LIKE ?
     )
     ORDER BY LastName, FirstName
     LIMIT ? OFFSET ?'
);
$stmt->bind_param('issssii', $userId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$contacts = [];
while ($row = $result->fetch_assoc()) {
    $contacts[] = [
        'id'        => (int)$row['ID'],
        'firstName' => $row['FirstName'],
        'lastName'  => $row['LastName'],
        'phone'     => $row['Phone'],
        'email'     => $row['Email'],
        'userId'    => (int)$row['UserID'],
        'createdAt' => $row['CreatedDate']
    ];
}

sendResultInfoAsJson([
    'contacts' => $contacts,
    'results'  => $contacts,
    'total'    => $totalCount,
    'page'     => $page,
    'limit'    => $limit,
    'error'    => ''
]);

$stmt->close();
$conn->close();

?>