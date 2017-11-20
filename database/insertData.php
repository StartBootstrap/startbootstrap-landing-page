<?php
include 'config.php';
var_dump($_POST);

$user_id = $_POST['user_id'];
$video_id = $_POST['video_id'];
$url = $_POST['url'];
#$url = substr($url, 0, strpos($url, "#t="));
$video_progress = $_POST['video_progress'];
$total_duration = $_POST['total_duration'];
$last_viewed = $date = date('Y-m-d H:i:s');

// make the entry for the user if it doesn't exist

$sql = "INSERT INTO `user`(`user_id`, `email`, `name`, `last_login`) VALUES ('$user_id', 'swarnims@bu.edu', 'Swarnim', '$last_viewed')
ON DUPLICATE KEY UPDATE `last_login` = '$last_viewed'";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    echo $sql;
}

$sql = "INSERT INTO `video`(`video_id`, `user_id`, `url`, `video_progress`, `total_duration`, `last_viewed`) VALUES ('0', '$user_id', '$url', '$video_progress', '$total_duration', '$last_viewed')
ON DUPLICATE KEY UPDATE `video_progress` = '$video_progress', `last_viewed` = '$last_viewed'";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    echo $sql;
}
$conn->close();
?>