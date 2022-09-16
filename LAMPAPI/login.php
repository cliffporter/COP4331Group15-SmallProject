<?php

	$inData = getRequestInfo();
	
	//Get fields from clients JSON POST
	$id = 0;
	$firstName = "";
	$lastName = "";

	//Connect to mySQL
	$conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER"); 	
	if( $conn->connect_error )
	{
		returnWithError(-10, $conn->connect_error );
	}
	else
	{
		//Search for the clients login and password
		$stmt = $conn->prepare("SELECT ID,firstName,lastName FROM Users WHERE Login=? AND Password =?");
		$stmt->bind_param("ss", $inData["login"], $inData["password"]);
		$stmt->execute();
		$result = $stmt->get_result();
		

		//Check if the correct login+password matches a registered user
		if( $row = $result->fetch_assoc()  )
		{
			//Update last logged in time
			$stmt = $conn->prepare("UPDATE Users SET DateLastLoggedIn= CURRENT_TIMESTAMP WHERE ID=?;");
			$stmt->bind_param("s", $row['ID']);
			$stmt->execute();
			
			//Return login success
			returnWithInfo( $row['firstName'], $row['lastName'], $row['ID'] );
		}
		else
		{
			returnWithError(0, "No Records Found");
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
	
	//Return JSON to user with the users info
	//PARAM: $firstName, $lastName, $id from database
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
