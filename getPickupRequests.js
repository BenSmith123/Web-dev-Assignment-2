/*
This file is used by admin.htm to both assign a taxi and display the current taxi bookings
*/

// this function uses the getRequestsFromDB.php to get the booking requests and add them to a table
function getPickupRequests(dataSource, divID){

	if(xhr) { // the XHR obj is defined in the xhrHttpRequest.js file
		var obj = document.getElementById(divID); // create object for the html div

		xhr.open("POST", dataSource, true); // call the php file
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		xhr.onreadystatechange = function() {

			if (xhr.readyState == 4 && xhr.status == 200) { // successful
			
				if (xhr.responseText.includes("Error")){ // if a database or connection error has occurred
					//obj.innerHTML = xhr.responseText; 
					alert(xhr.responseText);// send the error message back to the admin.htm
				}	
				else if (xhr.responseText == ""){ // if there are no bookings
					//obj.innerHTML = "There are currently no bookings.";
					alert("There are currently no bookings.");
					
				} else { // bookings found!				
					createTable(xhr.responseText); // create the table based on the responseText from the PHP file
				}
			}
		}
	xhr.send();
	} 
}

// this function takes the input from admin.htm to assign a taxi by calling the assignTaxi.php
function assignTaxi(dataSource, divID, textInput){

	if(xhr) {
		var obj = document.getElementById(divID);
		
		var requestbody ="input="+encodeURIComponent(textInput);

		xhr.open("POST", dataSource, true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		xhr.onreadystatechange = function() {

			if (xhr.readyState == 4 && xhr.status == 200) { // successful
			
				obj.innerHTML = xhr.responseText; // send the results text back to the admin.htm

			}
		}
		 
	xhr.send(requestbody); // post the value from admin.htm to the assignTaxi.php
	} 
}

// this function creates a new cell to be added to the existing table
function addNewCell(name) {
	
	c1 = document.createElement("td");
	v1 = document.createTextNode(name);
	c1.appendChild(v1);
	newRow.appendChild(c1);
	
}

/*  this function takes each booking request (returned from the getRequestsFromDB.php)
	and separates each variable to be created into a table cell */
function extractData(body,requests){
	
	// $ is the key for a new booking request
	while(requests.includes("$")){ // for each booking request,
		
		record = requests.substring(0,requests.indexOf("$")+1); // get a single record from the requests

		newRow = document.createElement("tr"); // create new row for each record
		
		while(record.includes("#")){ // break each record variable up 
			addNewCell(record.substring(0,record.indexOf("#"))); // create a new text node for each variable
			record = record.substring(record.indexOf("#")+1,record.length); // remove the variable from the string
		}
		
		requests = requests.substring(requests.indexOf("$")+1,requests.length); // after the record is added, remove from requests
		
		body.appendChild(newRow); // add each entry to the table
		
	}
	
}

// this function creates a table based on the text of booking requests passed to it by the getRequestsFromDB.php
function createTable(dataString) {
	
	// if the table already exists, delete it and create a new one
	var theTable = document.getElementById("tbl");
	if (theTable.firstChild != null){
		var currentTable = theTable.childNodes[0];  
		theTable.removeChild(currentTable);
	}

	// create new table and body
	var theTable = document.getElementById("tbl");
	var tBody = document.createElement("TBODY");
	theTable.appendChild(tBody);
	
	newRow = document.createElement("tr"); // create new row for all titles

	// call function to add new cells for each row title
	addNewCell("Ref no.");
	addNewCell("Booking time");
	addNewCell("Name");
	addNewCell("Phone");
	addNewCell("Suburb");
	addNewCell("Street name");
	addNewCell("Street number");
	addNewCell("Unit number");
	addNewCell("Pick-up date"); 
	addNewCell("Pick-up time");
	addNewCell("Destination street");
	addNewCell("Destination number");
	addNewCell("Booking date");
	tBody.appendChild(newRow);
	
	extractData(tBody,dataString); // call the function that creates the table based on the dataString of booking requests
	
}