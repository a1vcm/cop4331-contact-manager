<?php

    require_once 'utils.php';

	$inData = getRequestInfo();

    // user does not connect directly to database, so use line below
	$conn = new mysqli("localhost", "admin", "adminCOP", "COP4331"); // API connects to database using this
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
        $stmt = $conn->prepare("SELECT ID, FirstName, LastName FROM Users WHERE Login=? AND Password=?");
		$stmt->bind_param("ss", $inData["username"], $inData["password"]);
		$stmt->execute();
		$result = $stmt->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			returnWithLoginInfo( $row['FirstName'], $row['LastName'], $row['ID'] );
		}
		else
		{
			returnWithError("No Records Found");
		}

		$stmt->close();
		$conn->close();
	}
?>
