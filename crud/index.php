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
    <title>index</title>
    <script type="text/javascript" src="jquery.in.js"></script>
</head>
<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }
    ?>
    <h1>hey</h1>
	<div id="view">
		<!-- <p>
			<a href="add.php">Add New</a> |
			<a href="viewapi.php" target="_blank">viewapi.php</a>
		</p> -->
		<!-- <button type="button" name="add" id="add">Add New</button> -->
	</div>
	<script type="text/javascript">
		btnCancel = document.createElement("button");
		btnCancel.innerHTML = "Cancel";

		btnAdd = document.createElement("button");
		btnAdd.innerHTML = "Add";
		btnAdd.name = "add";
		btnAdd.id = "add";
		btnAdd.onclick = function() {
			$('#view').empty();
				console.log('Elements inside of #view deleted');
				$('#view').append(
					'<form method="POST"> \
					<p>Title:<input type="text" name="title"></p> \
					<p>Plays:<input type="text" name="plays"></p> \
					<p>Rating:<input type="text" name="rating"></p> \
					<p><input type="submit" name="add_new" value="Add New"><input type="button" name="cancel" value="Cancel" id="calncel" onclick="javascript:$(\'#view\').empty(); document.getElementById(\'view\').append(btnAdd);"></p> \
					</form');
		}
		document.getElementById("view").append(btnAdd);
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#cancel').click(function(event) {
				$('#view').empty();
				console.log('Elements inside of #view deleted');
				document.getElementById("view").append(btnCancel);
				//$('#view').append(
				//	'<button type="button" name="add" id="add">Add New</button>');
			});
		});
	</script>

<table border="1">
  <tbody id="mytab">
  </tbody>
</table>
<script type="text/javascript">
// Simple htmlentities leveraging JQuery
function htmlentities(str) {
   return $('<div/>').text(str).html();
}
</script>
<script type="text/javascript">
// Do this *after* the table tag is rendered
$.getJSON('getjson.php', function(rows) {
    $("#mytab").empty();
    // console.log(rows);
    found = false;
    for (var i = 0; i < rows.length; i++) {
        row = rows[i];
        found = true;
        // window.console && console.log('Row: '+i+' '+row.title);
        $("#mytab").append("<tr><td>"+htmlentities(row.title)+'</td><td>'
            + htmlentities(row.plays)+'</td><td>'
            + htmlentities(row.rating)+"</td><td>\n"
            + '<a href="edit.php?id='+htmlentities(row.user_id)+'">'
            + 'Edit</a> / '
            + '<a href="delete.php?id='+htmlentities(row.user_id)+'">'
            + 'Delete</a>\n</td></tr>');
    }
    if ( ! found ) {
        $("#mytab").append("<tr><td>No entries found</td></tr>\n");
    }
});
</script>
</body>
</html>