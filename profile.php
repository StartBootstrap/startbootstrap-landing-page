<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pause</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <style>
        .main-body  {
            padding-top: 30px;
        }
        .ellipsis   {
            white-space: nowrap;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></
span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <a class="navbar-brand" href="#">Pause</a>
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            </ul>
                <!-- <button id="signout" class="btn btn-danger my-2 my-sm-0" type="submit">Sign Out</button> -->
                
                <form class="form-inline my-2 my-lg-0">
                <button class="btn btn-danger my-2 my-sm-0" type="submit" href="https://pause.trixster.xyz/">Sign Out</button>
                </form>
        </div>
    </nav>
    <div class="container main-body">
        <div class="jumbotron">
            <!-- Add username -->
            <?php
            include "database/config.php";
            $user_id = $_GET['username'];
            $sql = "SELECT * FROM user WHERE `user_id` = '$user_id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $userName = $row['name'];
            echo "<h2 id='username'>Welcome $userName,</h2>";
            ?>
        </div>
        <div class="jumbotron">
            <h4>Watching</h4>
            <table class="table table-striped ">
                <thead class="thead-dark">
                    <tr>

                        <th>Title</th>
                        <th>Progress</th>

                    </tr>
                </thead>
                <tbody>
                        <?php
                        include "database/config.php";
                        $user_id = $_GET['username'];
                        $sql = "SELECT * FROM video WHERE `user_id` = '$user_id'";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $video_progress = $row['video_progress'];
                            $url = $row['url'].'#t='.$row['video_progress'];
                            $title = $row['video_title'];
                            echo "<tr>";
                            echo "<td><a href=$url class='video_name' target=\"_blank\">$title</a></td>";
                            echo "<td>" . gmdate("H:i:s", (int)$video_progress). "/" . gmdate("H:i:s", (int)$row["total_duration"])."</td>";
                            echo "</tr>";
                        }
                        ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- UI Manip Scripts -->
    <script>
        console.log(localStorage.user_id);
        var user_id = localStorage.user_id;
        $.ajax({
            type: "POST",
            url: "database/getData.php",
            data: {
                'user_id': user_id
            },
            success: function(data) {
                console.log(data);
            }
        });
        $('#viewVideo').click(function() {
            alert();
        });
    </script>
    <script>
    $( document ).ready(function() {
        // var omdbData = document.getElementsByClassName('video_name');
        var omdbData = $(".video_name");
        // console.log(omdbData[0].innerHTML);
        testVar = 0
        setTimeout(function(){
        for (var iterator = 0; iterator < omdbData.length; iterator++) {
            // console.log(omdbData[i].innerHTML);
            var toSendTitle = escape(omdbData[iterator].innerHTML);
            console.log(toSendTitle);
            var url = "https://www.omdbapi.com?t="+toSendTitle+"&apiKey=thewdb";
            console.log(url)
            $.ajax({
                type: 'GET',
                url: url,
                error: function() {
                    console.log('')
                },
                success: function(data) {
                    if (data.Plot != 'undefined')
                        omdbData[testVar].setAttribute('title', data.Plot);
                    testVar += 1;
                },
                timeout: 3000
            });
        }
    });
    });
    </script>
    <script>
        function extractHostname(url) {
            var hostname;
            if (url.indexOf("://") > -1) {
                hostname = url.split('/')[2];
            }
            else {
                hostname = url.split('/')[0];
            }
            hostname = hostname.split(':')[0];
            hostname = hostname.split('?')[0];
            return hostname;
        }

        $(".open-link").click(function(e){
            window.open(e.currentTarget.dataset.link);
        });
    </script>
</body>
</html>