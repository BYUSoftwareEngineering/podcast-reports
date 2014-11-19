<?php
require 'db.php';
// ---------------------------------------------------------------------------------------------------------------------
// Transaction Functions -----------------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------------------------
function updateDatabase() {
	global $con, $net_id;
	$action = $_GET['action'];
	$podcast_id = $_GET['podcast-id'];
	if ($action == "add-time") {
		$sql = "INSERT INTO `records`(`net_id`, `podcast_id`, `is_valid`) VALUES ('$net_id',$podcast_id,'1')";
		mysqli_query($con, $sql);
	}
	else if ($action == "delete-time") {
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' AND `is_valid`='1'";
		$result = mysqli_query($con, $sql);
		$result_size = mysqli_num_rows($result);
		if ($result_size == 1) {
			$sql = "UPDATE `records` SET `is_valid`='0' WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' AND `is_valid`='1'";
			mysqli_query($con, $sql);
		}
		else if ($result_size > 1)
			echo "<script> alert('ERROR: Unable to delete entry. Multiple valid entries for $net_id and $podcast_id'); </script>";
		else// result_size must be 0
			echo "<script> alert('ERROR: Unable to delete entry. No valid entries for $net_id and $podcast_id'); </script>";
	}
}
// Function to configure the podcast information to be displayed
function configDisplay() {
	global $con, $net_id, $display, $full_name;
	$sql = "SELECT * FROM `students` WHERE `net_id`='$net_id'";
	$result = mysqli_query($con, $sql);
	$result_size = mysqli_num_rows($result);
	if ($result_size > 0) {
		$sql = 'SELECT * FROM `podcasts`';
		$podcast_result = mysqli_query($con, $sql);
		$display = "<h1>Podcast Report</h1>";
		$display .= "<h2>$full_name</h2>";
		while ($row = mysqli_fetch_array($podcast_result)) {
			$podcast_id = $row['podcast_id'];
			$btn_id = 'p_' . $podcast_id;
			$podcast_name = $row['podcast_name'];
			$watched = false;
			$div_color = '';
			$btn_txt = 'Click when watched';
			$div_txt = 'You have not watched this podcast yet.';
			$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' ORDER BY `time` DESC";
			$student_result = mysqli_query($con, $sql);
			$times_table = '<table><tbody>';
			if ($student_result) {
				while ($times_row = mysqli_fetch_array($student_result)) {
					$strike_before = "<del>";
					$strike_after = "</del>";
					if ($times_row['is_valid']) {
						$watched = true;
						$strike_before = $strike_after = "";
					}
					$times_table .= '<tr><td>' . $strike_before . $times_row['time'] . $strike_after . '</td></tr>';
				}
			}
			$times_table .= '</table></tbody>';
			if ($watched) {
				$btn_txt = 'Undo';
				$div_txt = 'You have watched this podcast.';
				$div_color = ' style="color:gray"';
			}
			$display .= '<div' . $div_color . '>';
			$display .= "<div class=\"box\"><b>$podcast_name:</b>&nbsp;</div><div class=\"div-txt\">$div_txt</div>";
			$display .= "<button type=\"button\" id=\"$btn_id\" onclick=\"click_button(this);\">$btn_txt</button>";
			$display .= $times_table;
			$display .= '</div><br>';
		}
	}
	else {
		$display = "<br><b>Sorry, our records do not show you registered for this course.<br>
								If you would like to be added, please send an email to 
								<a href='mailto:byu.software.engineering@gmail.com'>byu.software.engineering@gmail.com</a> 
								and we will add you to the course. Thank you.</b><br><br>";
	}
}
// ---------------------------------------------------------------------------------------------------------------------
// Core Code (execution begins here) -----------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------------------------
$net_id = $_GET['netId'];// Get student's netID for database actions
$full_name = $_GET['fullName'];// Get student's full name for display purposes
$title = "Podcast Report | CS 428 - Software Engineering";
databaseConnect();
if (isset($_GET['update']))// The database will be updated only if update is set
	updateDatabase();
configDisplay();// Function to populate podcast buttons and timestamps
?>
<!--#########################################################################-->
<!-- Start HTML #############################################################-->
<!--#########################################################################-->
<!DOCTYPE html>
<html>
<?php require "head.php"; ?>
<body>
	<!-- Style ################################################################-->
	<style>
		div.div-txt { margin-left: 25px; }
		button { margin-left: 50px; }
		table { margin-left: 50px; }
		b { font-size: 125%; }
		}
	</style>
	<!-- Script ################################################################-->
	<script type="text/javascript">
		function showDisplay() {
			alert('<?php echo $display; ?>');
		}
		function click_button(btn) {
			text = btn.innerHTML;
			if (text == "Click when watched") {
				$("#action").val("add-time");
				$("#podcast-id").val(btn.getAttribute("id").substr(2));
				$("#update-form").submit();
		  }
			else if (text == "Undo") {
				var answer = confirm("Unmarking this action means you will not get credit for clicking the button before.");
				if (answer) {
					$("#action").val("delete-time");
					$("#podcast-id").val(btn.getAttribute("id").substr(2));
					$("#update-form").submit();
				}
		  }
		}
	</script>
	<form id="update-form" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<input type="hidden" id="action" name="action"></input>
		<input type="hidden" id="update" name="update"  value="1"></input>
		<input type="hidden" id="podcast-id" name="podcast-id"></input>
		<input type="hidden" id="netId" name="netId" value="<?php echo $net_id;?>"></input>
		<input type="hidden" id="fullName" name="fullName" value="<?php echo $full_name;?>"></input>
	</form>
	<!--#######################################################################-->
	<!-- Start Content Display ################################################-->
	<!--#######################################################################-->
	<?php require 'header.html'; ?>
	<div class="content">
			<!--<button type="button" onclick="showDisplay();">click me</button>-->
			<?php echo $display; ?>
	</div>
<?php require 'footer.html'; ?>
</body>
</html>










