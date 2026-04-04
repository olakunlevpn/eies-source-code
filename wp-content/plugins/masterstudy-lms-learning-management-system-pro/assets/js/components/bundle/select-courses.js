(function ($) {
	$(document).ready(function () {
		let currentPage = 1;
		let loadedCourses = 0;
		let currentQuery = '';

		const $courseContainer = $('.masterstudy-bundle-select__courses');
		const $loadContainer = $('.masterstudy-bundle-select__load');
		const $loadMoreBtn = $('[data-id="load_courses"]');
		const $searchInput = $('input[name="bundle_courses_search"]');
		const $searchButton = $('.masterstudy-bundle-select__search-button');

		$('.masterstudy-bundle-select').removeAttr('style');

		$loadMoreBtn.on('click', function (e) {
			e.preventDefault();
			currentPage++;

			loadCourses(currentPage, currentQuery);
		});

		$searchButton.on('click', function () {
			currentQuery = $searchInput.val().trim();
			currentPage = 1;
			loadedCourses = 0;
			$courseContainer.empty();
			loadCourses(currentPage, currentQuery, true);
		});

        $searchInput.on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                $searchButton.trigger('click');
            }
        });

		let searchTimeout;

		$searchInput.on('input', function () {
			const query = $(this).val().trim();

			if (query.length >= 3) {
				searchTimeout = setTimeout(() => {
					currentQuery = query;
					currentPage = 1;
					loadedCourses = 0;
					$courseContainer.empty();
					loadCourses(currentPage, currentQuery, true);
				}, 400);
			} else if (query.length === 0) {
				currentQuery = '';
				currentPage = 1;
				loadedCourses = 0;
				$courseContainer.empty();
				loadCourses(currentPage, currentQuery, true);
			}
		});

		function loadCourses(page = 1, query = '', search = false) {
			$.ajax({
				url: stm_lms_ajaxurl,
				method: 'GET',
				data: {
					action: 'stm_lms_get_instructor_courses',
					nonce: stm_lms_nonces['stm_lms_get_instructor_courses'],
					pp: 9,
					offset: page - 1,
					status: 'publish',
					coming_soon_bundle: 1,
					s: query
				},
				beforeSend: function () {
					$loadMoreBtn.addClass('masterstudy-button_loading');
                    if (search) {
                        $('.masterstudy-bundle-select__loader').addClass('masterstudy-bundle-select__loader_show');
                    }
				},
				success: function (response) {
					if (response.posts && response.posts.length > 0) {
						response.posts.forEach(course => {
							$courseContainer.append(renderCourseCard(course));
						});
						loadedCourses += response.posts.length;

						if (Array.isArray(bundle_courses.my_courses) && bundle_courses.my_courses.length > 0) {
							markSelectedCourses();
						}

						if (response.total) {
							$loadContainer.hide();
						} else {
							$loadContainer.show();
						}
					} else {
						if (page === 1) {
							$courseContainer.html(`<div class="masterstudy-bundle-select__courses-empty">${select_courses.no_found}</div>`);
						}
						$loadContainer.hide();
					}

                    if (search) {
                        $('.masterstudy-bundle-select__loader').removeClass('masterstudy-bundle-select__loader_show');
                    }
				},
				complete: function () {
					$loadMoreBtn.removeClass('masterstudy-button_loading');
				}
			});
		}

		function renderCourseCard(course) {
			return `
				<div id="${course.id}" class="masterstudy-bundle-course">
					<span class="masterstudy-bundle-course__checkbox masterstudy-bundle-course__checkbox_show"></span>
					${course.image}
					<div class="masterstudy-bundle-course__content">
						<span class="masterstudy-bundle-course__title">${course.title}</span>
						<div class="masterstudy-bundle-course__price">
							${course.sale_price ? `<span class="masterstudy-bundle-course__sale-single">${course.sale_price}</span>` : ''}
							${course.price ? `
								<span class="masterstudy-bundle-course__price-single ${course.sale_price ? 'masterstudy-bundle-course__price-single_discounted' : ''}">
									${course.price}
								</span>` : ''}
						</div>
					</div>
                    <div class="masterstudy-bundle-course__trash"></div>
				</div>`;
		}

		function markSelectedCourses() {
			if (!Array.isArray(window.selectedCourseIds)) {
				window.selectedCourseIds = Array.isArray(bundle_courses.my_courses)
					? bundle_courses.my_courses.map(String)
					: [];
			}

			$courseContainer.find('.masterstudy-bundle-course').each(function () {
				const courseId = $(this).attr('id');
				const $checkbox = $(this).find('.masterstudy-bundle-course__checkbox');
				if (window.selectedCourseIds.includes(courseId)) {
					$checkbox.addClass('masterstudy-bundle-course__checkbox_checked');
				} else {
					$checkbox.removeClass('masterstudy-bundle-course__checkbox_checked');
				}
			});
		}
	});
})(jQuery);