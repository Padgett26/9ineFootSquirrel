<?php
include "functions.php";

if (filter_input ( INPUT_GET, 'logout', FILTER_SANITIZE_STRING )) {
	destroySession ();
}

$page = (filter_input ( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) && file_exists ( filter_input ( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) . ".php" )) ? filter_input ( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) : "home";

$lid = "0";

if (filter_input ( INPUT_POST, 'hi', FILTER_SANITIZE_EMAIL )) {
	$email = filter_input ( INPUT_POST, 'hi', FILTER_SANITIZE_EMAIL );
	$stmt = $db->prepare ( "SELECT COUNT(*),id,fName FROM users WHERE email=?" );
	$stmt->execute ( array (
			$email
	) );
	$row = $stmt->fetch ();
	if ($row [0] == "1") {
		$fName = $row ['fName'];
		$lid = $row ['id'];
		$cryptid = hash ( 'sha512', ("9fs" . $lid), FALSE );
	}
}

if (filter_input ( INPUT_POST, 'pwd', FILTER_SANITIZE_STRING )) {
	$pass = filter_input ( INPUT_POST, 'pwd', FILTER_SANITIZE_STRING );
	$cryptid = filter_input ( INPUT_POST, 'i', FILTER_SANITIZE_STRING );
	$stmt = $db->prepare ( "SELECT id,pwd,salt FROM users" );
	$stmt->execute ();
	while ( $row = $stmt->fetch () ) {
		$mid = $row ['id'];
		$pwd = $row ['pwd'];
		$salt = $row ['salt'];
		if ($cryptid === hash ( 'sha512', ("9fs" . $mid), FALSE )) {
			if ($pwd === hash ( 'sha512', ($salt . $pass), FALSE )) {
				$_SESSION ['loggedin'] = "1";
			}
		}
	}
}

$loggedin = (isset ( $_SESSION ['loggedin'] ) && $_SESSION ['loggedin'] == "1") ? "1" : "0";

if ($loggedin == "1") {
	include "adminProcessing.php";
}

$titleStyle = " font-size:6em; letter-spacing:10px; font-family: 'supafly';";
$subTitle = " font-family: \"supafly\"; letter-spacing:3px;";
?>
<!DOCTYPE HTML>
<html>
<head>
<title>9ine Foot Squirrel</title>
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/lightbox.js"></script>
<link href="css/lightbox.css" rel="stylesheet" />
<link href='http://fonts.googleapis.com/css?family=Eater'
	rel='stylesheet' type='text/css'>
        <?php
								if ($page == "songs") {
									echo '<script type="text/javascript" src="js/usableforms.js"></script>';
								}
								?>
        <style type="text/css">
body {
	background-color: #660000;
	background-image: url('images/gradiant.gif');
	background-repeat: repeat-x;
	font-family: Arial;
	color: #ffffff;
}

a {
	text-decoration: none;
	color: #ffffff;
}

input {
	background-color: #880000;
	color: #ffffff;
}

textarea {
	background-color: #880000;
	color: #ffffff;
}

@font-face {
	font-family: supafly;
	src: url('images/Supafly_36.ttf');
}
</style>
<script type="text/javascript">
            function toggleview(itm)
            {
                var itmx = document.getElementById(itm);
                if (itmx.style.display === "none")
                {
                    itmx.style.display = "block";
                }
                else
                {
                    itmx.style.display = "none";
                }
            }
        </script>
