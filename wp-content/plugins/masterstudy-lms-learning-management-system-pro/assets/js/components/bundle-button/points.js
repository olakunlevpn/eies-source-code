(function($) {
    $(document).ready(function() {
        $('.masterstudy-bundle-button .masterstudy-points').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);

            if ($this.hasClass('masterstudy-points-not-enough-points')) return false;

            var bundleId = $this.data('bundle');
            var buttonData = window.masterstudy_bundle_button_points.find(item => item.bundle_id == bundleId);

            if (!buttonData) {
                console.error('No data found for bundle_id:', bundleId);
                return false;
            }

            if (!confirm(buttonData.translate.confirm)) return false;

            $.ajax({
                url: buttonData.ajax_url,
                dataType: 'json',
                context: this,
                data: {
                    action: 'stm_lms_buy_bundle_for_points',
                    bundle_id: buttonData.bundle_id,
                    nonce: buttonData.get_nonce,
                },
                beforeSend: function() {
                    $this.addClass('loading');
                },
                complete: function(data) {
                    var response = data['responseJSON'];

                    if (response && response.url) {
                        window.location.href = response.url;
                    }

                    $this.removeClass('loading');
                }
            });
        });

        $('.masterstudy-bundle-button .masterstudy-points__icon').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var win = window.open($(this).data('href'), '_blank');
            if (win) win.focus();
        });
    });
})(jQuery);