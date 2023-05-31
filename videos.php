<?php

if ($loggedin == "1") {
    if (filter_input(INPUT_POST, 'videoEdit', FILTER_SANITIZE_STRING)) {
        $sid = filter_input(INPUT_POST, 'videoEdit', FILTER_SANITIZE_STRING);
        $videoName = filter_input(INPUT_POST, 'videoName', FILTER_SANITIZE_STRING);
        $caption = filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_STRING);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $delvideo = (filter_input(INPUT_POST, 'delvideo', FILTER_SANITIZE_NUMBER_INT) == "1") ? "1" : "0";
        if ($delvideo == "1") {
            $stmt = $db->prepare("DELETE FROM videos WHERE id=?");
            $stmt->execute(array($sid));
        } else {
            if ($sid == "new" && filter_input(INPUT_POST, 'videoName', FILTER_SANITIZE_STRING)) {
                $stmt = $db->prepare("INSERT INTO videos VALUES" . "(NULL,?,?,?,'0','0')");
                $stmt->execute(array($videoName, $caption, $title));
            } else {
                $stmt = $db->prepare("UPDATE videos SET caption=?,title=?,videoName=? WHERE id=?");
                $stmt->execute(array($caption, $title, $videoName, $sid));
            }
        }
    }

    echo "<table style='width:738px; border:1px solid #ffffff; margin:0px; padding:0px;'>";
    echo "<tr><td><form action='index.php' method='post' /><input type='text' name='videoName' placeholder='YouTube code' /><br /><br />";
    echo "<input type='text' name='title' placeholder='Title' /></td>";
    echo "<td><textarea name='caption' cols='20' rows='5' placeholder='caption'></textarea></td>";
    echo "<td><input type='hidden' name='videoEdit' value='new' /><input type='submit' value=' upload ' /></form></td></tr></table>";
    echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
    $stmt2 = $db->prepare("SELECT id,videoName,caption,title FROM videos ORDER BY RAND()");
    $stmt2->execute();
    while ($row2 = $stmt2->fetch()) {
        $videoId = $row2['id'];
        $videoName = $row2['videoName'];
        $caption = $row2['caption'];
        $title = $row2['title'];
        echo "<tr><td><iframe width='420' height='345' src='http://www.youtube.com/embed/$videoName'></iframe></td>";
        echo "<td><form action='index.php' method='post'><input type='text' name='title' value='$title' /><br /><br />
            <textarea name='caption' cols='20' rows='5'>$caption</textarea><br /><br />
            Delete video?<br /><input type='checkbox' name='delvideo' value='1' /><br /><br />
            <input type='hidden' name='videoEdit' value='$videoId' /><input type='submit' value=' Edit ' /></form></td></tr>";
        echo "<tr><td colspan='2'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
    }
    echo "</table>";
} else {
    echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
    $stmt2 = $db->prepare("SELECT videoName,caption,title FROM videos ORDER BY RAND()");
    $stmt2->execute();
    while ($row2 = $stmt2->fetch()) {
        $videoName = $row2['videoName'];
        $caption = nl2br($row2['caption']);
        $title = $row2['title'];
        echo "<tr></tr>";
        echo "<tr><td style='vertical-align:top;'><iframe width='420' height='345' src='http://www.youtube.com/embed/$videoName'></iframe></td>";
        echo "<td style='vertical-align:top;'><div style='text-align:center; font-size:1.25em; margin-bottom:20px;$subTitle'>$title</div><div style='text-align:justify; font-size:1em; padding:10px;'>$caption</div></td></tr>";
        echo "<tr><td colspan='2'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
    }
    echo "</table>";
}
?>
