<?php

	header("content-type:application/json");
    include_once "config.php";

	$data = json_decode(file_get_contents("php://input"));

	$conn = new mysqli($host_name, $user_name, $password, $database);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	if($_SERVER['REQUEST_METHOD'] === 'GET'){
		

	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$cmdtype = $data->type;

		//CREATE SCENARIO
		if($cmdtype === "create")
		{
			$id = $data->user_id;
			$event = $data->event_info;
			$location = $data->location_info;

			$sql = "START TRANSACTION;

			INSERT INTO location(latitude, longitude, specificName) VALUES(" . $location->latitude . ", " . $location->longitude . ", '" . $location->name . "');

			SET @locationKey = LAST_INSERT_ID();

			INSERT INTO eventmeeting (location_id, s_id, event_type, name, date, start_time, end_time, description, phone_num, email) VALUES (@locationKey, " . $id . ", " . $event->event_type . ", '" . $event->name . "', '" . $event->date . "', '" . $event->start_time . "', '" . $event->end_time . "', '" . $event->description . "', " . $event->phone_num . ", '" . $event->email . "');

			COMMIT;";

			$results = mysqli_multi_query($conn, $sql);

			if($results)
			{
				echo "Successfully performed Event Creation transaction.";
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully peformed Event Creation transaction.";
				$response_array['status'] = "failure event creation";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
		else if($cmdtype === "delete")
		{
			$id = $data->user_id;
			$eventid = $data->event_id;

			$sql = "DELETE FROM eventmeeting WHERE e_id = " . $eventid . " AND s_id = " . $id . ";";

			$results = $conn->query($sql);

			if($results)
			{
				echo "Success deleting event.";
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully deleted event.";
				$response_array['status'] = "failure deleting event";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
	}

?>