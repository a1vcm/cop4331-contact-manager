<?php

    require_once 'utils.php';
 
	$inData = getRequestInfo();
 
	$conn = new mysqli("206.81.15.115", "TheBeast", "WeLoveCOP4331", "Tables_in_COP4331");
	if( $conn->connect_error )
	{
		returnWithErrorRegistration( $conn->connect_error );
	}
	else
	{
		// Check if login already exists
		$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login=?");
		$stmt->bind_param("s", $inData["login"]);
		$stmt->execute();
		$result = $stmt->get_result();
 
		if( $result->fetch_assoc() )
		{
			returnWithError("Login already exists");
		}
		else
		{
			// Insert new user
			$stmt = $conn->prepare("INSERT INTO Users (firstName, lastName, login, password, phone, email) VALUES (?,?,?,?,?,?)");
			$stmt->bind_param("ssssss", $inData["firstName"], $inData["lastName"], $inData["login"], $inData["password"], $inData["phone"], $inData["email"]);
			$stmt->execute();
 
			if( $stmt->affected_rows > 0 )
			{
				returnWithInfoRegistration( $stmt->insert_id );
			}
			else
			{
				returnWithErrorRegistration("Registration failed");
			}
		}
 
		$stmt->close();
		$conn->close();
	}
 
?>


