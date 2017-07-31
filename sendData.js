/* 	This javascript file is used by the booking.htm to send input data
	to the PHP file specified (dataSource)


	PARAMETER VALUE IN THIS ORDER
	namefield.value
	phone.value
	pickup_suburb.value
	pickup_street_name.value
	pickup_street_number.value
	pickup_unit_number.value
	pickup_date.value
	pickup_time.value
	dropoff_suburb.value
	dropoff_street_name.value
	dropoff_street_number.value */
function getData(dataSource, divID, val1, val2, val3, val4, val5, val6, val7, val8, val9, val10, val11) {
	
	if(xhr) {
		var obj = document.getElementById(divID);
		
		// get each value from the input fields for the PHP to then process
		var requestbody ="name="+encodeURIComponent(val1)
		+"&phone="+encodeURIComponent(val2)
		+"&pickup_suburb="+encodeURIComponent(val3)
		+"&pickup_street_name="+encodeURIComponent(val4)
		+"&pickup_street_number="+encodeURIComponent(val5)
		+"&pickup_unit_number="+encodeURIComponent(val6)
		+"&pickup_date="+encodeURIComponent(val7)
		+"&dropoff_suburb="+encodeURIComponent(val8)
		+"&dropoff_street_name="+encodeURIComponent(val9)
		+"&dropoff_street_number="+encodeURIComponent(val10)
		+"&pickup_time="+encodeURIComponent(val11);
		
		xhr.open("POST", dataSource, true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		xhr.onreadystatechange = function() {
			// alert(xhr.readyState); // debug
			if (xhr.readyState == 4 && xhr.status == 200) { // success!
				obj.innerHTML = xhr.responseText; // send the text returned from the PHP file to the booking.htm
			}
			
		}
		 
	xhr.send(requestbody); // send the input fields to php
		
	}
}




