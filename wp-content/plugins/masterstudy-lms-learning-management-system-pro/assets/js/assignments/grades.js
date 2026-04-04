(function ($) {
	// Grades Table
	$(document).on('click', '.masterstudy-grades-hint__button', function (e) {
		e.preventDefault();
		$('.masterstudy-grades-table').css('display', 'flex').addClass('masterstudy-alert_open');
	});
	$(document).on('click', '.masterstudy-grades-table .masterstudy-alert__header-close', function (e) {
		e.preventDefault();
		$('.masterstudy-grades-table').removeClass('masterstudy-alert_open');
	});

	$('.masterstudy-alert').on('click', function(event) {
		if (event.target === this) {
			$(this).removeClass('masterstudy-alert_open');
		}
	});

	// User Assignments
	$(document).ready(function () {
		showGradeField();
	});
	$(document).on('change', '.masterstudy-lms-assignment-grade__type', function () {
		showGradeField();
	});

	// Enable Submit Review button on grade change
	$(document).on('change', '.masterstudy-lms-assignment-grade__field', function () {
		$('.masterstudy-user-assignment__submit a').removeClass('masterstudy-button_disabled');
	});

	function showGradeField() {
		const gradeType = $('.masterstudy-lms-assignment-grade__type').val();

		$('.masterstudy-lms-assignment-grade__field').hide();
		$('.masterstudy-lms-assignment-grade__hint').hide();
		$(`.masterstudy-lms-assignment-grade__field.field-${gradeType}`).css('display','inline-block');
		$(`.masterstudy-lms-assignment-grade__hint.hint-${gradeType}`).css('display','inline-block');
	}
})(jQuery);