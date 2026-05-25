<?php

function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($err)
{
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

function returnWithInfo($info)
{
    sendResultInfoAsJson($info);
}
 
function returnWithErrorRegistration( $err )
{
	$retValue = '{"id":0,"error":"' . $err . '"}'; // return id:0, safety measure
	sendResultInfoAsJson( $retValue );
}
 
function returnWithRegistrationInfo( $id )
{
	$retValue = '{"id":' . $id . ',"error":""}';
	sendResultInfoAsJson( $retValue );
}

function returnWithLoginInfo( $firstName, $lastName, $id )
{
	$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
	sendResultInfoAsJson( $retValue );
}

?>
