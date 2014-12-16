<?php
class PodcastsDAO {
	var $mysqli;
	//------------------------------
	function PodcastsDAO(&$mysqli) {
		$this->mysqli = $mysqli;
	}
	//-------------------------------
	function podcastExists($podcast_name) {
		$sql = "SELECT * FROM `podcasts` WHERE `podcast_name`='$podcast_name'";
		$result = $this->mysqli->query($sql);
		$result_size = Database::getNumRows($result);
		return $result_size > 0;
	}
	//------------------------------------
	// Function to take podcast name and return corresponding podcast id from database
	function getPodcastId($podcast_name) {
		$sql = "SELECT `podcast_id` FROM `podcasts` WHERE `podcast_name`='$podcast_name'";
		$result = $this->mysqli->query($sql);
		return Database::getNextRow($result)['podcast_id'];
	}
	//------------------------------------
	// Function to take podcast id and return corresponding podcast name from database
	function getPodcastName($podcast_id) {
		$podcast_id = $this->mysqli->real_escape_string($podcast_id);
		$sql = "SELECT `podcast_name` FROM `podcasts` WHERE `podcast_id`='$podcast_id'";
		$result = $this->mysqli->query($sql);
		return Database::getNextRow($result)['podcast_name'];
	}
	//-------------------------
	function getAllPodcasts() {
		$sql = 'SELECT * FROM `podcasts` ORDER BY `podcast_name` ASC';
		return $this->mysqli->query($sql);
	}
	//------------------------------
	function addPodcast($podcast_name) {
		$sql = "INSERT INTO `podcasts`(`podcast_name`) VALUES ('$podcast_name')";
		return $this->mysqli->query($sql);
	}
}
?>
