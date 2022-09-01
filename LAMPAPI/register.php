
<?php

	$inData = getRequestInfo();
	
	$login = $inData["login"]);
	$password = $inData["password"]);
	$firstName = $inData["firstName"]);
	$lastName = $inData["lastName"]);


	$conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER"); 
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login=?");
		$stmt->bind_param("s", $login);
		$stmt->execute();
		$result = $stmt->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			returnWithError("User already exists");
		}
		//No login found
		else
		{
			//save to database 
			$stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
			$stmt->execute();
			
			//return login success
			returnWithError("");
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
	
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
