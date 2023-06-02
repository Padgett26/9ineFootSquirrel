<?php
// User processing
if (filter_input(INPUT_POST, 'userup', FILTER_SANITIZE_STRING)) {
    $uid = filter_input(INPUT_POST, 'userup', FILTER_SANITIZE_STRING);
    $fName = filter_input(INPUT_POST, 'fName', FILTER_SANITIZE_STRING);
    $email = (filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) ? filter_input(
            INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : "";
    $pwd = filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING);
    $deluser = filter_input(INPUT_POST, 'deluser', FILTER_SANITIZE_NUMBER_INT);

    if ($deluser == "1") {
        $stmt = $db->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute(array(
                $uid
        ));
        $msg = "User deleted...";
    } else {
        $stmt = $db->prepare(
                "SELECT COUNT(*) FROM users WHERE id != ? AND email = ?");
        $stmt->execute(array(
                $uid,
                $email
        ));
        $row = $stmt->fetch();
        $ecount = $row[0];
        if ($ecount >= "1") {
            $msg = "The email address seems to already be used, pick another.";
        } elseif ($fName == "" || $fName == " " || $email == "") {
            $msg = "Not all of the information was filled out correctly, please try again.";
        } else {
            if ($uid == "new") {
                if ($pwd == "" || $pwd == " ")
                    $msg = "The passowrd was blank, please try again.";
                else {
                    $salt = rand(100000, 999999);
                    $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
                    $stmt = $db->prepare(
                            "INSERT INTO users VALUES" .
                            "(NULL,?,?,?,?,?,'0','0')");
                    $stmt->execute(
                            array(
                                    $email,
                                    $hidepwd,
                                    $time,
                                    $fName,
                                    $salt
                            ));
                    $msg = "User added...";
                }
            } else {
                if ($pwd != "" || $pwd != " ") {
                    $stmt = $db->prepare("SELECT salt FROM users WHERE id=?");
                    $stmt->execute(array(
                            $uid
                    ));
                    $row = $stmt->fetch();
                    $salt = $row['salt'];
                    $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
                    $stmt = $db->prepare("UPDATE users SET pwd=? WHERE id=?");
                    $stmt->execute(array(
                            $hidepwd,
                            $uid
                    ));
                }
                $stmt = $db->prepare(
                        "UPDATE users SET fName=?,email WHERE id=?");
                $stmt->execute(array(
                        $fName,
                        $email,
                        $uid
                ));
            }
        }
    }
}

if (filter_input(INPUT_POST, 'merchUp', FILTER_SANITIZE_STRING)) {
    $merchId = filter_input(INPUT_POST, 'merchUp', FILTER_SANITIZE_STRING);
    $merchTitle = filter_input(INPUT_POST, 'merchTitle', FILTER_SANITIZE_STRING);
    $merchDescription = filter_input(INPUT_POST, 'merchDescription',
            FILTER_SANITIZE_STRING);
    $merchSoldOut = (filter_input(INPUT_POST, 'merchSoldOut',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $merchShowOrder = filter_input(INPUT_POST, 'merchShowOrder',
            FILTER_SANITIZE_NUMBER_INT);
    $ppCode = filter_input(INPUT_POST, 'ppCode', FILTER_UNSAFE_RAW);
    $delPic1 = (filter_input(INPUT_POST, 'delPic1', FILTER_SANITIZE_NUMBER_INT) ==
            '1') ? '1' : '0';
    $delPic2 = (filter_input(INPUT_POST, 'delPic2', FILTER_SANITIZE_NUMBER_INT) ==
            '1') ? '1' : '0';

    if ($delPic1 == '1') {
        $m2 = $db->prepare(
                "SELECT merchPic1, merchPic1Ext FROM merch WHERE id=?");
        $m2->execute(array(
                $merchId
        ));
        $mrow2 = $m2->fetch();
        $mp1 = $mrow2['merchPic1'];
        $mp1e = $mrow2['merchPic1Ext'];
        if (file_exists("merch/" . $mp1 . "." . $mp1e)) {
            unlink("merch/" . $mp1 . "." . $mp1e);
        }
        $p2stmt = $db->prepare("UPDATE merch SET merchPic1=? WHERE id=?");
        $p2stmt->execute(array(
                '0',
                $merchId
        ));
    }

    if ($delPic2 == '1') {
        $m2 = $db->prepare(
                "SELECT merchPic2, merchPic2Ext FROM merch WHERE id=?");
        $m2->execute(array(
                $merchId
        ));
        $mrow2 = $m2->fetch();
        $mp2 = $mrow2['merchPic2'];
        $mp2e = $mrow2['merchPic2Ext'];
        if (file_exists("merch/" . $mp2 . "." . $mp2e)) {
            unlink("merch/" . $mp2 . "." . $mp2e);
        }
        $p2stmt = $db->prepare("UPDATE merch SET merchPic2=? WHERE id=?");
        $p2stmt->execute(array(
                '0',
                $merchId
        ));
    }

    if ($merchId == 'new') {
        $mstmt1 = $db->prepare(
                "INSERT INTO merch VALUES(NULL,?,'','0','jpg','0','jpg','0','1','','0','0','0')");
        $mstmt1->execute(array(
                $time
        ));
        $mstmt2 = $db->prepare(
                "SELECT id FROM merch WHERE merchTitle=? ORDER BY id DESC LIMIT 1");
        $mstmt2->execute(array(
                $time
        ));
        $mrow2 = $mstmt2->fetch();
        $merchId = $mrow2['id'];
    }

    $image1 = $_FILES["image1m"]["tmp_name"];
    $image1Name = ($time + 1);
    list ($width1, $height1) = (getimagesize($image1) != null) ? getimagesize(
            $image1) : null;
    if ($width1 != null && $height1 != null) {
        $image1Type = getPicType($_FILES["image1m"]['type']);
        processPic("$domain/merch", $image1Name . "." . $image1Type, $image1,
                400, 150);
        $p1stmt = $db->prepare(
                "UPDATE merch SET merchPic1=?, merchPic1Ext=? WHERE id=?");
        $p1stmt->execute(array(
                $image1Name,
                $image1Type,
                $merchId
        ));
    }
    $image2 = $_FILES["image2m"]["tmp_name"];
    $image2Name = ($time + 2);
    list ($width2, $height2) = (getimagesize($image2) != null) ? getimagesize(
            $image2) : null;
    if ($width2 != null && $height2 != null) {
        $image2Type = getPicType($_FILES["image2m"]['type']);
        processPic("$domain/merch", $image2Name . "." . $image2Type, $image2,
                400, 150);
        $p2stmt = $db->prepare(
                "UPDATE merch SET merchPic2=?, merchPic2Ext=? WHERE id=?");
        $p2stmt->execute(array(
                $image2Name,
                $image2Type,
                $merchId
        ));
    }

    $mstmt = $db->prepare(
            "UPDATE merch SET merchTitle=?, merchDescription=?, merchSoldOut=?, merchShowOrder=?, ppCode=? WHERE id=?");
    $mstmt->execute(
            array(
                    $merchTitle,
                    $merchDescription,
                    $merchSoldOut,
                    $merchShowOrder,
                    $ppCode,
                    $merchId
            ));
}
?>
