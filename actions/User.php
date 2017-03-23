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

		$sql = "SELECT U.username, U.name, U.email FROM users U WHERE U.s_id = " . $id;
		$result = $conn->query($sql);

		$rows = array();

		if ($result->num_rows > 0) {
		    // output data of each row
		    while($r = $result->fetch_assoc()) {
		        $rows[] = $r;
		    }
		    print json_encode($rows);
		} else {
		    print json_encode("{}");
		}
	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST')
	{

	    $id = $data->user_id;
	    $username = $data->username;
	    $email = $data->email;
	    $name = $data->name;
	    $pass = $data->password;

	    $sql = "UPDATE users SET username='" . $username . "', email='" . $email . "', name='" . $name . "', password ='" . $pass . "' WHERE s_id =" . $id;

	    /*
	    if(isset($data['password']))
	    {
	    	$pass = $data->password;
	    	$sql .= ", password ='" . $pass . "' WHERE s_id =" . $id;
	    }
	    else
	    {
	    	$sql .= " WHERE s_id =" . $id;
	    }
	    */

		if($conn->query($sql) === TRUE){

			$response_array['status'] = "success";
			$response_array['message'] = "";
			print json_encode($response_array);
		}
		else{

			$response_array['status'] = "failure";
			$response_array['message'] = $conn->error;
			print json_encode($response_array);
		}
	}
	
	$conn->close();
?>