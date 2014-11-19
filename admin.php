<?php
	function addToDatabase() {
		$action = $_GET['action'];
		$db = new Database();

		if ($action == "student") {
			$studentsDAO = &$db->getStudentsDAO();
			$students = json_decode($_GET['values']);
			$success = true;
			$unsuccessful = array();

			foreach ($students as $new_id) {
				if (!$studentsDAO->studentExists($new_id)) {
					$indiv_success = $studentsDAO->addStudentId($new_id);
					$success = $success && $indiv_success;

					if (!$indiv_success) {
						$unsuccessful[] = $new_id;
					}
				}
			}

			if ($success) {
				$db->close(true);
				echo '<script>alert("All student net ids were successfully added.")</script>';
			}
			else {
				$db->close(false);
				$output = '<script>alert("The students net ids were not added to the database.\n'
										. 'The following net ids caused the problem:\n';
				
				foreach ($unsuccessful as $each) {
					$output .= $each . '\n';
				}
				$output .= '")</script>';

			}
		}
		else if ($action == "podcast") {
			$podcastsDAO = &$db->getPodcastsDAO();
			$podcasts = json_decode($_GET['values']);
			$success = true;
			$unsuccessful = array();

			foreach ($podcasts as $podcast_name) {
				if (!$podcastsDAO->podcastExists($podcast_name)) {
					$indiv_success = $podcastsDAO->addPodcast($podcast_name);
					$success = $success && $indiv_success;

					if (!$indiv_success) {
						$unsuccessful[] = $podcast_name;
					}
				}
			}

			if ($success) {
				$db->close(true);
				echo '<script>alert("All podcasts were successfully added.")</script>';
			}
			else {
				$db->close(false);
				$output = '<script>alert("The podcasts were not added to the database.\n'
										. 'The following podcasts caused the problem:\n';
				
				foreach ($unsuccessful as $each) {
					$output .= $each . '\n';
				}
				$output .= '")</script>';
		}
	}
}

	function configContent() {
		global $CONTENT;
		$db = new Database();
		$studentsDAO = $db->getStudentsDAO();
		$podcastsDAO = $db->getPodcastsDAO();

		$CONTENT .= '<form id="add-form" method="get" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">' . "\n";
		$CONTENT .= '<input type="hidden" id="page" name="page" value="admin"></input>' . "\n";
		$CONTENT .= '<input type="hidden" id="action" name="action"></input>' . "\n";
		$CONTENT .= '<input type="hidden" id="update" name="update"  value="1"></input>' . "\n";
		$CONTENT .= '<input type="hidden" id="values" name="values"></input>' . "\n";
		$CONTENT .= '</form>' . "\n";

		$CONTENT .= '<div class="center float-parent" style="width:50%">' . "\n";
		$CONTENT .= '<div class="tables">' . "\n";
		$CONTENT .= '<h3>Students</h3>' . "\n";
		$CONTENT .= '<table><tbody>' . "\n";
		$CONTENT .= '<tr><th>Net ID</th><th>Full Name</th></tr>' . "\n";
		$students_result = $studentsDAO->getAllStudents();
		while ($row = Database::getNextRow($students_result)) {
			$net_id = $row['net_id'];
			$full_name = $row['full_name'];
			$CONTENT .= "<tr><td>$net_id</td><td>$full_name</td></tr>" . "\n";
		}
		$CONTENT .= '</tbody></table>' . "\n";
		$CONTENT .= '<button onclick="addStudents()">Add new student(s)</button>' . "\n";
		$CONTENT .= '</div>' . "\n";
		$CONTENT .= '<div class="tables">' . "\n";
		$CONTENT .= '<h3>Podcasts</h3>' . "\n";
		$CONTENT .= '<table><tbody>' . "\n";
		$CONTENT .= '<tr><th>Podcast Name</th></tr>' . "\n";
		$podcasts_result = $podcastsDAO->getAllPodcasts();
		while ($row = Database::getNextRow($podcasts_result)) {
			$podcast_name = $row['podcast_name'];
			$CONTENT .= "<tr><td>$podcast_name</td></tr>" . "\n";
		}
		$CONTENT .= '</tbody></table>' . "\n";
		$CONTENT .= '<button onclick="addPodcasts()">Add new podcast(s)</button>' . "\n";
		$CONTENT .= '</div>' . "\n";
		$CONTENT .= '</div>' . "\n";
	}

	function configAuxContent() {
		global $AUX_CONTENT;
		$AUX_CONTENT .= '	<style>
												div.tables {
													float: left;
													margin: 25px;
												}
												h3 {
													text-align: center;
												}
												</style>';
	} 

	$TITLE = 'Admin Page | ' . $TITLE;
	$CONTENT = "<h1>Administrator Page</h1>" . "\n";
	if (isset($_GET['update'])) {
		addToDatabase();
	}
	configContent();
	configAuxContent();
?>








