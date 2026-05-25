<?php

    require_once 'utils.php';

	$inData = getRequestInfo();
	
	$ID = 0;
	$FirstName = "";
	$LastName = "";
    $Email = "";
    $Phone = "";

    // user does not connect directly to database, so use line below
	$conn = new mysqli("206.81.15.115", "TheBeast", "WeLoveCOP4331", "Tables_in_COP4331"); // API connects to database using this
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
        $stmt = $conn->prepare("SELECT ID, FirstName, LastName FROM Users WHERE Login=? AND Password=?");
		$stmt->bind_param("ss", $inData["Login"], $inData["Password"]);
		$stmt->execute();
		$result = $stmt->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			returnWithInfo( $row['FirstName'], $row['LastName'], $row['ID'] );
		}
		else
		{
			returnWithError("No Records Found");
		}

		$stmt->close();
		$conn->close();
	}
?>
