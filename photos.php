<?php
if ($loggedin == "1") {
	$pic1Name = filter_input ( INPUT_POST, 'pic1Name', FILTER_SANITIZE_NUMBER_INT );
	$folder = "media/pics";
	$foldersm = "media/pics/thumbs";
	if (isset ( $_FILES ['image1'] ['name'] )) {
		$tmpFile = $_FILES ["image1"] ["tmp_name"];
		list ( $width, $height ) = (getimagesize ( $tmpFile ) != null) ? getimagesize ( $tmpFile ) : null;
		if ($width != null && $height != null) {
			$saveto = "$folder/$pic1Name.jpg";
			$savetosm = "$foldersm/$pic1Name" . ".jpg";
			$image = new Imagick ( $tmpFile );
			$image->thumbnailImage ( 600, 600, true );
			$image->writeImage ( $saveto );
			$image->thumbnailImage ( 100, 100, true );
			$image->writeImage ( $savetosm );
		}
	}

	if (filter_input ( INPUT_POST, 'picEdit', FILTER_SANITIZE_STRING )) {
		$pid = filter_input ( INPUT_POST, 'picEdit', FILTER_SANITIZE_STRING );
		$caption = filter_input ( INPUT_POST, 'caption', FILTER_SANITIZE_STRING );
		$cate = (filter_input ( INPUT_POST, 'newcat', FILTER_SANITIZE_NUMBER_INT ) == "1" && filter_input ( INPUT_POST, 'cat', FILTER_SANITIZE_STRING )) ? filter_input ( INPUT_POST, 'cat', FILTER_SANITIZE_STRING ) : filter_input ( INPUT_POST, 'category', FILTER_SANITIZE_STRING );
		$delpic = (filter_input ( INPUT_POST, 'delpic', FILTER_SANITIZE_NUMBER_INT ) == "1") ? "1" : "0";
		if ($delpic == "1") {
			$stmt2 = $db->prepare ( "SELECT picName FROM pictures WHERE id=?" );
			$stmt2->execute ( array (
					$pid
			) );
			$row2 = $stmt2->fetch ();
			if (file_exists ( "media/pics/" . $row2 ['picName'] . ".jpg" )) {
				unlink ( "media/pics/" . $row2 ['picName'] . ".jpg" );
				unlink ( "media/pics/thumbs/" . $row2 ['picName'] . ".jpg" );
			}
			$stmt3 = $db->prepare ( "UPDATE news SET pic1Id='0' WHERE pic1Id=?" );
			$stmt3->execute ( array (
					$pid
			) );
			$stmt4 = $db->prepare ( "UPDATE news SET pic2Id='0' WHERE pic2Id=?" );
			$stmt4->execute ( array (
					$pid
			) );
			$stmt = $db->prepare ( "DELETE FROM pictures WHERE id=?" );
			$stmt->execute ( array (
					$pid
			) );
		} else {
			if ($pid == "new") {
				$stmt = $db->prepare ( "INSERT INTO pictures VALUES" . "(NULL,?,?,?,'0','0','0')" );
				$stmt->execute ( array (
						$pic1Name,
						$cate,
						$caption
				) );
			} else {
				$stmt = $db->prepare ( "UPDATE pictures SET caption=?,category=? WHERE id=?" );
				$stmt->execute ( array (
						$caption,
						$cate,
						$pid
				) );
			}
		}
	}

	echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
	echo "<tr><td colspan='5'><form action='index.php' method='post' enctype='multipart/form-data'>New picture:<br /><input type='file' name='image1' /><br /><select name='category' size='1'>";
	$stmt3 = $db->prepare ( "SELECT DISTINCT category FROM pictures ORDER BY category" );
	$stmt3->execute ();
	while ( $row3 = $stmt3->fetch () ) {
		echo "<option value='" . $row3 ['category'] . "'>" . $row3 ['category'] . "</option>";
	}
	echo "</select><br /><input type='checkbox' name='newcat' value='1' /><input type='text' name='cat' placeholder='New category' /><br />";
	echo "<textarea name='caption' cols='20' rows='5' placeholder='caption'></textarea><br /><br />";
	echo "<input type='hidden' name='picEdit' value='new' /><input type='hidden' name='pic1Name' value='$time' /><input type='submit' value=' upload ' /></form></td></tr>";
	$stmt = $db->prepare ( "SELECT DISTINCT category FROM pictures ORDER BY RAND()" );
	$stmt->execute ();
	while ( $row = $stmt->fetch () ) {
		$cat = $row ['category'];
		echo "<tr><td colspan='2'><div style='text-align:center; color:#ffffff; font-weight:bold; font-size:1.5em;'>$cat</div></td><td colspan='3'>&nbsp;</td></tr>";
		$stmt2 = $db->prepare ( "SELECT id,picName,caption,category FROM pictures WHERE category=? ORDER BY RAND()" );
		$stmt2->execute ( array (
				$cat
		) );
		$t = 1;
		echo "<tr>";
		while ( $row2 = $stmt2->fetch () ) {
			$picId = $row2 ['id'];
			$picName = $row2 ['picName'];
			$caption = $row2 ['caption'];
			$category = $row2 ['category'];
			echo "<td style='width:148px;'><img src='media/pics/thumbs/$picName.jpg' alt='' style='padding:5px; border:1px solid #ffffff; margin:18px; max-width:100px;' /><br /><br />";
			echo "<form action='index.php' method='post'><select name='category' size='1'>";
			$stmt3 = $db->prepare ( "SELECT DISTINCT category FROM pictures ORDER BY category" );
			$stmt3->execute ();
			while ( $row3 = $stmt3->fetch () ) {
				echo "<option value='" . $row3 ['category'] . "'";
				if ($category == $row3 ['category'])
					echo " selected='selected'";
				echo ">" . $row3 ['category'] . "</option>";
			}
			echo "</select><br /><input type='checkbox' name='newcat' value='1' /><input type='text' name='cat' placeholder='New category' /><br />";
			echo "<textarea name='caption' cols='20' rows='5'>$caption</textarea><br /><br />";
			echo "<input type='checkbox' name='delpic' value='1' /> Delete pic<br /><br /><input type='hidden' name='picEdit' value='$picId' /><input type='submit' value=' Edit ' /></form></td>";
			if ($t % 4 == 0)
				echo "</tr><tr>";
			$t ++;
		}
		echo "</tr><tr><td colspan='5'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
	}
	echo "</table>";
} else {
	echo "<table style='width:740px; border:0px; margin:0px; padding:0px;'>";
	$stmt = $db->prepare ( "SELECT DISTINCT category FROM pictures ORDER BY RAND()" );
	$stmt->execute ();
	while ( $row = $stmt->fetch () ) {
		$cat = $row ['category'];
		echo "<tr><td colspan='2'><div style='text-align:center; color:#ffffff; font-weight:bold; font-size:1.5em;$subTitle'>$cat</div></td><td colspan='3'>&nbsp;</td></tr>";
		$stmt2 = $db->prepare ( "SELECT picName,caption FROM pictures WHERE category=? ORDER BY RAND()" );
		$stmt2->execute ( array (
				$cat
		) );
		$t = 1;
		echo "<tr>";
		while ( $row2 = $stmt2->fetch () ) {
			$picName = $row2 ['picName'];
			$caption = $row2 ['caption'];
			echo "<td align='center'><a href='media/pics/$picName.jpg' rel='lightbox[$cat]' title='$caption'><img src='media/pics/thumbs/$picName.jpg' alt='' style='padding:5px; border:1px solid #ffffff; margin:11px; max-width:100px;' /></a></td>";
			if ($t % 5 == 0)
				echo "</tr><tr>";
			$t ++;
		}
		echo "</tr><tr><td colspan='5'><div style='display:block; height:30px;'>&nbsp;</div></td></tr>";
	}
	echo "</table>";
}
?>
