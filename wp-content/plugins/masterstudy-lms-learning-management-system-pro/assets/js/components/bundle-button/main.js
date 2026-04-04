(function ($) {
    $(document).ready(function () {

		$('.masterstudy-bundle-button').each(function () {
			const button = $(this);
			let animationActive = false;

			button.on('click', function() {
				toggleAnimation(button);
			});

			button.find('.masterstudy-bundle-button_plans-dropdown').on('click', function(event) {
				event.stopPropagation();
			});

			button.find('.masterstudy-bundle-button__link_disabled').on('click', function(event) {
				event.preventDefault();
			});

			function toggleAnimation(btn) {
				animationActive = !animationActive;
				$('.masterstudy-bundle-button').not(btn).removeClass('dropdown-show');
				btn.toggleClass('dropdown-show', animationActive);
			}

			$(document).on('click', function(event) {
				if (!$(event.target).closest(button).length && animationActive) {
					toggleAnimation(button);
				}
			});
		});

        $('[data-purchase-bundle]').on('click', handleButtonClick );
        $('[data-guest]').on('click', handleButtonClick );
        $('.masterstudy-bundle-button__link_active').on('click', handleButtonClick );

        function handleButtonClick(e) {
            const $this = $(this);

            if ( $this.attr('href') !== '#') {
                return;
            }

            e.preventDefault();

            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: {
                    action: bundle_data.guest_checkout ? 'stm_lms_add_to_cart_guest' : 'stm_lms_add_bundle_to_cart',
                    item_id: bundle_data.guest_checkout ? $this.data('guest') : $this.data('purchase-bundle'),
                    nonce: bundle_data.guest_checkout ? bundle_data.guest_nonce : bundle_data.nonce,
                },
                beforeSend: function () {
					$this.find('.masterstudy-bundle-button__title').addClass('masterstudy-bundle-button__loading');
				},
                complete: function (data) {
                    data = data['responseJSON'];
                    $this.find('.masterstudy-bundle-button__title').removeClass('masterstudy-bundle-button__loading');
                    $this.find('.masterstudy-bundle-button__title').text(data['text']);
                    if (data['cart_url']) {
                        if (data['redirect']) window.location = data['cart_url'];
                        $this.attr('href', data['cart_url']);
                    }
                }
            });

            if (bundle_data.guest_checkout) {
                let item_id = $this.data('guest');
                let currentCart = getCookie('stm_lms_notauth_cart');

                currentCart = (currentCart === undefined || currentCart === null) ? [] : JSON.parse(decodeURIComponent(currentCart));

                let item_id_str = item_id.toString();

                currentCart = currentCart.map(String);

                if (!currentCart.includes(item_id_str)) {
                    currentCart.push(item_id_str);
                }

                // Update cookies
                setCookie('stm_lms_notauth_cart', JSON.stringify(currentCart).replace(/"/g, ''), {path: '/'});

                // Get cookies
                function getCookie(name) {
                    const value = `; ${document.cookie}`;
                    const parts = value.split(`; ${name}=`);
                    if (parts.length === 2) return parts.pop().split(';').shift();
                }

                // Install cookies
                function setCookie(name, value, options = {}) {
                    document.cookie = `${name}=${value}; path=${options.path}`;
                }
            }
        }
    });
})(jQuery);