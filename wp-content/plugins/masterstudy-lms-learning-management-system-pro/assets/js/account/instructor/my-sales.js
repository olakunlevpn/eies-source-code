(function ($) {
  let salesTable = null;
  let searchValue = '';
  let subscriptionTypeValue = ['category', 'full_site'];
  let searchField = $('input[name="table-search"]');
  const routes = {
    orders: 'instructor-orders',
    subscriptions: 'instructor-subscriptions',
    memberships: 'instructor-memberships',
  };

  function getCurrentActiveRoute() {
    const activePrimaryTab = $('.masterstudy-my-sales-page__subtabs .masterstudy-tabs__item_active').data('tab');

    if (activePrimaryTab === routes.orders) {
      return routes.orders;
    }

    if (my_sales_page_data.is_admin) {
      return $('.masterstudy-my-sales-page__tabs .masterstudy-tabs__item_active').data('tab') || routes.memberships;
    }

    return routes.subscriptions;
  }

  document.addEventListener('datesUpdated', function() {
    let activeTab = getCurrentActiveRoute();
    activeTab = my_sales_page_data.is_subscriptions_enabled ? activeTab : routes.orders;
    updateTable(activeTab, true);
    toggleSubtabs(activeTab);
  });

  $(document).ready(function() {
    initializeDatepicker('#masterstudy-datepicker-my-sales');
    updateTable(routes.orders);
    toggleSubtabs(routes.orders);
      $('.masterstudy-my-sales-page-table')
          .find('.masterstudy-select[data-id="subscription_type_front"]')
          .css('display', 'none');

    $('.masterstudy-my-sales-page__subtabs .masterstudy-tabs__item').click(function() {
        const tabRoute = $(this).data('tab');
        $(this).addClass('masterstudy-tabs__item_active');
        $(this).siblings().removeClass('masterstudy-tabs__item_active');
        searchField.val('');
        searchField.closest('.masterstudy-search').removeClass('masterstudy-search_inuse');
        updateTable(tabRoute, true);
        toggleSubtabs(tabRoute);
        showSubscriptionTypeSelect(tabRoute);
    });

    $('.masterstudy-search__icon').click(function() {
      let activeTab = getCurrentActiveRoute();
      activeTab = my_sales_page_data.is_subscriptions_enabled ? activeTab : routes.orders;
      updateTable(activeTab, true);
    })

    $('.masterstudy-search__clear-icon').click(function() {
      searchField.val('');
      searchField.closest('.masterstudy-search').removeClass('masterstudy-search_inuse');
      let activeTab = getCurrentActiveRoute();
      activeTab = my_sales_page_data.is_subscriptions_enabled ? activeTab : routes.orders;
      updateTable(activeTab, true);
    })

    $('.masterstudy-my-sales-page__tabs .masterstudy-tabs__item').click(function() {
        const tabRoute = $(this).data('tab');
        $(this).addClass('masterstudy-tabs__item_active');
        $(this).siblings().removeClass('masterstudy-tabs__item_active');
        searchField.val('');
        searchField.closest('.masterstudy-search').removeClass('masterstudy-search_inuse');
        updateTable(tabRoute, true);
        toggleSubtabs(tabRoute);

        showSubscriptionTypeSelect(tabRoute);
    });
  });

  function toggleSubtabs(currentRoute) {
    const $subtabsWrap = $('.masterstudy-my-sales-page__tabs');

    const shouldShow =
      currentRoute === routes.subscriptions ||
      currentRoute === routes.memberships;

      if (shouldShow) {
      $subtabsWrap.show();

      $subtabsWrap.find('.masterstudy-tabs__item')
        .removeClass('masterstudy-tabs__item_active')
        .filter(`[data-tab="${currentRoute}"]`)
        .addClass('masterstudy-tabs__item_active');

      $('.masterstudy-my-sales-page__subtabs .masterstudy-tabs__item')
        .removeClass('masterstudy-tabs__item_active')
        .filter(`[data-tab="${routes.memberships}"],[data-tab="${routes.subscriptions}"]`)
        .first()
        .addClass('masterstudy-tabs__item_active');
    } else {
      $subtabsWrap.hide();
    }
  }

    function showSubscriptionTypeSelect(tabRoute) {
        if (tabRoute == 'instructor-memberships') {
            $('.masterstudy-my-sales-page-table')
                .find('.masterstudy-select[data-id="subscription_type_front"]')
                .css('display', 'flex');
        } else {
            $('.masterstudy-my-sales-page-table')
                .find('.masterstudy-select[data-id="subscription_type_front"]')
                .css('display', 'none');
        }
    }

  function updateTable(currentRoute, reloadTable = false) {
    const dataSrc = function (json) {
      const pageInfo = $('#masterstudy-datatable-my-sales').DataTable().page.info();
      const start = pageInfo.start;
      json.data = json.data.map((item, index) => {
        item.number = start + index + 1;
        item.coupon_value = item.coupon_value ? `-${item.coupon_value}` : ''
        return item;
      });
      return json.data;
    };

    const route = (currentRoute === 'instructor-memberships')
    ? 'instructor-subscriptions'
    : currentRoute;

    searchValue = searchField.val();

    const columns = my_sales_page_data[currentRoute];
    const columnDefs = buildColumnDefs(columns, currentRoute);

    salesTable = updateDataTable(
      salesTable,
      '#masterstudy-datatable-my-sales',
      ['[data-chart-id="my-sales-table"]'],
      `analytics/${route}`,
      columns,
      dataSrc,
      columnDefs,
      reloadTable,
      false,
      searchValue,
      '',
      buildExtraParams(currentRoute)
    );
  }

  function buildColumnDefs(columns, route) {
    const last = columns.length - 1;

    const whitelistByRoute = {
      [routes.subscriptions]: new Set(['subscription_id', 'status']),
      [routes.memberships]: new Set(['subscription_id', 'status']),
      [routes.orders]: new Set(['date', 'user_info', 'total_items', 'payment_code', 'status_name', 'total_price']),
    };
    const whitelist = whitelistByRoute[route];

    const defs = columns.map((col, index) => {
      let orderable = !(index === 0 || index === last) && col.data != null;

      if (whitelist) {
        orderable = orderable && whitelist.has(col.data);
      }

      const def = {
        targets: index,
        data: col.data,
        orderable,
      };

      if (col.data === 'plan_name') {
        def.render = function (_data, _type, row) {
          let content = '';

          if (row.type === 'course') {
            const courseTitle = row.course?.title
              ? `<div class="masterstudy-my-sales-page-table__course-title">${row.course.title}</div>`
              : '';
            const planTitle = `${my_sales_page_data.plan_title}: ${row.plan ? row.plan : ''}`;
            content = `${courseTitle}${planTitle}`;
          } else {
            content = `${row.plan || ''}`;
          }

          return content;
        };
      }

      return def;
    });

    defs.push({ targets: 0, width: '20px' });

    defs.push({
      targets: last - 1,
      data: null,
      width: '30px',
      render: function (_data, _type, row) {
        const status = row.status || row.status_name;
        return renderStatus(status);
      },
    });

    defs.push({
      targets: last,
      orderable: false,
      data: null,
      width: '100px',
      render: function (_data, _type, row) {
        const id = getRowEntityId(row, route);
        if (!id) return '';
        const url = buildDetailsUrl(route, id);
        const detailedTitle = route !== routes.orders;
        return renderReportButton(url, detailedTitle);
      },
    });

    return defs;
  }

  const buildExtraParams = (route) => {
    const params = {};

      if (route === 'instructor-memberships') {
          params.subscription_type = subscriptionTypeValue;
      }

      if (route === 'instructor-subscriptions') {
      params.subscription_type = [ 'course' ];
    }

    return params;
  };

    function fetchInstructorMemberships(params) {
        const activeTab = getCurrentActiveRoute();
        updateTable(activeTab, true);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('subscription_type_front');

        if (select) {
            document.addEventListener('msfieldEvent', function(event) {
                const fieldValue = event.detail.value;
                const params = buildExtraParams('instructor-memberships');

                switch ( event.detail.value ) {
                    case 'category':
                    case 'full_site':
                        subscriptionTypeValue = [fieldValue];
                        fetchInstructorMemberships(params);
                        break;

                    case '':
                        subscriptionTypeValue = ['category', 'full_site'];
                        fetchInstructorMemberships(params);
                        break;
                }

            })

            select.addEventListener('change', () => {
                const params = buildExtraParams('instructor-memberships');

                fetchInstructorMemberships(params);
            });

        }

    });
  function renderReportButton(url, detailedTitle = false) {
    const title = detailedTitle ? my_sales_page_data.details_title : my_sales_page_data.report_button_title;

    return '<div class="masterstudy-my-sales-page-table-button__wrapper">' +
              '<a href="' + url + '" class="masterstudy-my-sales-page-report-button">' + title + '</a>' +
           '</div>';
  }

  function renderStatus(status) {
    const statusMap = {
      'trialing': { text: my_sales_page_data.statuses.trialing, class: 'masterstudy-my-sales-page-table-status_trialing' },
      'trial': { text: my_sales_page_data.statuses.trial, class: 'masterstudy-my-sales-page-table-status_trial' },
      'active': { text: my_sales_page_data.statuses.active, class: 'masterstudy-my-sales-page-table-status_active' },
      'completed': { text: my_sales_page_data.statuses.completed, class: 'masterstudy-my-sales-page-table-status_completed' },
      'cancelled': { text: my_sales_page_data.statuses.cancelled, class: 'masterstudy-my-sales-page-table-status_cancelled' },
      'on_hold': { text: my_sales_page_data.statuses.on_hold, class: 'masterstudy-my-sales-page-table-status_on_hold' },
      'processing': { text: my_sales_page_data.statuses.processing, class: 'masterstudy-my-sales-page-table-status_processing' },
      'pending': { text: my_sales_page_data.statuses.pending, class: 'masterstudy-my-sales-page-table-status_pending' },
      'approval_pending': { text: my_sales_page_data.statuses.approval_pending, class: 'masterstudy-my-sales-page-table-status_approval_pending' },
      'expired': { text: my_sales_page_data.statuses.expired, class: 'masterstudy-my-sales-page-table-status_expired' },
      'refunded': { text: my_sales_page_data.statuses.refunded, class: 'masterstudy-my-sales-page-table-status_refunded' },
    };
    const { text, class: statusClass } = statusMap[status] || { text: status, class: '' };
    return `<span class="masterstudy-my-sales-page-table-status ${statusClass}">${text}</span>`;
  }

  function getDetailsSlugByRoute(route) {
    const map = {
      [routes.orders]: 'instructor-sales-details',
      [routes.subscriptions]: 'instructor-subscription-details',
      [routes.memberships]: 'instructor-subscription-details',
    };
    return map[route] || 'instructor-sales-details';
  }

  function getRowEntityId(row, route) {
    if (route === routes.subscriptions) return row.subscription_id ?? row.id ?? null;
    if (route === routes.memberships) return row.subscription_id ?? row.id ?? null;
    return row.order_id ?? row.id ?? null;
  }

  function buildDetailsUrl(route, id) {
    const baseUrl = `${window.location.origin}${
      window.location.pathname.replace(/\/$/, '').split('/').slice(0, 2).join('/')
    }`;
    const slug = getDetailsSlugByRoute(route);
    return `${baseUrl}/${slug}/${id}/`;
  }
})(jQuery);
