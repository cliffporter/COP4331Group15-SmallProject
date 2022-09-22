<?php

	$inData = getRequestInfo();

	//Get fields from clients JSON POST
    $userid = $inData["userID"];
	$string = "%" . $inData["search"] . "%";
	$pageSize = $inData["pageSize"];
	$pageNum = $inData["pageNum"];

    $searchCount = 0;
	$searchResults = "";

	//Connect to mySQL
    $conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER");
    
    if ($conn->connect_error)
	{
		returnWithError(-10, $conn->connect_error );
	}
	else
	{
		//Set the offset amount, get all of a users contacts if no search provided
		$offsetAmt = $pageSize * ($pageNum-1);
		if ($inData["search"] == "")
		{
			//Search with page offsets, limit to pageSize results
			$stmt = $conn->prepare("SELECT * FROM Contacts WHERE UserID=? ORDER BY Name LIMIT ? OFFSET ?");
			$stmt->bind_param("sss", $userid, $pageSize, $offsetAmt);
		}
		else
		{
			//Search using search string, with page offsets
			$stmt = $conn->prepare("SELECT * FROM Contacts WHERE Name LIKE ? AND UserID=? ORDER BY Name LIMIT ? OFFSET ?");
			$stmt->bind_param("ssss", $string, $userid, $pageSize, $offsetAmt);
		}

		$stmt->execute();
		$result = $stmt->get_result();

		//Process results into a string
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '{' . '"name": ' . '"' . $row["Name"] . '",';
			$searchResults .= '"phoneNumber": ' . '"' . $row["Phone_number"] . '",';
			$searchResults .= '"email": ' . '"' . $row["Email"] . '",';
			$searchResults .= '"id": ' . $row["ID"] . '}';

		}
		
		//Return JSON
		if( $searchCount == 0 )
		{
			returnWithError(0, "no contacts found" );
		}
		else
		{
			returnWithInfo( $searchResults );
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
		//$retValue = '{"id":"' . $errID . '","error":"' . $errSTR . '"}';
		$retValue = '{"results":[],"id":"' . $errID . '","error":"' . $errSTR . '"}';
		sendResultInfoAsJson( $retValue );
	}

	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"id":"1","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>