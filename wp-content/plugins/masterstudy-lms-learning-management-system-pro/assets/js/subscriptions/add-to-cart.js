(function ($) {
	$(document).ready(function () {
		$(".masterstudy-add-to-cart-subscription").click(function () {
			const button = $(this);
			const redirectUrl = button.data('redirect');
			let planId = button.data("plan-id");

			if (redirectUrl) {
				window.location.href = redirectUrl;
				return;
			}

			let current_data = {
				action: 'stm_lms_add_to_cart_subscription',
				plan_id: planId,
				nonce: stm_lms_nonces['stm_lms_add_to_cart_subscription']
			};

			if (buy_button_subs_data.guest_checkout) {
				current_data['action'] = 'stm_lms_add_to_cart_guest';
				current_data['nonce'] = buy_button_subs_data.guest_nonce;
				deleteCookie('stm_lms_notauth_cart');

				let currentCart = getCookie('stm_lms_notauth_cart');

				currentCart = currentCart ? JSON.parse(decodeURIComponent(currentCart)) : [];

				let plan_id_str = planId.toString();
				currentCart = currentCart.map(String);

				if (!currentCart.includes(plan_id_str)) {
					currentCart.push(plan_id_str);
				}

				setCookie('stm_lms_notauth_cart', JSON.stringify(currentCart).replace(/"/g, ''), {path: '/'});
			} else if (!buy_button_subs_data.logged_in) {
				return;
			}

			$.ajax({
				url: stm_lms_ajaxurl,
				type: 'POST',
				data: current_data,
				success(res) {
					if (typeof res.success !== 'undefined') {
						if (res.data) {
							if (res.data.redirect) {
								window.location.href = res.data.cart_url;
							} else {
								if (res.data.text) {
									button.find('.masterstudy-purchase-button__title').text(res.data.text);
								}
								button.attr('data-redirect', res.data.cart_url);
							}
						}
					} else if (res['cart_url']) {
						if (res['redirect']) {
							window.location.href = res['cart_url'];
						} else {
							if (res['text']) {
								button.find('.masterstudy-purchase-button__title').text(res['text']);
							}
							button.attr('data-redirect', res['cart_url']);
						}
					}
				}
			})
		});

		function getCookie(name) {
			const value = `; ${document.cookie}`;
			const parts = value.split(`; ${name}=`);
			if (parts.length === 2) return parts.pop().split(';').shift();
		}

		function setCookie(name, value, options = {}) {
			document.cookie = `${name}=${encodeURIComponent(value)}; path=${options.path}`;
		}

		function deleteCookie(name) {
			document.cookie = name + "=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";

			document.cookie = name + "=; path=/; domain=" + window.location.hostname + "; expires=Thu, 01 Jan 1970 00:00:00 GMT";

			const parts = window.location.hostname.split('.');
			if (parts.length > 2) {
				const rootDomain = '.' + parts.slice(-2).join('.');
				document.cookie = name + "=; path=/; domain=" + rootDomain + "; expires=Thu, 01 Jan 1970 00:00:00 GMT";
			}
		}
	});
})(jQuery);
