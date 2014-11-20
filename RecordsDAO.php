<?php
class RecordsDAO {
	var $mysqli;
	//-----------------------------
	function RecordsDAO(&$mysqli) {
		$this->mysqli = $mysqli;
	}
	//-------------------------------------------------------------------
	// Function to insert a record of a student watching a podcast
	function addPodcastWatchedRecord($net_id, $podcast_id, $percent_read, $grade_week) {
		$sql = "INSERT INTO `records`(`net_id`, `podcast_id`, `percent_read`, `is_valid`, `grade_week`) VALUES ('$net_id',$podcast_id,$percent_read,'1','$grade_week')";
		// echo "<script>alert(\"$sql\");</script>";
		return $this->mysqli->query($sql);
	}
	//---------------------------------------------------------
	function deletePodcastWatchedRecord($net_id, $podcast_id) {
		$sql = "UPDATE `records` SET `is_valid`='0',`grade_week`='0' WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' AND `is_valid`='1'";
		return $this->mysqli->query($sql);
	}
	//--------------------------------------------------------------------------
	function getPodcastSpecificValidPodcastWatchedRecord($net_id, $podcast_id) {
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' AND `is_valid`='1'";
		return $this->mysqli->query($sql);
	}
	//---------------------------------------------------------------------
	function getAllValidPodcastWatchedRecordsByGradeWeek($net_id, $order) {
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `is_valid`='1' ORDER BY `grade_week` $order";
		return $this->mysqli->query($sql);
	}
	//-----------------------------------------------------------------------
	function getGradeWeekSpecificPodcastWatchedRecord($net_id, $grade_week) {
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `grade_week`='$grade_week'";
		return $this->mysqli->query($sql);
	}
	//------------------------------------------------------------------------
	function getAllRecordsForStudentAndPodcast($net_id, $podcast_id, $order) {
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `podcast_id`='$podcast_id' ORDER BY `time` $order";
		return $this->mysqli->query($sql);
	}
	//-------------------------------------------------------------------------------
	function existsPodcastWatchedRecordForStudentAndGradeWeek($net_id, $grade_week) {
		$sql = "SELECT * FROM `records` WHERE `net_id`='$net_id' AND `grade_week`='$grade_week'";
		$result = $this->mysqli->query($sql);
		return Database::getNextRow($result)['net_id'] != '';
	}
}
?>