<?php
if ($loggedin == "1") {

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

	if (filter_input ( INPUT_POST, 'postnote', FILTER_SANITIZE_NUMBER_INT )) {
		$id = filter_input ( INPUT_POST, 'postnote', FILTER_SANITIZE_NUMBER_INT );
		$title = filter_input ( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
		$pic1Caption = filter_input ( INPUT_POST, 'pic1Caption', FILTER_SANITIZE_STRING );
		$pic2Caption = filter_input ( INPUT_POST, 'pic2Caption', FILTER_SANITIZE_STRING );
		$article = filter_input ( INPUT_POST, 'article', FILTER_SANITIZE_STRING );
		$delpic1 = (filter_input ( INPUT_POST, 'delpic1', FILTER_SANITIZE_NUMBER_INT ) == '1') ? '1' : '0';
		$delpic2 = (filter_input ( INPUT_POST, 'delpic2', FILTER_SANITIZE_NUMBER_INT ) == '1') ? '1' : '0';
		if ($delpic1 == '1') {
			$stmt = $db->prepare ( "SELECT pic1Id FROM bio WHERE id=?" );
			$stmt->execute ( array (
					$id
			) );
			$row = $stmt->fetch ();
			if ($row ['pic1Id'] != "0") {
				$stmt3 = $db->prepare ( "UPDATE bio SET pic1Id=? WHERE id=?" );
				$stmt3->execute ( array (
						"0",
						$id
				) );
			}
		}
		if ($delpic2 == '1') {
			$stmt = $db->prepare ( "SELECT pic2Id FROM bio WHERE id=?" );
			$stmt->execute ( array (
					$id
			) );
			$row = $stmt->fetch ();
			if ($row ['pic2Id'] != "0") {
				$stmt3 = $db->prepare ( "UPDATE bio SET pic2Id=? WHERE id=?" );
				$stmt3->execute ( array (
						"0",
						$id
				) );
			}
		}
		$stmt = $db->prepare ( "UPDATE bio SET title=?,article=? WHERE id=?" );
		$stmt->execute ( array (
				$title,
				$article,
				$id
		) );
		if ($pic1Name != '0') {
			$stmt5 = $db->prepare ( "INSERT INTO pictures VALUES" . "(NULL,?,?,?,'0','0','0')" );
			$stmt5->execute ( array (
					$pic1Name,
					"Bio",
					$pic1Caption
			) );
			$stmt6 = $db->prepare ( "SELECT id FROM pictures WHERE picName = ?" );
			$stmt6->execute ( array (
					$pic1Name
			) );
			$row6 = $stmt6->fetch ();
			$pic1Id = $row6 ['id'];
			$stmt3 = $db->prepare ( "UPDATE bio SET pic1Id=? WHERE id=?" );
			$stmt3->execute ( array (
					$pic1Id,
					$id
			) );
		}
		if ($pic2Name != '0') {
			$stmt5 = $db->prepare ( "INSERT INTO pictures VALUES" . "(NULL,?,?,?,'0','0','0')" );
			$stmt5->execute ( array (
					$pic2Name,
					"Bio",
					$pic2Caption
			) );
			$stmt6 = $db->prepare ( "SELECT id FROM pictures WHERE picName = ?" );
			$stmt6->execute ( array (
					$pic2Name
			) );
			$row6 = $stmt6->fetch ();
			$pic2Id = $row6 ['id'];
			$stmt3 = $db->prepare ( "UPDATE bio SET pic2Id=? WHERE id=?" );
			$stmt3->execute ( array (
					$pic2Id,
					$id
			) );
		}
		echo "Post updated...<br />";
	}
}

$stmt = $db->prepare ( "SELECT * FROM bio WHERE id='1'" );
$stmt->execute ();
$row = $stmt->fetch ();
$id = $row ['id'];
$title = $row ['title'];
$article = nl2br ( $row ['article'] );
$pic1Id = $row ['pic1Id'];
$stmt2 = $db->prepare ( "SELECT picName,caption FROM pictures WHERE id=?" );
$stmt2->execute ( array (
		$pic1Id
) );
$row2 = $stmt2->fetch ();
if ($row2) {
	$pic1 = $row2 ['picName'];
	$pic1Caption = nl2br ( $row2 ['caption'] );
}
$pic2Id = $row ['pic2Id'];
$stmt3 = $db->prepare ( "SELECT picName,caption FROM pictures WHERE id=?" );
$stmt3->execute ( array (
		$pic2Id
) );
$row3 = $stmt3->fetch ();
if ($row3) {
	$pic2 = $row3 ['picName'];
	$pic2Caption = nl2br ( $row3 ['caption'] );
}
if ($loggedin == "1") {
	echo "<div style='color:#ffffff; font-weight:bold; font-size:1em; text-decoration:none; font-family:sans-serif;'>\n";
	echo "<div style='margin-top:10px;'><form action='index.php' method='post' enctype='multipart/form-data'>Title: <input type='text' name='title' maxlength='190' value='$title' /><br /><br />\n";
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
	echo "Caption:<br /><textarea name='pic2Caption' rows='5' cols='50'>" . $row3 ['caption'] . "</textarea><br /><br />\n";
	echo "<input type='hidden' name='postnote' value='$id' />\n";
	echo "<input type='submit' value=' Upload ' /></form></div></div><br />\n";
} else {
	echo "<div style='color:#ffffff; text-align:center; font-weight:bold; font-size:1.5em; padding:10px; text-decoration:underline;$subTitle'>$title</div>\n";
	if (file_exists ( "media/pics/$pic1.jpg" )) {
		echo "<img src='media/pics/$pic1.jpg' alt='' style='float:right; margin:10px 0px 10px 10px; max-width:400px;' />";
	}
	echo "<div style='color:#ffffff; text-align:justify; padding:10px;'>$article</div>";
	if (file_exists ( "media/pics/$pic2.jpg" )) {
		echo "<img src='media/pics/$pic2.jpg' alt='' style='margin:10px 70px;' />";
	}
	echo "<br /><br />\n";
}
?>
