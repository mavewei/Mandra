<?php include('pages/page_header.php'); ?>
<link href="css/flexbox.css" rel="stylesheet" type="text/css" />
<link href="css/status.css" rel="stylesheet" type="text/css" />
<link href="css/center.css" rel="stylesheet" type="text/css" />
<?php include('pages/page_meta.php'); ?>
<?php
	require_once('db/db_config.php');
	$lastPage = $_SESSION['LAST_PAGE'];
	$status = $_SESSION['STATUS'];
	switch($status) {
	case "1":
		/**
			Success from admin_registration page
		**/
		echo "<div class=\"container vertical-center-jumbo\"><div class=\"jumbotron\">";
		echo "<h1>Woows. You're done!</h1><p></p>";
		echo "<p>We're finish initialized without any problem, and, the main database and correspond tables are created. This system are still under beta version, kindly contact to system programmer once bug or any system unstable occurred. Please don't hesitate to contact us if have any suggestions.</p>";
		echo "<p>You'll redirected to login page. Please re-login to system using the user account and password created just now.</p>";
		echo "<p>Thank You and enjoy.</p>";
		echo "<p><a class=\"btn btn-primary btn-lg\" href=\"logout.php\" role=\"button\">Start now</a></p>";
		echo "</div></div>";
		break;
	case "2":
		/**
			User authentication successful!
		**/
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Authentication Success!</h2>You'll be redirected to dashboard in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;dashboard.php'>";
		break;
	case "3":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>User Account Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;users.php'>";
		break;
	case "4":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">User Information Updated!</h2>You'll be redirected to dashboard in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;dashboard.php\">";
		break;
	case "5":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">Supplier Info Updated!</h2>You'll be redirected to dashboard in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;suppliers.php\">";
		break;
	case "6":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">Items Info Updated!</h2>You'll be redirected to dashboard in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;items.php\">";
		break;
	case "7":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">Request Submitted!</h2>You'll be redirected to dashboard in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;dashboard.php\">";
		break;
	case "8":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">Request Modified!</h2>This window will close in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;close.php\">";
		break;
	case "9":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">Quotation Generated!</h2>This window will close in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;request_quote.php\">";
		break;
	case "10":
		echo "<div class=\"block\" style=\"height:100%\"><div class=\"centered-login-status\"><div class=\"media\">";
		echo "<div class=\"media-left media-middle\"><a href=\"javascript:;\"><div class=\"container-login-status\">";
		echo "<img class=\"media-object image-middle-login-status\" src=\"images/fail.png\" alt></div></a></div>";
		echo "<div class=\"media-body\"><h2 class=\"media-heading\">Access Denied!</h2>Please contact to site administrator or webmaster.</div></div></div></div>";
		echo "<meta http-equiv=\"refresh\" content=\"3;dashboard.php\">";
		break;
	case "11":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">Purchase Order Generated!</h2>You'll be redirected to dashboard in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;purchase_o_g.php\">";
		break;
	case "12":
		$imgmsg = "
					<div class=\"block\" style=\"height:100%\">
						<div class=\"centered-login-status\">
							<div class=\"media\">
								<div class=\"media-left media-middle\">
									<a href=\"javascript:;\">
										<div class=\"container-login-status\">
											<img class=\"media-object image-middle-login-status\" src=\"images/success.png\" alt>
										</div>
									</a>
								</div>
								<div class=\"media-body\">
									<h2 class=\"media-heading\">Request Order Canceled!</h2>You'll be redirected to dashboard in 3 seconds.
								</div>
							</div>
						</div>
					</div>
				";
		echo "<meta http-equiv=\"refresh\" content=\"3;purchase_pr_hist.php\">";
		break;
	case "13":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Department Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "14":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>New Company Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "15":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>New Employee Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;employees.php'>";
		break;
	case "16":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>New Tax Code Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "17":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>New Unit Infor Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "18":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>New Position Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "19":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Company Infor Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "20":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Department Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "21":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Tax Code Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "22":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Unit Infor Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "23":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Position Infor Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "24":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>User Infor Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "25":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Employee Infor Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "26":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>New Parts Created!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;parts_mfile.php'>";
		break;
	case "27":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Parts Details Updated!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;parts_mfile.php'>";
		break;
	case "28":
		echo "<div class='block' style='height:100%'><div class='centered-login-status'><div class='media'>";
		echo "<div class='media-left media-middle'><a href='javascript:;'><div class='container-login-status'>";
		echo "<img class='media-object image-middle-login-status' src='images/success.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Parts Details Uploaded!</h2>You'll be redirected to previous page in 3 seconds.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;parts_mfile.php'>";
		break;
	case "29":
		/**
			status add successful!
		**/
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Status Details Added!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "30":
		/**
			status add successful!
		**/
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Status Details Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;$lastPage'>";
		break;
	case "31":
		/**
			status add successful!
		**/
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Employees Uploaded!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;employees.php'>";
		break;
	case "32":
		// parts uom add successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>New UOM Added!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "33":
		// parts category add successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>New Category Added!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "34":
		// parts brand add successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>New Brand Added!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "35":
		// parts model add successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>New Model Added!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "36":
		// parts model add successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>New Equip Type Added!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "37":
		// parts model add successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>UOM Details Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "38":
		// parts category updated successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Category Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "39":
		// parts brand updated successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Brand Details Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "40":
		// parts brand updated successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Model Details Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "41":
		// parts brand updated successful!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Equipment Type Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;pmf_settings.php'>";
		break;
	case "42":
		// Material request form created!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Material Request Form Submited!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;prc.material_request.php'>";
		break;
	case "43":
		// Material request form updated!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>Material Request Form Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;prc.material_request.php'>";
		break;
	case "44":
		// User account position updated!
		echo "<div class='container'>";
		echo "<div class='row vcenter'>";
		echo "<div class='col-xs-6 col-sm-6 col-md-5'>";
		echo "<div class='media'>";
		echo "<div class='media-left media-middle'>";
		echo "<a href='javascript:;'>";
		echo "<div class='status-img'>";
		echo "<img class='media-object image-middle-status' src='images/success.png' alt>";
		echo "</div></a></div>";
		echo "<div class='media-body'>";
		echo "<h2 class='media-heading'>User Profile Updated!</h2>You'll be redirected to previous page in 3 seconds.";
		echo "</div></div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;logout.php'>";
		break;
	default:
		echo "<div class='block' style='height:100%'><div class='centered-login-status'>";
		echo "<div class='media'><div class='media-left media-middle'>";
		echo "<a href='javascript:;'><div class='container-login-status'><img class='media-object image-middle-login-status' src='images/fail.png' alt></div></a></div>";
		echo "<div class='media-body'><h2 class='media-heading'>Authentication Failed!</h2>Please proceed to login page or contact administrator.</div></div></div></div>";
		echo "<meta http-equiv='refresh' content='3;logout.php'>";
	};
?>
<?php //echo $imgmsg; ?>
<?php include("pages/page_jquery.php"); ?>
</body>
</html>