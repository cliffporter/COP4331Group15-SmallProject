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
		returnWithError(-10, $conn->connect_error );
	}
	else if ( $name == "")
	{
		//Do not allow Contacts with no name
		returnWithError( -1, "null value in name");
	}
	else
	{
		//Search for a contact owned by the user with the same name
		$stmt = $conn->prepare("SELECT ID FROM Contacts WHERE Name=? AND UserID=?");
		$stmt->bind_param("ss", $name, $userID);
		$stmt->execute();
		$result = $stmt->get_result();
		
		//Check if a duplicate contact exists
		if( $row = $result->fetch_assoc()  ) 
		{
			//A duplicate entry was found in the users contacts
			returnWithError( 0, "A duplicate contact exists, enter a diffrent name");
		}
		else
		{
			//Update the contact
			$stmt = $conn->prepare("UPDATE Contacts SET Name=?, Phone_number=?, Email=? WHERE ID=?;");
			$stmt->bind_param("ssss", $name, $phoneNumber, $email, $id);
			$stmt->execute();

			//Check if anything was edited
			if( $row = $stmt->affected_rows  ) 
			{
				returnWithError(1, "contact edited");
			}
			else
			{
				returnWithError(0, "no contacts updated (ID not found or edit matches current value)");
			}
		
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
