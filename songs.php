<?php

if ($loggedin == "1") {
    if (isset($_FILES['song']['tmp_name'])) {
        $songName = $time;
        $folder = "media/songs";
        if (!is_dir("$folder")) {
            mkdir("$folder", 0777, true);
        }
        $saveto = "$folder/$songName.mp3";
        move_uploaded_file($_FILES['song']['tmp_name'], $saveto);
    }
    
    if (filter_input(INPUT_POST, 'issueEdit', FILTER_SANITIZE_STRING)) {
        $album = filter_input(INPUT_POST, 'issueEdit', FILTER_SANITIZE_STRING);
        $issueDate = mktime(0,0,0,filter_input(INPUT_POST, 'issueMonth', FILTER_SANITIZE_NUMBER_INT),filter_input(INPUT_POST, 'issueDay', FILTER_SANITIZE_NUMBER_INT),filter_input(INPUT_POST, 'issueYear', FILTER_SANITIZE_NUMBER_INT));
        $stmt = $db->prepare("UPDATE songs SET issueDate=? WHERE album=?");
        $stmt->execute(array($issueDate,$album));
    }

    if (filter_input(INPUT_POST, 'songEdit', FILTER_SANITIZE_STRING)) {
        $sid = filter_input(INPUT_POST, 'songEdit', FILTER_SANITIZE_STRING);
        $trackNum = filter_input(INPUT_POST, 'trackNum', FILTER_SANITIZE_NUMBER_INT);
        $caption = filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_STRING);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $album = (filter_input(INPUT_POST, 'newAlbum', FILTER_SANITIZE_NUMBER_INT) == "1" && filter_input(INPUT_POST, 'alb', FILTER_SANITIZE_STRING)) ? filter_input(INPUT_POST, 'alb', FILTER_SANITIZE_STRING) : filter_input(INPUT_POST, 'album', FILTER_SANITIZE_STRING);
        $delsong = (filter_input(INPUT_POST, 'delsong', FILTER_SANITIZE_NUMBER_INT) == "1") ? "1" : "0";
        if ($delsong == "1") {
            $stmt2 = $db->prepare("SELECT songName FROM songs WHERE id=?");
            $stmt2->execute(array($sid));
            $row2 = $stmt2->fetch();
            if (file_exists("media/songs/" . $row2['songName'] . ".mp3")) {
                unlink("media/songs/" . $row2['songName'] . ".mp3");
            }
            $stmt = $db->prepare("DELETE FROM songs WHERE id=?");
            $stmt->execute(array($sid));
        } else {
            if (filter_input(INPUT_POST, 'newAlbum', FILTER_SANITIZE_NUMBER_INT) == "1" && filter_input(INPUT_POST, 'alb', FILTER_SANITIZE_STRING)) {
                $issueDate = mktime(0, 0, 0, filter_input(INPUT_POST, 'issueMonth', FILTER_SANITIZE_NUMBER_INT),filter_input(INPUT_POST, 'issueDay', FILTER_SANITIZE_NUMBER_INT),filter_input(INPUT_POST, 'issueYear', FILTER_SANITIZE_NUMBER_INT));
            } else {
                $stmt = $db->prepare("SELECT issueDate FROM songs WHERE album=? LIMIT 1");
                $stmt->execute(array($album));
                $row = $stmt->fetch();
                $issueDate = $row['issueDate'];
            }
            if ($sid == "new" && isset($songName)) {
                $stmt = $db->prepare("INSERT INTO songs VALUES" . "(NULL,?,?,?,?,?,?,'0','0','0')");
                $stmt->execute(array($songName, $caption, $title, $album, $trackNum, $issueDate));
            } else {
                $stmt = $db->prepare("UPDATE songs SET caption=?,title=?,album=?,trackNum=?,issueDate=? WHERE id=?");
                $stmt->execute(array($caption, $title, $album, $trackNum, $issueDate, $sid));
            }
        }
    }

    echo "<table style='width:738px; border:1px solid #ffffff; margin:0px; padding:0px;'>";
    echo "<tr><td><form action='index.php' method='post' enctype='multipart/form-data'>New song:<br /><input type='file' name='song' /><br /><br />";
    echo "<select name='album' size='1'>";
    $stmt3 = $db->prepare("SELECT DISTINCT album FROM songs ORDER BY album");
    $stmt3->execute();
    while ($row3 = $stmt3->fetch()) {
        echo "<option value='" . $row3['album'] . "'>" . $row3['album'] . "</option>";
    }
    echo "</select><br /><input type='checkbox' name='newAlbum' value='1' rel='issueDatenew' /><input type='text' name='alb' placeholder='New album' />";
    echo "<div rel='issueDatenew'>Issue Date: m<select name='issueMonth'>";
    for ($m = 1; $m <= 12; $m++) {
        echo "<option value='$m'>$m</option>\n";
    }
    echo "</select> d<select name='issueDay'>";
    for ($d = 1; $d <= 31; $d++) {
        echo "<option value='$d'>$d</option>\n";
    }
    echo "</select> y<select name='issueYear'>";
    for ($y = (date("Y") + 1); $y >= 2010; $y--) {
        echo "<option value='$y'>$y</option>\n";
    }
    echo "</select></div>";
    echo "</td>";
    echo "<td><input type='text' name='trackNum' size='3' max-length='3' /> Track Number<br /><br ><input type='text' name='title' placeholder='Title' /><br /><br ><textarea name='caption' cols='20' rows='5' placeholder='caption'></textarea></td>";
    echo "<td><input type='hidden' name='songEdit' value='new' /><input type='hidden' name='songName' value='$time' /><input type='submit' value=' upload ' /></form></td></tr></table>";
    echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
    $stmt = $db->prepare("SELECT DISTINCT album FROM songs ORDER BY issueDate DESC");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $alb = $row['album'];
        echo "<tr><td colspan='2'><div style='text-align:center; color:#ffffff; font-weight:bold; font-size:1.5em;' onclick='toggleview(\"$alb\")'>$alb</div></td><td>&nbsp;</td></tr>";
        echo "<tr><td colspan='3'><table id='$alb' width='740px' style='display:none;'>";
        $stmt2 = $db->prepare("SELECT id,songName,caption,title,trackNum FROM songs WHERE album=? ORDER BY trackNum");
        $stmt2->execute(array($alb));
        while ($row2 = $stmt2->fetch()) {
            $songId = $row2['id'];
            $songName = $row2['songName'];
            $caption = $row2['caption'];
            $title = $row2['title'];
            $trackNum = $row2['trackNum'];
            echo "<tr><td rowspan='2' style='width:370px;'><audio controls><source src='media/songs/$songName.mp3' type='audio/mpeg'>Your browser does not support the audio element.</audio><br /><br />
                <form action='index.php' method='post'>";
            echo "<select name='album' size='1'>";
            $stmt3 = $db->prepare("SELECT DISTINCT album FROM songs ORDER BY issueDate DESC");
            $stmt3->execute();
            while ($row3 = $stmt3->fetch()) {
                echo "<option value='" . $row3['album'] . "'";
                if ($row3['album'] == $alb)
                    echo " selected='selected'";
                echo ">" . $row3['album'] . "</option>";
            }
            echo "</select><br /><input type='checkbox' name='newAlbum' value='1' rel='issueDate$songId' /><input type='text' name='alb' placeholder='New album' />";
            echo "<div rel='issueDate$songId'>Issue Date: m<select name='issueMonth'>";
            for ($m = 1; $m <= 12; $m++) {
                echo "<option value='$m'>$m</option>\n";
            }
            echo "</select> d<select name='issueDay'>";
            for ($d = 1; $d <= 31; $d++) {
                echo "<option value='$d'>$d</option>\n";
            }
            echo "</select> y<select name='issueYear'>";
            for ($y = (date("Y") + 1); $y >= 2010; $y--) {
                echo "<option value='$y'>$y</option>\n";
            }
            echo "</select></div>";
            echo "</td>";
            echo "<td style='width:270px;'><input type='text' name='trackNum' size='3' max-length='3' value='$trackNum' /> Track Number<br /><br ><input type='text' name='title' value='$title' /></td>";
            echo "<td style='width:100px;'>Delete song?<br /><input type='checkbox' name='delsong' value='1' /></td>";
            echo "</tr><tr>";
            echo "<td><textarea name='caption' cols='20' rows='5'>$caption</textarea></td>";
            echo "<td><input type='hidden' name='songEdit' value='$songId' /><input type='submit' value=' Edit ' /></form></td></tr>";
            echo "<tr><td colspan='3'><hr style='width:75%;' /></td></tr>";
        }
        echo "</table></td></tr>";
        echo "<tr><td colspan='3'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
    }
    echo "</table>";
    echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
    echo "<tr><td colspan='2'><div style='text-align:center; color:#ffffff; font-weight:bold; font-size:1.5em;' onclick='toggleview(\"editIssue\")'>Edit Album Issue Dates</div></td><td>&nbsp;</td></tr>";
    echo "<tr><td colspan='3'><table id='editIssue' width='740px' style='display:none;'>";
    $stmt = $db->prepare("SELECT DISTINCT album FROM songs ORDER BY album");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $alb = $row['album'];
        $stmt2 = $db->prepare("SELECT issueDate FROM songs WHERE album=? LIMIT 1");
        $stmt2->execute(array($alb));
        $row2 = $stmt2->fetch();
        $issueDate = $row2['issueDate'];
        echo "<tr><td style='font-weight:bold; width:246px;'>$alb</td><td style='width:394px;'><form action='index.php' method='post'>Issue Date: m<select name='issueMonth'>";
        for ($m = 1; $m <= 12; $m++) {
            echo "<option value='$m'";
            if ($m == date("n",$issueDate))
                    echo " selected='selected'";
            echo ">$m</option>\n";
        }
        echo "</select> d<select name='issueDay'>";
        for ($d = 1; $d <= 31; $d++) {
            echo "<option value='$d'";
            if ($d == date("j",$issueDate))
                    echo " selected='selected'";
            echo ">$d</option>\n";
        }
        echo "</select> y<select name='issueYear'>";
        for ($y = (date("Y") + 1); $y >= 2010; $y--) {
            echo "<option value='$y'";
            if ($y == date("Y",$issueDate))
                    echo " selected='selected'";
            echo ">$y</option>\n";
        }
        echo "</select>";
        echo "</td><td style='width:100px;'><input type='hidden' name='issueEdit' value='$alb' /><input type='submit' value=' Edit ' /></form></td></tr>";
    }
    echo "</table></td></tr>";
    echo "<tr><td colspan='3'><div style='display:block; height:10px;'>&nbsp;</div></td></tr>";
    echo "</table>";
} else {
    echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
    $stmt = $db->prepare("SELECT DISTINCT album FROM songs ORDER BY issueDate DESC");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $alb = $row['album'];
        echo "<tr><td colspan='2'><div style='text-align:center; color:#ffffff; font-weight:bold; font-size:1.5em; text-decoration:underline; cursor:pointer;$subTitle' onclick='toggleview(\"$alb\")'>$alb</div></td><td>&nbsp;</td></tr>";
        echo "<tr><td><table id='$alb' style='display:none; width:740px;'>";
        $stmt2 = $db->prepare("SELECT songName,caption,title FROM songs WHERE album=? ORDER BY trackNum");
        $stmt2->execute(array($alb));
        while ($row2 = $stmt2->fetch()) {
            $songName = $row2['songName'];
            $caption = nl2br($row2['caption']);
            $title = $row2['title'];
            echo "<tr><td rowspan='2' style='width:370px;'><audio controls><source src='media/songs/$songName.mp3' type='audio/mpeg'>Your browser does not support the audio element.</audio></td>";
            echo "<td style='text-align:left; font-size:1.25em; width:370px;'>$title</td>";
            echo "</tr><tr>";
            echo "<td style='padding:10px; text-align:justify; width:370px;'>$caption</td></tr>";
        }
        echo "</table></td></tr>";
        echo "<tr><td colspan='2'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
    }
    echo "</table>";
}
?>
