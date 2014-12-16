<?php
	// ---------------------------------------------------------------------------------------------------------------------
	// Transaction Functions -----------------------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------------------------------------------
	function reloadPage() {
		global $NET_ID, $FULL_NAME;
		?>
<form id="reload-page" method="get" action=<?php echo '"' . htmlspecialchars($_SERVER["PHP_SELF"]) . '"';?>>
	<input type="hidden" id="page" name="page" value="report"></input>
	<input type="hidden" id="netId" name="netId" value=<?php echo '"' . $NET_ID . '"'; ?>></input>
	<input type="hidden" id="fullName" name="fullName" value=<?php echo '"' . $FULL_NAME . '"'?>></input>
</form>
<script>
	document.getElementById("reload-page").submit();
</script>
		<?php
	}
	function updateGradeWeeks($db, $recordsDAO) {
		global $NET_ID;
		$timestamps = $recordsDAO->getValidTimestamps($NET_ID);
		$recordsDAO->clearGradeWeeks($NET_ID);
		// For each valid timestamp
		foreach ($timestamps as $ts) {
			$actual_week = $db->getGradeWeekFromTimestamp($ts);
			$grade_week = $db->getNextGradeWeek($NET_ID, $actual_week);
			$recordsDAO->setGradeWeekForTimestamp($NET_ID, $ts, $grade_week);
		}

		return;
	}
	//-------------------------
	function updateDatabase() {
		global $NET_ID, $EXTRA_CONTENT, $BASE_DATE;
		$action = $_GET['action'];
		$podcast_id = $_GET['podcast-id'];
		$success = true;
		$db = new Database();
		$recordsDAO = &$db->getRecordsDAO();

		$result = $recordsDAO->getPodcastSpecificValidPodcastWatchedRecord($NET_ID, $podcast_id);
		$result_size = $db->getNumRows($result);

		if (($action == "add-time") && ($result_size == 0)) {
			$percent_read = $_GET['percent-read'];

			if ($percent_read == '100'
				|| $percent_read == '90'
				|| $percent_read == '80'
				|| $percent_read == '70'
				|| $percent_read == '60'
				|| $percent_read == '50'
				|| $percent_read == '40'
				|| $percent_read == '30'
				|| $percent_read == '20'
				|| $percent_read == '10'
				|| $percent_read == '0'
				|| $percent_read == '-1') {
				$success = $recordsDAO->addPodcastWatchedRecord($NET_ID, $podcast_id, $percent_read);
				updateGradeWeeks($db, $recordsDAO);
			}
			else {
				echo '<script>alert("You must select a value for \'reading completed\' from the drop-down menu.");</script>';
			}
		}
		else if ($action == "delete-time") {
			if ($result_size == 1) {
				$success = $recordsDAO->deletePodcastWatchedRecord($NET_ID, $podcast_id);
				updateGradeWeeks($db, $recordsDAO);
			}
			else if ($result_size > 1) {
				echo "<script> alert('ERROR: Unable to delete entry. Multiple valid entries for $NET_ID and podcast #$podcast_id. Please contact the TAs to get the problem fixed.'); </script>";
			}
			else {// result_size must be 0
				echo "<script> alert('ERROR: Unable to delete entry. No valid entries for $NET_ID and podcast #$podcast_id'); </script>";
			}
		}
		if ($success) {
			$db->close(true);
		}
		else {
			$db->close(false);
		}

		reloadPage();
		exit();
	}
	// Creates the rows of the times table *****************************************************************************
	function createTimesTable($student_result, $has_reading, &$watched) {
		$times_table = '';
		// For each time the student has reported watching this podcast
		while ($times_row = Database::getNextRow($student_result)) {
			$strike_before = "<del>";
			$strike_after = "</del>";
			$percent_read = $times_row['percent_read'] . '%';
			// Does this podcast have a reading associated with it?
			if (!$has_reading) {
				$percent_read = "No reading required";
			}
			// Is this time watched the valid one?
			if ($times_row['is_valid']) {
				$watched = true;
				$strike_before = $strike_after = "";
			}
			// Print the timestamp
			$times_table .= '<tr><td>' . $strike_before . $times_row['time'] . $strike_after . '</td>';
			// Is the credit week greater than 0?
			if ($times_row['grade_week'] != '0') {
				$times_table .= '<td>Week ' . $times_row['grade_week'] . '</td>';
			}
			else {
				$color = '';
				if (!$watched) {
					$color = ' style="color: red"';
				}
				$times_table .= '<td' . $color . '>No credit earned</td>';
			}
			$times_table .= '<td>' . $strike_before . $percent_read . $strike_after . '</td>';
			// Close the row for this time the podcast was watched
			$times_table .= '</tr>' . "\n";
		}
		return $times_table;
	}
	// Displays the Percent Read drop-down *****************************************************************************
	function displayPercentReadDropDown($sel_id, $has_reading) {
		if ($has_reading) {
		//==============================================================================================================
		?>
		<select <?php echo "id=\"$sel_id\""; ?>>
			<option value="100">100%</option>
			<option value="90">90%</option>
			<option value="80">80%</option>
			<option value="70">70%</option>
			<option value="60">60%</option>
			<option value="50">50%</option>
			<option value="40">40%</option>
			<option value="30">30%</option>
			<option value="20">20%</option>
			<option value="10">10%</option>
			<option value="0">0%</option>
			<option value="-" selected>-</option>
		</select>
		<?php
		//--------------------------------------------------------------------------------------------------------------
		}
		else {
			echo 'No reading required';
		}
	}
	// Displays the Weekly Summary table *******************************************************************************
	function displayWeeklySummaryTable(&$db, &$recordsDAO, &$podcastsDAO) {
		global $NET_ID, $BASE_DATE;

		$current_ts = $db->getCurrentTimestamp();
		$current_week = $db->getGradeWeekFromTimestamp($current_ts);
		//==============================================================================================================
		?>
<div id="grade-week-summary">
	<h2>Weekly Summary:</h2>
	<table><tbody>
		<tr><th>Week</th><th>Podcast Watched</th><th>Reading Completed</th></tr>
		<?php
		//--------------------------------------------------------------------------------------------------------------
		for ($grade_week = 1; $grade_week <= 16; $grade_week++) {
			$result = $recordsDAO->getGradeWeekSpecificPodcastWatchedRecord($NET_ID, $grade_week);
			$podcast_id = $podcast_name = $percent_read = '';
			// Is there a record for this week?
			if ($row = Database::getNextRow($result)) {
				$podcast_id = $row['podcast_id'];
				$podcast_name = $podcastsDAO->getPodcastName($podcast_id);
				$percent_read = $row['percent_read'];
				$percent_read = ($percent_read == '' ? '' : ($percent_read == -1 ? 'No reading required' : $percent_read . '%'));
			}
			// If there is no record for this grade week, has this grade week already passed?
			else if ($grade_week < $current_week) {
				$podcast_name = 'No podcast watched';
				$percent_read = '-';
			}

			$is_current_week = '';
			if ($grade_week == $current_week) {
				$is_current_week = '*';
			}
			echo '<tr><td class="text-center">' . $is_current_week . $grade_week . $is_current_week . ' </td><td>' . $podcast_name . ' </td><td>' . $percent_read . '</td></tr>' . "\n";
		}
		//==============================================================================================================
		?>
	</tbody></table>
	<i style="margin-left: 50px; font-style: normal">* current week</i>
</div>
		<?php
		//--------------------------------------------------------------------------------------------------------------
	}
	// Function to configure the podcast information to be displayed ***************************************************
	function displayContent() {
		global $NET_ID, $FULL_NAME, $BASE_DATE;
		$db = new Database();
		$studentsDAO = &$db->getStudentsDAO();
		$podcastsDAO = &$db->getPodcastsDAO();
		$recordsDAO = &$db->getRecordsDAO();

		if ($studentsDAO->studentExists($NET_ID)) {
			//==========================================================================================================
			?>

<form id="update-form" method="get" action=<?php echo '"' . htmlspecialchars($_SERVER["PHP_SELF"]) . '"';?>>
	<input type="hidden" id="page" name="page" value="report"></input>
	<input type="hidden" id="action" name="action"></input>
	<input type="hidden" id="update" name="update"  value="1"></input>
	<input type="hidden" id="podcast-id" name="podcast-id"></input>
	<input type="hidden" id="percent-read" name="percent-read"></input>
	<input type="hidden" id="netId" name="netId" value=<?php echo '"' . $NET_ID . '"'; ?>></input>
	<input type="hidden" id="fullName" name="fullName" value=<?php echo '"' . $FULL_NAME . '"'?>></input>
</form>
<h1>Podcast Report</h1>
<h2><?php echo $FULL_NAME; ?></h2>
<div id="podcasts-summary">

			<?php
			//----------------------------------------------------------------------------------------------------------
			$podcast_result = $podcastsDAO->getAllPodcasts();
			// For each podcast, we need to construct a list of all the times the student has watched it
			while ($row = Database::getNextRow($podcast_result)) {
				$podcast_id = $row['podcast_id'];
				$btn_id = 'but_' . $podcast_id;
				$sel_id = 'sel_' . $podcast_id;
				$podcast_name = $row['podcast_name'];
				$watched = false;
				$has_reading = ($row['has_reading'] == 1 ? true : false);
				$div_color = '';
				$btn_txt = 'Click when watched';
				$div_txt = 'You have <b style="color: red">NOT</b> watched this podcast yet.';
				$student_result = $recordsDAO->getAllRecordsForStudentAndPodcast($NET_ID, $podcast_id, 'DESC');
				$times_table = '<table><tbody>' . "\n";
				$times_table .= '<tr><th>Time Completed</th><th>Credit Earned for</th>';
				$times_table .= '<th>Reading Completed</th>';
				// Close the header row
				$times_table .= '</tr>' . "\n";
				// If there are records for this student and podcast
				if ($student_result) {
					$times_table .= createTimesTable($student_result, $has_reading, $watched);
				}

				$times_table .= '</table></tbody>' . "\n";
				// Has this podcast been watched?
				if ($watched) {
					$btn_txt = 'Undo';
					$div_txt = 'You have watched this podcast.';
					$div_color = ' style="color:#aaa"';

				}
				//======================================================================================================
				?>

	<div <?php echo $div_color; ?>>
		<h2 class="podcast-title"><?php echo $podcast_name; ?>:</h2>
		<div class="div-txt"><?php echo $div_txt; ?></div>
		<button type="button" <?php echo "id=\"$btn_id\""; ?> onclick="updatePodcastReport(this);"><?php echo $btn_txt; ?></button>


				<?php
				//------------------------------------------------------------------------------------------------------
				// Has this podcast been watched?
				if (!$watched) {
					echo "<b>Reading completed: </b>";
					displayPercentReadDropDown($sel_id, $has_reading);
				}

				echo $times_table;
				//======================================================================================================
				?>
	</div><br>
				<?php
				//------------------------------------------------------------------------------------------------------
			}
			//==========================================================================================================
			?>
</div>
			<?php
			//----------------------------------------------------------------------------------------------------------
			displayWeeklySummaryTable($db, $recordsDAO, $podcastsDAO);
		}
		else {
			//==========================================================================================================
			?>

<br><b>Sorry, our records do not show you registered for this course.<br>
If you would like to be added, please send an email to
<a href='mailto:byu.software.engineering@gmail.com'>byu.software.engineering@gmail.com</a>
and we will add you to the course. Thank you.</b><br><br>

			<?php
		}
		$db->close(true);
	}
	// Function to insert style and script tags to be used by page
	function displayAuxContent() {
		//==============================================================================================================
		?>
<style>
	div.div-txt {
		margin-left: 25px;
	}
	button {
		margin: 10px 50px;
	}
	table {
		margin-left: 50px;
	}
</style>

		<?php
		//--------------------------------------------------------------------------------------------------------------
	}
	// Function to make sure student's name is in the database
	function checkName() {
		global $NET_ID, $FULL_NAME;
		$db = new Database();
		$studentsDAO = &$db->getStudentsDAO();
		$success = true;

		$result = $studentsDAO->getStudentName($NET_ID);
		if(Database::getNextRow($result)['full_name'] == '')
			$success = $studentsDAO->addStudentName($NET_ID, $FULL_NAME);

		$db->close($success);
	}
	// ---------------------------------------------------------------------------------------------------------------------
	// Core Code (execution begins here) -----------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------------------------------------------
	$NET_ID = $_GET['netId'];// Get student's netID for database actions
	$FULL_NAME = $_GET['fullName'];// Get student's full name for display purposes
	$TITLE = "Podcast Report | " . $TITLE;
	if (isset($_GET['update']))// The database will be updated only if update is set
		updateDatabase();
	// configContent();// Function to populate podcast buttons and timestamps
	// configAuxContent();
	checkName();
?>
