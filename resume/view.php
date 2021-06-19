<?php
	require_once "pdo.php";
	require_once "utill.php";

	if(isset($_POST['done'])) {
		header("Location: index.php");
		return;
	}	

	if (! isset($_REQUEST['profile_id'])) {
		$_SESSION['error'] = "Missing profile_id";
		header('Location: index.php');
		return;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>View</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>
	<div class="form">
		<h1>Profile information</h1>
		<div  style='color:white;'>
			<?php

			$sql = "SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = 11";
			$stmt = $pdo->query($sql);
			$stmt->execute();

			while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<p>First Name: ";
				echo ($info['first_name']);
				echo "</p>";
				echo "<p>Last Name: ";
				echo ($info['last_name']);
				echo "</p>";
				echo "<p>Email: ";
				echo ($info['email']);
				echo "</p>";
				echo "<p>Summary: ";
				echo ($info['summary']);
				echo "</p>";
				echo "<p>Position</p>";
			}
			$sql = "SELECT year, description FROM position WHERE profile_id = 11";
			$stmt = $pdo->query($sql);
			$stmt->execute();
			echo "<ul>";
			while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<li>";
				echo ($info['year'].": ".$info['description']);
				echo "</li>";
			}
			echo "</ul>";
			?>
		</div>
		<form method="POST">
		<button type="submit" name="done" class="button button-block">Done</button>
		</form>
	</div>
</body>
</html>