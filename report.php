<?php
	// ---------------------------------------------------------------------------------------------------------------------
	// Transaction Functions -----------------------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------------------------------------------
	function updateGradeWeeks($net_id) {
		// For right now, the added value of this function isn't worth the cost of implementing it
	}
	function updateDatabase() {
		global $NET_ID;
		$action = $_GET['action'];
		$podcast_id = $_GET['podcast-id'];
		$success = true;
		$db = new Database();
		$recordsDAO = &$db->getRecordsDAO();

		$result = $recordsDAO->getPodcastSpecificValidPodcastWatchedRecord($NET_ID, $podcast_id);
		$result_size = $db->getNumRows($result);
		$grade_week = $db->getNextGradeWeek($NET_ID);
		if (($action == "add-time") && ($result_size == 0))
			$success = $recordsDAO->addPodcastWatchedRecord($NET_ID, $podcast_id, $grade_week);
		else if ($action == "delete-time") {
			if ($result_size == 1) {
				$success = $recordsDAO->deletePodcastWatchedRecord($NET_ID, $podcast_id);
				//updateGradeWeeks($NET_ID);
			}
			else if ($result_size > 1)
				echo "<script> alert('ERROR: Unable to delete entry. Multiple valid entries for $NET_ID and podcast #$podcast_id. Please contact the TAs to get the problem fixed.'); </script>";
			else // result_size must be 0
				echo "<script> alert('ERROR: Unable to delete entry. No valid entries for $NET_ID and podcast #$podcast_id'); </script>";
		}
		if ($success) {
			$db->close(true);
		}
		else {
			$db->close(false);
		}

	}
	// Function to configure the podcast information to be displayed
	function configContent() {
		global $NET_ID, $CONTENT, $FULL_NAME, $BASE_DATE;
		$db = new Database();
		$studentsDAO = &$db->getStudentsDAO();
		$podcastsDAO = &$db->getPodcastsDAO();
		$recordsDAO = &$db->getRecordsDAO();
		//if (!($studentsDAO instanceof StudentsDAO))
			//exit("Not an instance of StudentsDAO: " + $studentsDAO.get_class());

		if ($studentsDAO->studentExists($NET_ID)) {
			$CONTENT .= '<form id="update-form" method="get" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">' . "\n";
			$CONTENT .= '<input type="hidden" id="page" name="page" value="report"></input>' . "\n";
			$CONTENT .= '<input type="hidden" id="action" name="action"></input>' . "\n";
			$CONTENT .= '<input type="hidden" id="update" name="update"  value="1"></input>' . "\n";
			$CONTENT .= '<input type="hidden" id="podcast-id" name="podcast-id"></input>' . "\n";
			$CONTENT .= '<input type="hidden" id="netId" name="netId" value="' . $NET_ID . '"></input>' . "\n";
			$CONTENT .= '<input type="hidden" id="fullName" name="fullName" value="' . $FULL_NAME . '"></input>' . "\n";
			$CONTENT .= '</form>' . "\n";
			$CONTENT .= "<h1>Podcast Report</h1>" . "\n";
			$CONTENT .= "<h2>$FULL_NAME</h2>" . "\n";
			$CONTENT .= '<div id="podcasts-summary">' . "\n";
			$podcast_result = $podcastsDAO->getAllPodcasts();
			// For each podcast, we need to construct a list of all the times the student has watched it
			while ($row = Database::getNextRow($podcast_result)) {
				$podcast_id = $row['podcast_id'];
				$btn_id = 'p_' . $podcast_id;
				$podcast_name = $row['podcast_name'];
				$watched = false;
				$div_color = '';
				$btn_txt = 'Click when watched';
				$div_txt = 'You have not watched this podcast yet.';
				$student_result = $recordsDAO->getAllRecordsForStudentAndPodcast($NET_ID, $podcast_id, 'DESC');
				$times_table = '<table><tbody>' . "\n";
				$times_table .= '<tr><th>All Times Watched</th><th>Week Graded</th></tr>' . "\n";
				if ($student_result) {
					while ($times_row = Database::getNextRow($student_result)) {
						$strike_before = "<del>";
						$strike_after = "</del>";
						if ($times_row['is_valid']) {
							$watched = true;
							$strike_before = $strike_after = "";
						}
						$times_table .= '<tr><td>' . $strike_before . $times_row['time'] . $strike_after . '</td>';
						if ($times_row['grade_week'] != '0')
							$times_table .= '<td>Week ' . $times_row['grade_week'] . '</td>';
						else
							$times_table .= '<td>N/A</td>';
						$times_table .= '</tr>' . "\n";
					}
				}
				$times_table .= '</table></tbody>' . "\n";
				if ($watched) {
					$btn_txt = 'Undo';
					$div_txt = 'You have watched this podcast.';
					$div_color = ' style="color:#ccc"';
				}
				$CONTENT .= '<div' . $div_color . '>' . "\n";
				$CONTENT .= "<div class=\"box\"><b>$podcast_name:</b>&nbsp;</div><div class=\"div-txt\">$div_txt</div>" . "\n";
				$CONTENT .= "<button type=\"button\" id=\"$btn_id\" onclick=\"updatePodcastReport(this);\">$btn_txt</button>" . "\n";
				$CONTENT .= $times_table;
				$CONTENT .= '</div><br>' . "\n";
			}
			$CONTENT .= '</div>' . "\n";
			$CONTENT .= '<div id="grade-week-summary">' . "\n";
			$CONTENT .= '<div><b>Weekly Summary:</b></div><br>' . "\n";
			$CONTENT .= '<table><tbody>' . "\n";
			for ($grade_week = 1; $grade_week <= 14; $grade_week++) { 
				$result = $recordsDAO->getGradeWeekSpecificPodcastWatchedRecord($NET_ID, $grade_week);
				$podcast_id = Database::getNextRow($result)['podcast_id'];
				$podcast_name	= $podcastsDAO->getPodcastName($podcast_id);
				$tr_color = '';
				if ($podcast_name != '')
					$tr_color = ' style="color:#ccc"';
				if ($podcast_name == '' && $grade_week == $db->getCurrentGradeWeekFromBase($BASE_DATE))
					$podcast_name = '(current week)';
				$CONTENT .= '<tr' . $tr_color . '><td>Week ' . $grade_week . ' -</td><td>' . $podcast_name . '</td></tr>' . "\n";
			}
			$CONTENT .= '</tbody></table>' . "\n";
			$CONTENT .= '</div>' . "\n";
		}
		else {
			$CONTENT = "<br><b>Sorry, our records do not show you registered for this course.<br>
									If you would like to be added, please send an email to 
									<a href='mailto:byu.software.engineering@gmail.com'>byu.software.engineering@gmail.com</a> 
									and we will add you to the course. Thank you.</b><br><br>";
		}
		$db->close(true);
	}
	// Function to insert style and script tags to be used by page
	function configAuxContent() {
		global $AUX_CONTENT;
		$AUX_CONTENT .= '
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
												b {
													font-size: 125%;
												}
											</style>';
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
	configContent();// Function to populate podcast buttons and timestamps
	configAuxContent();
	checkName();
?>
	










