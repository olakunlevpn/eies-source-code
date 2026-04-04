(function($) {
    $(document).ready(function() {
        $('.masterstudy-points-button').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);

            if ($this.hasClass('masterstudy-points-button_not-enough-points')) return false;

            var courseId = $this.data('course');
            var buttonData = window.masterstudy_buy_button_points.find(item => item.course_id == courseId);

            if (!buttonData) {
                console.error('No data found for course_id:', courseId);
                return false;
            }

            if (!confirm(buttonData.translate.confirm)) return false;

            $.ajax({
                url: buttonData.ajax_url,
                dataType: 'json',
                context: this,
                data: {
                    action: 'stm_lms_buy_for_points',
                    course_id: buttonData.course_id,
                    nonce: buttonData.get_nonce,
                },
                beforeSend: function() {
                    $this.addClass('masterstudy-points-button__loading');
                },
                complete: function(data) {
                    var response = data['responseJSON'];

                    if (response && response.url) {
                        window.location.href = response.url;
                    }

                    $this.removeClass('masterstudy-points-button__loading');
                }
            });
        });

        $('.masterstudy-points__icon').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var win = window.open($(this).data('href'), '_blank');
            if (win) win.focus();
        });
    });
})(jQuery);