(function ($) {
  $(document).ready(function () {
    var $root = $(".masterstudy-points");
    if (!$root.length) return;

    var $list = $root.find(".masterstudy-points__list");
    var $pagination = $root.find(".masterstudy-points__pagination");
    var $loader = $root.find(".masterstudy-points__loader");
    var $empty = $root.find(".masterstudy-points__empty");

    if (!$list.length) return;

    var loading = false;
    var pending = null;

    function showLoader() {
      if ($loader.length) $loader.addClass("masterstudy-points__loader_show");
    }

    function hideLoader() {
      if ($loader.length) $loader.removeClass("masterstudy-points__loader_show");
    }

    function showEmpty() {
      if ($empty.length) $empty.addClass("masterstudy-points__empty_show");
    }

    function hideEmpty() {
      if ($empty.length) $empty.removeClass("masterstudy-points__empty_show");
    }

    function abortPending() {
      if (pending && typeof pending.abort === "function") pending.abort();
      pending = null;
    }

    function initPagination(currentPage, totalPages) {
      if (typeof window.pages_data !== "undefined" && window.pages_data) {
        window.pages_data.current_page = parseInt(currentPage, 10) || 1;
        window.pages_data.total_pages = parseInt(totalPages, 10) || 0;
        window.pages_data.max_visible_pages =
          parseInt(window.pages_data.max_visible_pages, 10) || 5;
        window.pages_data.item_width =
          parseInt(window.pages_data.item_width, 10) || 50;
      }

      if (typeof window.initializePagination === "function") {
        window.initializePagination(
          parseInt(currentPage, 10) || 1,
          parseInt(totalPages, 10) || 0
        );
      }
    }

    function renderPayload(payload, fallbackPage) {
      var html = payload && payload.html ? payload.html : "";
      var paginationHtml = payload && payload.pagination ? payload.pagination : "";
      var totalPages = parseInt(payload && payload.total_pages, 10) || 0;
      var currentPage =
        parseInt(payload && payload.current_page, 10) || parseInt(fallbackPage, 10) || 1;

      // list
      if (html && $.trim(html).length) {
        $list.html(html);
      } else {
        showEmpty();
      }

      // pagination
      if ($pagination.length) {
        if (paginationHtml && $.trim(paginationHtml).length && totalPages > 1) {
          $pagination.html(paginationHtml);
          initPagination(currentPage, totalPages);
        } else {
          $pagination.empty();
        }
      }
    }

    function getCurrentPaginationPage() {
      var $current = $root.find(
        ".masterstudy-pagination__item_current .masterstudy-pagination__item-block"
      );
      return parseInt($current.data("id"), 10) || 1;
    }

    function loadPage(nextPage) {
      var p = parseInt(nextPage, 10) || 1;
      if (p < 1) p = 1;

      if (loading) return;

      if (
        typeof window.stm_lms_ajaxurl === "undefined" ||
        typeof window.stm_lms_nonces === "undefined" ||
        typeof window.stm_lms_nonces["stm_lms_get_user_points_history"] === "undefined"
      ) {
        return;
      }

      loading = true;
      abortPending();

      $list.empty();
      $pagination.empty();
      hideEmpty();

      showLoader();
      scrollToListTop();

      pending = $.ajax({
        url: window.stm_lms_ajaxurl,
        method: "GET",
        dataType: "json",
        data: {
          action: "stm_lms_get_user_points_history",
          nonce: window.stm_lms_nonces["stm_lms_get_user_points_history"],
          page: p,
        },
      })
        .done(function (resp) {
          // resp is the payload itself because backend uses wp_send_json(...)
          renderPayload(resp, p);
          $(document).trigger("masterstudy:points_history:updated", [resp]);
        })
        .fail(function (xhr) {
          if (xhr && xhr.statusText === "abort") return;
          alert("Error");
        })
        .always(function () {
          loading = false;
          hideLoader();
          pending = null;
        });
    }

    // Page number click
    $root.on("click", ".masterstudy-pagination__item-block", function (e) {
      e.preventDefault();
      var p = parseInt($(this).data("id"), 10) || 1;
      loadPage(p);
    });

    // Prev / Next click
    $root.on(
      "click",
      ".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next",
      function (e) {
        e.preventDefault();

        var $btn = $(this);

        var current = getCurrentPaginationPage();
        var next = $btn.hasClass("masterstudy-pagination__button-next")
          ? current + 1
          : current - 1;

        loadPage(next);
      }
    );
    
    function scrollToListTop() {
      var offset = $list.offset();
      if (!offset || typeof offset.top !== "number") return;
      $("html, body").stop(true).animate({ scrollTop: offset.top - 90 }, 250);
    }
  });
})(jQuery);
