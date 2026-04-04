let table = null;
let currentSearchValue = '';

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        if (jQuery.fn.select2) {
            const selectElement = jQuery('select.masterstudy-grades-student__select');
            const selectTable = jQuery('select.dt-input');

            if (selectElement.data('select2')) {
                selectElement.select2('destroy');
            }
            if (selectTable.data('select2')) {
                selectTable.select2('destroy');
            }
        }
    }, 100);
    // Apply Search Values
    setSearchValue();

    updateTable();

    document.addEventListener('click', (event) => {
        const activeDropdowns = document.querySelectorAll('.masterstudy-account-grades-student__search-dropdown_active');
        const row = event.target.closest('table.dataTable tbody tr');
        const gradeDetails = document.querySelector('.masterstudy-grade-details');
        const gradeDetailsBlock = document.querySelector('.masterstudy-grade-details__block');
        const gradeLoader = document.querySelector('.masterstudy-grade-details__loader');

        if (activeDropdowns.length) {
            activeDropdowns.forEach((dropdown) => {
                const parent = dropdown.closest('.masterstudy-account-grades-student__search-wrapper');
                const input = parent.querySelector('input');
                const isClickInsideInput = input && input.contains(event.target);

                if (!isClickInsideInput) {
                    dropdown.classList.remove('masterstudy-account-grades-student__search-dropdown_active');
                }
            });
        }

        if (row) {
            const emptyTd = row.querySelectorAll('td.dt-empty');
            if (emptyTd.length === 1 && row.children.length === 1) {
                return;
            }

            const courseElement = row.querySelector('.masterstudy-grades-td__course-title');
            const userCourseId = courseElement ? courseElement.dataset.user_course_id : '';
            const fetchPath = `student-grades/${userCourseId}`;

            gradeDetails?.classList.add('masterstudy-grade-details_show');
            document.body.classList.add('masterstudy-grade-details-body-hidden');

            api.get(fetchPath).then(result => {
                renderCourseGrade(result);
            })
        } else if (gradeDetailsBlock && !gradeDetailsBlock.contains(event.target)) {
            document.body.classList.remove('masterstudy-grade-details-body-hidden');
            gradeDetails.classList.remove('masterstudy-grade-details_show');
            gradeLoader.classList.remove('masterstudy-grade-details__loader_hide');
        }
    });

    document.addEventListener('intentTableSearch', function (event) {
        setSearchValue();
        updateTable(true);
    });

    const dateSelect = document.querySelector('#date-select');

    if (dateSelect) {
        dateSelect.value = Object.keys(defaultDateRanges).find(
            key => defaultDateRanges[key] === selectedPeriod
        ) || 'all_time';
        setTimeout(() => {
            dateSelect.dispatchEvent(new Event('change'))
        }, 0)
    }

    document.addEventListener('msfieldEvent', (e) => {
        if (e.detail?.name === 'date-select') {
            const selectedValue = e.detail.value
            selectedPeriod = defaultDateRanges[selectedValue] || defaultDateRanges['all_time']
            localStorage.setItem('GradesSelectedPeriodKey', selectedValue);
            updateTable(true);
        }
    })

    if (document.querySelectorAll('input[id*="-filter"]').length) {
        document.querySelectorAll('input[id*="-filter"]').forEach(function (selector) {
            selector.addEventListener('input', function () {
                const query = this.value.trim();
                const fetchPath = `search?search=${encodeURIComponent(query)}&type=stm-courses`;
                const dropdown = this.parentNode.querySelector(`.masterstudy-account-grades-student__search-dropdown`);

                if (!dropdown) return;

                if (query.length > 2) {
                    wpApi.get(fetchPath).then(result => {
                        if (result.error_code) {
                            return
                        }

                        dropdown.innerHTML = '';

                        if (result.length > 0) {
                            result.forEach(item => {
                                const dropdownItem = document.createElement('div');
                                dropdownItem.className = `masterstudy-account-grades-student__search-dropdown-item`;
                                dropdownItem.setAttribute('data-id', item.id);
                                dropdownItem.textContent = item.title;
                                dropdown.appendChild(dropdownItem);
                            });
                            dropdown.classList.add(`masterstudy-account-grades-student__search-dropdown_active`);
                        } else {
                            dropdown.classList.remove(`masterstudy-account-grades-student__search-dropdown_active`);
                        }
                    })
                } else if (query.length === 0) {
                    this.dataset.id = '';
                    setSearchValue();
                    updateTable(true);
                    dropdown.classList.remove(`masterstudy-account-grades-student__search-dropdown_active`);
                    dropdown.innerHTML = '';
                } else {
                    dropdown.classList.remove(`masterstudy-account-grades-student__search-dropdown_active`);
                    dropdown.innerHTML = '';
                }
            });
        });
    }

    document.querySelector('.masterstudy-grade-details__close').addEventListener('click', function (event) {
        document.body.classList.remove('masterstudy-grade-details-body-hidden');
        document.querySelector('.masterstudy-grade-details').classList.remove('masterstudy-grade-details_show');
        document.querySelector('.masterstudy-grade-details__loader').classList.remove('masterstudy-grade-details__loader_hide');
    });

    // Regenerate Grades
    document.querySelector('[data-id="regenerate-grades"]').addEventListener('click', function (event) {
        const regenerateButton = document.querySelector('[data-id="regenerate-grades"]');
        const userCourseId = regenerateButton.dataset.user_course_id;
        const gradeLoader = document.querySelector('.masterstudy-grade-details__loader');
        const fetchPath = `student-grades/${userCourseId}/regenerate`;

        gradeLoader.classList.remove('masterstudy-grade-details__loader_hide');
        gradeLoader.classList.add('masterstudy-grade-details__loader_show');

        api.get(fetchPath).then(result => {
            renderCourseGrade(result);

            gradeLoader.classList.remove('masterstudy-grade-details__loader_show');
            gradeLoader.classList.add('masterstudy-grade-details__loader_hide');
            updateTable(true);
        });
    });
});

