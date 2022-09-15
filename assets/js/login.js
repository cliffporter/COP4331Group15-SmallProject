const urlBase = 'http://cop4331group15.xyz/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";

	let login = document.getElementById("Uname").value;
	let password = document.getElementById("Pass").value;

	document.getElementById("loginResult").innerHTML = "";

	let tmp = {login:login,password:password};
	let jsonPayload = JSON.stringify( tmp );

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
				userId = jsonObject.id;

				if( userId < 1 )
				{
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}

				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();

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

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}



function doRegister()
{
userId = 0;

let login = document.getElementById("Usernamereg").value;
let password = document.getElementById("Passwordreg").value;
let firstName = document.getElementById("FirstName").value;
let lastName = document.getElementById("LastName").value;


document.getElementById("RegisterResult").innerHTML = "";

let tmp = {login:login, password:password, firstName:firstName, lastName:lastName };
let jsonPayload = JSON.stringify( tmp );

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

			if( userId == -1 )
			{
				document.getElementById("RegisterResult").innerHTML = "Please ensure all fields are filled out!";
				return;
			}

			if( userId == 0 )
			{
				document.getElementById("RegisterResult").innerHTML = "Username already exists! Please try another username!";
				return;
			}

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
