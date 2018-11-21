<?php
$servername = "localhost";
$username = "ptr";
$password = "Check444!";
$database = "reward";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// START BY CHECKING UPDATES

if (array_key_exists("day", $_GET)) {
    $uday = htmlspecialchars($_GET["day"]);
    $uhour = htmlspecialchars($_GET["hour"]);
    $ustate = htmlspecialchars($_GET["newstate"]);

    $sql = "INSERT INTO rewards (id, day, hour, success) VALUES (0, '" . $uday . "', " . $uhour . ", " . $ustate . ")";
    print($sql);
    $result = mysqli_query($conn, $sql);
    if ($result == false) {
        die(mysqli_error($conn));
    }    
}



$sql = "SELECT * FROM `rewards` ORDER BY day ASC, hour ASC";
$result = mysqli_query($conn, $sql);
if ($result == false) {
    die(mysqli_error($conn));
}

$boxes = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $day = $row["day"];
        $hour = $row["hour"];
        $success = $row["success"];
        $boxes[$day][$hour] = $success;
    }
}

$startDate = new DateTime('2018-11-18');
$endDate = new DateTime('tomorrow');
$interval = new DateInterval('P1D');
$daterange = new DatePeriod($startDate, $interval ,$endDate);
?>
<html>
<script>
function update(day, hour, oldstate) {
    var curr_page = window.location.href,
        next_page = "";

    var res = curr_page.split("?");
    next_page = res[0] + "?day="  + day + "&hour=" + hour + "&newstate=" + oldstate;
    window.location = next_page;    
}
</script>
<?php
print("<table border=\"1\">");
print("<tr>");
print("<td>++Day++</td>");
foreach (range(0, 23) as $hour) {
    print("<td>" . $hour . "-" . ($hour+1) . "</td>");
}
print("<td>Total</td>");
print("</tr>");


function getSuccess($array, $date, $hour) {
    if (array_key_exists($date, $array) and array_key_exists($hour, $array[$date])) {
        return $array[$date][$hour];
    } else {
        return -1;
    }
}

foreach ($daterange as $date){
    $datestr = $date->format("Y-m-d");
    $count = 0;
    print("<tr>");
    print("<td>" . $date->format("M-d") . "</td>");
    foreach (range(0, 23) as $hour) {

        $t = getSuccess($boxes, $datestr, $hour);        
        $bgcolor = "#BBBBBB";
        if ($t == 0) {
            $bgcolor = "#FF0000";
        } else if ($t == 1) {
            $bgcolor = "#00FF00";
            $count += 1;
        }

        $style = "style='width:50px; height:50px'";
        $bgstr = "bgcolor='" . $bgcolor . "'";
        $newt = $t - 1;
        if ($newt < -1) {
            $newt = 1;
        }
        $onclick = "onclick='update(\"" . $datestr . "\", \"" . $hour . "\", \"" . $newt . "\");'";
              
        print("<td " .  $style . " " . $bgstr . " " . $onclick . ">..</td>");
    }
    print("<td>" . $count . "</td>");
    print("</tr>");
}


// if ($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         $day = $row["day"];
//         $hour = $row["hour"];
//         $success = $row["success"];
//         print("<tr>");
//         print("<td>");
//         print($row["hour"]);
//         // print_r($row);
//         print("</td>");
//         $boxes[$day][$hour] = $success;
//         print("</tr>");
//     }
// }
print("</table>");
print("</html>");

// print_r($boxes);
$conn->close();

?>