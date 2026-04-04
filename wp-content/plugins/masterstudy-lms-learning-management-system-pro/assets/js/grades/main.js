const api = new MasterstudyApiProvider();
const wpApi = new MasterstudyApiProvider();
const defaultDateRanges = getDefaultDateRanges();
let selectedPeriod = defaultDateRanges.this_month;
let storedPeriodKey = localStorage.getItem('GradesSelectedPeriodKey');

if (storedPeriodKey && defaultDateRanges[storedPeriodKey] ) {
    selectedPeriod = defaultDateRanges[storedPeriodKey];
}

if (window.location.href.includes('stylemixthemes')) {
    selectedPeriod = defaultDateRanges['all_time'];
}

// Change baseURl to WordPress REST API
wpApi.baseURL = api_data.wp_rest_url;

function renderStudent(data) {
    return '<div class="masterstudy-grades-td__student">' +
                '<div data-id="' + data.id + '" class="masterstudy-grades-td__student-name">' + data.name + '</div>' +
                '<div class="masterstudy-grades-td__student-email">' + data.email + '</div>' +
           '</div>';
}

function renderStudentCourse(data) {
    return '<div class="masterstudy-grades-td__course">' +
                '<img class="masterstudy-grades-td__course-image" src="' + data.img + '">' +
                '<div data-id="' + data.id + '" data-user_course_id="' + data.user_course_id + '" class="masterstudy-grades-td__course-title">' + data.title + '</div>' +
           '</div>';
}

function renderCourse(data) {
    return '<div class="masterstudy-grades-td__course">' +
                '<div data-id="' + data.id + '" data-user_course_id="' + data.user_course_id + '" class="masterstudy-grades-td__course-title">' + data.title + '</div>' +
           '</div>';
}

function renderGrades(data) {
    return '<div class="masterstudy-grades-td__grade">' +
                '<div class="masterstudy-grades-td__grade-badge" style="background:' + data.color + '">' +
                    data.badge +
                '</div>' +
                '<div class="masterstudy-grades-td__grade-value">' + 
                    '(' + data.current.toFixed(2) + grades_data.score_separator + data.max_point.toFixed(2) + ')' +
                '</div>' +
           '</div>';
}