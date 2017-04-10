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

		if(isset($_GET['user_id']) && isset($_GET['false']))
		{
			// retrieve all events not approved
			$user_id = $_GET['user_id'];

			$sql = "SELECT * FROM eventmeeting WHERE approved_by_superadmin = 0";

			$results = $conn->query($sql);

			$rows = array();

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
					//$rows[$r->username] = "{comment: '" . $r->comment . "', date: '" . $r->date . "'}";
				}
				print json_encode($rows);
			} 
			else 
			{
				print json_encode("{}");
			}
		}
		else if(isset($_GET['user_id']))
		{
			//Get both approved and non approved events
			$user_id = $_GET['user_id'];

			$sql = "SELECT * FROM eventmeeting";

			$results = $conn->query($sql);

			$rows = array();

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
					//$rows[$r->username] = "{comment: '" . $r->comment . "', date: '" . $r->date . "'}";
				}
				print json_encode($rows);
			} 
			else 
			{
				print json_encode("{}");
			}
		}
	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST')
	{

		$cmdtype = $data->type;

		//CREATE SCENARIO
		if($cmdtype === "update")
		{
			$id = $data->user_id;
			$event_id = $data->event_id;
			$approval = $data->status;

			$sql = "UPDATE eventmeeting SET approved_by_superadmin = " . $approval . " WHERE e_id = " . $event_id . ";";

			$results = $conn->query($sql);

			if($results)
			{
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				$response_array['status'] = "failure updating event status";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		
		}
	}
?>