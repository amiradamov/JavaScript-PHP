<?php
session_start();
if (isset($_POST['reset'])) {
	$_SESSION['chats'] = array();
	header("Location: index.php");
	return;
}

if (isset($_POST['message'])) {
	if (! isset($_SESSION['chats'])) $_SESSION['chats'] = array();
	$_SESSION['chats'] [] = array($_POST['message'],
									date(DATE_RFC2822));
	header("Location: index.php");
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
	<h1>Chat</h1>
	<form method="POST" action="index.php">
		<p>
			<input type="text" name="message" size="60" />
			<input type="submit" value="Chat" />
			<input type="submit" name="reset" value="Reset" />
		</p>
	</form>

	<div id="chatcontent">
		<img src="spinner.gif" alt="Loading...">
	</div>

	<script type="text/javascript">
		function updateMsg() {
			console.log('Requesting JSON');
			$.ajax({
			url : "chatlist.php",
			cache : false,
			success : function(rowz) {
			// })
			// $.getJSON('chatlist.php', function(rowz) {
				console.log('JSON Received');
				console.log(rowz);
				$("#chatcontent").empty();
				for (var i = 0; i < rowz.length; i++) {
					entry = rowz[i];

				$('#chatcontent').append('<p>' +entry[0]+'<br>&nbsp;&nbsp;'+entry[1]+'</p>\n');
				}
				setTimeout('updateMsg()', 4000);
			}
			});
		}
		console.log("Startup complete");
		updateMsg(); // Call the first time to get things started
		// Make sure JSON requestes are not cached
		// $(document).ready(function() {
		// 	$.ajaxSetup({cache: false});
		// 	updateMsg();
		// });
	</script>
</body>
</html>