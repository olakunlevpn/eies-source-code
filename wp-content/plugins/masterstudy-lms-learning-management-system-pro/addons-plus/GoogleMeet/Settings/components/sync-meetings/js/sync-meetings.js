(function ($) {
	$(document).ready(function () {
		$('body').on('click', '.nuxy_reset_sync_meetings', function () {
			$(this).addClass('disabled').attr('disabled', 'disabled');
			$(this).css('cursor','not-allowed');
			$('.nuxy_reset_sync_meetings').addClass('installing');
			var formData = new FormData();
			var vm = $(this);
			formData.append('action', 'masterstudy_lms_admin_sync_meetings');
			formData.append('nonce', stm_google_meet_ajax_variable.nonce);
			$.ajax({
				url: stm_google_meet_ajax_variable.url,
				type: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success(response) {
					if (typeof response.url !== 'undefined') {
						window.location.href = response.url;
					}
					setTimeout(function() {
						$('.nuxy_reset_sync_meetings').removeClass('installing');
						$('.nuxy_reset_sync_meetings').addClass('installed');
					}, 2000);
				},
				error(xhr, ajaxOptions, thrownError) {
					$('#sync-error-message').html(xhr.responseText);
					$('#sync-error-message').css('display', 'block');
				}
			});
		});
	})
	
})(jQuery);