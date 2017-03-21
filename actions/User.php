<?php
    include_once "config.php";

    $id = "2";
// Create connection
$conn = new mysqli($host_name, $user_name, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT U.username, U.name, U.email  FROM users U WHERE U.s_id = " . $id;
$result = $conn->query($sql);

$rows = array();

if ($result->num_rows > 0) {
    // output data of each row
    while($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }
    echo json_encode($rows);
} else {
    echo "0 results";
}
$conn->close();
?>