<?php

/*
	this file processes the booking request by getting the inputs from the
	booking.htm (passed by sendData.js) to verify each input
	
	if the input is successful, connect to the database and store the data
	
	then output that the booking has been successful
	
	if any errors occurred, add a description of the error to a global variable
	which will then be shown on the booking.htm

*/


/******** this function checks the variable for NOTNULL or empty and also checks the var against its pattern
var - the variable to check
pattern - the pattern to check if the var is correctly
name - the name of the var (return where the error went wrong)
********/
function check_form_pattern($var, $pattern, $name){
	
	if ((isset($var)) && ($var != "")){ // check if variable is not NULL and not empty
	
		if (preg_match($pattern, $var)){ // check if variable matches the pattern param
			// echo $var; // debug
		} else {$GLOBALS['error_text'] .= "$name - invalid input <br>";}

	} else {$GLOBALS['error_text'] .= "$name - not filled out <br>";}
}

/**********
This function checks if the param is NOT NULL
***********/
function check_form($var, $name){

	if ((isset($var)) && ($var != "")){ // check if variable is not NULL and not empty
		// echo "successful"; // debug
	} else {$GLOBALS['error_text'] .= "$name - not filled out <br>";}
}


// initialize all variables from the booking.htm
$name = $_POST['name'];
$phone = $_POST['phone'];
$pickup_suburb = $_POST['pickup_suburb'];
$pickup_street_name = $_POST['pickup_street_name'];
$pickup_street_number = $_POST['pickup_street_number'];
$pickup_unit_number = $_POST['pickup_unit_number'];
$pickup_date = $_POST['pickup_date'];
$pickup_time = $_POST['pickup_time'];

$dropoff_suburb = $_POST['dropoff_suburb'];
$dropoff_street_name = $_POST['dropoff_street_name'];
$dropoff_street_number = $_POST['dropoff_street_number'];

$booking_date = date("d/m/y");
$booking_time = date("h:i:sa");

$error_text = ""; // GLOBAL: used to output what was not filled out correctly

// check each input variable and their patterns
check_form_pattern($name,'/^[A-Za-z ]*$/',"Name");
check_form_pattern($phone,'/^[0-9]/',"Phone");
// don't check suburb
check_form_pattern($pickup_street_name,'/^[A-Za-z ]*$/',"Pick up street name");
check_form_pattern($pickup_street_number, '/^[0-9]/',"Pick up street number");
// don't check unit number
check_form($pickup_date,"Date");
check_form($pickup_time,"Time");
// don't check suburb
check_form_pattern($dropoff_street_name,'/^[A-Za-z ]*$/',"Drop off street name");
check_form_pattern($dropoff_street_number, '/^[0-9]/',"Drop off street number");


// check if the pickup date is not before the current date
$month = substr($pickup_date,5,2);
$day = substr($pickup_date,8,2);

if ($month == date("m")){ // if the taxi is booked on the current month

	if ($day == date("d")){ // // if taxi is booked for today
	
		$hour = substr($pickup_time,0,2);
		$current_hour = date("h");
		
		// if either time is less than 12, add 12, as 1pm is larger than 12pm
		if ($current_hour < 12){$current_hour += 12;}
		if ($hour < 12){$hour += 12;}	

		if ($hour < $current_hour){
			$GLOBALS['error_text'] .= "The time you are wishing to book has already passed (change the HOUR)";
			
		} else if ($hour == $current_hour){ // check if booking is this current hour

			$minute = substr($pickup_time,3,2); // get the minute
			
			if ($minute <= date("i")){
				$GLOBALS['error_text'] .= "The time you are wishing to book has already passed (change the MINUTE)";
			} else {
				// successful time
			}
		} 
		
	} else if ($day < date("d")){ // if the day being booked has already passed
		$GLOBALS['error_text'] .= "The date you are wishing to book has already passed (change the DAY) ";
	}
}

// if an error has occurred, print out the what went wrong
if (!$error_text == ""){
	echo $error_text;
} 
else
{
	// if there wasn't any input errors
	
	require_once('../../conf/settings.php');
	$connect = mysqli_connect($host,$user,$pswd,$dbnm) or die('Failed to connect to server');

	// if database cannot connect
	if (!$connect) {
		echo "Database connection failure"; // error message
	} else {
		// DATABASE IS CONNECTED!

		// check if the table exists
		$table_exists = mysqli_query($connect,"SELECT * FROM taxi;");
		if (!$table_exists) {
			// create table if not exist
			echo "CREATE TABLE taxi (booking_no INT(4) PRIMARY KEY AUTO_INCREMENT, 
			name VARCHAR(40),
			phone VARCHAR(14),
			pickup_suburb VARCHAR(40),
			pickup_street_name VARCHAR(255),
			pickup_street_number VARCHAR(10),
			pickup_unit_number VARCHAR(20),
			pickup_date DATE,
			pickup_time TIME,
			dropoff_suburb VARCHAR(40),
			dropoff_street_name VARCHAR(255),
			dropoff_street_number VARCHAR(10),
			status VARCHAR(80),
			booking_date DATE,
			booking_time TIME
			);";
		} else { // if table already exists
			
			// ADD INFO INTO THE DATABASE
			$db_date = date("y/m/d"); // have to change the date format for the database
			
			$add_to_db = "INSERT INTO taxi(name,phone,pickup_suburb,pickup_street_name,pickup_street_number,pickup_unit_number,
			pickup_date,pickup_time,dropoff_suburb,dropoff_street_name,dropoff_street_number,status,booking_date,booking_time) 
			VALUES ('$name','$phone','$pickup_suburb','$pickup_street_name','$pickup_street_number','$pickup_unit_number','$pickup_date',
			'$pickup_time','$dropoff_suburb','$dropoff_street_name','$dropoff_street_number','Unassigned','$db_date','$booking_time');";
					
			$result = mysqli_query($connect,$add_to_db);
			if ($result == true){
				
				//$query = "SELECT booking_no FROM taxi WHERE name LIKE '%$name%';"; // don't query name cause there might be multiple copies
				$query = "SELECT MAX(booking_no) FROM taxi;"; // get the last booking_id number from the database
				$get_reference_no = mysqli_query($connect,$query);
				
				if ($get_reference_no == true){
				
					$query = mysqli_fetch_array($get_reference_no);
					$reference_no = $query[0]; //$query['booking_no'];
					
					$format_date = "$day-$month-2017"; // reformat the date for displaying
					
					echo "\nYou're booking has been successfully placed! 
					<br> Your reference number is: $reference_no
					<br> You will be picked up from: $pickup_street_number $pickup_street_name at $pickup_time on the $format_date.";
					
				}
				
			} else { // if query is unsuccessful
				echo "A database error has occurred, cannot book a taxi!";
			}

		}
	}
	
	// close the result and database connection		
	mysqli_free_result($result);
	mysqli_close($connect);
	
}

/* DEBUG VARIABLES
echo "\n name $name <p>";
echo "\n phone $phone <p>";
echo "\n pickup_suburb $pickup_suburb <p>";
echo "\n pickup_street_name $pickup_street_name <p>";
echo "\n pickup_street_number $pickup_street_number <p>";
echo "\n pickup_unit_number $pickup_unit_number <p>";
echo "\n pickup_date $pickup_date <p>";

echo "\n dropoff_suburb $dropoff_suburb <p>";
echo "\n dropoff_street_name $dropoff_street_name <p>";
echo "\n dropoff_street_number $dropoff_street_number <p>"; */

?>