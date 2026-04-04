(function($) {
    // Fetch data global variables
    let chartsData = null;
    let revenueChart = null;
    let table = null;

    // Fetch data
    fetchDataCharts();

    document.addEventListener('datesUpdated', function(event) {
        fetchDataCharts();
        updateTable(true);
    });

    $(document).ready(function() {
        initializeDatepicker('#masterstudy-datepicker-bundle');

        //Update data
        updateCharts();
        updateTable();
    });

    // Fetch data methods
    function fetchDataCharts() {
        if ( isDomReady ) {
            showLoaders('.masterstudy-analytics-bundle-page-line');
            showLoaders('.masterstudy-analytics-bundle-page-stats');
            showLoaders('.masterstudy-analytics-bundle-page-doughnut');
        }

        api.get( routes.bundleCharts ).then(result => {
            if (result.error_code) {
                return
            }
            chartsData = {
                revenue: result.revenue,
                orders: result.orders,
                revenue_chart: {
                    period: result.period,
                    items: [
                        { label: bundle_page_data.titles.revenue_chart, values: result.values },
                    ]
                },
            }

            updateCharts();
        })
    }

    // Update charts & table methods
    function updateCharts() {
        if (chartsData && isDomReady) {
            if (!revenueChart) {
                revenueChart = createChart(document.getElementById('masterstudy-line-chart-revenue').getContext('2d'), 'line', [], [], true);
            }

            updateStatsBlock('.masterstudy-stats-block_revenue', chartsData.revenue, 'currency');
            updateStatsBlock('.masterstudy-stats-block_orders', chartsData.orders);
            updateLineChart(revenueChart, chartsData.revenue_chart.period, chartsData.revenue_chart.items);
            updateTotal('#revenue-total', chartsData.revenue, 'currency');
            hideLoaders('.masterstudy-analytics-bundle-page-line');
            hideLoaders('.masterstudy-analytics-bundle-page-stats');
        }
    }

    function updateTable(reloadTable = false) {
        const dataSrc = function (json) {
            const pageInfo = $('#masterstudy-datatable-courses').DataTable().page.info();
            const start = pageInfo.start;

            json.data = json.data.map((item, index) => {
                item.number = start + index + 1;
                return item;
            });

            return json.data;
        };

        columnDefs = [
            { targets: bundle_page_data.courses.length - 1, orderable: false },
            { targets: 0, width: '30px', orderable: false },
            {
                targets: bundle_page_data.courses.length - 1,
                data: 'course_id',
                render: function (data, type, row) {
                    const currentUrl = window.location.href;
                    const builderUrl = new URL(stats_data.user_account_url) + 'edit-course/' + data;
                    const courseUrl = new URL(stats_data.courses_page_url) + row.course_slug;
                    const baseUrl = new URL(stats_data.user_account_url);
                    if (!currentUrl.startsWith(baseUrl)) {
                        
                        const newUrl = new URL(currentUrl);
                        const keysToDelete = ['course_id','bundle_id'];
                        const keysToSet = { course_id: data };
                        keysToDelete.forEach(key => newUrl.searchParams.delete(key));
                        Object.entries(keysToSet).forEach(([key, value]) => newUrl.searchParams.set(key, value));

                        return renderCourseButtons(newUrl, builderUrl, courseUrl);
                    } else {
                        
                        const newPath = `analytics/course/${data}`;
                        baseUrl.pathname += `${newPath}`;

                        return renderCourseButtons(baseUrl, builderUrl, courseUrl);
                    }
                }
            }
        ];

        table = updateDataTable(
            table,
            '#masterstudy-datatable-courses',
            ['.masterstudy-analytics-bundle-page-table'],
            routes.bundlecoursesTable,
            bundle_page_data.courses,
            dataSrc,
            columnDefs,
            reloadTable,
            false,
            false,
            [],
            '',
            2
        );
    }
})(jQuery);
