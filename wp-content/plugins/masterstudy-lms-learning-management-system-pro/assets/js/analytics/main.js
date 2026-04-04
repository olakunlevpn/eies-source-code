const api = new MasterstudyApiProvider( 'analytics/' );
const currentUrl = window.location.href;
const userAccountDashboardPage = currentUrl === stats_data.user_account_url;
const user_id = currentUrl.includes(stats_data.user_account_url) ? currentUrl.split('/').filter(Boolean).pop() : getQueryParam('user_id');
const course_id = currentUrl.includes(stats_data.user_account_url) ? currentUrl.split('/').filter(Boolean).pop() : getQueryParam('course_id');
const bundle_id = currentUrl.includes(stats_data.user_account_url) ? currentUrl.split('/').filter(Boolean).pop() : getQueryParam('bundle_id');
const routes = {
    revenueCharts: 'revenue',
    payoutsChart: 'revenue/payouts',
    revenueCoursesTable: 'revenue/courses',
    revenueGroupsTable: 'revenue/groups',
    revenueBundlesTable: 'revenue/bundles',
    revenueStudentsTable: 'revenue/students',
    engagementCharts: 'engagement',
    engagementCoursesTable: 'engagement/courses',
    engagementStudentsTable: 'engagement/students',
    instructorSalesTable: 'instructor-orders',
    instructorSubscriptionsTable: 'instructor-subscriptions',
    usersStudentTable: 'students',
    usersMyStudentTable: 'instructor-students',
    usersInstructorTable: 'instructors',
    usersCharts: 'users',
    reviewsCharts: 'reviews-charts',
    reviewedCoursesTable: 'reviews-courses',
    reviewersTable: 'reviews-users',
    reviewsPublishedTable: 'reviews-publish',
    reviewsPendingTable: 'reviews-pending',
    studentCharts: `student/${user_id}/data`,
    studentCoursesTable: `student/${user_id}/courses`,
    studentMembershipTable: `student/${user_id}/membership`,
    instructorCharts: `instructor/${user_id}/data`,
    instructorCoursesTable: `instructor/${user_id}/courses`,
    instructorMembershipTable: `instructor/${user_id}/membership`,
    courseCharts: `course/${course_id}/data`,
    courseLessonsTable: `course/${course_id}/lessons`,
    courseBundlesTable: `course/${course_id}/bundles`,
    courseLessonsUsersTable: `course/${course_id}/lessons-by-users`,
    bundleCharts: `bundle/${bundle_id}/data`,
    bundlecoursesTable: `bundle/${bundle_id}/courses`,
    shortReportCharts: 'instructor/short-report',
};

let pageTitle = false;
const courseTitleElement = document.querySelector('.masterstudy-analytics-course-page__title');
const bundleTitleElement = document.querySelector('.masterstudy-analytics-bundle-page__title');
const userNameElement = document.querySelector('.masterstudy-analytics-student-page__name') || document.querySelector('.masterstudy-analytics-instructor-page__name');
const userRoleElement = document.querySelector('.masterstudy-analytics-student-page__role') || document.querySelector('.masterstudy-analytics-instructor-page__role');

if (courseTitleElement) {
    pageTitle = courseTitleElement.textContent;
} else if (bundleTitleElement) {
    pageTitle = bundleTitleElement.textContent;
} else if (userNameElement) {
    pageTitle = userNameElement.textContent;
} else if (userRoleElement) {
    pageTitle = userRoleElement.textContent;
}

if (pageTitle) {
    document.title = pageTitle;
}

let _periodKey = 'AnalyticsSelectedPeriodKey';
let _period = 'AnalyticsSelectedPeriod';

if ( typeof stats_data.is_student !== "undefined" && stats_data.is_student ) {
    _periodKey = 'StudentListSelectedPeriodKey';
    _period = 'StudentListSelectedPeriod';
}

const defaultDateRanges = getDefaultDateRanges();
let storedPeriodKey = localStorage.getItem( _periodKey );
let selectedPeriod;

if (storedPeriodKey && defaultDateRanges[storedPeriodKey] && !userAccountDashboardPage) {
    selectedPeriod = defaultDateRanges[storedPeriodKey];
} else {
    const defaultDateRange = typeof customDateRange != 'undefined' ? customDateRange : defaultDateRanges.this_month;
    const lmsDateRange = userAccountDashboardPage ? defaultDateRanges.all_time : defaultDateRange;
    const storedPeriod = !userAccountDashboardPage ? localStorage.getItem( _period ) : null;
    selectedPeriod = storedPeriod
        ? JSON.parse( storedPeriod )
        : lmsDateRange;
}

function getQueryParam(param) {
    let urlParams = new URLSearchParams(window.location.search);

    return urlParams.get(param);
}

function renderReportButton(url, detailedTitle = false) {
    const title = detailedTitle ? stats_data.details_title : stats_data.report_button_title;

    return '<div class="masterstudy-analytics-report-button__wrapper">' +
                '<a href="' + url + '" class="masterstudy-analytics-report-button">' + title + '</a>' +
           '</div>';
}

function renderCourseButtons(reportUrl, builderUrl, courseUrl) {
    return '<div class="masterstudy-analytics-report-button__wrapper">' +
                '<a href="' + reportUrl + '" class="masterstudy-analytics-report-button">' + stats_data.report_button_title + '</a>' +
                '<a href="' + builderUrl + '" class="masterstudy-analytics-builder-button"></a>' +
                '<a href="' + courseUrl + '" class="masterstudy-analytics-course-button"></a>' +
           '</div>';
}

function renderRating(rating) {
    const stars = [1, 2, 3, 4, 5];
    const filledStarClass = 'masterstudy-analytics-rating__star_filled';

    return '<div class="masterstudy-analytics-rating">' +
                stars.map(star => {
                    const starClass = star <= Math.floor(rating) ? filledStarClass : '';
                    return `<span class="masterstudy-analytics-rating__star ${starClass}"></span>`;
                }).join('') +
           '</div>';
}

const renderProgress = ({ progress, url }) => `
  <div class="masterstudy-analytics-progress__wrapper">
    <div class="masterstudy-analytics-progress">
      <div class="masterstudy-analytics-progress__bars">
        <span class="masterstudy-analytics-progress__bar-empty"></span>
        <span
          class="masterstudy-analytics-progress__bar-filled"
          style="width:${progress}%"
        ></span>
      </div>
      <div class="masterstudy-analytics-progress__bottom">
        <div class="masterstudy-analytics-progress__title">
          ${stats_data.progress_title}: 
          <span class="masterstudy-analytics-progress__percent">
            ${progress}%
          </span>
        </div>
      </div>
    </div>
    ${stats_data.instructor_can_add_students ? `<a href="${url}" class="masterstudy-analytics-view">
      ${stats_data.button_view}
    </a>` : ''}
  </div>
`;