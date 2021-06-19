<?php
	require_once "pdo.php";
	require_once "utill.php";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Home page</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>
	<div class="form" style='color:white;'>
	<?php
	$nRows = $pdo->query('select count(*) from profile')->fetchColumn();

	$stmt = $pdo -> query("SELECT * FROM profile");
	$stmt -> execute();


	// $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (isset($_SESSION['user_id'])) {
		echo "".htmlentities($_SESSION['name'])."";
		flashmessage();
		echo ('<p><a href="logout.php">Logout</a></p>'."\n");

		if ($nRows > 0) {
		echo '<table border = "1"."\n"';
		echo '<tr><th>Name</th><th>Headline</th><th>Action</th></tr>';
		while ($profiles = $stmt->fetch(PDO::FETCH_ASSOC)) {
			echo "<tr><td>";
			echo ('<a href="view.php?profile_id='.$profiles['profile_id'].'">'.$profiles['first_name']." ".$profiles['last_name'].'</a>');
			echo ("</td><td>");
			echo ($profiles['headline']);
			echo ("</td><td>");
			echo ('<a href="edit.php?profile_id='.$profiles['profile_id'].'">Edit</a> / ');
			echo ('<a href="delete.php?profile_id='.$profiles['profile_id'].'">Delete</a>');
			echo "</td></tr>";
		}
		echo "</table>\n";
		}	

		echo ('<p><a href="add.php">Add New Entry</a></p>'."\n");
	}else {
		echo ('<p><a href="login.php">Login</a></p>'."\n");

		if ($nRows > 0) {
			echo '<table border = "1"."\n"';
			echo '<tr><th>Name</th><th>Headline</th></tr>';
			while ($profiles = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<tr><td>";
				echo ('<a href="view.php?profile_id='.$profiles['profile_id'].'">'.$profiles['first_name']." ".$profiles['last_name'].'</a>');
				echo ("</td><td>");
				echo ($profiles['headline']);
				echo "</td></tr>";
			}
			echo "</table>\n";
		}
	}
	?>
</div>
</body>
</html>



