<?php
require_once "pdo.php";
if (isset($_POST['cancel'])) {
	header("Location: index.php");
	return;
}
if (isset($_POST['add_new'])) {
	
	if(strlen($_POST['title']) < 1 || strlen($_POST['plays']) < 1 || strlen($_POST['rating']) < 1) {
		$_SESSION['error'] = 'All fields are required';
		header('Location: index.php');
		return;
	}
    if (!is_numeric($_POST['plays']) || !is_numeric($_POST['rating'])) {
        $_SESSION['error'] = 'Plays and Rating must be numeric';
        header('Location: index.php');
        return;
    }
    $sql = "INSERT INTO myid (title, plays, rating) VALUES 
            (:title, :plays, :rating)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':title' => $_POST['title'],
            ':plays' => $_POST['plays'],
            ':rating' => $_POST['rating']));
    $_SESSION['success'] = "Record Added";
    header('Location: index.php');
    return;
	
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>add</title>
</head>
<body>
	<form method="POST">
		<p>Title:<input type="text" name="title"></p>
		<p>Plays:<input type="text" name="plays"></p>
		<p>Rating:<input type="text" name="rating"></p>
		<p><input type="submit" name="add_new" value="Add New"><input type="submit" name="cancel" value="Cancel" id="calncel"></p>
	</form>
</body>
</html>