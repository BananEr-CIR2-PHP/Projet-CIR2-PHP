<?php
session_start();
if (! isset($_SESSION["user_id"])) {
    header("location:login.php");
    exit;
}
if (! isset($_GET['medecin'])) {
    header("location:medecins.php");
    exit;
}
include_once('database.php');
include_once('time.php');
$conn=dbConnect();

$doc_fullname = dbGetDocFullName($conn, $_GET['medecin']);
// If somebody is trying to see timetable of an unknown doc => redirect
if ($doc_fullname === false) {
    header("location:medecins.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title> Créneaux du Dr. <?php echo $doc_fullname; ?> </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="scripts/timetable.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <nav class="navbar bg-primary navbar-expand-lg d-flex justify-content-between">
            <img src="img/logo_small.png" srcset="img/logo_medium 900w, img/logo_large.png 1900w, img/logo_xlarge.png 3000w" alt="Logo">
            <form method="get" action="request.php">
                <button class="btn border-light rounded-5 text-light" type="submit" value="logout">Se déconnecter</button>
            </form>
        </nav>

        <div class="mx-auto" style="width:98%">

<?php
if (isset($_REQUEST['selected-week'])) {
    $selected_week = $_REQUEST['selected-week'];
}
else {
    $selected_week = time();
}
$week_start_tmstmp = getWeekDay($selected_week, "monday", 0, 0, 0);
$week_end_tmstmp = getWeekDay($selected_week, "sunday", 23, 59, 59);

// --- Week selector ---
$week_duration = 7*24*3600;
$next_week = $selected_week + $week_duration;
$previous_week = $selected_week - $week_duration;

echo "<div class=\"d-flex justify-content-center align-items-center gap-4 m-2\">";

// Button to previous week
echo "<form action=\"\" method=\"get\">";
echo "<input type=\"hidden\" name=\"medecin\" value=\"{$_GET['medecin']}\">";
echo "<button type=\"submit\" class=\"btn btn-primary\" name=\"selected-week\" value=\"$previous_week\"><</button>";
echo "</form>";

echo "<h2>Semaine du ".formatDay($week_start_tmstmp)." au ".formatDay($week_end_tmstmp)."</h2>";

// Button to next week
echo "<form action=\"\" method=\"get\">";
echo "<input type=\"hidden\" name=\"medecin\" value=\"{$_GET['medecin']}\">";
echo "<button type=\"submit\" class=\"btn btn-primary\" name=\"selected-week\" value=\"$next_week\">></button>";
echo "</form>";

echo "</div>";

// --- Show timetable of available appointment slots ---

$available_slots = dbGetAvailableRDVSlots($conn, $_GET['medecin'], $week_start_tmstmp, $week_end_tmstmp);

// If no appointment is available, show message
if (count($available_slots) == 0) {
    echo "<br>Aucun rendez-vous disponible cette semaine.";
}
// Otherwise, show timetable
else {
    // Get min and max slot to show on table. (table is dynamic)
    $positions_per_hour = 4;
    $position_duration = 60* 60/$positions_per_hour;     // 15 minutes
    $last_position = $positions_per_hour * 24;
    $slot_height = 15;  // 15px
    getMinMaxAvailableSlots($available_slots, $position_duration, $week_start_tmstmp, $week_end_tmstmp, $min_position, $max_position);
    // Round min_position and max_position to whole hour (i.e. avoid beginning the day at 7:15, start at 7:00 instead)
    $min_position = $min_position - $min_position % $positions_per_hour;
    $max_position = $max_position % $positions_per_hour == 0 ? $max_position : $max_position + $positions_per_hour - $max_position % $positions_per_hour;

    echo "<table class=\"table table-bordered table-sm\" style=\"table-layout: fixed;\" id=\"timetable\" data-doctor=\"Dr. $doc_fullname\">";

    // Header
    echo "<thead><th scope=\"col\">Dr. $doc_fullname</th>";
    echo "<th scope=\"col\">Lundi<br>".formatDay(getWeekDay($week_start_tmstmp, 'monday'))."</th>";
    echo "<th scope=\"col\">Mardi<br>".formatDay(getWeekDay($week_start_tmstmp, 'tuesday'))."</th>";
    echo "<th scope=\"col\">Mercredi<br>".formatDay(getWeekDay($week_start_tmstmp, 'wednesday'))."</th>";
    echo "<th scope=\"col\">Jeudi<br>".formatDay(getWeekDay($week_start_tmstmp, 'thursday'))."</th>";
    echo "<th scope=\"col\">Vendredi<br>".formatDay(getWeekDay($week_start_tmstmp, 'friday'))."</th>";
    echo "<th scope=\"col\">Samedi<br>".formatDay(getWeekDay($week_start_tmstmp, 'saturday'))."</th>";
    echo "<th scope=\"col\">Dimanche<br>".formatDay(getWeekDay($week_start_tmstmp, 'sunday'))."</th>";
    echo "</thead>";

    // Body
    echo "<tbody>";
    for ($slot=$min_position; $slot<=$max_position; $slot++) {
        if ($slot % $positions_per_hour == 0) {
            echo "<tr><th scope=\"row\" rowspan=\"$positions_per_hour\">";
            echo floor($slot/$positions_per_hour);
            echo ":00</th>"; 
        }

        for ($i=0; $i<7; $i++) {
            echo "<td class=\"td-timetable\" id=\"table-$i-$slot\"></td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    echo "
    <div class=\"modal\" role=\"dialog\" id=\"RDV-confirm\">
    <div class=\"modal-dialog\" role=\"document\"><div class=\"modal-content\">
    <div class=\"modal-header\">
    <h3 id=\"RDV-confirm-doctor\" class=\"modal-title\"></h3>
    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
    </div><div class=\"modal-body\">
    <p id=\"RDV-confirm-date\"></p>
    <p id=\"RDV-confirm-time\"></p>
    <p id=\"RDV-confirm-place\"></p>
    </div><div class=\"modal-footer\">
    <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Fermer</button>
    <form action=\"request.php\" method=\"post\">
        <input type=\"hidden\" name=\"slot\" id=\"RDV-confirm-slot-id\">
        <button type=\"submit\" class=\"btn btn-primary\" name=\"rdv\" value=\"rdv\">Prendre rendez-vous</button>
    </form>
    </div></div></div></div>";

    // A bit of CSS
    echo "<style>
    td.td-timetable {
        height: {$slot_height}px;
        padding: 0px;
    }

    .slot-btn {
        padding: 0px 5% 0px 5%;
        overflow: hidden;
        max-width: 100%;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    </style>";
    
    // Then, use JS to place slots in table
    echo "<script>";

    foreach ($available_slots as $slot) {
        $time_start = getTimeSinceDaystart($slot['start_tmstmp']);
        $time_end = getTimeSinceDaystart($slot['end_tmstmp']);
        $position_start = round($time_start / $position_duration);
        $position_end = round($time_end / $position_duration);
        $start_day = getDaysTo($week_start_tmstmp, $slot['start_tmstmp']);
        $end_day = getDaysTo($week_start_tmstmp, $slot['end_tmstmp']);
        $start_date = formatDay($slot['start_tmstmp']);
        $slot_id = $slot['slot_id'];
        
        // If start and end on same day, set slot in place
        if ($end_day == $start_day) {
            echo "insertSlot($start_day, \"$start_date\", $position_start, $position_end, $time_start, $time_end, \"{$slot['place']}\", $slot_height, $slot_id);";
        }

        // If start and end on different days, split slot
        else {
            echo "insertSlot($start_day, \"$start_date\", $position_start, $last_position, $time_start, $time_end, \"{$slot['place']}\", $slot_height, $slot_id);";
            for ($i=$start_day+1; $i<$end_day; $i++) {
                echo "insertSlot($i, \"$start_date\", 0, $last_position, $time_start, $time_end, \"{$slot['place']}\", $slot_height, $slot_id);";
            }
            echo "insertSlot($end_day, \"$start_date\", 0, $position_end, $time_start, $time_end, \"{$slot['place']}\", $slot_height, $slot_id);";
        }
    }
    echo "</script>";
}
?>

        </div>
    </body>
</html>