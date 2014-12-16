<?php
class RecordsDAO {
	var $mysqli;
	//-----------------------------
	function RecordsDAO(&$mysqli) {
		$this->mysqli = $mysqli;
	}
	//-------------------------------------------------------------------
	// Function to insert a record of a student watching a podcast
	function addPodcastWatchedRecord($net_id, $podcast_id, $percent_read) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$podcast_id = $this->mysqli->real_escape_string($podcast_id);
		$percent_read = $this->mysqli->real_escape_string($percent_read);

		$sql = "INSERT INTO `records`(`net_id`, `podcast_id`, `percent_read`, `is_valid`) VALUES ('$net_id',$podcast_id,$percent_read,'1')";
		// echo "<script>alert(\"$sql\");</script>";
		return $this->mysqli->query($sql);
	}
	//---------------------------------------------------------
	function deletePodcastWatchedRecord($net_id, $podcast_id) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$podcast_id = $this->mysqli->real_escape_string($podcast_id);

		$sql = "UPDATE `records` SET `is_valid`='0' WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' AND `is_valid`='1'";
		return $this->mysqli->query($sql);
	}
	//-----------------------------------------------------
	function setGradeWeekForTimestamp($net_id, $timestamp, $grade_week) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$timestamp = $this->mysqli->real_escape_string($timestamp);
		$grade_week = $this->mysqli->real_escape_string($grade_week);

		$sql = "UPDATE `records` SET `grade_week`='$grade_week' WHERE `net_id`='$net_id' AND `time`='$timestamp'";
		return $this->mysqli->query($sql);
	}
	//--------------------------------------------------------------------------
	function getPodcastSpecificValidPodcastWatchedRecord($net_id, $podcast_id) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$podcast_id = $this->mysqli->real_escape_string($podcast_id);
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' AND `is_valid`='1'";
		return $this->mysqli->query($sql);
	}
	//---------------------------------------------------------------------
	function getAllValidPodcastWatchedRecordsByGradeWeek($net_id, $order) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `is_valid`='1' ORDER BY `grade_week` $order";
		return $this->mysqli->query($sql);
	}
	//-----------------------------------------------------------------------
	function getGradeWeekSpecificPodcastWatchedRecord($net_id, $grade_week) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `grade_week`='$grade_week'";
		return $this->mysqli->query($sql);
	}
	//------------------------------------------------------------------------
	function getAllRecordsForStudentAndPodcast($net_id, $podcast_id, $order) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$podcast_id = $this->mysqli->real_escape_string($podcast_id);
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' ORDER BY `time` $order";
		return $this->mysqli->query($sql);
	}
	//-------------------------------------------------------------------------------
	function existsPodcastWatchedRecordForStudentAndGradeWeek($net_id, $grade_week) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `grade_week`='$grade_week'";
		$result = $this->mysqli->query($sql);
		return Database::getNextRow($result)['net_id'] != '';
	}
	//------------------------------------
	function getValidTimestamps($net_id) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$sql = "SELECT `time` FROM `records` WHERE `net_id`='$net_id' AND `is_valid`='1' ORDER BY `time` ASC";
		$result = $this->mysqli->query($sql);
		$timestamps = array();

		while ($row = Database::getNextRow($result)) {
			$timestamps[] = $row['time'];
		}

		return $timestamps;
	}
	//---------------------------------
	function clearGradeWeeks($net_id) {
		$net_id = $this->mysqli->real_escape_string($net_id);
		$sql = "UPDATE `records` SET `grade_week`='0' WHERE `net_id`='$net_id'";
		return $this->mysqli->query($sql);
	}
}
?>
