<?php
	require_once "pdo.php";
	require_once "utill.php";

	// if the user is not logged in redirect back to index.php
	if (!isset($_SESSION['user_id'])) {
		die("ACCESS DENIED");
		return;
	}
	// if the user requested cancel go back to index.php
	if (isset($_POST['cancel'])) {
		header("Location: index.php");
		return;
	}

	// Make sure the REQUEST parameter is present
	if (! isset($_REQUEST['profile_id'])) {
		$_SESSION['error'] = "MIssing profile_id";
		header('Location: index.php');
		return;
	}

	// Load up the profile in question
	$sql = "SELECT * FROM profile WHERE profile_id = :pd AND user_id = :ud";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array (
		':pd' => $_REQUEST['profile_id'],
		':ud' => $_SESSION['user_id']));
	$profile = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($profile === false) {
		$_SESSION['error'] = "Could not load profile";
		header("Location: index.php");
		return;
	}

	// Handle the incoming data
	if (isset($_POST['add'])) {

		$msg = validateProfile();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
			return;
		}
		$msg = validatePos();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
			return;
		}
		$sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
		WHERE profile_id = :pd AND user_id = :ud";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array (
		':pd' => $_REQUEST['profile_id'],
		':ud' => $_SESSION['user_id'],
		':fn' => $_POST['first_name'],
		':ln' => $_POST['last_name'],
		':em' => $_POST['email'],
		':he' => $_POST['headline'],
		':su' => $_POST['summary']));

		// Clear out the old position entries
		$stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
		$stmt->execute(array (':pid' => $_REQUEST['profile_id']));

		//Insert position entries
		$rank = 1;
    	for($i=1; $i<=9; $i++) {
	        if ( ! isset($_POST['year'.$i]) ) continue;
	        if ( ! isset($_POST['desc'.$i]) ) continue;
	        $year = $_POST['year'.$i];
	        $desc = $_POST['desc'.$i];

	        $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
	        $stmt->execute(array(
	            ':pid' => $_REQUEST['profile_id'],
	            ':rank' => $rank,
	            ':year' => $year,
	            ':desc' => $desc)
	        );
	        $rank++;
   		}
   		$_SESSION['success'] = "Profile updated";
   		header("Location: index.php");
   		return;
	}
	$positions = loadPos($pdo, $_REQUEST['profile_id']);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>EDIT</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
	<script type="text/javascript" src="jquery.in.js"></script>
</head>
<body>
	<div class="form">
		<h1>Editing profile for <?= htmlentities($_REQUEST['profile_id']) ?></h1>
		<?php flashmessage() ?>

		<form method="POST" action="edit.php">
			<input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']); ?>">
			<div class="top-row">
			<div class="field-wrap">
				<input type="text" name="first_name" id="first_nmae" value="<?= htmlentities($profile['first_name']); ?>" autocomplete="off">
			</div>

			<div class="field-wrap">
				<input type="text" name="last_name" id="last_name" value="<?= htmlentities($profile['last_name']); ?>" autocomplete="off">
			</div>
			</div>

			<div class="field-wrap">
			<input type="text" name="email" id="email" value="<?= htmlentities($profile['email']); ?>" autocomplete="off">
			</div>

			<div class="field-wrap">
				<input type="text" name="headline" id="headline" value="<?= htmlentities($profile['headline']); ?>" autocomplete="off">
			</div>

			<div class="field-wrap">
				<textarea id="summary" name="summary" placeholder="Summary" rows="4"cols="40"><?= htmlentities($profile['summary']); ?></textarea>
			</div>

			<?php

			$pos = 0;
			echo('<button type="submit" name="addPos" id="addPos" class="button button-block">Add Position</button>'."\n");
			echo('<div id="position_fields">'."\n");
			foreach($positions as $position) {
				$pos++;
				echo('<br><div id="position'.$pos.'">'."\n");
				echo('<div class="top-row">'."\n");
				echo('<div class="field-wrap"><input type="text" name="year'.$pos.'"');
				echo ('value="'.$position['year'].'"/>'."\n");
				echo('</div>'."\n");
				echo('<div class="field-wrap"><input type="button" value="-" ');
				echo('onclick="$(\'#position'.$pos.'\').remove(); countPos--; return false;"></div>'."\n");
				echo('</div>'."\n");
				echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
				echo(htmlentities($position['description'])."\n");
				echo("\n</textarea>\n");
				echo('</div>'."\n");
			}
			echo('</div>');
			echo('<br>');
			?>

			<button type="submit" name="add" class="button button-block">Save</button>
			<br>
			<button type="submit" name="cancel" class="button button-block">Cancel</button>
		</form>
	</div>

	<script>
	countPos = <?= $pos ?>

	// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
	$(document).ready(function(){
	    window.console && console.log('Document ready called');
	    $('#addPos').click(function(event){
	        // http://api.jquery.com/event.preventdefault/
	        event.preventDefault();
	        if ( countPos >= 9 ) {
	            alert("Maximum of nine position entries exceeded");
	            return;
	        }
	        countPos++;
	        window.console && console.log("Adding position "+countPos);
	        $('#position_fields').append(
		            '<div id="position'+countPos+'"> \
		            <div class="top-row"> \
		            <div class="field-wrap"><input type="text" name="year'+countPos+'" value="" /> </div>\
		            <div class="field-wrap"><input type="button" style value="-" \
		                onclick="$(\'#position'+countPos+'\').remove(); countPos--; return false;"></div> \
		                </div> \
		            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
		            </div>');
	    });
	});
	</script>
</body>
</html>
