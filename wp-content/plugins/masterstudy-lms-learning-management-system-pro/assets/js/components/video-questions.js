(function ($) {
    $(document).ready(function() {

        $('[data-id="masterstudy-video-question-submit"]').attr('disabled', 1).addClass('masterstudy-button_disabled');

        $('body').on('change', '.masterstudy-lesson-video-question input[type="radio"], .masterstudy-lesson-video-question input[type="checkbox"]', function() {
            const current_question = $(this).closest('.masterstudy-lesson-video-question');
            const submitButton = current_question.find('[data-id="masterstudy-video-question-submit"]');

            if (current_question.find('input:checked').length > 0) {
                submitButton.removeAttr('disabled').removeClass('masterstudy-button_disabled');
            } else {
                submitButton.attr('disabled', 1).addClass('masterstudy-button_disabled');
            }
        });

        $(document).on('click', '.masterstudy-lesson-video-question__answer', function(e) {
            if ( ! $(this).closest('.masterstudy-lesson-video-question').hasClass('masterstudy-lesson-video-question_answered') ) {
                let input = $(this).find('input');
                if (input.is(':radio')) {
                    input.prop('checked', true).trigger('change');
                    $(this).siblings('.masterstudy-lesson-video-question__answer').find('input:radio').prop('checked', false).trigger('change');
                    $(this).addClass('masterstudy-lesson-video-question__answer_selected');
                    $(this).find('.masterstudy-lesson-video-question__radio').addClass('masterstudy-lesson-video-question__radio_checked');
                    $(this).siblings('.masterstudy-lesson-video-question__answer').removeClass('masterstudy-lesson-video-question__answer_selected');
                    $(this).siblings('.masterstudy-lesson-video-question__answer').find('.masterstudy-lesson-video-question__radio').removeClass('masterstudy-lesson-video-question__radio_checked');
                } else if (input.is(':checkbox')) {
                    if (!input.prop('checked')) {
                        input.prop('checked', true).trigger('change');
                        $(this).addClass('masterstudy-lesson-video-question__answer_selected');
                        $(this).find('.masterstudy-lesson-video-question__checkbox').addClass('masterstudy-lesson-video-question__checkbox_checked');
                    } else {
                        input.prop('checked', false).trigger('change');
                        $(this).removeClass('masterstudy-lesson-video-question__answer_selected');
                        $(this).find('.masterstudy-lesson-video-question__checkbox').removeClass('masterstudy-lesson-video-question__checkbox_checked');
                    }
                }
            }
        })
    })
})(jQuery);