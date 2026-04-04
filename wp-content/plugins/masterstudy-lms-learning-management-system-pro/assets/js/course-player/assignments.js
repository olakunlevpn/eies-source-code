(function ($) {
    $(document).ready(function () {
        let content = '',
            editor = '',
            countInterval = 0,
            timeOut,
            alertPopup = $("[data-id='assignment_submit_alert']");

        const getEditorContainer = () => {
            return $('.masterstudy-course-player-assignments__edit[data-editor="' + assignments_data.editor_id + '"] .masterstudy-wp-editor');
        };

        const submitAssignment = ($buttonElement, submitWithoutCondition = false, isTimeoutSubmit = false) => {
            const isDraft = $buttonElement.is('[data-id="masterstudy-course-player-assignments-save-draft-button"]');

            if (editor) {
                content = editor.getContent();
            }

            if ( !isDraft && content.length < 1 && $('.masterstudy-attachment-media__materials').children().length < 1 && !submitWithoutCondition) {
                alertPopup.addClass('masterstudy-alert_open');
                return;
            }

            let formData = new FormData();
            formData.append('content', content);
            formData.append('action', 'stm_lms_accept_draft_assignment');
            formData.append('nonce', assignments_data.submit_nonce);
            formData.append('draft_id', assignments_data.draft_id);
            formData.append('item_id', assignments_data.assignment_id);
            formData.append('course_id', assignments_data.course_id);
            formData.append('is_draft', isDraft);
            formData.append('is_timeout_submit', isTimeoutSubmit);
            $.ajax({
                url: assignments_data.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $buttonElement.addClass('masterstudy-button_loading');
                },
                success: function () {
                    location.reload();
                    $buttonElement.removeClass('masterstudy-button_loading');
                },
                error: function () {
                    location.reload();
                }
            });
        }

        // open|close requirements
        $.each( $('.masterstudy-course-player-assignments__accordion-button'), function(i, accordion){
            $(accordion).click(function() {
                $(this).parent().find('.masterstudy-course-player-assignments__accordion-content').slideToggle();
                $(this).toggleClass('masterstudy-course-player-assignments__accordion-button_rotate');
            });
        });

        // submit assignment
        $('.masterstudy-course-player-navigation__send-assignment a').on('click', function (e) {
            e.preventDefault();
            submitAssignment($(this))
        });
        // close alert
        alertPopup.find("[data-id='cancel']").click(closeAlertPopup);
        alertPopup.find('.masterstudy-alert__header-close').click(closeAlertPopup);

        function closeAlertPopup(e) {
            e.preventDefault();
            alertPopup.removeClass('masterstudy-alert_open');
        }

        if (typeof tinyMCE !== 'undefined') {
            getEditor();
        }

        if (typeof MasterstudyAudioPlayer !== 'undefined') {
            MasterstudyAudioPlayer.init({
                selector: '.masterstudy-audio-player',
                showDeleteButton: false
            });
        }

        // watch wp-editor changes, disable "submit" button if wp-editor is empty
        function getEditor() {
            const editorContainer = getEditorContainer();
            if (editorContainer.length > 0) {
                editorContainer.addClass('masterstudy-wp-editor_loading');
            }

            editor = tinyMCE.get(assignments_data.editor_id);
            if ( editor ) {
                if (editor.iframeElement === undefined) {
                    setTimeout(function () {
                        getEditor();
                    }, 500);
                } else {
                    if (editorContainer.length > 0) {
                        editorContainer.removeClass('masterstudy-wp-editor_loading');
                        editorContainer.addClass('masterstudy-wp-editor_ready');
                    }
                    content = editor.getContent({ format: 'raw' });
                    editor.on('keyup', function (e) {
                        content = editor.getContent({ format: 'raw' });
                    });
                }
            }
        }

        function getServerNowMs(jqXHR = null, fallbackMs = 0) {
            if (jqXHR && typeof jqXHR.getResponseHeader === 'function') {
                const dateHeader = jqXHR.getResponseHeader('Date');
                const parsedHeaderTs = Date.parse(dateHeader);
                if (!Number.isNaN(parsedHeaderTs)) {
                    return parsedHeaderTs;
                }
            }

            return fallbackMs;
        }

        function countTo(initialRemainingMs) {
            clearInterval(countInterval);

            const startMonotonicMs = performance.now();

            const renderTimer = function () {
                const elapsedMs = performance.now() - startMonotonicMs;
                const distance = Math.max(0, initialRemainingMs - elapsedMs);
                const totalSeconds = Math.floor(distance / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hoursRaw = Math.floor((totalSeconds % 86400) / 3600);
                const minutesRaw = Math.floor((totalSeconds % 3600) / 60);
                const secondsRaw = totalSeconds % 60;
                const hours = hoursRaw < 10 ? '0' + hoursRaw : String(hoursRaw);
                const minutes = minutesRaw < 10 ? '0' + minutesRaw : String(minutesRaw);
                const seconds = secondsRaw < 10 ? '0' + secondsRaw : String(secondsRaw);

                if (days < 1 && hoursRaw < 1) {
                    $('.masterstudy-course-player-assignment-timer__days').text('');
                    $('.masterstudy-course-player-assignment-timer__hours').text('');
                    $('.masterstudy-course-player-assignment-timer__separator[data-id="hours"]').removeClass('masterstudy-course-player-assignment-timer__separator_show');
                    $('.masterstudy-course-player-assignment-timer__minutes').text(minutes);
                    $('.masterstudy-course-player-assignment-timer__seconds').text(seconds);
                    $('.masterstudy-course-player-assignment-timer__separator[data-id="minutes"]').addClass('masterstudy-course-player-assignment-timer__separator_show');
                } else if (days < 1) {
                    $('.masterstudy-course-player-assignment-timer__days').text('');
                    $('.masterstudy-course-player-assignment-timer__hours').text(hours);
                    $('.masterstudy-course-player-assignment-timer__minutes').text(minutes);
                    $('.masterstudy-course-player-assignment-timer__seconds').text(seconds);
                    $('.masterstudy-course-player-assignment-timer__separator').addClass('masterstudy-course-player-assignment-timer__separator_show');
                } else {
                    let daysText = $('.masterstudy-course-player-assignment-timer').attr('data-text-days');
                    $('.masterstudy-course-player-assignment-timer__days').text(days + ' ' + daysText);
                }
                if(!timeOut && distance <= 0) {
                    clearInterval(countInterval);
                    timeOut = true;
                    const submitButton = $('[data-id="masterstudy-course-player-assignments-send-button"]').first();

                    if (submitButton.length) {
                        submitAssignment(submitButton, true, true);
                    } else {
                        location.reload();
                    }
                }
            };

            renderTimer();
            countInterval = setInterval(renderTimer, 1000);
        }

        function startTimer() {
            if (!assignments_data.has_timer)  return

            const timerEndTs = parseInt(assignments_data.timer_end_ts, 10);
            const defaultTimerErrorMessage = assignments_data.timer_error_message || 'Unable to start assignment timer. Please refresh the page and try again.';
            const localizedServerNowTs = parseInt(assignments_data.server_now_ts, 10);
            const localizedServerNowMs = Number.isFinite(localizedServerNowTs) && localizedServerNowTs > 0
                ? localizedServerNowTs * 1000
                : 0;
            const safeNowMs = localizedServerNowMs > 0 ? localizedServerNowMs : Date.now();

            $.ajax({
                url: assignments_data.ajax_url,
                dataType: 'json',
                context: this,
                data: {
                    'course_id': assignments_data.course_id,
                    'assignment_id': assignments_data.draft_id,
                    'action': 'stm_lms_start_assignment_timer',
                    'nonce' : assignments_data.start_nonce,
                    'source' : assignments_data.course_id,
                },
                success: function(data, textStatus, jqXHR) {
                    if ( $('.masterstudy-course-player-assignment-timer').length > 0 ) {
                        const responseData = data && data.data ? data.data : 0;
                        const timerEndTs = parseInt(
                            typeof responseData === 'object' ? responseData.timer_end_ts : responseData,
                            10
                        );
                        const responseServerNowTs = parseInt(
                            typeof responseData === 'object' ? responseData.server_now_ts : 0,
                            10
                        );
                        const serverNowMs = responseServerNowTs > 0
                            ? responseServerNowTs * 1000
                            : getServerNowMs(jqXHR, safeNowMs);

                        if (!Number.isFinite(timerEndTs) || timerEndTs <= 0) {
                            location.reload();
                            return;
                        }

                        countTo(Math.max(0, timerEndTs * 1000 - serverNowMs));
                        $('.masterstudy-course-player-assignment-timer').addClass('masterstudy-course-player-assignment-timer_started');
                    }
                },
                error: function(e) {
                    if (timerEndTs > 0 && $('.masterstudy-course-player-assignment-timer').length > 0) {
                        // Avoid client clock drift; prefer server-side timestamp from localized data.
                        countTo(Math.max(0, timerEndTs * 1000 - safeNowMs));
                        $('.masterstudy-course-player-assignment-timer').addClass('masterstudy-course-player-assignment-timer_started');
                        return;
                    }

                    const serverErrorMessage = e?.responseJSON?.data?.message || e?.responseJSON?.message;
                    alert(serverErrorMessage || defaultTimerErrorMessage);
                    location.reload()
                }
            });
        }

        startTimer()
    });
})(jQuery);
