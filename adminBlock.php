<form action="index.php?logout=yep" method="post"><input type="submit" value=" Logout " /></form><br /><br />
<div style="text-decoration:underline; cursor:pointer;" onclick="toggleview('users')">Manage users</div>
<div id="users" style="display:none; margin:0px 10px;">
    <table cellpadding="5px" cellspacing="2px" style="border:1px solid black;">
        <form action="index.php" method="post">
            <tr>
                <td><input type="text" name="fName" placeholder="First Name" /></td>
                <td><input type="text" name="email" placeholder="Email" /></td>
                <td><input type="text" name="pwd" placeholder="password" /></td>
                <td>New user</td>
                <td><input type="hidden" name="userup" value="new" /><input type="submit" value=" Go " /></td>
            </tr>
        </form>
        <?php
        $stmt = $db->prepare("SELECT id,email,fName FROM users");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $uid = $row['id'];
            $email = $row['email'];
            $fName = $row['fName'];
            echo "<form action='index.php' method='post'>";
            echo "<tr>";
            echo "<td><input type='text' name='fName' value='$fName' /></td>";
            echo "<td><input type='text' name='email' value='$email' /></td>";
            echo "<td><input type='text' name='pwd' placeholder='change password' /></td>";
            echo "<td><input type='checkbox' name='deluser' placeholder='1' />Del user</td>";
            echo "<td><input type='hidden' name='userup' value='$uid' /><input type='submit' value=' Go ' /></td>";
            echo "</tr>";
            echo "</form>";
        }
        ?>
    </table>
</div>
<div style="text-decoration:underline; cursor:pointer;" onclick="toggleview('merch')">Merch Items</div>
<div id="merch" style="display:none; margin:0px 10px;">
    <table>
        <?php
        $tdStyle = "border:1px solid #660000; padding:10px 20px;";
        $stmt2 = $db->prepare("SELECT COUNT(*) FROM merch");
        $stmt2->execute();
        $row2 = $stmt2->fetch();
        $merchCount = $row2[0];

        echo "<tr><td style='$tdStyle'><form action='index.php' method='post' enctype='multipart/form-data'>";
        echo "Title:<br /><input type='text' name='merchTitle' value='' size='30' /><br /><br />";
        echo "Description:<br /><textarea name='merchDescription' rows='5' cols='30'></textarea></td>";

        echo "<td style='$tdStyle'>";
        echo "Pic 1 not uploaded<br />";
        echo "<input type='file' name='image1m' /><br /><br />";
        echo "Pic 2 not uploaded<br />";
        echo "<input type='file' name='image2m' /></td>";

        echo "<td style='$tdStyle'>Display Order: <select name='merchShowOrder' size='1'>";
        for ($i = 1; $i <= ($merchCount + 1); $i++) {
            echo "<option value='$i'>$i</option>/n";
        }
        echo "</select><br /><br />";
        echo "Show as sold out: <input type='checkbox' name='merchSoldOut' value='1' />";
        echo "<br /><br />Paypal Button Code:<br /><textarea name='ppCode' rows='5' cols='30'></textarea><br />";
        echo "<input type='hidden' name='merchUp' value='new' /><input type='submit' value=' Update ' /></form></td></tr>";
        echo "<tr><td style='$tdStyle' colspan='3'></td></tr>";

        $stmt1 = $db->prepare("SELECT * FROM merch ORDER BY merchShowOrder");
        $stmt1->execute();
        while ($row1 = $stmt1->fetch()) {
            $merchId = $row1['id'];
            $merchTitle = $row1['merchTitle'];
            $merchDescription = $row1['merchDescription'];
            $merchPic1 = $row1['merchPic1'];
            $merchPic1Ext = $row1['merchPic1Ext'];
            $merchPic2 = $row1['merchPic2'];
            $merchPic2Ext = $row1['merchPic2Ext'];
            $merchSoldOut = $row1['merchSoldOut'];
            $merchShowOrder = $row1['merchShowOrder'];
            $ppCode = $row1['ppCode'];

            echo "<tr><td style='$tdStyle'><form action='index.php' method='post' enctype='multipart/form-data'>";
            echo "Title:<br /><input type='text' name='merchTitle' value='$merchTitle' size='30' /><br /><br />";
            echo "Description:<br /><textarea name='merchDescription' rows='5' cols='30'>$merchDescription</textarea></td>";

            echo "<td style='$tdStyle'>";
            if (file_exists("merch/" . $merchPic1 . "." . $merchPic1Ext)) {
                echo "<img src='merch/" . $merchPic1 . "." . $merchPic1Ext . "' alt='' style='max-height:100px; max-width:100px;' /><br />";
            } else {
                echo "Pic 1 not uploaded<br />";
            }
            echo "<input type='file' name='image1m' />";
            if (file_exists("merch/" . $merchPic1 . "." . $merchPic1Ext)) {
                echo "<br />Delete Pic 1: <input type='checkbox' name='delPic1' value='1' />";
            }
            echo "<br /><br />";
            if (file_exists("merch/" . $merchPic2 . "." . $merchPic2Ext)) {
                echo "<img src='merch/" . $merchPic2 . "." . $merchPic2Ext . "' alt='' style='max-height:100px; max-width:100px;' /><br />";
            } else {
                echo "Pic 2 not uploaded<br />";
            }
            echo "<input type='file' name='image2m' />";
            if (file_exists("merch/" . $merchPic2 . "." . $merchPic2Ext)) {
                echo "<br />Delete Pic 2: <input type='checkbox' name='delPic2' value='1' />";
            }
            echo "</td>";

            echo "<td style='$tdStyle'>Display Order: <select name='merchShowOrder' size='1'>";
            for ($i = 1; $i <= $merchCount; $i++) {
                echo "<option value='$i'";
                if ($i == $merchShowOrder) {
                    echo " selected='selected'";
                }
                echo ">$i</option>/n";
            }
            echo "</select><br /><br />";
            echo "Show as sold out: <input type='checkbox' name='merchSoldOut' value='1'";
            echo ($merchSoldOut == '1') ? " checked='checked' />" : " />";
            echo "<br /><br />Paypal Button Code:<br /><textarea name='ppCode' rows='5' cols='30'>$ppCode</textarea><br />";
            echo "<input type='hidden' name='merchUp' value='$merchId' /><input type='submit' value=' Update ' /></form></td></tr>";
            echo "<tr><td style='$tdStyle' colspan='3'></td></tr>";
        }
        ?>
    </table>
</div>