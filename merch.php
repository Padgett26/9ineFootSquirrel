<table style="width:100%;">
    <tr>
        <td style="width:370px; position:relative;">
            <div style="color:#ffffff; text-align:left; font-weight:bold; font-size:3em; position:absolute; top:0px; left:10px;">
                stuff 4 you
            </div>
            <div style="position:absolute; top:70px; left:40px;">
                <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" >
                    <input type="hidden" name="cmd" value="_cart">
                    <input type="hidden" name="business" value="3HFBHGKGN9GVG">
                    <input type="hidden" name="display" value="1">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_viewcart_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>

            </div>
        </td>
        <?php
        $t = 0;
        $stmt1 = $db->prepare("SELECT * FROM merch ORDER BY merchSoldOut,merchShowOrder");
        $stmt1->execute();
        while ($row1 = $stmt1->fetch()) {
            $merchTitle = $row1['merchTitle'];
            $merchDescription = nl2br($row1['merchDescription']);
            $merchPic1 = $row1['merchPic1'];
            $merchPic1Ext = $row1['merchPic1Ext'];
            $merchPic2 = $row1['merchPic2'];
            $merchPic2Ext = $row1['merchPic2Ext'];
            $merchSoldOut = $row1['merchSoldOut'];
            $ppCode = $row1['ppCode'];

            if ($t != 0) {
                echo "<tr>";
            }
            echo "<td style='width:370px;' rowspan='2'>";
            echo "<div style='position:relative; top:0px; left:0px; width:370px; border:1px solid white;'>";
            echo "<header style='text-align:center; font-weight:bold; font-size:1.25em; margin-top:20px;'>$merchTitle</header>";
            if (file_exists("merch/" . $merchPic1 . "." . $merchPic1Ext)) {
                echo "<div style='margin:20px auto; width:80%;'><img src='merch/" . $merchPic1 . "." . $merchPic1Ext . "' alt='$merchTitle' style='width:96%; border:1px solid #ffffff; padding:5px;' /></div>";
            }
            echo "<article style='text-align:justify; padding:0px 20px;'>$merchDescription</article>";
            if (file_exists("merch/" . $merchPic2 . "." . $merchPic2Ext)) {
                echo "<div style='margin:20px auto; width:80%;'><img src='merch/" . $merchPic2 . "." . $merchPic2Ext . "' alt='$merchTitle' style='width:96%; border:1px solid #ffffff; padding:5px;' /></div>";
            }
            if ($merchSoldOut == '1') {
                echo "<div style='position:absolute; top:0px; left:0px; height:100%; width:100%; z-index:11;'><img src='merch/white.gif' style='width:100%; height:100%; opacity:0.25;filter:alpha(opacity=25)' /></div>";
                echo "<div style='position:absolute; top:70px; left:70px; font-size:6em; z-index:12; color:#660000; -ms-transform:rotate(-45deg); -webkit-transform:rotate(-45deg); transform:rotate(-45deg);'>Sold Out</div>";
            } else {
                echo "<article style='text-align:left; margin:20px 0px 0px 19px;'>$ppCode</article>";
            }
            echo "<br /><br /></div>";
            echo "</td></tr>";
            $t++;
        }

        echo "<tr><td>&nbsp;</td></tr>";
        ?>
    </tr>
</table>