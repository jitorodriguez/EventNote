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

		if(isset($_GET['user_id']))
		{

			$user_id = $_GET['user_id'];

			$sql = "SELECT * FROM university";

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
			$name = $data->name;
			$address = $data->address;
			$emailType = $data->email;

			$sql = "START TRANSACTION;

			SET @name = (SELECT U.name FROM users U WHERE U.s_id = " . $id . "); 

			INSERT INTO university(s_id, name, address, emailtype, creator) VALUES(" . $id . ", '" . $name . "', '" . $address . "', '" . $emailType . "', @name);

			COMMIT;";

			echo

			$results = mysqli_multi_query($conn, $sql);

			if($results)
			{
				echo "Success creating university.";
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully created university.";
				$response_array['status'] = "failure creating university";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		
		}
		else if($cmdtype === "update")
		{
			$id = $data->uni_id;
			$name = $data->name;
			$address = $data->address;
			$emailType = $data->email;

			$sql = "UPDATE university SET name = '" . $name . "', address = '" . $address . "' WHERE uni_id = " . $id . ";";

			$results = $conn->query($sql);

			if($results)
			{
				echo "Success updated university.";
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully updated university.";
				$response_array['status'] = "failure updated university";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
		else if($cmdtype === "delete")
		{
			$id = $data->user_id;
			$event = $data->event_id;

			$sql = "DELETE FROM comments WHERE s_id = " . $id . " AND e_id = " . $event;

			$results = $conn->query($sql);

			if($results)
			{
				echo "Success creating comment.";
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully created comment.";
				$response_array['status'] = "failure creating comment";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
	}

?>