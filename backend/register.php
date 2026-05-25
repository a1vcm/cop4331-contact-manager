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
		$stmt->bind_param("s", $inData["Login"]);
		$stmt->execute();
		$result = $stmt->get_result();
 
		if( $result->fetch_assoc() )
		{
			returnWithError("Login already exists");
		}
		else
		{
			// Insert new user, ID should be created in sql via incrementing
			$stmt = $conn->prepare("INSERT INTO Users (firstName, lastName, 'Login', 'Password') VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $inData["FirstName"], $inData["LastName"], $inData["Login"], $inData["Password"]);
			$stmt->execute();
 
			if( $stmt->affected_rows > 0 )
			{
				returnWithRegistrationInfo( $stmt->insert_id );
			}
			else
			{
				returnWithError("Registration failed");
			}
		}
 
		$stmt->close();
		$conn->close();
	}
    
?>


