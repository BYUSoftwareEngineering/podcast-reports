function updatePodcastReport(btn) {
	text = btn.innerHTML;
	if (text == "Click when watched") {
		var podcast_id = btn.getAttribute("id").substr(4);
		$("#action").val("add-time");
		$("#podcast-id").val(podcast_id);


		var sel_id = '#sel_' + podcast_id;
		var percent_read = -1;
		if ($(sel_id).length) {
			percent_read = $(sel_id).val();
		}
		$("#percent-read").val(percent_read);

		$("#update-form").submit();
  }
	else if (text == "Undo") {
		var answer = confirm("Are you sure you want to delete this entry?");
		if (answer) {
			$("#action").val("delete-time");
			$("#podcast-id").val(btn.getAttribute("id").substr(4));
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