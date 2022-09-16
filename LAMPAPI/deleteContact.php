<?php

	$inData = getRequestInfo();
	
	//Get fields from clients JSON POST
	$id = $inData["id"];

	//Connect to mySQL
	$conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER");		
	if( $conn->connect_error )
	{
		returnWithError(-10,  $conn->connect_error );
	}
	else
	{
		//remove contact
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=?");
		$stmt->bind_param("s", $id);
		$stmt->execute();


		//Check if anything was deleted
		//See : https://stackoverflow.com/questions/2966418/check-mysql-query-results-if-delete-query-worked
		if( $row = $stmt->affected_rows  ) 
		{
			returnWithError(1, "contact deleted");
		}
		else
		{
			returnWithError(0, "contact not found");
		}
	

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
	//PARAM: $errID - the ID of the specific error
	//       $errSTR - a message describing the error, mostly for debugging
	function returnWithError($errID, $errSTR)
	{
		$retValue = '{"id":"' . $errID . '","error":"' . $errSTR . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
