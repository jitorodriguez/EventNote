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
		
	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$cmdtype = $data->type;

		//CREATE SCENARIO
		if($cmdtype === "create")
		{
			$id = $data->user_id;
			$rso = $data->rso_info;

			$sql = "INSERT INTO rso(s_id, name, description) VALUES(" . $id . ", '" . $rso->name . "', '" . $rso->description . "');";

			$results = $conn->query($sql);

			if($results)
			{
				echo "Success creating rso.";
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully created rso.";
				$response_array['status'] = "failure creating rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		
		}
		else if($cmdtype === "join")
		{
			$id = $data->user_id;
			$rso = $data->rso_id;

			$sql = "INSERT INTO joinrso(s_id, rso_id) VALUES(" . $id . ", " . $rso . ");";

			$results = $conn->query($sql);

			if($results)
			{
				echo "Success joining rso.";
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				echo "Failure joining rso.";
				$response_array['status'] = "failure joining rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
		else if($cmdtype === "delete")
		{
			$id = $data->user_id;
			$rso = $data->rso_id;

			$sql = "START TRANSACTION;

			DELETE FROM rso WHERE e_id = " . $rso . " AND s_id = " . $id . ";

			DELETE FROM admin WHERE s_id = " . $id . " AND NOT EXISTS(SELECT J.rso_id FROM joinrso J WHERE (SELECT COUNT(S.s_id) FROM joinrso S, rso R WHERE S.rso_id = J.rso_id AND J.rso_id = R.rso_id AND  R.s_id = " . $id . ") > 5;

			COMMIT;";

			$results = mysqli_multi_query($conn, $sql);

			if($results)
			{
				echo "Success deleting rso.";
				$response_array['status'] = "successfully deleted rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
			else
			{
				echo "Unsuccessfully deleted rso.";
				$response_array['status'] = "failure deleting rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}

	}

?>