<?php
	header("content-type:application/json");
    include_once "config.php";

	$data = json_decode(file_get_contents("php://input"));
	
// Create connection
	$conn = new mysqli($host_name, $user_name, $password, $database);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	if($_SERVER['REQUEST_METHOD'] === 'GET'){
		//The request is using a GET method
		
		$id = $_GET['user_id'];

		$sql = "DELETE FROM users WHERE s_id = " . $id;
		$result = $conn->query($sql);

		if($results)
		{
			$response_array['status'] = "success deleted user";
			$response_array['message'] = "";
			print json_encode($response_array);
		}
		else
		{
			$response_array['status'] = "failure deleting user";
			$response_array['message'] = $conn->error;
			print json_encode($response_array);
		}
	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST')
	{
	    $username = $data->username;
	    $email = $data->email;
	    $name = $data->name;
	    $password = $data->password;

	    $sql = "INSERT INTO users (username, email, name, password) VALUES ('" . $username . "', '" . $email . "', '" . $name . "', '" . $password . "');";
		if($conn->query($sql) === TRUE){

			$response_array['status'] = "successfully created user";
			$response_array['message'] = "";
			$sql = "INSERT INTO `student`(`s_id`, `uni_id`) VALUES (" . $conn->insert_id . ",1)";
			echo $sql;

			//INSERT INTO `student`(`s_id`, `uni_id`) VALUES (19,1)
			//INSERT INTO 'student'('s_id', 'uni_id') VALUES (23,1)
			//$sql = "INSERT INTO 'student'('s_id', 'uni_id') VALUES (" . $conn->insert_id . ",1)";

			if($conn->query($sql) === TRUE)
			{
				print json_encode($response_array);
			}
			else
			{
				$response_array['status'] = "Failed on transfering User to Student.";
				$response_array['message'] = $conn->error;
				print json_encode($response_array);
			}
		}
		else{

			$response_array['status'] = "failure creating user";
			$response_array['message'] = $conn->error;
			print json_encode($response_array);
		}
	}
	
	$conn->close();
?>