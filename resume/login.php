<?php
	require_once "pdo.php";
	require_once "utill.php";

	unset($_SESSION['name']);
	unset($_SESSION['user_id']);

	if (isset($_POST['cancel'])) {
		header('Location: index.php');
		return;
	}

	$salt = "XyZzy12*_'";

	if (isset($_POST['login'])) {
		if (strlen($_POST['email']) < 1 || strlen($_POST['password']) < 1) {
			$_SESSION['error'] = "Email and password are required";
			header('Location: login.php');
			return;
		}
		$check = hash('md5', $salt.$_POST['password']);

		$sql = "SELECT user_id, name FROM users WHERE email = :em AND password = :pass";
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(array(
				':em' => $_POST['email'],
				':pass' => $_POST['password']));
		$row = $stmt -> fetch(PDO::FETCH_ASSOC);

		if ($row !== false) {
			$_SESSION['name'] = $row['name'];
			$_SESSION['user_id'] = $row['user_id'];
			$_SESSION['success'] = "Success!!!";
			header("Location: index.php");
			return;
			} else {
				$_SESSION['error'] = "Incorrect email or password";
				header("Location: login.php");
				echo "".$_SESSION['error'];
				return;
			}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Log In</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
	<script type="text/javascript" src="./jquery.in.js"></script>
</head>
<body>
	<div class="form">
		<div class="login">
			<?php flashmessage();
			?>
			<form method="POST">
				<div class="field-wrap">
				<label>Email<span class="req">*</span></label><input type="text" name="email" id="email">
				</div>
				
				<div class="field-wrap">
				<label>Password<span class="req">*</span></label><input type="password" name="password" id="password" autocomplete="off">
				</div>
				<button type="submit" name="login" onclick="return doValidation();" class="button button-block">Log In</button>
				<!-- <p class="forgot"><a href="index.php">Cancel</a></p> -->
				<br>
				<button type="submit" name="cancel" class="button button-block">Cancel</button>
			</form>
		</div>
	</div>
	<script  src="./script.js"></script>
	<script>
	function doValidate() {
	    console.log('Validating...');
	    try {
	        addr = document.getElementById('email').value;
	        pw = document.getElementById('id_1723').value;
	        console.log("Validating addr="+addr+" pw="+pw);
	        if (addr == null || addr == "" || pw == null || pw == "") {
	            alert("Both fields must be filled out");
	            return false;
	        }
	        if ( addr.indexOf('@') == -1 ) {
	            alert("Invalid email address");
	            return false;
	        }
	        return true;
	    } catch(e) {
	        return false;
	    }
	    return false;
	}
	</script>
</body>
</html>