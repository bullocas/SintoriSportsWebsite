<!--************************************************
Author: Cassidy Bullock
Date: March 30, 2018
Description: staff login page
************************************************-->
<?php
	if(session_status() == PHP_SESSION_NONE){
		session_start();
	}
?>

<!doctype html>

<html>

<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	<?php
		//variables for login
		$staffID="";
		$staffPass="";
		$DBstaffID="";
		$DBstaffPass="";

		//function for security of entered data
		function validateInput($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}

		//check that login has been entered
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$errors = array();

			//validate that information has been entered correctly
			if(empty($_POST['staffID'])){
				array_push($errors, "Please enter staff id. <br/>");
			}
			else{
				$staffID = validateInput($_POST['staffID']);
			}
			if(empty($_POST['staffPass'])){
				array_push($errors, "Please enter password. <br/>");
			}
			else{
				$staffPass = validateInput($_POST['staffPass']);
			}

			//check the login if there are no errors
			if(empty($errors)){
				// connect to database
				require ('connectDB.php');

				//check that ID and password match DB
				//escape special characters
				$staffID = mysqli_real_escape_string($dbc, validateInput($staffID));
				$staffPass = mysqli_real_escape_string($dbc, validateInput($staffPass));

				//encode password using sha1
				$staffPass = sha1($staffPass);

				//get database staff login info
				$q = "SELECT staffID, staffPass, position FROM staff where staffID='$staffID'";
				$r = @mysqli_query($dbc, $q);

				$row = mysqli_fetch_array($r);

				$DBstaffID = $row[0];
				$DBstaffPass = $row[1];

				//check if information matches
				if($DBstaffID == $staffID){
					if($DBstaffPass == $staffPass){
						//set user
						$_SESSION['user']="$row[2]";

						//extra security
						$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

						//redirect to logged in
						require ('redirect.inc.php');
						redirect('login_success.php');
					}
					else{
						array_push($errors, "ID and password do not match. <br/>");
					}
				}
				else{
					array_push($errors, "That admin ID does not exist. <br/>");
				}

				//display any errors
				if(!empty($errors)){
					foreach($errors as $error){
						echo "<font color=\"red\">ERROR: $error </font>";
					}
				}
			}
			else{
				foreach($errors as $error){
					echo "<font color=\"red\">ERROR: $error </font>";
				}
			}
		}
	?>

	<?php include ('nav.inc.php'); ?>

	<h1>Staff Login</h1>
	<form action="login_form.php" method="post">
		<p>Staff ID: <input class="col-3 form-control" type="text" name="staffID" maxlength="20" value='<?php echo $staffID ?>' /></p>
		<p>Password: <input class="col-3 form-control" type="password" name="staffPass" maxlength="40" /></p>
		<p><input class="btn btn-outline-info" type="submit" name="submit" value="submit" /></p>
</body>

</html>
