<?php
global $conn;
session_start();

if (!isset($_SESSION["username"])) {
    // Redirect to the login page or any other page
    header("Location: login.php");
    exit(); // Ensure that code execution stops here to prevent further execution of unauthorized code
}

// Include the database connection file
require_once 'Db.php';


$username = $_SESSION["username"];
// Create a parameterized query to avoid SQL injection
$stmt = $conn->prepare("SELECT studentImg FROM Student WHERE studentUsername = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$resultImg = $stmt->get_result();

if ($resultImg->num_rows > 0) {
    $resultImgRow = $resultImg->fetch_assoc();
    $studentImg = $resultImgRow['studentImg']; // Get the student image
}
$stmt->close(); // Close the statement


$query1 = "
SELECT 
    hb.helpPostContent, 
    hb.helpPostDate, 
    s.studentName AS studentName,
    hb.helpPostStudentResp
FROM 
    HelpBoard hb
INNER JOIN 
    Student s ON hb.helpPostStudent = s.studentId
ORDER BY 
    hb.helpPostDate DESC
LIMIT 
    5;
";

$result1 = mysqli_query($conn, $query1);

$helpPosts = array();
while ($row = mysqli_fetch_assoc($result1)) {
    $helpPost = array(
        'helpPostContent' => $row['helpPostContent'],
        'helpPostDate' => $row['helpPostDate'],
        'studentName' => $row['studentName'],
        'helpPostStudentResp' => $row['helpPostStudentResp']
    );
    $helpPosts[] = $helpPost;
}

// Retrieve the 5 latest post contents from the taskboard table
$query2 = "
SELECT 
    tb.taskPostContent, 
    tb.taskPostDate, 
    t.taskObjective AS taskName, 
    s.studentName AS studentName
FROM 
    TaskBoard tb
INNER JOIN 
    Task t ON tb.taskId = t.taskId
INNER JOIN 
    Student s ON tb.taskStudent = s.studentId
ORDER BY 
    tb.taskPostDate DESC
LIMIT 
    5;
";

$result2 = mysqli_query($conn, $query2);

$taskPosts = array();
while ($row = mysqli_fetch_assoc($result2)) {
    $taskPosts = array(
        'taskPostContent' => $row['taskPostContent'],
        'taskPostDate' => $row['taskPostDate'],
        'taskName' => $row['taskName'],
        'studentName' => $row['studentName']
    );
    $taskPosts[] = $taskPosts;
}

$currentDayOfWeek = date("w")-1;

// Get the student ID associated with the logged-in user
$username = $_SESSION["username"];
$queryStudentId = "SELECT studentId FROM Student WHERE studentUsername = '$username'";
$resultStudentId = mysqli_query($conn, $queryStudentId);
$rowStudentId = mysqli_fetch_assoc($resultStudentId);
$studentId = $rowStudentId['studentId'];

// Query to check if the student has made any posts on Monday and Tuesday of the current week
$queryPosts = "SELECT DISTINCT DATE_FORMAT(taskPostDate, '%w') AS dayOfWeek 
               FROM TaskBoard 
               WHERE taskStudent = $studentId AND DAYOFWEEK(taskPostDate) IN (2, 3) 
               AND WEEK(taskPostDate) = WEEK(CURDATE())";
$resultPosts = mysqli_query($conn, $queryPosts);
$daysWithPosts = array();
while ($rowPosts = mysqli_fetch_assoc($resultPosts)) {
    $daysWithPosts[] = $rowPosts['dayOfWeek'];
}

// Function to determine the CSS class for each day
function getDayClass($currentDay, $dayOfWeek, $daysWithPosts) {
    if ($currentDay == $dayOfWeek) {
        return "day-today day-un";
    } elseif (in_array($dayOfWeek+1, $daysWithPosts)) {
        return "day-success";
    } elseif ($dayOfWeek < $currentDay) {
        return "day-fail";
    } else {
        return "day-un";
    }
}



?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HomePage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="Index-Home.js" defer></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap');

        body {
            font-family: "Titillium Web", sans-serif;
        }

        .BigBox > main {
            min-height: 100vh;
        }

        .curr-date {
            border-radius: 45px;
            position: relative;
        }

        .status-bubble {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 20px;
            height: 20px;
            background-color: #2DC4B6;
        }

        .days-list > * {
            width: 14.28%;
            color: white;
            aspect-ratio: 1 / 1;
        }

        .day-success {
            background-color: #2DC4B6;
        }

        .day-fail {
            background-color: #F14616;
        }

        .day-un {
            background-color: lightgray;
        }

        .day-today {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }

        header {
            border-bottom: 6px #FBFBFB solid;
            box-shadow: 0px 15px 10px -15px rgba(0,0,0,.15);
        }

        .bold {
            font-weight: bold;
        }

        .aside-section {
            border-right: 4px #2DC4B6 solid ;
        }

        nav ul li {
            transition-duration: 0.25s;
            border-radius: 20px;
        }

        nav ul li > p{
            line-height: 100%;

        }

        nav ul li:hover {
            border-radius: 25px;
            background-color: #2DC4B6;
            color: white;
            opacity: 0.5;
        }

        i {
            color: #FF9E1C;
        }

        .post-button {
            border-radius: 25px;
            background-color: #2DC4B6;
            color: white;
            transition-duration: 0.25s;
        }

        .post-button:hover {
            opacity: 0.5;
        }

        .post-button > p {
            line-height: 100%;
        }

        .profile-pic {
            aspect-ratio: 1 / 1;
        }

        #profile-pic {
            border-radius:50%;
            background-position:center;
            background-size:cover;
        }

    </style>
