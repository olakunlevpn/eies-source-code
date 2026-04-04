(function ($) {
  let instructorsTable   = null;
  let currentSearchValue = null;

  document.addEventListener('intentTableSearch', function(event) {
      currentSearchValue = event.detail.searchValue;
      if($(event.detail.searchTarget).parents('.masterstudy-analytics-sales-page-table').data('chart-id') === 'instructor-orders-table') {
        updateTable(routes.instructorSalesTable, true);
      }
  });

  document.addEventListener('datesUpdated', function() {
    const activeTab = $('.masterstudy-tabs__item_active').data('tab');
    if (activeTab === 'sales') {
      updateTable(routes.instructorSalesTable, true);
    } else if (activeTab === 'subscriptions') {
      if (typeof updateSubscriptionsTable === 'function') {
        updateSubscriptionsTable(routes.instructorSubscriptionsTable, true);
      }
    }
  });

  $(document).ready(function() {
    initializeDatepicker('#masterstudy-datepicker-global-datepicker');
    updateTable(routes.instructorSalesTable);
  });

  const STATUS_MAP = (users_page_data && users_page_data.statuses) || {};
  const getStatusName = (html) => {
    if (typeof html !== 'string') return html;
    if (html.indexOf('<span') === -1) {
      const key = html.trim().toLowerCase();
      return STATUS_MAP[key] ?? html;
    }
    return html.replace(
      /(<span\b[^>]*>)([^<]*)(<\/span>)/gi,
      (_, open, inner, close) => {
        const key = inner.trim().toLowerCase();
        const localized = STATUS_MAP[key] ?? inner;
        return open + localized + close;
      }
    );
  };

  const getTranslatedPaymentCode = (paymentCode) => {
    switch(paymentCode) {
      case 'wire_transfer':
        return users_page_data.payment_code_wire_transfer;
      case 'cash':
        return users_page_data.payment_code_cash;
      default:
        return paymentCode;
    }
  }

  function updateTable(currentRoute, reloadTable = false) {
    const dataSrc = function (json) {
      const pageInfo = $(`#masterstudy-datatable-${currentRoute}`)
        .DataTable()
        .page.info()
      const start = pageInfo.start;

      json.data = json.data.map((item, index) => {
        item.number = start + index + 1
        item.payment_code = getTranslatedPaymentCode(item.payment_code)
        item.status_name = getStatusName(item.status_name)
        return item
      });

      updateTotal(`#${currentRoute}-table-total`, json.recordsTotal);

      $('.masterstudy-analytics-sales-page__title').find('span').empty();
      $('.masterstudy-analytics-sales-page__title').append(`<span>${json.recordsTotal}</span>`);

      return json.data;
    }

    const columnDefs = users_page_data[currentRoute].map((col, index) => {
      const def = {
        targets: index,
        orderable: true,
        data: col.data
      };

      if (index === 0 || index === users_page_data[currentRoute].length - 1) {
        def.orderable = false;
      }

      if (col.data === 'subtotal' || col.data === 'taxes') {
        def.orderable = false;
      }

      if (index === users_page_data[currentRoute].length - 1) {
        def.render = function (data, type, row) {
          const baseUrl = `${window.location.origin}${window.location.pathname.split('/').slice(0, 2).join('/')}`;
          const newUrl = `${baseUrl}/instructor-sales-details/${data}/`;
          return renderReportButton(newUrl);
        };
      }

      return def;
    });

    tableToUpdate = instructorsTable;

    tableToUpdate = updateDataTable(
      tableToUpdate,
      `#masterstudy-datatable-${currentRoute}`,
      [`[data-chart-id="${currentRoute}-table"]`],
      currentRoute,
      users_page_data[currentRoute],
      dataSrc,
      columnDefs,
      reloadTable,
      false,
      false,
      [],
      currentSearchValue,
    );

    if (routes.instructorSalesTable === currentRoute) {
      instructorsTable = tableToUpdate;
    }
}

})(jQuery);
