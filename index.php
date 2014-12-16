<?php
	// Insert authentication functions -----------------------------------------------------------------------------------
	// Insert database functions -----------------------------------------------------------------------------------------
	require 'Database.php';
	// Core code to construct the correct page ---------------------------------------------------------------------------
	$TITLE = "CS 428 - Software Engineering";// Default title
	$BASE_DATE = '2014-11-20 00:00:00';
	$CONTENT = $AUX_CONTENT = $EXTRA_CONTENT = '';
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
	<?php displayAuxContent(); ?>
	<?php require 'header.html'; ?>
	<div class="content">
			<?php
				displayContent();
				echo $EXTRA_CONTENT;
			?>
	</div>
<?php require 'footer.html'; ?>
</body>
</html>