</head>
<body>
<div class="BigBox d-flex">
    <!-- left side -->
    <aside class="aside-section col-md-2 d-flex flex-column pl-4 pr-0">
        <!-- logo -->
        <div class="py-4 d-flex justify-content-center">
            <div class="col-md-11 d-flex justify-content-center">
                <div class="col-md-2 d-flex align-items-center">
                    <i class="fa-solid fa-graduation-cap h3"></i>
                </div>
                <div class="px-3 col-md-10 text-center">
                    <p class="bold m-0 h4">School Name</p>
                </div>
            </div>
        </div>

        <!-- under logo-->
        <div class="d-flex flex-column flex-grow-1 py-2">
            <!-- profile -->
            <div class="d-flex justify-content-center py-5">
                <!-- profile text-->
                <div class="col-md-10 d-flex justify-content-center">
                    <div class="col-md-4 p-0 profile-pic" id="profile-pic" style="background-image: url(<?php echo htmlspecialchars($resultImgRow['studentImg'], ENT_QUOTES, 'UTF-8'); ?>);">
                    </div>
                    <div class="d-flex align-items-center px-3">
                        <div>
                            <p class="m-0 bold"> <?php echo $_SESSION["username"] ?></php> </p>
                            <p>@studentnumber</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column justify-content-center flex-grow-1">
                <!-- nav bar(home, explore, etc)-->
                <nav class=" flex-grow-1">
                    <ul class="col-md-8 mx-auto list-unstyled">
                        <li class="p-3 my-2"><a href="index.php" class="text-reset text-decoration-none p-0 m-0"><p class="px-2 m-0">Home</p></a></li>
                        <li class="p-3 my-2"><p class="px-2 m-0">Explore</p></li>
                        <li class="p-3 my-2"><a href="Help_page.php" class="text-reset text-decoration-none p-0 m-0"><p class="px-2 m-0">Help forum</p></a></li>
                        <li class="p-3 my-2"><p class="px-2 m-0">Profile</p></li>
                        <li class="p-3 my-2"><p class="px-2 m-0">Settings</p></li>
                        <form id="logoutForm" method="post" action="">
                            <li class="p-3 my-2" id="Logout-button" type="submit" name="logout"><p class="px-2 m-0">logout</p></li>
                        </form>

                    </ul>

                </nav>

                <!-- post something-->
                <div class=" mt-auto">
                    <div class="d-flex justify-content-center py-5">
                        <div class="post-button col-9 d-flex justify-content-center">
                            <p class="m-0 py-3 h5 bold">Post something</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </aside>

    <!-- right side-->
    <main class="col-md-10 px-5">
        <!-- todays date, week things-->
        <header class="col-md-12 d-flex bold">
            <div class="col-4 d-flex justify-content-center">
                <div class="d-flex align-items-center rounded p-4">
                    <div class="shadow  p-3 px-4 curr-date">
                        <div class="rounded-circle status-bubble"></div>
                        <h4 class="m-0">
                            <?php
                            $date = date("D d M, Y");
                            echo $date;
                            ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-7 d-flex justify-content-end">
                <div class="days-list col-md-7 d-flex align-items-center justify-content-between">
                    <?php
                    $days = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
                    for ($i = 0; $i < 7; $i++) {
                        $dayOfWeek = ($i);
                        $class = getDayClass((string)$currentDayOfWeek, $dayOfWeek, $daysWithPosts);
                        echo '<div class="flex-grow-1 flex-shrink-1 d-flex justify-content-center p-2">';
                        echo '<div class="' . $class . ' rounded-circle p-3 flex-grow-1 text-center d-flex align-items-center justify-content-center">' . $days[$dayOfWeek] . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>

            </div>

        </header>

        <?php echo json_encode($helpPosts); ?>
        <!-- post section -->
        <div class="d-flex ">
            <div class="col-9">
                <?php
                for ($i = 0; $i <= count($helpPosts)-1; $i++) {
                    echo '<div class="student-post">';
                    echo '<div class="pfp-post">';
                    echo '<img src="../scss/student1/images/harvey.jpeg" alt="">';
                    echo '</div>';

                    echo '<div class="content-post">';
                    echo '<div class="name-hour-post">';

                    echo "<h3>";
                    $name = json_encode($helpPosts[$i]['studentName']);
                    echo str_replace('"', '', $name);
                    echo "</h3>";

                    echo "<p>";
                    $date = json_encode($helpPosts[$i]['helpPostDate']);
                    echo str_replace('"', '', $date);
                    echo "</p>";

                    echo "</div>";

                    echo '<div class="mt-2">';
                    $content = json_encode($helpPosts[$i]['helpPostContent']);
                    echo str_replace('"', '', $content);
                    echo '</div>';

                    if ($helpPosts[$i]['helpPostStudentResp'] == null) {
                        echo '<div class="mt-2">';
                        echo '<button class="btn-help-request" onclick="give_Help('."'" .$_SESSION["username"]."'". "," . $i . ')">I want to help!</button>';
                        echo '</div>';
                    } else {
                        echo '<div class="mt-2">';

                        $stmt = $conn->prepare("SELECT studentName FROM Student WHERE studentId = ?");

                        $stmt->bind_param("s", $helpPosts[$i]['helpPostStudentResp']);
                        $stmt->execute();
                        $resultName  = $stmt->get_result();

                        if ($resultName->num_rows > 0) {
                            $resultNameRow = $resultName->fetch_assoc();
                            $studentName = $resultNameRow['studentName']; // Get the student image
                        }
                        $stmt->close(); // Close the statement


                        echo '<p class="btn-help-request" style="font-size: inherit; font-weight: inherit; color: black; border: 1px solid #2DC4B6; background-color: rgba(45, 196, 182, 0.1)">'. $studentName .' is helping!</p>';

                        echo '</div>';
                    }

                    echo '</div>';

                    echo '</div>';
                }
                ?>
            </div>

            <div class="col-3 p-0">
                <div class="announcements">
                    <h1>Event Example 🎭</h1>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem pariatur, voluptatibus recusandae at culpa error ducimus, eos consequatur non reprehenderit perspiciatis. Quisquam, doloremque recusandae! Ratione, tempora. Quisquam impedit porro voluptatem!</p>
                    <button class="btn-announc">Check me</button>
                </div>

                <div class="announcements announcements-blue mt-4">
                    <h1>Message from Us 🎉</h1>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem pariatur, voluptatibus recusandae at culpa error ducimus, eos consequatur non reprehenderit perspiciatis.</p>
                </div>

                <div class="announcements-empty mt-4">
                    <span>Nothing else to see here for today...</span>
                    <p>😊</p>
                </div>
            </div>
        </div>
    </main>
</div>
</body>

<script>
    function give_Help(username, HelpId) {
        // Use AJAX to send the username to the backend endpoint
        $.ajax({
            url: 'update_help.php', // Endpoint to handle the request
            type: 'POST', // HTTP method
            data: { username: username, HelpId: HelpId+1 }, // Data to send
            success: function (response) { // Callback for successful request
                // Parse the JSON response
                let res = JSON.parse(response);

                if (res.status === 'success') {
                    console.log("Help update successful");
                    // Add further handling, like updating the UI or giving user feedback
                } else {
                    console.error("Error updating help: " + res.message);
                }
            },
            error: function (xhr, status, error) { // Callback for failed request
                console.error("AJAX error: " + status + ", " + error);
            }
        });
    }
</script>
</html>
