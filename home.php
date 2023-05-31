<?php
if ($loggedin == "1") {
	if (filter_input ( INPUT_POST, 'confdelpost', FILTER_SANITIZE_NUMBER_INT )) {
		$id = filter_input ( INPUT_POST, 'confdelpost', FILTER_SANITIZE_NUMBER_INT );
		$stmt = $db->prepare ( "DELETE FROM news WHERE id=?" );
		$stmt->execute ( array (
				$id
		) );
		echo "Post deleted...";
	}

	$folder = "media/pics";
	$foldersm = "media/pics/thumbs";
	$pic1Name = "0";
	$pic2Name = "0";
	if (isset ( $_FILES ['image1'] ['name'] )) {
		$tmpFile = $_FILES ["image1"] ["tmp_name"];
		list ( $width, $height ) = (getimagesize ( $tmpFile ) != null) ? getimagesize ( $tmpFile ) : null;
		if ($width != null && $height != null) {
			$pic1Name = filter_input ( INPUT_POST, 'pic1Name', FILTER_SANITIZE_NUMBER_INT );
			$saveto = "$folder/$pic1Name.jpg";
			$savetosm = "$foldersm/$pic1Name" . ".jpg";
			$image = new Imagick ( $tmpFile );
			$image->thumbnailImage ( 600, 600, true );
			$image->writeImage ( $saveto );
			$image->thumbnailImage ( 100, 100, true );
			$image->writeImage ( $savetosm );
		}
	}

	if (isset ( $_FILES ['image2'] ['name'] )) {
		$tmpFile = $_FILES ["image2"] ["tmp_name"];
		list ( $width, $height ) = (getimagesize ( $tmpFile ) != null) ? getimagesize ( $tmpFile ) : null;
		if ($width != null && $height != null) {
			$pic2Name = filter_input ( INPUT_POST, 'pic2Name', FILTER_SANITIZE_NUMBER_INT );
			$saveto = "$folder/$pic2Name.jpg";
			$savetosm = "$foldersm/$pic2Name" . ".jpg";
			$image = new Imagick ( $tmpFile );
			$image->thumbnailImage ( 600, 600, true );
			$image->writeImage ( $saveto );
			$image->thumbnailImage ( 100, 100, true );
			$image->writeImage ( $savetosm );
		}
	}

	if (filter_input ( INPUT_POST, 'postnote', FILTER_SANITIZE_STRING )) {
		$id = filter_input ( INPUT_POST, 'postnote', FILTER_SANITIZE_STRING );
		$title = filter_input ( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
		$pic1Caption = filter_input ( INPUT_POST, 'pic1Caption', FILTER_SANITIZE_STRING );
		$pic2Caption = filter_input ( INPUT_POST, 'pic2Caption', FILTER_SANITIZE_STRING );
		$article = filter_input ( INPUT_POST, 'article', FILTER_SANITIZE_STRING );
		$p1N = ($pic1Name != "0") ? filter_input ( INPUT_POST, 'pic1Name', FILTER_SANITIZE_NUMBER_INT ) : '0';
		$p2N = ($pic2Name != "0") ? filter_input ( INPUT_POST, 'pic2Name', FILTER_SANITIZE_NUMBER_INT ) : '0';
		$delpic1 = (filter_input ( INPUT_POST, 'delpic1', FILTER_SANITIZE_NUMBER_INT ) == '1') ? '1' : '0';
		$delpic2 = (filter_input ( INPUT_POST, 'delpic2', FILTER_SANITIZE_NUMBER_INT ) == '1') ? '1' : '0';
		$delpost = (filter_input ( INPUT_POST, 'delpost', FILTER_SANITIZE_NUMBER_INT ) == '1') ? '1' : '0';
		if ($id == "new") {
			$pic1Id = "0";
			$pic2Id = "0";
			if ($p1N != "0") {
				$stmt = $db->prepare ( "INSERT INTO pictures VALUES" . "(NULL,?,?,?,'0','0','0')" );
				$stmt->execute ( array (
						$p1N,
						"News Feed",
						$pic1Caption
				) );
				$stmt2 = $db->prepare ( "SELECT id FROM pictures WHERE picName = ?" );
				$stmt2->execute ( array (
						$p1N
				) );
				$row2 = $stmt2->fetch ();
				$pic1Id = $row2 ['id'];
			}
			if ($p2N != "0") {
				$stmt = $db->prepare ( "INSERT INTO pictures VALUES" . "(NULL,?,?,?,'0','0','0')" );
				$stmt->execute ( array (
						$p2N,
						"News Feed",
						$pic2Caption
				) );
				$stmt2 = $db->prepare ( "SELECT id FROM pictures WHERE picName = ?" );
				$stmt2->execute ( array (
						$p2N
				) );
				$row2 = $stmt2->fetch ();
				$pic2Id = $row2 ['id'];
			}
			$stmt = $db->prepare ( "INSERT INTO news VALUES" . "(NULL,?,?,?,?,?,'0','0','0')" );
			$stmt->execute ( array (
					$title,
					$article,
					$pic1Id,
					$pic2Id,
					$time
			) );
			echo "Post added...";
		} else {
			if ($delpost == "1")
				echo "Are you sure you want to delete this post? <form action='index.php' method='post'><input type='hidden' name='confdelpost' value='$id' /><input type='submit' value=' YES ' /></form> <form action='index.php?page=home' method='post'><input type='submit' value=' NO ' /></form>";
			else {
				if ($delpic1 == '1') {
					$stmt = $db->prepare ( "SELECT pic1Id FROM news WHERE id=?" );
					$stmt->execute ( array (
							$id
					) );
					$row = $stmt->fetch ();
					if ($row ['pic1Id'] != "0") {
						$stmt3 = $db->prepare ( "UPDATE news SET pic1Id=? WHERE id=?" );
						$stmt3->execute ( array (
								"0",
								$id
						) );
					}
				}
				if ($delpic2 == '1') {
					$stmt = $db->prepare ( "SELECT pic2Id FROM news WHERE id=?" );
					$stmt->execute ( array (
							$id
					) );
					$row = $stmt->fetch ();
					if ($row ['pic2Id'] != "0") {
						$stmt3 = $db->prepare ( "UPDATE news SET pic2Id=? WHERE id=?" );
						$stmt3->execute ( array (
								"0",
								$id
						) );
					}
				}
				$stmt = $db->prepare ( "UPDATE news SET title=?,article=? WHERE id=?" );
				$stmt->execute ( array (
						$title,
						$article,
						$id
				) );
				if ($pic1Name != '0') {
					$stmt5 = $db->prepare ( "INSERT INTO pictures VALUES" . "(NULL,?,?,?,'0','0','0')" );
					$stmt5->execute ( array (
							$pic1Name,
							"News Feed",
							$pic1Caption
					) );
					$stmt6 = $db->prepare ( "SELECT id FROM pictures WHERE picName = ?" );
					$stmt6->execute ( array (
							$pic1Name
					) );
					$row6 = $stmt6->fetch ();
					$pic1Id = $row6 ['id'];
					$stmt3 = $db->prepare ( "UPDATE news SET pic1Id=? WHERE id=?" );
					$stmt3->execute ( array (
							$pic1Id,
							$id
					) );
				}
				if ($pic2Name != '0') {
					$stmt = $db->prepare ( "SELECT pic2Id FROM news WHERE id=?" );
					$stmt->execute ( array (
							$id
					) );
					$row = $stmt->fetch ();
					if ($row ['pic2Id'] != "0") {
						$stmt5 = $db->prepare ( "INSERT INTO pictures VALUES" . "(NULL,?,?,?,'0','0','0')" );
						$stmt5->execute ( array (
								$pic2Name,
								"News Feed",
								$pic2Caption
						) );
						$stmt6 = $db->prepare ( "SELECT id FROM pictures WHERE picName = ?" );
						$stmt6->execute ( array (
								$pic2Name
						) );
						$row6 = $stmt6->fetch ();
						$pic2Id = $row6 ['id'];
						$stmt3 = $db->prepare ( "UPDATE news SET pic2Id=? WHERE id=?" );
						$stmt3->execute ( array (
								$pic2Id,
								$id
						) );
					}
				}
				echo "Post updated...<br />";
			}
		}
	}

	echo "<div style='color:#ffffff; font-weight:bold; font-size:1em; text-decoration:none; font-family:sans-serif;'>\n";

	echo "<div class='showblock' style='cursor:pointer; text-decoration:underline;'>Insert a new post:</div>\n";
	echo "<div class='block' style='margin-top:10px;'><form action='index.php' method='post' enctype='multipart/form-data'>\n";
	echo "Title: <input type='text' name='title' maxlength='240' /><br />\n";
	echo "Article:<br />\n";
	echo "<textarea name='article' cols='75' rows='15'></textarea><br /><br />\n";
	echo "Insert picture 1: <input type='file' name='image1' /><input type='hidden' name='pic1Name' value='" . $time . "1' /><br />";
	echo "<textarea name='pic1Caption' rows='5' cols='50' placeholder='caption'></textarea><br /><br />";
	echo "Insert picture 2: <input type='file' name='image2' /><input type='hidden' name='pic2Name' value='" . $time . "2' /><br />";
	echo "<textarea name='pic2Caption' rows='5' cols='50' placeholder='caption'></textarea><br /><br />";
	echo "<input type='hidden' name='postnote' value='new' />\n";
	echo "<input type='submit' value=' Upload ' /></form></div></div><br /><br />\n";
}
$tstart = (filter_input ( INPUT_GET, 'showAll', FILTER_SANITIZE_NUMBER_INT ) == "1") ? "0" : ($time - (52 * 7 * 24 * 60 * 60));
$stmt = $db->prepare ( "SELECT * FROM news WHERE postedDate BETWEEN $tstart AND $time ORDER BY postedDate DESC" );
$stmt->execute ();
while ( $row = $stmt->fetch () ) {
	$id = $row ['id'];
	$title = $row ['title'];
	$article = nl2br ( $row ['article'] );
	$pic1Id = $row ['pic1Id'];
	$pic2Id = $row ['pic2Id'];
	$postedDate = date ( "j M Y", $row ['postedDate'] );
	$stmt2 = $db->prepare ( "SELECT picName,caption FROM pictures WHERE id=?" );
	$stmt2->execute ( array (
			$pic1Id
	) );
	$row2 = $stmt2->fetch ();
	if ($row2) {
		$pic1 = $row2 ['picName'];
		$pic1Caption = nl2br ( $row2 ['caption'] );
	} else {
		$pic1 = "x";
		$pic1Caption = "";
	}
	$stmt3 = $db->prepare ( "SELECT picName,caption FROM pictures WHERE id=?" );
	$stmt3->execute ( array (
			$pic2Id
	) );
	$row3 = $stmt3->fetch ();
	if ($row3) {
		$pic2 = $row3 ['picName'];
		$pic2Caption = nl2br ( $row3 ['caption'] );
	} else {
		$pic2 = "x";
		$pic2Caption = "";
	}
	if ($loggedin == "1") {
		echo "<div style='color:#ffffff; font-weight:bold; font-size:1em; text-decoration:none; font-family:sans-serif;'>\n";
		echo "<div class='showblock'>Edit this post: <span style='text-decoration:underline; cursor:pointer;'>$postedDate $title</span></div>\n";
		echo "<div class='block' style='margin-top:10px;'><form action='index.php' method='post' enctype='multipart/form-data'>Title: <input type='text' name='title' maxlength='190' value='$title' /><br /><br />\n";
		echo "Article:<br />\n";
		echo "<textarea name='article' cols='75' rows='15'>" . $row ['article'] . "</textarea><br /><br />\n";
		echo "Picture 1:<br />";
		if (file_exists ( "media/pics/$pic1.jpg" ))
			echo "<img src='media/pics/thumbs/$pic1.jpg' alt='' /><br /><input type='checkbox' name='delpic1' value='1' /> Delete this pic<br />";
		echo "<input type='file' name='image1' /><input type='hidden' name='pic1Name' value='" . $time . "1' /><br />";
		echo "Caption:<br /><textarea name='pic1Caption' rows='5' cols='50'>" . $row2 ['caption'] . "</textarea><br /><br />";
		echo "Picture 2:<br />";
		if (file_exists ( "media/pics/$pic2.jpg" ))
			echo "<img src='media/pics/thumbs/$pic2.jpg' alt='' /><br /><input type='checkbox' name='delpic2' value='1' /> Delete this pic<br />";
		echo "<input type='file' name='image2' /><input type='hidden' name='pic2Name' value='" . $time . "2' /><br />";
		echo "Caption:<br /><textarea name='pic2Caption' rows='5' cols='50'>" . $row3 ['caption'] . "</textarea><br /><br />";
		echo "Delete this post: <input type='checkbox' name='delpost' value='1' /><br /><br />\n";
		echo "<input type='hidden' name='postnote' value='$id' />\n";
		echo "<input type='submit' value=' Upload ' /></form></div></div><br />\n";
	} else {
		echo "<table cellpadding='0' cellspacing='0' style='border:none;'><tr><td style='width:740px;'><div style='color:#ffffff; text-align:center; font-weight:bold; font-size:1.5em; padding:10px; text-decoration:underline;$subTitle'>$title</div>\n";
		if (file_exists ( "media/pics/$pic1.jpg" ))
			echo "<img id='h$pic1' src='media/pics/$pic1.jpg' alt='' style='float:right; margin:10px 0px 10px 10px; max-width:400px;' />";
		echo "<div style='color:#ffffff; text-align:justify; padding:10px;'>$article</div>";
		if (file_exists ( "media/pics/$pic2.jpg" ))
			echo "<img src='media/pics/$pic2.jpg' alt='' style='margin:10px 70px;' />";
		echo "</td></tr></table><br /><br />\n";
	}
}
$stmt = $db->prepare ( "SELECT COUNT(*) FROM news WHERE postedDate < ?" );
$stmt->execute ( array (
		$tstart
) );
$row = $stmt->fetch ();
if ($row [0] >= 1)
	echo "<div style='text-align:center; font-weight:bold; color:#ffffff;'><a href='index.php?page=home&showAll=1'>Show All</a></div>";
else {
	if ($tstart == "0") {
		$t = ($time - (52 * 7 * 24 * 60 * 60));
		$stmt = $db->prepare ( "SELECT COUNT(*) FROM news WHERE postedDate < ?" );
		$stmt->execute ( array (
				$t
		) );
		$row = $stmt->fetch ();
		if ($row [0] >= 1)
			echo "<div style='text-align:center; font-weight:bold; color:#ffffff;'><a href='index.php?page=home&showAll=0'>Show Less</a></div>";
	}
}
?>
