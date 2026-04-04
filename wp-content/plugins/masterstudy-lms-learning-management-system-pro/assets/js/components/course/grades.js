const detailsPath = `student-grade/${course_grade.course_id}`;
const regeneratePath = `student-grade/${course_grade.course_id}/regenerate`;
const api = new MasterstudyApiProvider();

document.addEventListener('DOMContentLoaded', function() {
    const gradeDetails = document.querySelector('.masterstudy-grade-details');
    const gradeDetailsBlock = document.querySelector('.masterstudy-grade-details__block');
    const gradeLoader = document.querySelector('.masterstudy-grade-details__loader');
    const gradeDetailsButton = document.querySelector('[data-id="show-grade-details"]');
    const gradeRegenerateButton = document.querySelector('[data-id="regenerate-course-grade"]');

    document.addEventListener('click', (event) => {
        if (gradeDetailsBlock && !gradeDetailsBlock.contains(event.target) && gradeDetailsButton && !gradeDetailsButton.contains(event.target)) {
            document.body.classList.remove('masterstudy-grade-details-body-hidden');
            gradeDetails.classList.remove('masterstudy-grade-details_show');
            gradeLoader.classList.remove('masterstudy-grade-details__loader_hide');
        }
    })

    if (gradeDetailsButton) {
        gradeDetailsButton.addEventListener('click', function(event) {
            event.preventDefault();
            document.body.classList.add('masterstudy-grade-details-body-hidden');
            gradeDetails.classList.add('masterstudy-grade-details_show');

            api.get(detailsPath).then(result => {
                if (result.error_code) {
                    return
                }

                if (Object.keys(result).length > 0) {
                    document.querySelector('.masterstudy-grade-details__loader').classList.add('masterstudy-grade-details__loader_hide');

                    const examsListElement = document.querySelector('.masterstudy-grade-details__exams-list');
                    if (examsListElement) {
                        examsListElement.innerHTML = '';

                        const createExamItem = (lesson) => {
                            return `
                            <div class="masterstudy-grade-details__exams-item masterstudy-grade-details__exams-item-${lesson.type}">
                                <div class="masterstudy-grade-details__exams-item-title">
                                    <div class="masterstudy-grade-details__exams-item-icon"></div>
                                    ${lesson.title}
                                </div>
                                ${lesson.grade && Object.keys(lesson.grade).length > 0 ? `
                                    <div class="masterstudy-grade-details__exams-item-attempt">${lesson.attempts} ${course_grade.attempts}</div>
                                    <div class="masterstudy-grade-details__exams-item-grade">
                                        <div class="masterstudy-grade-details__exams-item-grade-badge" style="background:${lesson.grade.color}">${lesson.grade.badge}</div>
                                        <div class="masterstudy-grade-details__exams-item-grade-value">(${lesson.grade.current}${course_grade.grade_separator}${lesson.grade.max_point})</div>
                                    </div>
                                    <div class="masterstudy-grade-details__exams-item-percent">${lesson.grade.range}%</div>
                                    `
                                :
                                `
                                    <div class="masterstudy-grade-details__exams-item-start">${course_grade.not_started}</div>
                                    `
                            }
                            </div>
                        `;
                        };

                        result.exams.forEach((lesson) => {
                            const examItemHTML = createExamItem(lesson);
                            examsListElement.insertAdjacentHTML('beforeend', examItemHTML);
                        });
                    }
                }
            });
        });
    }

    if (gradeRegenerateButton) {
        gradeRegenerateButton.addEventListener('click', function(event) {
            event.preventDefault();

            api.get(regeneratePath).then(result => {
                if (result.error_code) {
                    return
                }
                if (!document.querySelector('.masterstudy-single-course-grades__message_regenerate_sidebar')) {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('tab', 'grades');
                    window.location.href = currentUrl.toString();
                } else {
                    window.location.reload();
                }
            });
        });
    }

    document.querySelector('.masterstudy-grade-details__close').addEventListener('click', function(event) {
        document.body.classList.remove('masterstudy-grade-details-body-hidden');
        gradeDetails.classList.remove('masterstudy-grade-details_show');
        gradeLoader.classList.remove('masterstudy-grade-details__loader_hide');
    })
})