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

		if(isset( $_GET['user']) && isset( $_GET['pass']))
		{
			$user = $_GET['user'];
			$pass = $_GET['pass'];

			$sql = "SELECT U.s_id FROM users U WHERE U.username = '" . $user . "' AND U.password = '" . $pass . "';"; 

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
		else
		{
			$id = $_GET['user_id'];

			$sql = "SELECT U.username, U.name, U.email, S.major, S.minor, S.description FROM users U, student S WHERE U.s_id = " . $id . " AND U.s_id = S.s_id";

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
		
	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST')
	{
	    $id = $data->user_id;
	    $username = $data->username;
	    $email = $data->email;
	    $name = $data->name;
	    $pass = $data->password;
	    $major = $data->major;
	    $minor = $data->minor;
	    $description = $data->description;

	    $sql = "UPDATE users U, student S SET U.username='" . $username . "', U.email='" . $email . "', U.name='" . $name . "', U.password ='" . $pass . "', S.major = '" . $major . "', S.minor ='" . $minor . "', S.description = '" . $description . "' WHERE U.s_id =" . $id . " AND S.s_id = U.s_id;";

	    echo $sql;

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