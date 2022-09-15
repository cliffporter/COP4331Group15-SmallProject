
// Const Url and extention to reference differnet endpoints in the same JS file
const urlBase = 'http://cop4331group15.xyz/LAMPAPI';
const extension = 'php';

// Init the userId and first and last name to prevent issues
let userId = 0;
let firstName = "";
let lastName = "";


// Login in function that performs the login verification.
function doLogin()
{
	// init to 0 to prevent bugs.
	userId = 0;
	firstName = "";
	lastName = "";

	// Grabbing the text from those fileds inputted by the user (from HTML)
	let login = document.getElementById("Uname").value;
	let password = document.getElementById("Pass").value;

	// Set the login result to blank to reset any previous messages.
	document.getElementById("loginResult").innerHTML = "";

	// the data set that gets sent to the API (php file)
	let tmp = {login:login,password:password};

	// 	Converting to JSON
	let jsonPayload = JSON.stringify( tmp );
	// This just assembles teh URL to allow this JS file to be used with differnet
	// API endpoints.
	let url = urlBase + '/login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				let jsonObject = JSON.parse( xhr.responseText );
				// userId is the key that corresponds to each of our registered users
				userId = jsonObject.id;

				// If the API returns < 1, the combo either doesnt exist or is wrong!
				if( userId < 1 )
				{
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}

				// breaks down the JSON we recieved and sets them to our variables.
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
				// take us to the landing page! We are Logged in!
				window.location.href = "landing.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

// Function to save the cookies and remeber certain peices of data.
function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}


// our function that adds users to the database and creates them a profile.
function doRegister()
{
// Init to 0 to prevent bugs
userId = 0;

// Pull the data receievd from the user to send to the API.
let login = document.getElementById("Usernamereg").value;
let password = document.getElementById("Passwordreg").value;
let firstName = document.getElementById("FirstName").value;
let lastName = document.getElementById("LastName").value;

// Set the login result to blank to reset any previous messages.
document.getElementById("RegisterResult").innerHTML = "";

// the data set that gets sent to the API (php file)
let tmp = {login:login, password:password, firstName:firstName, lastName:lastName };
let jsonPayload = JSON.stringify( tmp );

// This just assembles teh URL to allow this JS file to be used with differnet
// API endpoints.
let url = urlBase + '/register.' + extension;

let xhr = new XMLHttpRequest();
xhr.open("POST", url, true);
xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
try
{
	xhr.onreadystatechange = function()
	{
		console.log(xhr.responseText);
		if (this.readyState == 4 && this.status == 200)
		{
			let jsonObject = JSON.parse( xhr.responseText );
			console.log(jsonObject);
			userId = jsonObject.id;
			// if the API returns 0, the username is taken!
			if( userId == 0 )
			{
				document.getElementById("RegisterResult").innerHTML = "Username already exists! Please try another username!";
				return;
			}
			// if the API returns -1, the user is attempting to send a NULL field.
			if( userId == -1 )
			{
				document.getElementById("RegisterResult").innerHTML = "Please ensure all fields are filled out!";
				return;
			}
			// if the API returns -10, the server is down!
			if( userId == -10 )
			{
				document.getElementById("RegisterResult").innerHTML = "Server not responding";
				return;
			}
		}
	};
	xhr.send(jsonPayload);
}
catch(err)
{
	document.getElementById("loginResult").innerHTML = err.message;
}
}
