<?php
/*
This file is used by admin.htm and called from the getPickupRequests.js file

It gets the booking requests that aren't assigned and are within 2 hours from the current date and 
outputs each booking request with unique string codes for the getPickupRequests.js to process
and store in a table on admin.htm
*/


// connect to database
require_once('../../conf/settings.php');
$connect = mysqli_connect($host,$user,$pswd,$dbnm) or die('Error - Failed to connect to server');

// if database cannot connect
if (!$connect) {
	echo "Error - Database connection failure"; // error message
} else {
	// DATABASE IS CONNECTED!

	// check if the table exists
	$table_exists = mysqli_query($connect,"SELECT * FROM taxi;");
	
	if (!$table_exists) {
		echo "Error - Database does not exist."; // create table if not exist, don't create one - there is no need as an admin
	} else { // if table exists!
		
		$todays_date = date("y/m/d"); // get todays date
		
		// get the date 2 hours from now
		if (date("a") == "pm"){ // if the date is past 12
			$two_hours_from_now = date("h")+14 . date(":i:s"); // add 14 hours to match the database 24hr time
			$current_time = date("h")+12 . date(":i:s"); // match the 24 hour time in the database
		} else {
			// AM time
			$two_hours_from_now = date("h:i:sa", strtotime('+2 hours')); // add two hours to current date
			$current_time = date("h:i:s");
			
			if (strpos($two_hours_from_now,"pm")==true){ // if in two hours from now it's PM time
				$two_hours_from_now = date("h")+2 . date(":i:s"); // make it 24hr time to match db
			}
		}

		//SELECT * FROM taxi WHERE (pickup_date = '2017-05-31' AND pickup_time <= '14:10:00' AND status = 'unassigned'); // debug mySQL online
		$pickup_query = "SELECT * FROM taxi WHERE (pickup_date = '$todays_date' AND pickup_time >= '$current_time' AND pickup_time <= '$two_hours_from_now' AND status = 'Unassigned');";
		//echo "Error - " . $pickup_query; // force javascript to cancel and print the query for debugging
		
		$results = mysqli_query($connect,$pickup_query);
		
		if ($results==true){ // if successful, get rows
			
			/* this is returned to javascript as one string
			# - variable in a booking request
			$ - new booking request
			*************/
			while($row = mysqli_fetch_array($results)){
				echo $row['booking_no'] . "#"; // separate each variable using # as the key
				echo $row['booking_time'] . "#";
				echo $row['name'] . "#";
				echo $row['phone'] . "#";
				echo $row['pickup_suburb'] . "#";
				echo $row['pickup_street_name'] . "#";
				echo $row['pickup_street_number'] . "#";
				echo $row['pickup_unit_number'] . "#";
				echo $row['pickup_date'] . "#";
				echo $row['pickup_time'] . "#";
				echo $row['dropoff_street_name'] . "#";
				echo $row['dropoff_street_number'] . "#";
				echo $row['booking_date'] . "#$"; // differentiate each record using $ as key
				// have to include the # otherwise it will skip the last variable ^^^
				
				// echo $row['status']; - don't show status cause only unassigned ones are queried
				
			}
		}
	}
}

// close the result and database connection		
mysqli_free_result($result);
mysqli_close($connect);
	
?>