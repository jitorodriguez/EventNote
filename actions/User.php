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

	$sql = "SELECT U.username, U.name, U.email  FROM users U WHERE U.s_id = " . $id;
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

    $sql = "UPDATE users SET username=" . $username . ", email=" . $email . "WHERE s_id=" . $id;
	if($conn->query($sql) === TRUE){
		print "Success";
	}
	else
	{
		print "Failure";
	}
	
}
$conn->close();
?>