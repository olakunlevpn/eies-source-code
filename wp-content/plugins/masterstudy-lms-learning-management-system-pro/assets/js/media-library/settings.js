(function ($) {
	$( document ).on(
		'click',
		'.integration-clear-cache-button',
		function (e) {
			const $this = $( this );
			e.preventDefault();
			console.log(stm_lms_pro_nonces)

			$.ajax(
				{
					url: stm_lms_ajaxurl,
					dataType: 'json',
					context: this,
					data: {
						action: 'stm_lms_flush_media_library_cache',
						nonce: stm_lms_pro_nonces['stm_lms_media_library_clear_integration_cache']
					},
					complete: function () {
						$this.addClass( 'integration-clear-cache-button_hidden' );
						$( '.integration-cache-exists-text' ).text( 'No cache' );
					}
				}
			);
		}
	);
})( jQuery );
