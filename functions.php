<?php
session_start ();

$dbhost = 'localhost';
$dbname = 'ninefoot_site';
$dbuser = 'ninefoot_joe';
$dbpass = 'PloiK0989';

try {
	$db = new PDO ( "mysql:host=$dbhost; dbname=$dbname", "$dbuser", "$dbpass" );
} catch ( PDOException $e ) {
	echo "Could not connect to database";
}

$time = time ();
$monthNames = array (
		"0",
		"January",
		"February",
		"March",
		"April",
		"May",
		"June",
		"July",
		"August",
		"September",
		"October",
		"November",
		"December"
);
function destroySession() {
	$_SESSION = array ();

	if (ini_get ( "session.use_cookies" )) {
		$params = session_get_cookie_params ();
		setcookie ( session_name (), '', time () - 42000, $params ["path"], $params ["domain"], $params ["secure"], $params ["httponly"] );
	}

	session_destroy ();
}
function delTree($dir) {
	$files = array_diff ( scandir ( $dir ), array (
			'.',
			'..'
	) );
	foreach ( $files as $file ) {
		(is_dir ( "$dir/$file" )) ? delTree ( "$dir/$file" ) : unlink ( "$dir/$file" );
	}
	return rmdir ( $dir );
}
function getPicType($imageType) {
	switch ($imageType) {
		case "image/gif" :
			$picExt = "gif";
			break;
		case "image/jpeg" :
			$picExt = "jpg";
			break;
		case "image/pjpeg" :
			$picExt = "jpg";
			break;
		case "image/png" :
			$picExt = "png";
			break;
		default :
			$picExt = "xxx";
			break;
	}
	return $picExt;
}
function processPic($imageName, $tmpFile, $picExt) {
	$saveto = "merch/$imageName.$picExt";

	list ( $width, $height ) = (getimagesize ( $tmpFile ) != null) ? getimagesize ( $tmpFile ) : null;
	if ($width != null && $height != null) {
		$image = new Imagick ( $tmpFile );
		$image->thumbnailImage ( 400, 400, true );
		$image->writeImage ( $saveto );
	}
}
function money($amt) {
	settype ( $amt, "float" );
	$fmt = new NumberFormatter ( 'en_US', NumberFormatter::CURRENCY );
	return $fmt->formatCurrency ( $amt, "USD" );
}