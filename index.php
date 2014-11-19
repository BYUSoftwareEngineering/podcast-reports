<?php
	// Insert authentication functions -----------------------------------------------------------------------------------
	// Insert database functions -----------------------------------------------------------------------------------------
	require 'Database.php';
	// Core code to construct the correct page ---------------------------------------------------------------------------
	$TITLE = "CS 428 - Software Engineering";// Default title
	$BASE_DATE = '2014-08-24 00:00:00';
	$CONTENT = $AUX_CONTENT = '';
	if (isset($_GET['page']))
		$page = $_GET['page'] . '.php';
	else
		$page = 'report.php';
	require $page;

?>

<!DOCTYPE html>
<html>
<?php require 'head.php'; ?>
<body>
	<?php echo $AUX_CONTENT; ?>
	<?php require 'header.html'; ?>
	<div class="content">
			<?php echo $CONTENT; ?>
	</div>
<?php require 'footer.html'; ?>
</body>
</html>