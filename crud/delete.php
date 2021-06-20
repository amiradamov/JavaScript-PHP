<?php
require_once "pdo.php";
if (isset($_POST['cancel'])) {
	header("Location: index.php");
	return;
}
if (!isset($_REQUEST['id'])) {
	header("Location: index.php");
	return;
}
if ( isset($_POST['delete']) && isset($_POST['id']) ) {
    $sql = "DELETE FROM myid WHERE user_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

$stmt = $pdo->prepare("SELECT title, user_id FROM myid where user_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for id';
    header( 'Location: index.php' ) ;
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>delete</title>
</head>
<body>
	<?php echo "<p>Confirm: Deleting ".htmlentities($row['title'])."</p>\n";?>
	
	<form method="POST">
		<?php echo('<input type="hidden" name="id" value="'.$row['user_id'].'">'."\n");?>
		<input type="submit" name="delete" value="Delete">
		<input type="submit" name="cancel" value="Cancel">
	</form>
</body>
</html>