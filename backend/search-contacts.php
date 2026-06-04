<?php

require_once 'utils.php';

$inData = getRequestInfo();

$userId    = isset($inData['userId']) ? (int)$inData['userId'] : 0;
$search    = $inData['search'] ?? '';

// Grab the new pagination & sorting values sent by your updated frontend UI
$page      = isset($inData['page']) ? (int)$inData['page'] : 1;
$perPage   = isset($inData['perPage']) ? (int)$inData['perPage'] : 10;
$sortBy    = $inData['sortBy'] ?? 'firstName';
$sortOrder = strtoupper($inData['sortOrder'] ?? 'ASC');

if ($userId == 0) {
    returnWithError('Missing userId.');
    exit;
}

// 1. Map frontend JavaScript properties to your exact database columns
$allowedColumns = [
    'firstName' => 'FirstName',
    'lastName'  => 'LastName',
    'email'     => 'Email',
    'createdAt' => 'CreatedDate'
];

// Fallback to defaults if values are missing or unrecognized to block SQL injection
$targetColumn = $allowedColumns[$sortBy] ?? 'FirstName';
if ($sortOrder !== 'ASC' && $sortOrder !== 'DESC') {
    $sortOrder = 'ASC';
}

if ($page < 1) $page = 1;
if (!in_array($perPage, [5, 10, 25])) $perPage = 10; // Validate page size options

$offset = ($page - 1) * $perPage;

$conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');
if ($conn->connect_error) {
    returnWithError('Database connection failed: ' . $conn->connect_error);
    exit;
}

$searchTerm = '%' . $search . '%';

// 2. Query #1: Fetch the total overall count for these search parameters
// (Crucial so index.html knows exactly how many total pages exist)
$countStmt = $conn->prepare(
    'SELECT COUNT(*) as Total
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
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();
$totalCount = (int)($countRow['Total'] ?? 0);
$countStmt->close();

// 3. Query #2: Fetch only the specified slice of records using LIMIT and OFFSET
$stmt = $conn->prepare(
    "SELECT ID, FirstName, LastName, Phone, Email, UserID, CreatedDate
     FROM Contacts
     WHERE UserID = ?
     AND (
        FirstName LIKE ?
        OR LastName LIKE ?
        OR Phone LIKE ?
        OR Email LIKE ?
     )
     ORDER BY $targetColumn $sortOrder
     LIMIT ? OFFSET ?"
);

$stmt->bind_param('issssi i', $userId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $perPage, $offset);
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

// 4. Return results structural payloads matching the custom UI requirements
sendResultInfoAsJson([
    'contacts'   => $contacts,
    'results'    => $contacts, 
    'total'      => $totalCount, // Main total reference
    'totalCount' => $totalCount, // UI fallback mechanism string
    'error'      => ''
]);

$stmt->close();
$conn->close();

?>