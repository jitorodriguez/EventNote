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
		if(isset($_GET['rso_id']) && isset($_GET['user_id']))
		{
			$id = $_GET['user_id'];

			$sql = "SELECT A.rso_id, R.name FROM admin A, rso R WHERE A.s_id = " . $id . " AND A.rso_id = R.rso_id";

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
		else if(isset($_GET['user_id']) && isset($_GET['events']))
		{
			$id = $_GET['user_id'];

			$sql = "SELECT DISTINCT E.e_id, E.location_id, E.s_id, E.event_type, E.name, E.start_time, E.end_time, E.description, E.phone_num, E.email, L.latitude, L.longitude, L.specificName, R.name AS rso_name, U.name AS creatorName, R.rso_id FROM eventmeeting E, location L, rso R, users U WHERE E.rso_id IN (SELECT rso_id FROM joinrso WHERE s_id = " . $id . ") AND E.location_id = L.location_id AND E.rso_id = R.rso_id AND E.s_id = U.s_id";

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
		else if(isset($_GET['user_id']) && isset($_GET['rso'])) //GET ALL RSO FOR USER TO BROWSE
		{
			$id = $_GET['user_id'];

			$sql = "SELECT R.name, R.description, R.rso_id, R.s_id, U.name AS creatorName, (CASE WHEN EXISTS(SELECT NULL FROM joinrso J WHERE J.rso_id = R.rso_id AND J.s_id = " . $id . ") THEN 1 ELSE 0 END) AS isPartOf FROM rso R, student S, student S1, users U WHERE S.s_id =" . $id . " AND S1.s_id = R.s_id AND S.uni_id = S1.uni_id AND S1.s_id = U.s_id";

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
		else if(isset($_GET['rso_id']))
		{
			$rid = $_GET['rso_id'];

			$sql = "SELECT U.name AS owner, R.name, R.description, S.name AS university FROM users U, rso R, university S, student T WHERE R.rso_id = " . $rid . " AND U.s_id = R.s_id AND U.s_id = T.s_id AND T.uni_id = S.uni_id;";

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
		else if(isset($_GET['user_id']))
		{
			$id = $_GET['user_id'];
			//GET ALL RSO GROUPS AND RSO EVENTS USER CAN INTERACT WITH

			$sql = "SELECT DISTINCT E.e_id, E.location_id, E.s_id, E.event_type, E.name, E.start_time, E.end_time, E.description, E.phone_num, E.email, L.latitude, L.longitude, L.specificName, R.name AS rso_name, U.name AS creatorName, R.rso_id FROM eventmeeting E, location L, rso R, users U WHERE E.rso_id IN (SELECT rso_id FROM joinrso WHERE s_id = " . $id . ") AND E.location_id = L.location_id AND E.rso_id = R.rso_id AND E.s_id = U.s_id";

			$results = $conn->query($sql);

			$rows = array();

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
				}
			}

			$sql = "SELECT R.name, R.description, R.rso_id, R.s_id, U.name AS creatorName, (CASE WHEN EXISTS(SELECT NULL FROM joinrso J WHERE J.rso_id = R.rso_id AND J.s_id = " . $id . ") THEN 1 ELSE 0 END) AS isPartOf FROM rso R, student S, student S1, users U WHERE S.s_id =" . $id . " AND S1.s_id = R.s_id AND S.uni_id = S1.uni_id AND S1.s_id = U.s_id";
			//$sql = "SELECT R.name, R.description, R.rso_id, R.s_id, U.name AS creatorName FROM rso R, student S, student S1, users U WHERE S.s_id =" . $id . " AND S1.s_id = R.s_id AND S.uni_id = S1.uni_id AND S1.s_id = U.s_id";

			$results = $conn->query($sql);

			if ($results->num_rows > 0) 
			{
				// output data of each row
				while($r = $results->fetch_assoc()) {
					$rows[] = $r;
				}
			}
			print json_encode($rows);
		}
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
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
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
				$response_array['status'] = "success";
				$response_array['message'] = "";

				$sql = "START TRANSACTION;

				SET @r = (SELECT DISTINCT A.rso_id FROM joinrso A WHERE A.rso_id = " . $rso . " AND (SELECT COUNT(B.s_id) FROM joinrso B WHERE B.rso_id = " . $rso . ") > 4);

				SET @user = (SELECT s_id FROM rso WHERE rso_id = " . $rso . ");

				INSERT INTO admin (rso_id, s_id) VALUES (@r, @user);

				COMMIT;";

				$results = mysqli_multi_query($conn, $sql);

				print json_encode($response_array);
			}
			else
			{
				$response_array['status'] = "failure joining rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
		else if($cmdtype === "leave")
		{
			$id = $data->user_id;
			$rso = $data->rso_id;

			$sql = "DELETE FROM joinrso WHERE s_id = " . $id . " AND rso_id = " . $rso . ";";

			$results = $conn->query($sql);

			if($results)
			{
				$response_array['status'] = "success";
				$response_array['message'] = "";

				$sql = "START TRANSACTION;

				SET @r = (SELECT DISTINCT A.rso_id FROM joinrso A WHERE A.rso_id = " . $rso . " AND (SELECT COUNT(B.s_id) FROM joinrso B WHERE B.rso_id = " . $rso . ") < 5);

				SET @user = (SELECT s_id FROM rso WHERE rso_id = " . $rso . ");

				DELETE FROM admin WHERE rso_id = @r;

				COMMIT;";

				$results = mysqli_multi_query($conn, $sql);

				print json_encode($response_array);
			}
			else
			{
				$response_array['status'] = "failure leaving rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
		else if($cmdtype === "delete")
		{
			$id = $data->user_id;
			$rso = $data->rso_id;

			$sql = "START TRANSACTION;

			DELETE FROM rso WHERE rso_id = " . $rso . " AND s_id = " . $id . ";

			DELETE FROM admin WHERE s_id = " . $id . " AND NOT EXISTS(SELECT J.rso_id FROM joinrso J WHERE (SELECT COUNT(S.s_id) FROM joinrso S, rso R WHERE S.rso_id = J.rso_id AND J.rso_id = R.rso_id AND  R.s_id = " . $id . " > 4));

			COMMIT;";

			$results = mysqli_multi_query($conn, $sql);

			if($results)
			{
				$response_array['status'] = "success";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
			else
			{
				$response_array['status'] = "failure deleting rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
		else if($cmdtype === "update")
		{
			$id = $data->user_id;
			$rso = $data->rso_info;

			$sql = "UPDATE rso SET name = '" . $rso->name . "', description = '" . $rso->description . "' WHERE rso_id = " . $rso->rso_id . " AND s_id = " . $id . ";";

			$results = $conn->query($sql);

			if($results)
			{
				$response_array['status'] = "success";
				$response_array['message'] = "";
				print json_encode($response_array);
			}
			else
			{
				$response_array['status'] = "failure changing rso";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}

	}

?>