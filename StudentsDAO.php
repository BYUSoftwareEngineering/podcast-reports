<?php
class StudentsDAO {
	var $mysqli;
	//------------------------------
	function StudentsDAO(&$mysqli) {
		$this->mysqli = $mysqli;
	}
	//-------------------------------
	function studentExists($net_id) {
		$sql = "SELECT * FROM `students` WHERE `net_id`='$net_id'";
		$result = $this->mysqli->query($sql);
		$result_size = Database::getNumRows($result);
		return $result_size > 0;
	}
	//-------------------------
	function getAllStudents() {
		$sql = 'SELECT * FROM `students`';
		return $this->mysqli->query($sql);
	}
	//--------------------------------
	function getStudentName($net_id) {
		$sql = "SELECT `full_name` FROM `students` WHERE `net_id`='$net_id'";
		return $this->mysqli->query($sql);
	}
	//--------------------------------------------
	function addStudentName($net_id, $full_name) {
		$sql = "UPDATE `students` SET `full_name`='$full_name' WHERE `net_id`='$net_id'";
		return $this->mysqli->query($sql);
	}
	//------------------------------
	function addStudentId($net_id) {
		$sql = "INSERT INTO `students`(`net_id`) VALUES ('$net_id')";
		return $this->mysqli->query($sql);
	}
}
?>