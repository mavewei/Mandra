<?php
	require_once('db/db_config.php');
	$fname = $_SESSION['FNAME'];
	$login = $_SESSION['LOGIN_ID'];
	$gid = $_SESSION['GID'];
/*
	$dbSelected = mysql_select_db($dbNAME) or die("Unable to select database: " . mysql_error());
	if($dbSelected) {
		$queryMenu = "SELECT * FROM purchaseREQUEST WHERE requestPerson = '$fname' and aprStatus = 'false'";
		$resultMenu = mysql_query($queryMenu);
		if(!$resultMenu) die ("Database access failed: " . mysql_error());
		$rowsMenu = mysql_num_rows($resultMenu);
	};
*/
?>
<div class="page-header">
	<div class="page-header-top">
		<div class="container">
			<div class="page-logo"><a href="dashboard.php"><img src="images/logo3.png" alt="logo" class="logo-default" /></a></div>
			<div class="top-menu">
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown dropdown-extended dropdown-dark dropdown-notification" id="header_noti">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"><i class="glyphicon glyphicon-flag"></i><span class="badge badge-default"><?php echo $rowsMenu; ?></span></a>
						<ul class="dropdown-menu">
							<li class="external">
								<h3>You have<strong> <?php echo $rowsMenu; ?> </strong>tasks</h3>
								<a href="javascript:;"> view all </a>
							</li>
						</ul>
					</li>
					<li class="dropdown dropdown-extended dropdown-dark dropdown-tasks" id="header_tasks">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"><i class="glyphicon glyphicon-envelope"></i><span class="badge badge-default">12</span></a>
						<ul class="dropdown-menu">
							<li class="external">
								<h3>You have<strong> 12 unread </strong>mails</h3>
								<a href="javascript:;"> view all </a>
							</li>
						</ul>
					</li>
					<li class="dropdown dropdown-separator">
						<span class="separator"></span>
					</li>
					<li class="dropdown dropdown-user dropdown-dark">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"><img alt class="img-circle" src="images/user_unknown.png" /><span class="username username-hide-mobile" id="firstname"> <?php echo $fname; ?> </span></a>
						<ul class="dropdown-menu dropdown-menu-default">
							<li>
								<a href="javascript:;"><i class="glyphicon glyphicon-user"></i> My Profile </a>
							</li>
							<!-- <li>
								<a href="javascript:;"><i class="glyphicon glyphicon-calendar"></i> My Calendar </a>
							</li> -->
							<!-- <li>
								<a href="inbox.php"><i class="glyphicon glyphicon-envelope"></i> My Inbox </a>
							</li> -->
							<!-- <li>
								<a href="javascript:;"><i class="glyphicon glyphicon-send"></i> My Tasks </a>
							</li> -->
							<li class="divider">
							</li>
							<!-- <li>
								<a href="javascript:;"><i class="glyphicon glyphicon-lock"></i> Lock Screen </a>
							</li> -->
							<li>
								<a href="javascript:;" id="logoutAlert" class="logoutAlert"><i class="glyphicon glyphicon-log-out"></i> Log Out </a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="page-header-menu">
		<div class="container">
			<!--
			<form class="search-form" action="#" method="get">
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Search" name="query" />
					<span class="input-group-btn"> <a href="javascript:;" class="btn submit"><i class="glyphicon glyphicon-search"></i></a></span></div>
			</form>
			-->
			<div class="hor-menu">
				<ul class="nav navbar-nav">
					<!-- DASHBOARD ACTIVE BEGIN -->
					<li class="active">
						<a href="dashboard.php">Dashboard</a>
					</li>
					<!--
					<li class="menu-dropdown mega-menu-dropdown mega-menu-full" style="display: none;">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"> Car <i class="fa fa-angle-down"></i></a>
						<ul class="dropdown-menu">
							<li>
								<div class="mega-menu-content">
									<div class="row">
										<div class="col-md-3">
											<ul class="mega-menu-submenu">
												<li>
													<h3>Car</h3>
												</li>
												<li>
													<a href="#"><i class="fa fa-angle-right"></i> Usage </a>
												</li>
											</ul>
										</div>
										<div class="col-md-3"></div>
										<div class="col-md-3"></div>
										<div class="col-md-3"></div>
									</div>
								</div>
							</li>
						</ul>
					</li>
					-->

					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Human Resources <!-- <i class="fa fa-angle-down"></i> --></a>
						<ul class="dropdown-menu pull-left">
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> HR Data </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="employees.php"> Employees </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Payroll </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function 1 </a>
									</li>
									<!--
									<li class=" ">
										<a href="db_reset.php"> Destroy </a>
									</li> -->
								</ul>
							</li>
						</ul>
					</li>
					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Procurement <!-- <i class="fa fa-angle-down"></i> --></a>
						<ul class="dropdown-menu pull-left">
							<li>
								<a href="parts_mfile.php" class="nav-link">Parts Master File</a>
							</li>

							<!--
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> Parts Master File </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function 1 </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Purchase Request </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function1 </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Purchase Order </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function1 </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Goods Receving </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function1 </a>
									</li>
								</ul>
							</li>
							-->



						</ul>
					</li>
					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Parts Inventory <!-- <i class="fa fa-angle-down"></i> --></a>
						<ul class="dropdown-menu pull-left">
							<!--
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> HR Data </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Employees </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Payroll </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function 1 </a>
									</li>
								</ul>
							</li>
							-->
						</ul>
					</li>
					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Fuel <!-- <i class="fa fa-angle-down"></i> --></a>
						<!--
						<ul class="dropdown-menu pull-left">
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> HR Data </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Employees </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Payroll </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function 1 </a>
									</li>
								</ul>
							</li>
						</ul>
						-->
					</li>
					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Equipment <!-- <i class="fa fa-angle-down"></i> --></a>
						<!--
						<ul class="dropdown-menu pull-left">
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> HR Data </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Employees </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Payroll </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function 1 </a>
									</li>
								</ul>
							</li>
						</ul>
						-->
					</li>
					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Logs Data <!-- <i class="fa fa-angle-down"></i> --></a>
						<!--
						<ul class="dropdown-menu pull-left">
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> HR Data </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Employees </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Payroll </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function 1 </a>
									</li>
								</ul>
							</li>
						</ul>
						-->
					</li>
					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Reports <!-- <i class="fa fa-angle-down"></i> --></a>
						<ul class="dropdown-menu pull-left">
							<!--
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> HR Data </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Employees </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Payroll </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="javascript:;"> Function 1 </a>
									</li>
								</ul>
							</li>
							-->
						</ul>
					</li>







					<!--
					<li class="menu-dropdown mega-menu-dropdown mega-menu-full">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"> Management <i class="fa fa-angle-down"></i></a>
						<ul class="dropdown-menu">
							<li>
								<div class="mega-menu-content">
									<div class="row">
										<div class="col-md-3">
											<ul class="mega-menu-submenu">
												<li>
													<h3>Purchase Request</h3>
												</li>
												<li>
													<a href="purchase_pr_orders_info.php"><i class="fa fa-angle-right"></i> Orders Information </a>
												</li>
											</ul>
										</div>
										<div class="col-md-3"></div>
										<div class="col-md-3"></div>
									</div>
								</div>
							</li>
						</ul>
					</li>
					-->



					<!--
					<li class="menu-dropdown mega-menu-dropdown mega-menu-full">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"> Departments <i class="fa fa-angle-down"></i></a>
						<ul class="dropdown-menu">
							<li>
								<div class="mega-menu-content">
									<div class="row">
										<div class="col-md-3">
											<ul class="mega-menu-submenu">
												<li>
													<h3>Warehouse</h3>
												</li>
												<li>
													<a href="javascript:;"><i class="fa fa-file-pdf-o"></i> Purchase Request </a>
												</li>
												<li>
													<a href="javascript:;"><i class="fa fa-gear"></i> Equipment Consumption </a>
												</li>
												<li>
													<a href="javascript:;"><i class="fa fa-tasks"></i> Stock Adjustment </a>
												</li>
												<li>
													<a href="javascript:;"><i class="fa fa-reply"></i> Goods Return </a>
												</li>
											</ul>
										</div>
										<div class="col-md-3">
											<ul class="mega-menu-submenu">
												<li>
													<h3>Finance & Accounting</h3>
												</li>
												<li>
													<a href="finance_po.php"><i class="fa fa-car"></i> View Purchase Orders </a>
												</li>
												<li>
													<a href="finance_search_po.php"><i class="fa fa-car"></i> Search Purchase Orders</a>
												</li>
											</ul>
										</div>
										<div class="col-md-3">
											<ul class="mega-menu-submenu">
												<li>
													<h3>Procurement</h3>
												</li>
												<li>
													<a href="purchase_pr_info.php"><i class="fa fa-user-plus"></i> Acknowledgement </a>
												</li>
												<li>
													<a href="request_quote.php"><i class="fa fa-users"></i> Request for Quotation (RFQ) </a>
												</li>
												<li>
													<a href="purchase_o_g.php"><i class="fa fa-users"></i> Purchase Order </a>
												</li>
											</ul>
										</div>
										<div class="col-md-3">
											<ul class="mega-menu-submenu">
												<li>
													<h3>Purchase Request</h3>
												</li>
												<li>
													<a href="purchase_pr.php"><i class="fa fa-file-text-o"></i> New submission </a>
												</li>
												<li>
													<a href="purchase_pr_hist.php"><i class="fa fa-file-text-o"></i> Order history </a>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</li>
					-->

					<!-- <li class="menu-dropdown classic-menu-dropdown" style="display: none;">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Analyse & Reports <i class="fa fa-angle-down"></i></a>
						<ul class="dropdown-menu pull-left">
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Procurements </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="purchase_pr_report.php"> Purchase Request </a>
									</li>
								</ul>
							</li>
						</ul>
					</li> -->


					<!--
					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Maintenance <i class="fa fa-angle-down"></i></a>
						<ul class="dropdown-menu pull-left">
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> Human Resources </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="registration.php"> Register </a>
									</li>
									<li class=" ">
										<a href="browse_users.php"> Browse </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Finance & Accounting </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="javascript:;"> Backup </a>
									</li>
									<li class=" ">
										<a href="javascript:;"> Rebuild </a>
									</li>
									<li class=" ">
										<a href="db_reset.php"> Destroy </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Procurements </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="javascript:;"> Backup </a>
									</li>
									<li class=" ">
										<a href="javascript:;"> Rebuild </a>
									</li>
									<li class=" ">
										<a href="db_reset.php"> Destroy </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Productions </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="javascript:;"> Backup </a>
									</li>
									<li class=" ">
										<a href="javascript:;"> Rebuild </a>
									</li>
									<li class=" ">
										<a href="db_reset.php"> Destroy </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-database"></i> Procurement </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="items.php"> Items </a>
									</li>
									<li class=" ">
										<a href="suppliers.php"> Suppliers </a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
					-->

					<li class="menu-dropdown classic-menu-dropdown">
						<a data-hover="dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Extras <!-- <i class="fa fa-angle-down"></i> --></a>
						<ul class="dropdown-menu pull-left">
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> General </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="general_settings.php"> Settings </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> Users </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="users.php"> All Users </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> Import & Export </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="import_export.php"> Parts Master File </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu" <?php if($gid > 1000) echo "style='display: none'"; ?>>
								<a href="javascript:;"><i class="fa fa-database"></i> Database </a>
								<ul class="dropdown-menu" style="display:none;">
									<li>
										<a href="db/db_backup.php"> Backup </a>
									</li>
									<li>
										<a href="db/db_restore.php"> Rebuild </a>
									</li>
									<li>
										<a href="db/db_reset.php"> Destroy </a>
									</li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a href="javascript:;"><i class="fa fa-users"></i> ChangeLogs </a>
								<ul class="dropdown-menu" style="display:none;">
									<li class=" ">
										<a href="changelog.php"> History </a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>