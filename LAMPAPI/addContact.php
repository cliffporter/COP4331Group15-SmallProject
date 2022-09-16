<?php

	$inData = getRequestInfo();
	
	//Get fields from clients JSON POST
	$name = $inData["name"];
	$phoneNumber = $inData["phoneNumber"];
	$email = $inData["email"];
	$userID = $inData["userID"];

	//Connect to mySQL
	$conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER"); 
	if( $conn->connect_error )
	{
		returnWithError( -10, $conn->connect_error );
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
		else if ( $name == "")
		{
			//Do not allow Contacts with no name
			returnWithError( -1, "null value in name");
		}
		else
		{
			//Add the new contact to the database
			$stmt = $conn->prepare("INSERT INTO Contacts (Name,Phone_number,Email,UserID) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $name, $phoneNumber, $email, $userID);
			$stmt->execute();

			//Get the new contact to find the ID and return it
			$stmt = $conn->prepare("SELECT * FROM Contacts WHERE Name LIKE ? AND Phone_number LIKE ? AND Email LIKE ? AND UserID = ?");
			$stmt->bind_param("ssss", $name, $phoneNumber, $email, $userID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			
			$newID = $row['ID'];
			$newName = $row['Name'];
			$newPhone = $row['Phone_number'];
			$newEmail = $row['Email'];
			

			//Return the new contact
			returnWithContact( $newName, $newPhone, $newEmail, $newID );
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
	
	//Return JSON to user with the users info
	//PARAM: $firstName, $lastName, $id from database
	function returnWithContact($name, $phoneNumber, $email, $id)
	{
		//modified code from search.php
		$retValue .= '{' . '"name": ' . '"' . $name . '",';
		$retValue .= '"phoneNumber": ' . '"' . $phoneNumber . '",';
		$retValue .= '"email": ' . '"' . $email . '",';
		$retValue .= '"id": ' . $id . '}';

		sendResultInfoAsJson( $retValue );
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
