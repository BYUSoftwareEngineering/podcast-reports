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

	function displayContent() {
		global $CONTENT;
		$db = new Database();
		$studentsDAO = $db->getStudentsDAO();
		$podcastsDAO = $db->getPodcastsDAO();
		//==============================================================================================================
		?>

<form id="add-form" method="get" action=<?php echo '"' . htmlspecialchars($_SERVER["PHP_SELF"]) . '"';?>>
	<input type="hidden" id="page" name="page" value="admin"></input>
	<input type="hidden" id="action" name="action"></input>
	<input type="hidden" id="update" name="update"  value="1"></input>
	<input type="hidden" id="values" name="values"></input>
</form>

<div class="center float-parent" style="width:50%">
	<div class="tables">
		<h3>Students</h3>
		<table><tbody>
			<tr><th>Net ID</th><th>Full Name</th></tr>

		<?php
		//--------------------------------------------------------------------------------------------------------------
		$students_result = $studentsDAO->getAllStudents();
		
		while ($row = Database::getNextRow($students_result)) {
			$net_id = $row['net_id'];
			$full_name = $row['full_name'];
			//==========================================================================================================
			?>

			<tr><td><?php echo $net_id; ?></td><td><?php echo $full_name; ?></td></tr>

			<?php
			//----------------------------------------------------------------------------------------------------------
		}
		//==============================================================================================================
		?>

		</tbody></table>
		<button onclick="addStudents()">Add new student(s)</button>
	</div>
	<div class="tables">
		<h3>Podcasts</h3>
		<table><tbody>
			<tr><th>Podcast Name</th></tr>

		<?php
		//--------------------------------------------------------------------------------------------------------------
		$podcasts_result = $podcastsDAO->getAllPodcasts();
		
		while ($row = Database::getNextRow($podcasts_result)) {
			$podcast_name = $row['podcast_name'];
			//==========================================================================================================
			?>

			<tr><td><?php echo $podcast_name; ?></td></tr>

			<?php
			//----------------------------------------------------------------------------------------------------------
		}
		//==============================================================================================================
		?>

		</tbody></table>
		<button onclick="addPodcasts()">Add new podcast(s)</button>
	</div>
</div>

		<?php
		//--------------------------------------------------------------------------------------------------------------
	}

	function displayAuxContent() {
		//==============================================================================================================
		?>
<style>
	div.tables {
		float: left;
		margin: 25px;
	}
	h3 {
		text-align: center;
	}
</style>

		<?php
		//--------------------------------------------------------------------------------------------------------------
	} 

	$TITLE = 'Admin Page | ' . $TITLE;
	$CONTENT = "<h1>Administrator Page</h1>" . "\n";
	if (isset($_GET['update'])) {
		addToDatabase();
	}
	// configContent();
	// configAuxContent();
?>








