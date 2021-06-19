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

	if (isset($_POST['add'])) {

		$msg = validateProfile();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: add.php");
			return;
		}
		$msg = validatePos();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: add.php");
			return;
		}

		// Data is valid -- Time to insert
		$sql = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:ud, :fn, :ln, :em, :hl, :su)";
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(array (
			':ud' => $_SESSION['user_id'],
			':fn' => $_POST['first_name'],
			':ln' => $_POST['last_name'],
			':em' => $_POST['email'],
			':hl' => $_POST['headline'],
			':su' => $_POST['summary']));
		$profile_id = $pdo->lastInsertId();

		// Insert the Position entries
		$rank = 1;
    	for($i=1; $i<=9; $i++) {
	        if ( ! isset($_POST['year'.$i]) ) continue;
	        if ( ! isset($_POST['desc'.$i]) ) continue;
	        $year = $_POST['year'.$i];
	        $desc = $_POST['desc'.$i];

	        $stmt = $pdo->prepare('INSERT INTO Position
	            (profile_id, rank, year, description)
	        VALUES ( :pid, :rank, :year, :desc)');
	        $stmt->execute(array(
	            ':pid' => $profile_id,
	            ':rank' => $rank,
	            ':year' => $year,
	            ':desc' => $desc)
	        );
	        $rank++;
   		}
   		$_SESSION['success'] = "Profile added";
   		header("Location: index.php");
   		return;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>ADD</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
	<script type="text/javascript" src="jquery.in.js"></script>
</head>
<body>
<div class="form">
	<?php 
		echo "<h1> Adding Profile for ".htmlentities($_SESSION['name'])."</h1>";
		flashmessage(); 
	?>
	<form method="POST">
		<div class="top-row">
			<div class="field-wrap">
				<label>First name<span class="req">*</span></label><input type="text" name="first_name" id="first_nmae" autocomplete="off">
			</div>

			<div class="field-wrap">
				<label>Last name<span class="req">*</span></label><input type="text" name="last_name" id="last_name" autocomplete="off">
			</div>
		</div>

		<div class="field-wrap">
		<label for="email">Email<span class="req">*</span></label><input type="text" name="email" id="email" autocomplete="off">
		</div>

		<div class="field-wrap">
			<label for="headline">Headline<span class="req">*</span></label><input type="text" name="headline" id="headline" autocomplete="off">
		</div>

		<div class="field-wrap">
			<textarea id="summary" name="summary" placeholder="Summary" rows="4" cols="40" style="resize: none;"></textarea>
		</div>

		<button type="submit" name="addPos" id="addPos" class="button button-block">Add Position</button>
		<div id="position_fields">
		</div>
		<br>
		<button type="submit" name="add" class="button button-block">Submit</button>
		<br>
		<button type="submit" name="cancel" class="button button-block">Cancel</button>
	</form>
	<script>
		countPos = 0;

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
</div>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="./script.js"></script>
</body>
</html>