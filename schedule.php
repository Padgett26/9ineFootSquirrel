<?php
$schedStyle = "style='border:1px solid #880000; margin:1px; text-align:center; padding:3px;'";
if ($loggedin == "1") {
    if (filter_input(INPUT_POST, 'schedEdit', FILTER_SANITIZE_STRING)) {
        $sid = filter_input(INPUT_POST, 'schedEdit', FILTER_SANITIZE_STRING);
        $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT);
        $month = filter_input(INPUT_POST, 'month', FILTER_SANITIZE_NUMBER_INT);
        $day = filter_input(INPUT_POST, 'day', FILTER_SANITIZE_NUMBER_INT);
        $venue = filter_input(INPUT_POST, 'venue', FILTER_SANITIZE_STRING);
        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
        $ticketsLink = (filter_input(INPUT_POST, 'ticketsLink', FILTER_SANITIZE_URL)) ? filter_input(INPUT_POST, 'ticketsLink', FILTER_SANITIZE_URL) : "0";
        $venueLink = (filter_input(INPUT_POST, 'venueLink', FILTER_SANITIZE_URL)) ? filter_input(INPUT_POST, 'venueLink', FILTER_SANITIZE_URL) : "0";
        $delsched = (filter_input(INPUT_POST, 'delsched', FILTER_SANITIZE_NUMBER_INT) == "1") ? "1" : "0";
        if ($delsched == "1") {
            $stmt = $db->prepare("DELETE FROM schedule WHERE id=?");
            $stmt->execute(array($sid));
        } else {
            if ($sid == "new") {
                $stmt = $db->prepare("INSERT INTO schedule VALUES" . "(NULL,?,?,?,?,?,?,?,'0','0','0')");
                $stmt->execute(array($year, $month, $day, $venue, $location, $venueLink, $ticketsLink));
            } else {
                $stmt = $db->prepare("UPDATE schedule SET year=?, month=?, day=?, venue=?, location=?, venueLink=?, ticketsLink=? WHERE id=?");
                $stmt->execute(array($year, $month, $day, $venue, $location, $venueLink, $ticketsLink, $sid));
            }
        }
    }

    echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
    echo "<tr><td colspan='6'><form action='index.php' method='post'>New show:<br />";
    ?>
    Y<select name="year">
        <?php
        $cyear = date('Y');
        for ($sy = ($cyear - 1); $sy <= ($cyear + 2); $sy++) {
            echo "<option value='$sy'";
            if ($cyear == $sy)
                echo " selected='selected'";
            echo ">$sy</option>\n";
        }
        ?>
    </select> M<select name="month">
        <?php
        for ($sm = 1; $sm <= 12; $sm++) {
            echo "<option value='$sm'>$sm</option>\n";
        }
        ?>
    </select> D<select name="day">
        <?php
        for ($sd = 1; $sd <= 31; $sd++) {
            echo "<option value='$sd'>$sd</option>\n";
        }
        ?>
    </select>
    <?php
    echo "<br /><input type='text' name='venue' placeholder='Venue' /><br /><input type='text' name='location' placeholder='Venue location' /><br />";
    echo "<input type='text' name='venueLink' placeholder='Venue website address' /><br /><input type='text' name='ticketsLink' placeholder='Tickets web link' /><br />";
    echo "<input type='hidden' name='schedEdit' value='new' /><input type='submit' value=' upload ' /></form></td></tr>";
    $stmt = $db->prepare("SELECT DISTINCT year FROM schedule ORDER BY year");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $year = $row['year'];
        echo "<tr><td colspan='2'><div style='text-align:left; color:#ffffff; font-weight:bold; font-size:1.5em;'>$year</div></td><td colspan='4'>&nbsp;</td></tr>";
        $stmt2 = $db->prepare("SELECT DISTINCT month FROM schedule WHERE year=? ORDER BY month");
        $stmt2->execute(array($year));
        while ($row2 = $stmt2->fetch()) {
            $month = $row2['month'];
            echo "<tr><td colspan='2'><div style='text-align:left; margin-left:20px; color:#ffffff; font-weight:bold; font-size:1.25em;'>" . $monthNames[$month] . "</div></td><td colspan='4'>&nbsp;</td></tr>";
            $stmt3 = $db->prepare("SELECT * FROM schedule WHERE year=? AND month=? ORDER BY day");
            $stmt3->execute(array($year, $month));
            while ($row3 = $stmt3->fetch()) {
                $schedId = $row3['id'];
                $day = $row3['day'];
                $venue = $row3['venue'];
                $location = $row3['location'];
                $venueLink = $row3['venueLink'];
                $ticketsLink = $row3['ticketsLink'];
                echo "<tr style='border:1px solid #ffffff;'><td $schedStyle>";
                echo "<form action='index.php' method='post'>";
                ?>
                Y<select name="year">
                    <?php
                    for ($sy = ($year - 1); $sy <= ($year + 2); $sy++) {
                        echo "<option value='$sy'";
                        if ($year == $sy)
                            echo " selected='selected'";
                        echo ">$sy</option>\n";
                    }
                    ?>
                </select> M<select name="month">
                    <?php
                    for ($sm = 1; $sm <= 12; $sm++) {
                        echo "<option value='$sm'";
                        if ($month == $sm)
                            echo " selected='selected'";
                        echo ">$sm</option>\n";
                    }
                    ?>
                </select> D<select name="day">
                    <?php
                    for ($sd = 1; $sd <= 31; $sd++) {
                        echo "<option value='$sd'";
                        if ($day == $sd)
                            echo " selected='selected'";
                        echo ">$sd</option>\n";
                    }
                    ?>
                </select>
                <?php
                echo "</td><td $schedStyle><input type='text' name='venue' value='$venue' /></td>";
                echo "<td $schedStyle><input type='text' name='location' value='$location' /></td>";
                echo "<td $schedStyle><input type='text' name='venueLink' value='$venueLink' /></td>";
                echo "<td $schedStyle><input type='text' name='ticketsLink' value='$ticketsLink' /></td>";
                echo "<td $schedStyle><input type='checkbox' name='delsched' value='1' /> Delete event <input type='hidden' name='schedEdit' value='$schedId' /> <input type='submit' value=' Edit ' /></form></td></tr>";
            }
        }
        echo "</tr><tr><td colspan='6'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
    }
    echo "</table>";
} else {
    echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
    $stmt = $db->prepare("SELECT DISTINCT year FROM schedule ORDER BY year");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $year = $row['year'];
        if ($year >= (date("Y") - 1)) {
            echo "<tr><td colspan='2'><div style='text-align:center; color:#ffffff; font-weight:bold; font-size:1.5em;$subTitle'>$year</div></td><td colspan='2'>&nbsp;</td></tr>";
            $stmt2 = $db->prepare("SELECT DISTINCT month FROM schedule WHERE year=? ORDER BY month");
            $stmt2->execute(array($year));
            while ($row2 = $stmt2->fetch()) {
                $month = $row2['month'];
                echo "<tr><td colspan='2'><div style='text-align:center; color:#ffffff; font-weight:bold; font-size:1.25em;$subTitle'>" . $monthNames[$month] . "</div></td><td colspan='2'>&nbsp;</td></tr>";
                $stmt3 = $db->prepare("SELECT * FROM schedule WHERE year=? AND month=? ORDER BY day");
                $stmt3->execute(array($year, $month));
                while ($row3 = $stmt3->fetch()) {
                    $day = $row3['day'];
                    $venue = $row3['venue'];
                    $location = $row3['location'];
                    $venueLink = $row3['venueLink'];
                    $ticketsLink = $row3['ticketsLink'];
                    echo "<tr style='border:1px solid #ffffff;'><td $schedStyle>$day</td>";
                    echo "<td $schedStyle>";
                    echo ($venueLink != "0") ? "<a href'$venueLink' target='_blank' style='text-decoration:underline;'>$venue</a>" : $venue;
                    echo "</td>";
                    echo "<td $schedStyle>$location</td>";
                    echo "<td $schedStyle>";
                    echo ($ticketsLink != "0" && $time < mktime(23, 59, 59, $month, $day, $year)) ? "<div style='display:block; width:80px; text-align:center;'><a href'$ticketsLink' target='_blank' style='text-decoration:underline;'>Get tickets</a></div>" : "<div style='display:block; width:80px; text-align:center;'>&nbsp;</div>";
                    echo "</td></tr>";
                }
            }
            echo "<tr><td colspan='4'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
        }
    }
    echo "</table>";
}
?>
