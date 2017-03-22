<?php

	header("content-type:application/json");
    include_once "config.php";

	$data = json_decode(file_get_contents("php://input"));

	$conn = new mysqli($host_name, $user_name, $password, $database);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	if($_SERVER['REQUEST_METHOD'] === 'GET')
	{
		//$id = ( isset( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ) ? intval( $_GET['user_id'] ) : -1;

		if( isset( $_GET['uni']) && isset( $_GET['user_id']) )
		{
			//Univeristy ID passed in with student id, find all private and public events possible from university
			$u_id = $_GET['user_id'];
			$uni = $_GET['uni'];

			$sql = "(SELECT DISTINCT E.e_id, E.location_id, E.event_type, E.name, E.date, E.start_time, E.end_time, E.description, E.phone_num, E.email, L.latitude, L.longitude, L.specificName FROM eventmeeting E, location L WHERE approved_by_superadmin = 1 AND event_type = 0 AND E.location_id = L.location_id AND E.uni_id = " . $uni . ") UNION (SELECT DISTINCT E1.e_id, E1.location_id, E1.event_type, E1.name, E1.date, E1.start_time, E1.end_time, E1.description, E1.phone_num, E1.email, L1.latitude, L1.longitude, L1.specificName FROM eventmeeting E1, location L1 WHERE approved_by_superadmin = 1 AND event_type = 1 AND E1.location_id = L1.location_id AND E1.uni_id IN (SELECT S.uni_id FROM student S WHERE S.s_id = " . $u_id . " AND S.uni_id = " . $uni . "))";

			$results = $conn->query($sql);

			$rows = array();

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
				}
				print json_encode($rows);
			} 
			else 
			{
				echo $conn->error;
				print json_encode("{}");
			}
		}
		else if(isset( $_GET['user_id']) && isset( $_GET['private']))
		{
			//student id only, find all private and public events possible
			$u_id = $_GET['user_id'];

			$sql = "SELECT DISTINCT E1.e_id, E1.location_id, E1.event_type, E1.name, E1.date, E1.start_time, E1.end_time, E1.description, E1.phone_num, E1.email, L1.latitude, L1.longitude, L1.specificName FROM eventmeeting E1, location L1 WHERE approved_by_superadmin = 1 AND event_type = 1 AND E1.location_id = L1.location_id AND E1.uni_id IN (SELECT S.uni_id FROM student S WHERE S.s_id = " . $u_id . ")";

			$results = $conn->query($sql);

			$rows = array();

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
				}
				print json_encode($rows);
			} 
			else 
			{
				echo $conn->error;
				print json_encode("{}");
			}
		}
		else if(isset( $_GET['user_id']))
		{
			//student id only, find all private and public events possible
			$u_id = $_GET['user_id'];

			$sql = "(SELECT DISTINCT E.e_id, E.location_id, E.event_type, E.name, E.date, E.start_time, E.end_time, E.description, E.phone_num, E.email, L.latitude, L.longitude, L.specificName FROM eventmeeting E, location L WHERE approved_by_superadmin = 1 AND event_type = 0 AND E.location_id = L.location_id) UNION (SELECT DISTINCT E1.e_id, E1.location_id, E1.event_type, E1.name, E1.date, E1.start_time, E1.end_time, E1.description, E1.phone_num, E1.email, L1.latitude, L1.longitude, L1.specificName FROM eventmeeting E1, location L1 WHERE approved_by_superadmin = 1 AND event_type = 1 AND E1.location_id = L1.location_id AND E1.uni_id IN (SELECT S.uni_id FROM student S WHERE S.s_id = " . $u_id . "))";

			$results = $conn->query($sql);

			$rows = array();

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
				}
				print json_encode($rows);
			} 
			else 
			{
				echo $conn->error;
				print json_encode("{}");
			}
		}
		else
		{
			//Nothing passed in, get public events
			$sql = "SELECT DISTINCT E.e_id, E.location_id, E.event_type, E.name, E.date, E.start_time, E.end_time, E.description, E.phone_num, E.email, L.latitude, L.longitude, L.specificName FROM eventmeeting E, location L WHERE approved_by_superadmin = 1 AND event_type = 0 AND E.location_id = L.location_id";

			$results = $conn->query($sql);

			$rows = array();

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
				}
				print json_encode($rows);
			} 
			else 
			{
				echo $conn->error;
				print json_encode("{}");
			}
		}
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
		else if($cmdtype === "update")
		{
			$id = $data->user_id;
			$event = $data->event_info;
			$location = $data->location_info;

			$sql = "START TRANSACTION;

			UPDATE eventmeeting SET name='" . $event->name . "', date='" . $event->date . "', start_time='" . $event->start_time . "', end_time='" . $event->end_time . "', description='" . $event->description . "', phone_num=" . $event->phone_num . ", email='" . $event->email . "' WHERE e_id=" . $event->e_id . " AND s_id = " . $id . ";

			SET @locationKey = (SELECT location_id FROM eventmeeting WHERE e_id =" . $event->e_id . " AND s_id =" . $id . ");

			UPDATE location SET latitude=" . $location->latitude . ", longitude=" . $location->longitude . ", specificName='" . $location->name . "' WHERE location_id=@locationKey;

			COMMIT;";

			$results = mysqli_multi_query($conn, $sql);

			if($results)
			{
				echo "Success updating event.";
				$response_array['status'] = "success";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully updated event.";
				$response_array['status'] = "failure updating event";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
	}

?>