<?php

require_once 'utils.php';

$inData = getRequestInfo();

$conn = new mysqli("206.81.15.115", "TheBeast", "WeLoveCOP4331", "Tables_in_COP4331"); // API connects to database
if( $conn->connect_error ) // if connection fails, return an error
{
	returnWithError( $conn->connect_error );
}
else
{
	$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email) VALUES (?,?,?,?,?)");
	$stmt->bind_param("ssss", $inData["FirstName"], $inData["LastName"], $inData["Phone"], $inData["Email"]);
	$stmt->execute();

	if( $stmt->affected_rows > 0 )
	{
		returnWithInfo( '{"id":' . $stmt->insert_id . ',"error":""}' );
	}
	else
	{
		returnWithError("Failed to create contact");
	}

	$stmt->close();
	$conn->close();
}

?>