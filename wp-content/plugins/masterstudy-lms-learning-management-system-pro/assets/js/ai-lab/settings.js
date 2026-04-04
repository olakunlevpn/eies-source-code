(function($) {
	$(document).ready(function() {
		let total_price = 0;
		
		let dalle2_images = $('#total-images-data[token="dall-e-2"]').val() ?? 0;
		total_price += dalle2_images * 0.02;
		
		let dalle3_images = $('#total-images-data[token="dall-e-3"]').val() ?? 0;
		total_price += dalle3_images * 0.04;
		
		$('.total-tokens-data').each(function() {
			let tokens = $(this).val() ?? 0;
			total_price += tokens / 1000 * 0.02;
		});
		
		total_price = (Math.round(total_price * 100) / 100).toFixed(2);
		$('.total-requests-price').text('('+total_price+'$)');
	});
})(jQuery);
