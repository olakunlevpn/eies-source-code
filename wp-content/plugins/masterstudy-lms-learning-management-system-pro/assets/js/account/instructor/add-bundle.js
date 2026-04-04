(function ($) {
    $(document).ready(function () {
        if (Array.isArray(bundle_courses.my_courses) && bundle_courses.my_courses.length > 0) {
            prepopulateBundleCourses();
        }

        if (!Array.isArray(window.selectedCourseIds)) {
            window.selectedCourseIds = Array.isArray(bundle_courses.my_courses)
                ? bundle_courses.my_courses.map(String)
                : [];
        }

        $('[data-id="add_course_bundle"]').click(function() {
            $('body').addClass('masterstudy-bundle-select-body-hidden');
            $('.masterstudy-bundle-select').addClass('masterstudy-bundle-select_show');
            markSelectedCourses();
        })

        $('.masterstudy-bundle-select__back').click(function() {
            $('body').removeClass('masterstudy-bundle-select-body-hidden');
            $('.masterstudy-bundle-select').removeClass('masterstudy-bundle-select_show');
        })

        $(document).on('click', '.masterstudy-bundle-select__courses .masterstudy-bundle-course', function () {
            const $course = $(this);
            const courseId = $course.attr('id');
            const $checkbox = $course.find('.masterstudy-bundle-course__checkbox');
            const isChecked = $checkbox.hasClass('masterstudy-bundle-course__checkbox_checked');

            if (isChecked) {
                $checkbox.removeClass('masterstudy-bundle-course__checkbox_checked');
                window.selectedCourseIds = window.selectedCourseIds.filter(id => id !== courseId);
                return;
            }

            if (window.selectedCourseIds.length >= bundle_courses.limit) {
                return;
            }

            $checkbox.addClass('masterstudy-bundle-course__checkbox_checked');
            window.selectedCourseIds.push(courseId);
        });

        $(document).on('click', function (e) {
            const $target = $(e.target);
            const $selectCourses = $('.masterstudy-bundle-select');
            const $block = $('.masterstudy-bundle-select__block');
            const $trigger = $('[data-id="add_course_bundle"]');

            const clickedInsideBlock = $block.is($target) || $block.has($target).length > 0;
            const clickedTrigger = $trigger.is($target) || $trigger.has($target).length > 0;

            if ($selectCourses.hasClass('masterstudy-bundle-select_show') && !clickedInsideBlock && !clickedTrigger) {
                $('body').removeClass('masterstudy-bundle-select-body-hidden');
                $selectCourses.removeClass('masterstudy-bundle-select_show');
            }
        });

        $('input[name="single_sale"], input[name="buy_for_points"]').on('change', updateSummary);
        $('input[name="one_time_price"], input[name="points_price"]').on('input', updateSummary);

        updateSummary();

        $('[data-id="save_bundle"]').on('click', function (e) {
            e.preventDefault();

            $('.masterstudy-add-bundle__error').remove();

            let title = $('input[name="bundle_title"]').val();
            let description = typeof tinyMCE !== 'undefined' ? tinyMCE.get('editor_add_bundle').getContent() : '';
            let buy_for_points = $('input[name="buy_for_points"]').is(':checked') ? 1 : 0;
            let single_sale = $('input[name="single_sale"]').is(':checked') ? 1 : 0;
            let price = $('input[name="one_time_price"]').val() ?? null;
            let points_price = $('input[name="points_price"]').val() ?? null;
            let price_info = $('input[name="one_time_price_info"]').val() ?? '';
            let points_info = $('input[name="points_price_info"]').val() ?? '';
            let course_ids = $('.masterstudy-add-bundle__courses .masterstudy-bundle-course').map(function () {
                return $(this).attr('id');
            }).get();
            let subscription_enabled = $('input[name="subscription"]').is(':checked')

            let formData = new FormData();
            formData.append('nonce', bundle_courses.nonce);
            formData.append('id', bundle_courses.my_bundle_id);
            formData.append('title', title);
            formData.append('courses', course_ids);
            formData.append('buy_for_points', buy_for_points);
            formData.append('single_sale', single_sale);
            formData.append('price', price);
            formData.append('points', points_price);
            formData.append('price_info', price_info);
            formData.append('points_info', points_info);
            formData.append('description', description);
            formData.append('subscription_enabled', subscription_enabled)
            const keys = ['id', 'name', 'price', 'recurring_value', 'featured_text', 'is_featured', 'recurring_interval']

            $('.masterstudy-pricing-item__subscription-plans').find('.masterstudy-pricing-item__subscription-plan').each((i, element) => {
                for (const key of keys) {
                    const data = $(element).data(key)
                    if (data) {
                        formData.append(`subscriptions[${i}][${key}]`, data)
                    }
                }
            })

            const $img = $('.masterstudy-image-upload__image');
            if ($img.length && $img.attr('src') && $img.attr('src').trim() !== '') {
                formData.append('file_exists', $img.attr('src'));
            }

            if (window.masterstudy_selected_image instanceof File) {
                formData.append('file', window.masterstudy_selected_image);
            }

            $.ajax({
                url: stm_lms_ajaxurl + '?action=stm_lms_save_bundle',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
					$('[data-id="save_bundle"]').addClass('masterstudy-button_loading');
				},
                success(res) {
                    if (res.status === 'success' && res.url) {
                        window.location.href = res.url;
                        $('[data-id="save_bundle"]').removeClass('masterstudy-button_loading');
                    } else if (res.status === 'error') {
                        $('[data-id="save_bundle"]').removeClass('masterstudy-button_loading');
                        const $container = $('.masterstudy-add-bundle__errors');

                        Object.values(res.message).forEach((fieldErrors) => {
                            if (Array.isArray(fieldErrors)) {
                                fieldErrors.forEach((msg) => {
                                    const $error = $('<div class="masterstudy-add-bundle__error masterstudy-add-bundle__error_show"></div>').text(msg);
                                    $container.append($error);
                                });
                            }
                        });

                        const $firstError = $('.masterstudy-add-bundle__error').first();
                        if ($firstError.length) {
                            const errorTop = $firstError.offset().top;
                            const scrollTop = $(window).scrollTop();
                            const windowHeight = $(window).height();

                            if (errorTop < scrollTop || errorTop > scrollTop + windowHeight) {
                                $('html, body').animate({
                                    scrollTop: errorTop - 100
                                }, 500);
                            }
                        }
                    }
                }
            });
        });

        $('[data-id="add_courses"]').on('click', function (e) {
            e.preventDefault();

            const $selected = $('.masterstudy-bundle-select__courses .masterstudy-bundle-course').filter(function () {
                return $(this).find('.masterstudy-bundle-course__checkbox').hasClass('masterstudy-bundle-course__checkbox_checked');
            });

            if ($selected.length === 0) return;

            const $targetContainer = $('.masterstudy-add-bundle__courses');
            const $emptyMessage = $('.masterstudy-add-bundle__courses-empty');

            $selected.each(function () {
                const courseId = $(this).attr('id');

                if (!window.selectedCourseIds.includes(courseId)) {
                    window.selectedCourseIds.push(courseId);
                }

                if ($targetContainer.find(`#${courseId}`).length > 0) return;
                if ($targetContainer.find('.masterstudy-bundle-course').length >= bundle_courses.limit) return;

                const $cloned = $(this).clone(true, true);

                $cloned.find('.masterstudy-bundle-course__checkbox')
                    .removeClass('masterstudy-bundle-course__checkbox_checked')
                    .removeClass('masterstudy-bundle-course__checkbox_show');

                $cloned.find('.masterstudy-bundle-course__trash').addClass('masterstudy-bundle-course__trash_show');
                $targetContainer.append($cloned);
                toggleAddButtonVisibility();
            });

            updateTotalPrice();

            if ($targetContainer.find('.masterstudy-bundle-course').length > 0) {
                $emptyMessage.addClass('masterstudy-add-bundle__courses-empty_hide');
            }

            $('body').removeClass('masterstudy-bundle-select-body-hidden');
            $('.masterstudy-bundle-select').removeClass('masterstudy-bundle-select_show');
        });

        $('.masterstudy-add-bundle__courses').on('click', '.masterstudy-bundle-course__trash', function () {
            const $course = $(this).closest('.masterstudy-bundle-course');
            const courseId = $course.attr('id');
            const $container = $('.masterstudy-add-bundle__courses');
            const $emptyMessage = $('.masterstudy-add-bundle__courses-empty');

            window.selectedCourseIds = window.selectedCourseIds.filter(id => id !== courseId);
            $course.remove();
            updateTotalPrice();

            if ($container.find('.masterstudy-bundle-course').length === 0) {
                $emptyMessage.removeClass('masterstudy-add-bundle__courses-empty_hide');
            }

            toggleAddButtonVisibility();
        });

        function updateSummary() {
            const singleSaleChecked = $('input[name="single_sale"]').is(':checked');
            const buyWithPointsChecked = $('input[name="buy_for_points"]').is(':checked');

            const price = parseFloat($('input[name="one_time_price"]').val()) || 0;
            const pointsPrice = parseInt($('input[name="points_price"]').val()) || 0;

            const $summary = $('.masterstudy-add-bundle__summary-list');

            const $oneTime = $summary.find('.masterstudy-add-bundle__summary-item_one-time');
            if (singleSaleChecked) {
                $oneTime.addClass('masterstudy-add-bundle__summary-item_show');
                $oneTime.find('.masterstudy-add-bundle__summary-item-value').text(formatCurrency(price));
            } else {
                $oneTime.removeClass('masterstudy-add-bundle__summary-item_show');
            }

            const $points = $summary.find('.masterstudy-add-bundle__summary-item_points');
            if (buyWithPointsChecked) {
                $points.addClass('masterstudy-add-bundle__summary-item_show');
                $points.find('.masterstudy-add-bundle__summary-item-value').text(pointsPrice);
            } else {
                $points.removeClass('masterstudy-add-bundle__summary-item_show');
            }

            updateBenefit();
        }

        function updateTotalPrice() {
            let total = 0;

            $('.masterstudy-add-bundle__courses .masterstudy-bundle-course').each(function () {
                const $course = $(this);
                const $salePrice = $course.find('.masterstudy-bundle-course__sale-single');
                const $regularPrice = $course.find('.masterstudy-bundle-course__price-single');

                let priceText = $salePrice.length ? $salePrice.text() : $regularPrice.text();
                priceText = priceText.replace(/[^\d.]/g, '');

                const price = parseFloat(priceText);
                if (!isNaN(price)) {
                    total += price;
                }
            });

            $('.masterstudy-add-bundle__summary-total').text(formatCurrency(total));

            updateBenefit();
        }

        function updateBenefit() {
            const totalText = $('.masterstudy-add-bundle__summary-total').text().replace(/[^\d.,]/g, '');
            const total = parseFloat(totalText.replace(bundle_courses.currency_thousands, '').replace(bundle_courses.currency_decimals, '.')) || 0;
            const bundlePriceText = $('.masterstudy-add-bundle__summary-item_one-time .masterstudy-add-bundle__summary-item-value').text().replace(/[^\d.,]/g, '');
            const bundlePrice = parseFloat(bundlePriceText.replace(bundle_courses.currency_thousands, '').replace(bundle_courses.currency_decimals, '.')) || 0;

            let benefit = 0;

            if (total > 0 && bundlePrice > 0 && bundlePrice < total) {
                benefit = Math.round(((total - bundlePrice) / total) * 100);
            }

            $('.masterstudy-add-bundle__summary-benefit-value').text(benefit + '%');
        }

        function markSelectedCourses() {
            if (!Array.isArray(window.selectedCourseIds)) return;

            $('.masterstudy-bundle-select__courses .masterstudy-bundle-course').each(function () {
                const courseId = $(this).attr('id');
                if (window.selectedCourseIds.includes(courseId)) {
                    $(this).find('.masterstudy-bundle-course__checkbox')
                        .addClass('masterstudy-bundle-course__checkbox_checked');
                } else {
                    $(this).find('.masterstudy-bundle-course__checkbox')
                        .removeClass('masterstudy-bundle-course__checkbox_checked');
                }
            });
        }

        function prepopulateBundleCourses() {
            const $targetContainer = $('.masterstudy-add-bundle__courses');
            const $emptyMessage = $('.masterstudy-add-bundle__courses-empty');

            $('.masterstudy-bundle-select__courses .masterstudy-bundle-course').each(function () {
                const courseId = $(this).attr('id');

                if (bundle_courses.my_courses.includes(courseId)) {
                    const $cloned = $(this).clone(true, true);

                    $cloned.find('.masterstudy-bundle-course__checkbox')
                        .removeClass('masterstudy-bundle-course__checkbox_checked')
                        .removeClass('masterstudy-bundle-course__checkbox_show');

                    $cloned.find('.masterstudy-bundle-course__trash').addClass('masterstudy-bundle-course__trash_show');
                    $targetContainer.append($cloned);
                }
            });

            if ($targetContainer.find('.masterstudy-bundle-course').length > 0) {
                $emptyMessage.addClass('masterstudy-add-bundle__courses-empty_hide');
            }

            updateTotalPrice();
            toggleAddButtonVisibility()
        }

        function formatCurrency(amount) {
            const symbol       = bundle_courses.currency_symbol || '$';
            const position     = bundle_courses.currency_position || 'left';
            const thousands    = bundle_courses.currency_thousands || ',';
            const decimals     = bundle_courses.currency_decimals || '.';
            const decimalsNum  = parseInt(bundle_courses.decimals_num) || 2;

            const fixedAmount = Number(amount).toFixed(decimalsNum);
            const parts = fixedAmount.split('.');
            let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands);
            let decimalPart = parts[1] || '';

            let formatted = integerPart;
            if (decimalsNum > 0) {
                formatted += decimals + decimalPart;
            }

            switch (position) {
                case 'left': return symbol + formatted;
                case 'left_space': return symbol + ' ' + formatted;
                case 'right': return formatted + symbol;
                case 'right_space': return formatted + ' ' + symbol;
                default: return symbol + formatted;
            }
        }

        function toggleAddButtonVisibility() {
            const $targetContainer = $('.masterstudy-add-bundle__courses');
            const $addButton = $('.masterstudy-add-bundle__courses-add');
            const $limitText = $('.masterstudy-add-bundle__courses-limit');
            const currentCount = $targetContainer.find('.masterstudy-bundle-course').length;

            if (currentCount >= bundle_courses.limit) {
                $addButton.addClass('masterstudy-add-bundle__courses-add_hide');
                $limitText.addClass('masterstudy-add-bundle__courses-limit_show');
            } else {
                $addButton.removeClass('masterstudy-add-bundle__courses-add_hide');
                $limitText.removeClass('masterstudy-add-bundle__courses-limit_show');
            }
        }
    })
})(jQuery);