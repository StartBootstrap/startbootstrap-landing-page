<?php
include 'config.php';
var_dump($_POST);
$user_id = $_POST['user_id'];
$sql = "SELECT * FROM video WHERE `user_id` = '$user_id'";
$result = $conn->query($sql);
echo $sql;
while($row = $result->fetch_assoc()) {
        echo "url: " . $row["url"]. "<br>";
    }
echo $url;
?>