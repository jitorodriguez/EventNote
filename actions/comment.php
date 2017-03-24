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

		if(isset($_GET['event_id']))
		{

			$event_id = $_GET['event_id'];

			$sql = "SELECT U.username, C.comment, DATE(C.dateCreated) AS date FROM users U, comments C WHERE C.e_id = " . $event_id . " AND U.s_id = C.s_id ORDER BY DATE(C.dateCreated) DESC";

			echo $sql;

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
			$event = $data->event_id;
			$comment = $data->comment;

			$sql = "INSERT INTO comments(s_id, e_id, comment) VALUES(" . $id . ", " . $event . ", '" . $comment . "');";

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
		else if($cmdtype === "update")
		{
			$id = $data->user_id;
			$event = $data->event_id;
			$comment = $data->comment;

			$sql = "UPDATE comments SET comment = '" . $comment . "' WHERE s_id = " . $id . " AND e_id = " . $event . ";";
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