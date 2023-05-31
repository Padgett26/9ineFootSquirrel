<?php
$disp = 1;
require_once ('js/recaptchalib.php');
$name = "";
$inquiry = "";
if (filter_input ( INPUT_POST, 'contactus', FILTER_SANITIZE_NUMBER_INT )) {
	$name = filter_input ( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
	$email = filter_input ( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
	$inquiry = filter_input ( INPUT_POST, 'inquiry', FILTER_SANITIZE_STRING );
	$emtext = nl2br ( $inquiry );
	$created = date ( "Y-m-d" );

	$privatekey = "6LfTpvMSAAAAAPut6lUmyWNrAoizTcz3HZ1V-s4g";
	$resp = recaptcha_check_answer ( $privatekey, $_SERVER ["REMOTE_ADDR"], $_POST ["recaptcha_challenge_field"], $_POST ["recaptcha_response_field"] );

	if (! $resp->is_valid) {
		echo "The reCAPTCHA wasn't entered correctly. Go back and try it again." . "(reCAPTCHA said: " . $resp->error . ")";
		$disp = 1;
	} else {
		if ($email) {
			$stmt = $db->prepare ( "INSERT INTO contact VALUES" . "(NULL,?,?,?,?,'0','0')" );
			$stmt->execute ( array (
					$name,
					$email,
					$inquiry,
					$created
			) );
			$message = "
        <html><head></head><body>
        Inquiry sent in from:<br><br>
        $name<br>
        <a href='mailto:$email'>$email</a><br><br>
        $emtext
        </body></html>";
			// In case any of our lines are larger than 70 characters, we should use wordwrap()
			$message = wordwrap ( $message, 70 );
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
			$headers .= "From: $name <$email>" . "\r\n";
			// Send
			mail ( 'fredd311@yahoo.com', 'An inquiry from the 9fs Website', $message, $headers );
			echo "Thank you for your inquiry.  You will receive a response soon.<br><br>";
			$disp = 0;
		} else {
			echo "Your email address did not verify, please check the email address you entered.<br><br>";
			$disp = 1;
		}
	}
}
if ($disp == 1) {
	echo "<form action='index.php' method='post'>
                <table cellpadding='0' cellspacing='0' border='0'>
                    <tr>
                        <td>
                            Name: <input type='text' name='name' max-length='250' size='25' value='$name' /><br><br>
                            Email: <input type='text' name='email' max-length='250' size='25' /><br>
                        </td>
                        <td>";
	$publickey = "6LfTpvMSAAAAAA59JE98r8dqqCwTM3wEAoxhr6ql";
	echo recaptcha_get_html ( $publickey );
	echo "</td>
                    </tr>
                    <tr>
                        <td colspan='2'>
                            Inquiry:<br><textarea name='inquiry' cols='85' rows='6'>$inquiry</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' align='center'>
                            <input type='hidden' name='contactus' value='1' /><input type='submit' value=' SEND ' />
                        </td>
                    </tr>
                </table>
            </form>";
}
?>