function renderCourseGrade(result) {
    if (result.error_code) {
        return
    }

    if (Object.keys(result).length > 0) {
        document.querySelector('.masterstudy-grade-details__loader').classList.add('masterstudy-grade-details__loader_hide');

        const courseTitleElement = document.querySelector('.masterstudy-grade-details__course-title');
        const studentNameElement = document.querySelector('.masterstudy-grade-details__student-name');
        const enrollDateElement = document.getElementById('enroll-date');
        const courseCompleteDateElement = document.getElementById('complete-date');
        const gradeBadgeElement = document.querySelector('.masterstudy-grade-details__mark-badge');
        const gradePointsElement = document.querySelector('#grade-points .masterstudy-grade-details__mark-points-value');
        const gradePercentElement = document.querySelector('#grade-percent .masterstudy-grade-details__mark-points-value');
        const gradeProgressElement = document.querySelector('.masterstudy-grade-details__mark-progress-fill');
        const regenerateButton = document.querySelector('[data-id="regenerate-grades"]');

        courseTitleElement.textContent = result.course || '';
        studentNameElement.textContent = result.student || '';
        enrollDateElement.textContent = result.enroll_date || '';
        courseCompleteDateElement.textContent = result.course_complete_date || '';
        gradeBadgeElement.textContent = result.grade.badge || '';
        gradeBadgeElement.style.background = result.grade.color || 'rgba(238, 241, 247, 1)';
        gradePointsElement.textContent = result.grade.current || '0';
        gradePercentElement.textContent = (result.grade.range || '0') + '%';
        gradeProgressElement.style.setProperty('--grade-percent', result.grade.range || '0');
        gradeProgressElement.style.stroke = result.grade.color || 'rgba(255, 255, 255, 1)';
        regenerateButton.dataset.user_course_id = result.user_course_id || '';

        if (parseInt(result.grade.range) === 0) {
            gradeProgressElement.style.stroke = 'rgba(255, 255, 255, 1)';
        }

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
                            <div class="masterstudy-grade-details__exams-item-attempt">${lesson.attempts} ${grades_student_data.attempts}</div>
                            <div class="masterstudy-grade-details__exams-item-grade">
                                <div class="masterstudy-grade-details__exams-item-grade-badge" style="background:${lesson.grade.color}">${lesson.grade.badge}</div>
                                <div class="masterstudy-grade-details__exams-item-grade-value">(${lesson.grade.current.toFixed(2)}${grades_student_data.grade_separator}${lesson.grade.max_point.toFixed(2)})</div>
                            </div>
                            <div class="masterstudy-grade-details__exams-item-percent">${lesson.grade.range}%</div>
                            `
                    :
                    `
                            <div class="masterstudy-grade-details__exams-item-start">${grades_student_data.not_started}</div>
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
}

function setSearchValue() {
    currentSearchValue = [];

    document.querySelectorAll('.grades-search').forEach(function (searchField) {
        if (searchField.dataset.id) {
            currentSearchValue.push(
                {
                    'value': searchField.dataset.id,
                    'column': searchField.dataset.column
                }
            );
        }
    });
}

function updateTable(reloadTable = false) {
    const gradesContainer = document.querySelector('.masterstudy-account-grades-student')
    gradesContainer.querySelector('.masterstudy-account-grades-student .masterstudy-account-grades-no-found__info')?.remove();
    gradesContainer.querySelector('.masterstudy-account-grades-student-table').style.display = 'block'

    const dataSrc = function (json) {
        if (!json.data || String(json.recordsTotal) === '0') {
            const template = document.getElementById('masterstudy-account-grades-student-no-records');
            if ( template ) {
                const clone = template.content.cloneNode(true);
                gradesContainer.querySelector('.masterstudy-account-grades-student-table').style.display = 'none'
                clone.querySelector(currentSearchValue ? '.masterstudy-no-records__no-items' : '.masterstudy-no-records__no-search').remove();
                gradesContainer.append(clone)
            }
        }
        return json.data;
    }

    let columnDefs = [
        {
            targets: 0,
            data: 'course',
            render: function (data, type, row) {
                return renderStudentCourse(data);
            }
        },
        {
            targets: 1,
            data: 'quiz',
            render: function (data, type, row) {
                return data.complete + grades_student_data.grade_separator + data.total;
            }
        },
        {
            targets: 2,
            data: 'assignment',
            render: function (data, type, row) {
                return data.complete + grades_student_data.grade_separator + data.total;
            }
        },
        {
            targets: 3,
            data: 'grade',
            render: function (data, type, row) {
                return renderGrades(data);
            }
        }
    ];

    table = updateDataTable(
        table,
        '#masterstudy-datatable-grades',
        [`[data-chart-id="grades-table"]`],
        'student-grades',
        grades_student_data.columns,
        dataSrc,
        columnDefs,
        reloadTable,
        false,
        false,
        [],
        currentSearchValue,
        4
    );
}