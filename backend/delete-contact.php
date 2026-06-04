<?php

    require_once 'utils.php';

    $inData = getRequestInfo();

    $contactId = $inData["contactId"] ?? 0;
    $userId = $inData["userId"] ?? 0;

    if ($contactId == 0 || $userId == 0)
    {
        returnWithError("Missing contactId or userId.");
        exit();
    }

    $conn = new mysqli('localhost', 'admin', 'adminCOP', 'COP4331');

    if ($conn->connect_error)
    {
        returnWithError('Database connection failed: ' . $conn->connect_error);
        exit();
    }

    $stmt = $conn->prepare(
        "DELETE FROM Contacts
         WHERE ID = ? AND UserID = ?"
    );

    $stmt->bind_param("ii", $contactId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0)
    {
        returnWithInfo('{"message":"Contact deleted","error":""}');
    }
    else
    {
        returnWithError("Contact not found.");
    }

    $stmt->close();
    $conn->close();

?>
