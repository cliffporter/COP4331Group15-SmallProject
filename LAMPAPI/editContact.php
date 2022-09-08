<?php

	$inData = getRequestInfo();
	
	//Get fields from clients JSON POST
	$name = $inData["name"];
	$phoneNumber = $inData["phoneNumber"];
	$email = $inData["email"];
	$id = $inData["id"];

	//Connect to mySQL
	$conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER"); 
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		//Update the contact
		$stmt = $conn->prepare("UPDATE Contacts SET Name=?, Phone_number=?, Email=? WHERE ID=?;");
		$stmt->bind_param("ssss", $name, $phoneNumber, $email, $id);
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
