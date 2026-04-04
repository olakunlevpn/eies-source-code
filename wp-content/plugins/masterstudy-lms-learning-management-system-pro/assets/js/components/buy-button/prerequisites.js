(function ($) {
    $(document).ready(function () {
        $('.masterstudy-prerequisites__button').on('click', function (e) {
            e.preventDefault();
            let $container = $(this).closest('.masterstudy-prerequisites');
            $container.toggleClass('active');
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('.masterstudy-prerequisites').length) {
                $('.masterstudy-prerequisites.active').removeClass('active');
            }
        });

        $('.masterstudy-prerequisites-list__explanation-title').on('click', function (e) {
            e.preventDefault();
            let $parent = $(this).closest('.masterstudy-prerequisites-list__explanation');
            $parent.toggleClass('active');
        });
    });
})(jQuery);