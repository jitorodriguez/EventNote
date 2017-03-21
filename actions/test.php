<?php

include_once "config.php";
// Create connection
$conn = new mysqli($host_name, $user_name, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT s_id FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["s_id"] . "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();
?>