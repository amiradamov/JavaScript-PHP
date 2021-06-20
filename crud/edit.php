<?php
require_once "pdo.php";

if (isset($_POST['cancel'])) {
	header("Location: index.php");
	return;
}
if ( isset($_POST['title']) && isset($_POST['plays']) 
     && isset($_POST['rating']) && isset($_POST['id']) ) {
    if ( $_POST['plays']+0 <= 0 || $_POST['rating']+0 <= 0 ||
        strlen($_POST['title']) < 1) {
        $_SESSION['error'] = 'Bad value for title, plays or rating';
        header( 'Location: index.php' ) ;
        return;
    }
    $sql = "UPDATE myid SET title = :title, 
            plays = :plays, rating = :rating
            WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':title' => $_POST['title'],
        ':plays' => $_POST['plays'],
        ':rating' => $_POST['rating'],
        ':id' => $_POST['id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
}

$stmt = $pdo->prepare("SELECT * FROM myid where user_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for id';
    header( 'Location: index.php' ) ;
    return;
}

$t = htmlentities($row['title']);
$p = htmlentities($row['plays']);
$r = htmlentities($row['rating']);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>add</title>
</head>
<body>
	<form method="POST">
		<p>Title:<input type="text" name="title" value="<?= $t ?>"></p>
		<p>Plays:<input type="text" name="plays" value="<?= $p ?>"></p>
		<p>Rating:<input type="text" name="rating" value="<?= $r ?>"></p>
		<?php echo('<input type="hidden" name="id" value="'.$row['user_id'].'">'."\n");?>
		<p><input type="submit" name="add_new" value="Update"><input type="submit" name="cancel" value="Cancel" id="calncel"></p>
	</form>
</body>
</html>

