(function ($) {
	$(document).ready(function () {
		$('body').on('click', '.nuxy_reset_credentials', function () {
			var formData = new FormData();
			var vm = $(this);
			formData.append('action', 'stm_gm_reset_credentials_action');
			formData.append('nonce', stm_google_meet_ajax_variable.nonce);
			if (confirm('Are you sure you want to delete this permanently from the site? Please confirm your choice?')) {
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
					},
					error(xhr, ajaxOptions, thrownError) {
						console.log(xhr.responseJSON)
					}
				});
			}

		});
		$('body').on('click', '.nuxy_change_account', function () {
			var formData = new FormData();
			var vm = $(this);
			formData.append('action', 'stm_gm_account_changed_action');
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
				},
				error(xhr, ajaxOptions, thrownError) {
					console.log(xhr.responseJSON)
				}
			});

		});
	})

})(jQuery);