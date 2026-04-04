(function ($) {
  let subscriptionsTable = null;
  let searchValue = '';
  let searchField = $('input[name="table-search"]');
  let subsIdToCancel = null;
  const routes = {
    subscriptions: 'my-subscription',
    memberships: 'my-membership',
  };

  document.addEventListener('datesUpdated', function() {
    const activeTab = $('.masterstudy-tabs__item_active').data('tab') || routes.memberships;
    updateTable(activeTab, true);
  });

  $(document).ready(function() {
    initializeDatepicker('#masterstudy-datepicker-my-subscriptions');
    updateTable(routes.memberships);

    $('.masterstudy-subscriptions-page__tabs .masterstudy-tabs__item').click(function() {
        const tabRoute = $(this).data('tab');
        $(this).addClass('masterstudy-tabs__item_active');
        $(this).siblings().removeClass('masterstudy-tabs__item_active');
        searchField.val('');
        searchField.closest('.masterstudy-search').removeClass('masterstudy-search_inuse');
        updateTable(tabRoute, true);
    });

    $('.masterstudy-search__icon').click(function() {
      const activeTab = $('.masterstudy-tabs__item_active').data('tab');
      updateTable(activeTab, true);
    })

    $('.masterstudy-search__clear-icon').click(function() {
      searchField.val('');
      searchField.closest('.masterstudy-search').removeClass('masterstudy-search_inuse');
      const activeTab = $('.masterstudy-tabs__item_active').data('tab');
      updateTable(activeTab, true);
    })

    $(document).on('click', '.masterstudy-subscriptions-page-cancel-button', function () {
      const $btn = $(this);
      subsIdToCancel = $btn.data('id');
      if (!subsIdToCancel) return;

      $('.masterstudy-alert').addClass('masterstudy-alert_open');
    });

    $(document).on('click', '.masterstudy-subscriptions-page-resubscribe-button', function () {
      const $btn = $(this);
      const subsId = $btn.data('id');
      if (!subsId) return;

      let current_data = {
				action: 'stm_lms_add_to_cart_subscription',
				plan_id: subsId,
				nonce: stm_lms_nonces['stm_lms_add_to_cart_subscription']
			};

      $.ajax({
				url: stm_lms_ajaxurl,
				type: 'POST',
				data: current_data,
				beforeSend: function () {
					$btn.addClass('masterstudy-subscriptions-page-resubscribe-button_loading');
				},
				success(res) {
					if (res.success) {
            if ( res.data['cart_url'].length > 0 ) {
              $btn.removeClass('masterstudy-subscriptions-page-resubscribe-button_loading');
              window.location.href = res.data['cart_url'];
					  }
          }
				}
			});
    });
  });

  $('.masterstudy-alert').on('click', function(event) {
		if (event.target === this) {
			$(this).removeClass('masterstudy-alert_open');
		}
	});

  $(document).on('click', '.masterstudy-alert__actions [data-id="cancel"]', function(e) {
		e.preventDefault();
		$('.masterstudy-alert').removeClass('masterstudy-alert_open');
	});

  $(document).on('click', '.masterstudy-alert__header-close', function(e) {
		e.preventDefault();
		$('.masterstudy-alert').removeClass('masterstudy-alert_open');
	});

  $(document).on('click', '.masterstudy-alert__actions [data-id="submit"]', function(e) {
		e.preventDefault();
    const _this = $(this);
    $.ajax({
      url: `${subscriptions_page_data.rest_url}my-subscription/${subsIdToCancel}/cancel`,
      method: 'PUT',
      headers: { 'X-WP-Nonce': subscriptions_page_data.nonce },
      data: { subscription_id: subsIdToCancel },
      beforeSend: function() {
          _this.addClass('masterstudy-button_loading');
      },
      success: function(data) {
        if (data && data.status === 'ok') {
          _this.removeClass('masterstudy-button_loading');
          $('.masterstudy-alert').removeClass('masterstudy-alert_open');
          const activeTab = $('.masterstudy-subscriptions-page__tabs .masterstudy-tabs__item_active').data('tab');
          updateTable(activeTab, true);
        }
      },
      error: function (xhr) {
        _this.removeClass('masterstudy-button_loading');

        let msg = 'An unknown error occurred.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
          try {
            const json = JSON.parse(xhr.responseText);
            if (json && json.message) msg = json.message;
          } catch (e) { /* ignore */ }
        }

        _this.closest('.masterstudy-alert__container')
          .find('.masterstudy-alert__error')
          .text(msg)
          .show();
      }
    })
	});

  function updateTable(currentRoute, reloadTable = false) {
    const dataSrc = function (json) {
      const pageInfo = $('#masterstudy-datatable-my-subscriptions').DataTable().page.info();
      const start = pageInfo.start;

      json.data = json.data.map((item, index) => {
        item.number = start + index + 1;
        return item;
      });

      return json.data;
    };

    const route = (currentRoute === 'my-membership')
    ? 'my-subscription'
    : currentRoute;

    searchValue = searchField.val();

    const columns = subscriptions_page_data[currentRoute];
    const columnDefs = buildColumnDefs(columns, currentRoute);

    subscriptionsTable = updateDataTable(
      subscriptionsTable,
      '#masterstudy-datatable-my-subscriptions',
      ['[data-chart-id="my-subscriptions-table"]'],
      `${route}/list`,
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
      [routes.subscriptions]: new Set(['id', 'status']),
      [routes.memberships]: new Set(['id', 'status']),
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

      if (col.data === 'plan') {
        def.render = function (_data, _type, row) {
          let content = '';

          if (row.type === 'course') {
            const courseTitle = row.course?.title
              ? `<div class="masterstudy-subscriptions-page-table__course-title">${row.course.title}</div>`
              : '';
            const planTitle = `${subscriptions_page_data.plan_title}: ${row.plan ? row.plan : ''}`;
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
        return renderStatus(row.status);
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
        return renderButton(id, url, row);
      },
    });

    return defs;
  }

  const buildExtraParams = (route) => {
    const params = {};

    if (route === 'my-membership') {
      params.subscription_type = [ 'category', 'full_site' ];
    }

    if (route === 'my-subscription') {
      params.subscription_type = [ 'course' ];
    }

    return params;
  };

  function renderButton(id, url, row) {
    let button = '';
    const for_course_enabled = row.subs_for_course_enabled && row.type === 'course';
    const membership_enabled = row.is_enabled && row.type !== 'course';

    if (row.status === 'active' || row.status === 'trial' || row.status === 'completed') {
      button =
        '<span data-id="' + id + '" class="masterstudy-subscriptions-page-cancel-button">' +
          subscriptions_page_data.cancel_title +
        '</span>';
    } else if ( (for_course_enabled || membership_enabled ) && row.is_latest && row.status !== 'trial' ) {
      button =
        '<span data-id="' + row.plan_id + '" class="masterstudy-subscriptions-page-resubscribe-button">' +
          subscriptions_page_data.resubscribe_title +
        '</span>';
    }

    return (
      '<div class="masterstudy-subscriptions-page-table-button__wrapper">' +
        button +
        '<a href="' + url + '" class="masterstudy-subscriptions-page-report-button">' +
          subscriptions_page_data.details_title +
        '</a>' +
      '</div>'
    );
  }

  function renderStatus(status) {
    const statusMap = {
      'trialing': { text: subscriptions_page_data.statuses.trialing, class: 'masterstudy-subscriptions-page-table-status_trialing' },
      'trial': { text: subscriptions_page_data.statuses.trial, class: 'masterstudy-subscriptions-page-table-status_trial' },
      'active': { text: subscriptions_page_data.statuses.active, class: 'masterstudy-subscriptions-page-table-status_active' },
      'completed': { text: subscriptions_page_data.statuses.completed, class: 'masterstudy-subscriptions-page-table-status_completed' },
      'cancelled': { text: subscriptions_page_data.statuses.cancelled, class: 'masterstudy-subscriptions-page-table-status_cancelled' },
      'on_hold': { text: subscriptions_page_data.statuses.on_hold, class: 'masterstudy-subscriptions-page-table-status_on_hold' },
      'pending': { text: subscriptions_page_data.statuses.pending, class: 'masterstudy-subscriptions-page-table-status_pending' },
      'approval_pending': { text: subscriptions_page_data.statuses.approval_pending, class: 'masterstudy-subscriptions-page-table-status_approval_pending' },
      'expired': { text: subscriptions_page_data.statuses.expired, class: 'masterstudy-subscriptions-page-table-status_expired' },
      'refunded': { text: subscriptions_page_data.statuses.refunded, class: 'masterstudy-subscriptions-page-table-status_refunded' },
    };
    const { text, class: statusClass } = statusMap[status] || { text: status, class: '' };
    return `<span class="masterstudy-subscriptions-page-table-status ${statusClass}">${text}</span>`;
  }

  function getDetailsSlugByRoute(route) {
    const map = {
      [routes.subscriptions]: 'student-subscription-details',
      [routes.memberships]: 'student-subscription-details',
    };
    return map[route] || 'student-subscription-details';
  }

  function getRowEntityId(row, route) {
    if (route === routes.subscriptions) return row.id ?? null;
    if (route === routes.memberships) return row.id ?? null;
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
