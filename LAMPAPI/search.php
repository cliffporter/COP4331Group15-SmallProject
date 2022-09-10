<?php

	$inData = getRequestInfo();

    $userid = $inData["userID"];
	$string = "%" . $inData["search"] . "%";

    $searchCount = 0;
	$searchResults = "";

    $conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER");
    
    if ($conn->connect_error)
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		if ($inData["search"] == "")
		{
            
			$stmt = $conn->prepare("SELECT * FROM Contacts WHERE UserID=?");
			$stmt->bind_param("s", $userid);
            
		}
		else
		{
			$stmt = $conn->prepare("SELECT * FROM Contacts WHERE Name LIKE ? AND UserID=?");
			$stmt->bind_param("ss", $string, $userid);
            
            
		}

		$stmt->execute();
		$result = $stmt->get_result();

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
			$searchResults .= '"ID": ' . $row["ID"] . '}';
		}

		if( $searchCount == 0 )
		{
			returnWithError( "No Contacts Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
		}

		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}

	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>