</head>
<body>
	<img src="images/back.gif" alt=""
		style="position: fixed; top: 0px; left: 0px; z-index: -999; padding: 0px; margin: 0px;" />
	<img src="images/back.gif" alt=""
		style="position: fixed; top: 0px; left: 600px; z-index: -999; padding: 0px; margin: 0px;" />
	<img src="images/back.gif" alt=""
		style="position: fixed; top: 0px; left: 1200px; z-index: -999; padding: 0px; margin: 0px;" />
	<img src="images/back.gif" alt=""
		style="position: fixed; top: 0px; left: 1800px; z-index: -999; padding: 0px; margin: 0px;" />
	<table cellpadding="0" cellspacing="0" style="width: 100%;">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" style="width: 1020px;">
					<tr>
						<td colspan="3">
							<div class="showblock" style="text-align:center; font-weight:bold; color:#660000; margin:20px 0px; text-shadow:6px 6px 6px #cccccc;<?php

							echo $titleStyle;
							?>">9ine Foot Squirrel</div>
                                <?php
																																if ($lid != "0") {
																																	?>
                                    <div
								style="padding: 5px 0px; background-color: #220000; text-align: center; color: #ffffff; margin-bottom: 20px; border: 1px solid #220000; border-radius: 25px;">
								<form action="index.php" method="post">Hello<?php

																																	echo " $fName";
																																	?>, whatcha know?..<input
										type="password" name="pwd" />&nbsp;&nbsp;<input type='hidden'
										name='i'
										value='<?php

																																	echo $cryptid;
																																	?>' /><input type="submit" value="\m/" />
								</form>
							</div>
                                    <?php
																																} else {
																																	if ($loggedin == "1") {
																																		?>
                                        <div class="block"
								style="padding: 20px; background-color: #220000; text-align: center; color: #ffffff; margin-bottom: 20px; border: 1px solid #220000; border-radius: 25px;">
                                            <?php
																																		include "adminBlock.php";
																																		?>
                                        </div>
                                        <?php
																																	} else {
																																		?>
                                        <div class="block"
								style="padding: 5px 0px; background-color: #220000; text-align: center; color: #ffffff; margin-bottom: 20px; border: 1px solid #220000; border-radius: 25px;">
								<form action="index.php" method="post">
									Hi...<input type="text" name="hi" />&nbsp;&nbsp;<input
										type="submit" value="\m/" />
								</form>
							</div>
                                        <?php
																																	}
																																}
																																?>
                            </td>
					</tr>
					<tr>
						<td colspan="3" align="center">
							<div
								style="display: block; width: 1000px; height: 30px; background-color: #660000; border: 1px solid #660000; border-radius: 25px; text-align: center; margin-bottom: 30px;">
								<span style="position: relative; top: 5px;"> <a
									href="index.php?page=home">Home</a>&nbsp;&nbsp;&nbsp;&nbsp; <img
									src="images/tribal<?php
									echo rand ( 1, 9 );
									?>.gif"
									alt="" style="width: 15px;" /> &nbsp;&nbsp;&nbsp;&nbsp;<a
									href="index.php?page=schedule">Gigs</a>&nbsp;&nbsp;&nbsp;&nbsp;
									<img
									src="images/tribal<?php
									echo rand ( 1, 9 );
									?>.gif"
									alt="" style="width: 15px;" /> &nbsp;&nbsp;&nbsp;&nbsp;<a
									href="index.php?page=songs">Songs</a>&nbsp;&nbsp;&nbsp;&nbsp; <img
									src="images/tribal<?php
									echo rand ( 1, 9 );
									?>.gif"
									alt="" style="width: 15px;" /> &nbsp;&nbsp;&nbsp;&nbsp;<a
									href="index.php?page=videos">Videos</a>&nbsp;&nbsp;&nbsp;&nbsp;
									<img
									src="images/tribal<?php
									echo rand ( 1, 9 );
									?>.gif"
									alt="" style="width: 15px;" /> &nbsp;&nbsp;&nbsp;&nbsp;<a
									href="index.php?page=photos">Photos</a>&nbsp;&nbsp;&nbsp;&nbsp;
									<img
									src="images/tribal<?php
									echo rand ( 1, 9 );
									?>.gif"
									alt="" style="width: 15px;" /> &nbsp;&nbsp;&nbsp;&nbsp;<a
									href="index.php?page=bio">Bio</a>&nbsp;&nbsp;&nbsp;&nbsp; <img
									src="images/tribal<?php
									echo rand ( 1, 9 );
									?>.gif"
									alt="" style="width: 15px;" /> &nbsp;&nbsp;&nbsp;&nbsp;<a
									href="index.php?page=merch">Merch</a>&nbsp;&nbsp;&nbsp;&nbsp; <img
									src="images/tribal<?php
									echo rand ( 1, 9 );
									?>.gif"
									alt="" style="width: 15px;" /> &nbsp;&nbsp;&nbsp;&nbsp;<a
									href="index.php?page=contact">Contact us</a></span>
							</div>
						</td>
					</tr>
					<tr>
						<td style="width: 110px; vertical-align: top;">
                                <?php
																																$stmt = $db->prepare ( "SELECT * FROM schedule ORDER BY year,month,day" );
																																$stmt->execute ();
																																$t = 1;
																																while ( $row = $stmt->fetch () ) {
																																	if ($time < mktime ( 23, 59, 59, $row ['month'], $row ['day'], $row ['year'] ) && $t <= 6) {
																																		if ($t == 1)
																																			echo "<div style='text-align:center; font-weight:bold; font-size:1.25em; margin-bottom:20px;'>Upcoming<br>shows</div>";
																																		else
																																			echo "<div><img src='images/tribal" . rand ( 1, 9 ) . ".gif' alt='' style='margin:10px 0px 10px 45px; width:20px;' /></div>";
																																		$month = $row ['month'];
																																		$day = $row ['day'];
																																		$venue = $row ['venue'];
																																		$venueLink = $row ['venueLink'];
																																		$ticketsLink = $row ['ticketsLink'];
																																		echo "<div style='text-align:center;'>";
																																		echo "<span style='text-decoration:underline;'>$day $monthNames[$month]</span><br>";
																																		echo ($venueLink != "0") ? "<a href='$venueLink' target='_blank'>$venue</a>" : "$venue";
																																		echo ($ticketsLink != "0") ? "<br><a href='$ticketsLink' target='_blank' style='text-decoration:underline; font-size:.8em;'>Get tickets</a>" : "";
																																		echo "</div>";
																																		$t ++;
																																	}
																																}
																																?>
                            </td>
						<td style="width: 800px; vertical-align: top;">
							<div
								style="display: block; width: 740px; margin: 9px; padding: 20px; border: 1px solid #ffffff; border-radius: 25px; background-color: #220000; color: #ffffff;">
                                    <?php
																																				include $page . ".php";
																																				?>
                                </div>
						</td>
						<td style="width: 110px; vertical-align: top;">
							<table cellpadding="0" cellspacing="0" border="0">
                                    <?php
																																				$stmt = $db->prepare ( "SELECT picName,caption FROM pictures ORDER BY RAND() LIMIT 6" );
																																				$stmt->execute ();
																																				$s = 1;
																																				while ( $row = $stmt->fetch () ) {
																																					$picName = $row ['picName'];
																																					$caption = $row ['caption'];
																																					if ($s != 1)
																																						echo "<tr><td align='center'><img src='images/tribal" . rand ( 1, 9 ) . ".gif' alt='' style='margin:10px 0px; width:20px;' /></td></tr>";
																																					echo "<tr><td align='center'><a href='index.php?page=photos'><img src='media/pics/thumbs/$picName.jpg' alt='$caption' title='$caption' style='max-width:100px; margin:5px; border:0px; padding:0px;' /></a></td></tr>";
																																					$s ++;
																																				}
																																				?>
                                    <tr>
									<td align="center"><a
										href="http://www.reverbnation.com/ninefootsquirrel?page_view_source=facebook_app"
										target="_blank"><img src="images/rn.png" alt="Reverbnation"
											style="width: 60px; margin-top: 20px;" /></a> <a
										href="https://www.facebook.com/pages/9ine-foot-squirrel-official-band-page/39205032776"
										target="_blank"><img src="images/fb2.png" alt="Facebook"
											style="width: 30px; margin-top: 20px;" /></a><br> <a
										href="https://www.youtube.com/user/TheFlywh33l"
										target="_blank"><img src="images/yt.png" alt="YouTube"
											style="width: 40px; margin-top: 10px;" /></a></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="width: 100%;">
							<div
								style="text-align: center; margin-top: 20px; font-weight: bold; color: #000000; text-shadow: 3px 3px 3px #cccccc; font-size: 2em; letter-spacing: 10px; font-family: 'supafly';">
                                    <?php
																																				$vstmt = $db->prepare ( "SELECT visitors FROM siteSettings WHERE id='1'" );
																																				$vstmt->execute ();
																																				$vrow = $vstmt->fetch ();
																																				$visitors = $vrow ['visitors'];
																																				echo $visitors . " visitors";

																																				if (filter_input ( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION )) {
																																					$ipAddress = filter_input ( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
																																					$recent = ($time - 86400);
																																					$vst = $db->prepare ( "DELETE FROM visitorLog WHERE visitTime <= ?" );
																																					$vst->execute ( array (
																																							$recent
																																					) );
																																					$vst1 = $db->prepare ( "SELECT COUNT(*) FROM visitorLog WHERE ipAddress = ?" );
																																					$vst1->execute ( array (
																																							$ipAddress
																																					) );
																																					$vst1row = $vst1->fetch ();
																																					if ($vst1row [0] == 0) {
																																						$vst2 = $db->prepare ( "INSERT INTO visitorLog VALUES(NULL, ?, ?, '0', '0', '0')" );
																																						$vst2->execute ( array (
																																								$ipAddress,
																																								$time
																																						) );
																																						$vst3 = $db->prepare ( "UPDATE siteSettings SET visitors = visitors + 1 WHERE id = '1'" );
																																						$vst3->execute ();
																																					}
																																				}
																																				?>
                                </div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<script>
            $(document).ready(function () {
                $('.block').hide();
                $('.showblock').toggle(
                        function () {
                            $(this).next('.block').slideDown();
                        },
                        function () {
                            $(this).next('.block').slideUp();
                        }
                );
            });
        </script>
</body>
</html>
