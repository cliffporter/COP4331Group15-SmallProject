<?php

	$inData = getRequestInfo();
	
	//Get fields from clients JSON POST
	$id = $inData["id"];

	//Connect to mySQL
	$conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER");		
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		//remove contact
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=?");
		$stmt->bind_param("s", $id);
		$stmt->execute();

		//Return a blank error (success)
		returnWithError("");
	

		$stmt->close();
		$conn->close();
	}
	
	//Get JSON from client, return as object
	//PARAM: none
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	//Send JASON to client
	//PARAM: $obj - A JSON object
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	//Return JSON to user with an error message
	//PARAM: $err - the message string
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
