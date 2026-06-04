<?php

    require_once 'utils.php';

    $inData = getRequestInfo();

    $userId = $inData["userId"] ?? 0;
    $search = $inData["search"] ?? "";

    if ($userId == 0)
    {
        returnWithError("Missing userId.");
        exit();
    }

    $conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');

    if ($conn->connect_error)
    {
        returnWithError('Database connection failed: ' . $conn->connect_error);
        exit();
    }

    $searchTerm = "%" . $search . "%";

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
         ORDER BY LastName, FirstName"
    );

    $stmt->bind_param("issss", $userId, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();

    $result = $stmt->get_result();

    $contacts = array();

    while ($row = $result->fetch_assoc())
    {
        $contacts[] = array(
            "id" => intval($row["ID"]),
            "firstName" => $row["FirstName"],
            "lastName" => $row["LastName"],
            "phone" => $row["Phone"],
            "email" => $row["Email"],
            "userId" => intval($row["UserID"])
        );
    }

    $retValue = json_encode(array(
        "contacts" => $contacts,
        "results" => $contacts,
        "total" => count($contacts),
        "error" => ""
    ));

    sendResultInfoAsJson($retValue);

    $stmt->close();
    $conn->close();

?>
