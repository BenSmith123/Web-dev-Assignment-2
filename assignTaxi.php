<?php
/*
This file is used by the admin.htm and called by the getPickupRequests.js file
It takes the input from admin.htm and querys the database with it, if successful,
the booking request will be Assigned and then a confirmation message will be displayed on the admin.htm
*/
$input = $_POST['input']; // get the input from the assign taxi button

require_once('../../conf/settings.php');
$connect = mysqli_connect($host,$user,$pswd,$dbnm) or die('Failed to connect to server');

if ($input != ""){
	
	if (!$connect) { // if database cannot connect
		echo "Database connection failure"; // error message
	} else {
		
		// DATABASE IS CONNECTED!

		// check if the table exists
		$table_exists = mysqli_query($connect,"SELECT * FROM taxi;");
		if (!$table_exists) {
			// don't create table if it doesn't exist - no need as an admin.
		} else {
			
			// if table exists
			$query = "SELECT * from taxi WHERE (booking_no = '$input');"; // select the request with the input entered
			$result = mysqli_query($connect,$query);
			
			if ($result == true){ // query successful
				
				$query = mysqli_fetch_array($result);
				$status = $query['status']; // set the result to the status of the record returned
					
				if ($status==""){ // if the status is empty, no record was found
					echo "There are no reference numbers that match '$input'."; // no reference number matches
					
				} else if ($status == 'Assigned'){
					
					echo "This booking request has already been assigned."; // already assigned
					
				} else { // booking request found and has not been assigned
					
					$query = "UPDATE taxi SET status='Assigned' WHERE (booking_no='$input');";
					$result = mysqli_query($connect,$query);
					
					if ($result == true){ // if the assignment was successful
						echo "Booking request $input has been successfully assigned!";
						
					} else { // if the assigning is somehow unsuccessful
						echo "A database error occurred, this record remains Unassigned";
					}

				}
				
			} else {
				echo "A database error has occurred, cannot assign taxi!";
			}
			
		}	
			
	// close the result and database connection		
	mysqli_free_result($result);
	mysqli_close($connect);
		
	}

}

?>