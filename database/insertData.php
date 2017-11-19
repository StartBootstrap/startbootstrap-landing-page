<?php
include 'config.php';
var_dump($_POST);

$user_id = $_POST['user_id'];
$video_id = $_POST['video_id'];
$url = $_POST['url'];
$video_progress = $_POST['video_progress'];
$total_duration = $_POST['total_duration'];
$last_viewed = $date = date('Y-m-d H:i:s');

// make the entry for the user if it doesn't exist
$sql1 = "SELECT * FROM `user` WHERE `user_id` = '$user_id'";
$result1 = $conn->query($sql1);
echo $sql1;
if ($result1->num_rows == 0) {
    $sql = "INSERT INTO `user`(`user_id`, `email`, `name`, `last_login`) VALUES ('$user_id', 'swarnims@bu.edu', 'Swarnim', '$last_viewed')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        echo $sql;
    }
}

$sql = "INSERT INTO `video`(`video_id`, `user_id`, `url`, `video_progress`, `total_duration`, `last_viewed`) VALUES ('0', '$user_id', '$url', '$video_progress', '$total_duration', '$last_viewed')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    echo $sql;
}
$conn->close();
?>