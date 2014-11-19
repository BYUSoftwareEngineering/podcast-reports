function updatePodcastReport(btn) {
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

function addStudents() {
	var input = prompt("Please enter a comma-separated list of the net ID of the students you would like to add", "student_1, student_2, student_3");
	var net_ids = input.split(/\s*,\s*/);
	var student_array = JSON.stringify(net_ids);
	$("#action").val("student");
	$("#values").val(student_array);
	$("#add-form").submit();

}

function addPodcasts() {
	var input = prompt("Please enter a comma-separated list of the names of the podcasts you would like to add", "podcast_1, podcast_2, podcast_3");
	var podcasts = input.split(/\s*,\s*/);
	var podcast_array = JSON.stringify(podcasts);
	$("#action").val("podcast");
	$("#values").val(podcast_array);
	$("#add-form").submit();
}