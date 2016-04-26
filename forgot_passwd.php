<?php include('pages/page_header.php'); ?>
<link href="css/flexbox.css" rel="stylesheet" type="text/css" />
<link href="css/forgot-pw.css" rel="stylesheet" type="text/css" />
<?php include('pages/page_meta.php'); ?>
<?php
	require_once('db/db_config.php');

	if(isset($_POST['login'])) {
		$emailAdd = filter_var($_POST['login'], FILTER_SANITIZE_EMAIL);
		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		$querryLogin = "SELECT * FROM userAccounts WHERE emailAdd = '$emailAdd' AND status = 'Active'";
		$resultLogin = mysql_query($querryLogin);
		if(!$resultLogin) {
			die ("Table access failed: " . mysql_error());
		} else {
			$rowLogin = mysql_num_rows($resultLogin);
			if($rowLogin == 1) {
				$dataLogin = mysql_fetch_assoc($resultLogin);
				$firstName = $dataLogin['firstName'];
				$emailAdd = $dataLogin['emailAdd'];
				$id = base64_encode($emailAdd);
				$hash = md5(uniqid(rand(), 1));

				require('PHPMailer_5.2.4/class.phpmailer.php');
			    $mail = new PHPMailer();

			    $mail->IsSMTP();  // telling the class to use SMTP
			    $mail->SMTPAuth = true; // SMTP authentication
			    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
				$mail->SMTPSecure = 'ssl';
			    $mail->Host = "smtp.gmail.com"; // SMTP server
			    $mail->Port = 465; // SMTP Port
			    $mail->Username = "mavewei@gmail.com"; // SMTP account username
			    $mail->Password   = "weibabyg5439";        // SMTP account password
			    $mail->SetFrom('admin@mandra.cf', 'Mandra Admin'); // FROM
			    $mail->AddReplyTo('admin@mandra.cf', 'Mandra Admin'); // Reply TO
			    $mail->AddAddress($emailAdd, $firstName); // recipient email
			    $mail->Subject = "Account Details Recovery - Password Reset"; // email subject
			    //$mail->Body = "Hi! \n\n This is my first e-mail sent through Google SMTP using PHPMailer.";
			    $mail->Body = "<h4>Hello , $firstName,<br /><br />You recently requested a password reset for your password. To complete the process, click the link below.<br /><br /><a href='http://www.mandra.cf/resetpass.php?id=$id&hash=$hash'>Reset now</a><br /><br />If you didn't make this change, just ignore this email.<br /><br />Thank you!<br /><br />Mandra Support</h4>";

				/*
		        $message = "";
		        $message .= "<h3>Hello , $firstName</h3>";
		        $message .= "<br /><br />";
		        $message .= "</h3>You recently requested a password reset for your password. To complete the process, click the link below.</h3>";
		        $message .= "<br /><br />";
		        $message .= "<h3><a href='http://www.mandra.cf/resetpass.php?id=$id&hash=$hash'>Reset now</a></h3>";
		        $message .= "<br /><br />";
		        $message .= "<h3>If you didn't make this change, just ignore this email.</h3>";
		        $message .= "<br /><br />";
		        $message .= "<h3>Thank you!</h3>";
		        $message .= "<br /><br />";
		        $message .= "<br /><br />";
		        $message .= "<h3>Mandra Support</h3>";
				*/
				//$mail->Body = $message;

				$mail->IsHTML(true);
				if($mail->Send()) {
					// Mail sent.
					$queryMail = "UPDATE userAccounts SET hash = '$hash' WHERE emailAdd = '$emailAdd' AND status = 'Active'";
					$resultMail = mysql_query($queryMail);
					if(!$resultMail) {
						die ("Table access failed: " . mysql_error());
					} else {
						$_SESSION['STATUS'] = 45;
						header('Location: status.php');
					}
				}
			} else {
				$_SESSION['STATUS'] = 45;
				header('Location: status.php');
			}
		}
	}
?>
<div class="container">
	<div class="row vcenter">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<form class="form-login" action="" method="post">
				<h2 style="text-align: center">Mandra Forestry Liberia Limited</h2>
				<h4 class="form-login-bottom" style="text-align: center">Management Information System</h4>
				<h4 style="text-align: center; color: #337ab7">Forget Password?</h4>
				<h5 style="text-align: center; color: gray; margin-bottom: 20px">Enter your e-mail address below to reset your password.</h5>
				<input type="email" id="input_email" class="form-control" placeholder="User ID (email address)" name="login" required autofocus autocomplete="off">
				<button class="btn btn-lg btn-primary btn-block" type="submit" id="submit-btn">SUBMIT</button>
			</form>
		</div>
	</div>
</div>
<?php include('pages/page_jquery.php'); ?>
</body>
</html>