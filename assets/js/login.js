
// Const Url and extention to reference differnet endpoints in the same JS file
const urlBase = 'http://cop4331group15.xyz/LAMPAPI';
const extension = 'php';

// Init the userId and first and last name to prevent issues
let userId = 0;
let firstName = "";
let lastName = "";
let contactId = 0;


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
			window.location.href = "index.html";
		}
	};
	xhr.send(jsonPayload);
}
catch(err)
{
	document.getElementById("loginResult").innerHTML = err.message;
}
}



function logOut()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function readCookie()
{
    userId = -1;
    let data = document.cookie;
    let splits = data.split(",");
    for(var i = 0; i < splits.length; i++)
    {
        let thisOne = splits[i].trim();
        let tokens = thisOne.split("=");
        if( tokens[0] == "firstName" )
        {
            firstName = tokens[1];
        }
        else if( tokens[0] == "lastName" )
        {
            lastName = tokens[1];
        }
        else if( tokens[0] == "userId" )
        {
            userId = parseInt( tokens[1].trim() );
        }
    }

    if( userId < 0 )
    {
        window.location.href = "index.html";
    }
    else
    {
        document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
    }
}
function doSearch()
{
	let srch = document.getElementById("searchText").value;
	//document.getElementById("colorSearchResult").innerHTML = "";

	let contactList = "";

	let tmp = {search:srch,userID:userId, pageSize:100, pageNum:1};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/search.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				//document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );

				for( let i=0; i<jsonObject.results.length; i++ )
				{
					contactList+="<tr>"
					contactList += "<td>"+ jsonObject.results[i].name+"</td>";
					contactList += "<td>"+ jsonObject.results[i].email+"</td>";
					contactList += "<td>"+ jsonObject.results[i].phoneNumber+"</td>";
					contactList += '<td><button type="button" onclick="togglePopup('+jsonObject.results[i].id+')" class="buttons"><i class="fa fa-edit blue-color" aria-hidden="true"></i></button></td>';
					contactList += '<td><button type="button" onclick="doDelete('+jsonObject.results[i].id+')" class="buttons"><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
					if( i < jsonObject.results.length - 1 )
					{
						contactList += "</tr>";
					}
				}

				document.getElementsByTagName("tbody")[0].innerHTML = contactList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorSearchResult").innerHTML = err.message;
	}

}

function doEdit()
{
	// let tmp = {phoneNumber:addPhone,name:fullName,userID:contactId,email:addEmail};
	// let jsonPayload = JSON.stringify( tmp );
	let editFirstName = document.getElementById("editName").value;
  let editLastName = document.getElementById("editLast").value;
  let editEmail = document.getElementById("editEmail").value;
  let editPhone = document.getElementById("editPhone").value;
  //document.getElementById("AddResult").innerHTML = "";
  let editfullName = editFirstName + " " + editLastName;
	//alert("Phone: "+editPhone+"Name: "+editfullName+"Email: "+editEmail+"ID: "+contactId);
  let tmp = {phoneNumber:editPhone,name:editfullName,id:contactId,email:editEmail};
  let jsonPayload = JSON.stringify( tmp );

  let url = urlBase + '/editContact.' + extension;

  let xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
  try
  {
      xhr.onreadystatechange = function()
      {
          if (this.readyState == 4 && this.status == 200)
          {
        // add success message here!
						togglePopup(0);
						doSearch();
          }
      };
      xhr.send(jsonPayload);
  }
  catch(err)
  {
  // Add error message here!
  }
}

function doDelete(id)
{
	let warning = window.confirm('Are you sure you want to delete this contact?');
    if(!warning){
        return;
		}
	//turns id into json object
	let tmp = {id:id};
	let jsonPayload = JSON.stringify( tmp );

	//access json php
	let url = urlBase + '/deleteContact.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{

				//document.getElementById("contactDeleteResult").innerHTML = "Contact has been deleted";
				// refresh page
				doSearch();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		//document.getElementById("contactDeleteResult").innerHTML = err.message;
	}
}



function doAdd()
{
	let addFirstName = document.getElementById("newName").value;
	let addLastName = document.getElementById("newLast").value;
	let addEmail = document.getElementById("newEmail").value;
	let addPhone = document.getElementById("newPhone").value;
	document.getElementById("AddResult").innerHTML = "";
	let fullName = addFirstName + " " + addLastName;

	let tmp = {phoneNumber:addPhone,name:fullName,userID:userId,email:addEmail};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/addContact.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				document.getElementById("AddResult").innerHTML = "Contact has been added";
				doSearch();
				//setTimeout(function(){window.location.href = "contact.html";}, 1000);
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("AddResult").innerHTML = err.message;
	}

}

function togglePopup(id){
	contactId = id;
  document.getElementById("popup-1").classList.toggle("active");
}
