(function ($) {
  $(document).ready(function () {
    var $root = $(".masterstudy-account-bundles");
    if (!$root.length || typeof window.bundles_data === "undefined") return;

    var cfg = window.bundles_data;

    var state = {
      page: 1,
      per_page: parseInt(cfg.per_page, 10) || 3,
      total_pages: parseInt(cfg.list && cfg.list.pages, 10) || 1,
      loading: false,
      pending: null,
    };

    var SEL = {
      list: ".masterstudy-account-bundles__list",
      pagination: ".masterstudy-account-bundles__pagination",
      loader: ".masterstudy-account-bundles__loader",
      empty: ".masterstudy-account-bundles__empty",
    };

    var CLS = {
      loaderShow: "masterstudy-account-bundles__loader_show",
      emptyShow: "masterstudy-account-bundles__empty_show",
    };

    var $list = $root.find(SEL.list);
    var $pagination = $root.find(SEL.pagination);
    var $loader = $root.find(SEL.loader);
    var $empty = $root.find(SEL.empty);

    if (!$list.length || !$pagination.length || !$empty.length) return;

    function ensureAjaxGlobals() {
      return (
        typeof window.stm_lms_ajaxurl !== "undefined" &&
        typeof window.stm_lms_nonces !== "undefined" &&
        typeof window.stm_lms_nonces["stm_lms_get_user_bundles"] !== "undefined"
      );
    }

    function showLoader() {
      if ($loader.length) $loader.addClass(CLS.loaderShow);
      $root.addClass("masterstudy-account-bundles__loading");
    }

    function hideLoader() {
      if ($loader.length) $loader.removeClass(CLS.loaderShow);
      $root.removeClass("masterstudy-account-bundles__loading");
    }

    function showNoResult() {
      $empty.addClass(CLS.emptyShow);
    }

    function hideNoResult() {
      $empty.removeClass(CLS.emptyShow);
    }

    function initPagination(page, totalPages) {
      if (typeof window.pages_data !== "undefined" && window.pages_data) {
        window.pages_data.current_page = parseInt(page, 10) || 1;
        window.pages_data.total_pages = parseInt(totalPages, 10) || 0;
        window.pages_data.max_visible_pages =
          parseInt(window.pages_data.max_visible_pages, 10) || 5;
        window.pages_data.item_width = parseInt(window.pages_data.item_width, 10) || 50;
        window.pages_data.is_queryable = !!window.pages_data.is_queryable;
      }

      if (typeof window.initializePagination === "function") {
        window.initializePagination(parseInt(page, 10) || 1, parseInt(totalPages, 10) || 0);
      }
    }

    function renderResponse(data, fallbackPage) {
      var html = data && data.html ? data.html : "";
      var paginationHtml = data && data.pagination ? data.pagination : "";

      var totalPages = parseInt(data && (data.total_pages ?? data.pages), 10) || 0;
      var currentPage =
        parseInt(data && data.current_page, 10) ||
        parseInt(fallbackPage, 10) ||
        1;

      // sync state from backend payload
      if (totalPages > 0) state.total_pages = totalPages;
      state.page = currentPage;

      $list.empty();
      $pagination.empty();

      if (html && $.trim(html).length) {
        $list.html(html);
        hideNoResult();
      } else {
        showNoResult();
      }

      if (paginationHtml && $.trim(paginationHtml).length && state.total_pages > 1) {
        $pagination.html(paginationHtml);
        initPagination(state.page, state.total_pages);
      }
    }

    function abortPending() {
      if (state.pending && typeof state.pending.abort === "function") {
        state.pending.abort();
      }
      state.pending = null;
    }

    function buildUrl(page) {
      var url =
        window.stm_lms_ajaxurl +
        "?action=stm_lms_get_user_bundles" +
        "&render=html" +
        "&nonce=" +
        encodeURIComponent(window.stm_lms_nonces["stm_lms_get_user_bundles"]) +
        "&page=" +
        encodeURIComponent(page) +
        "&per_page=" +
        encodeURIComponent(state.per_page);

      if (typeof window.pll_current_language !== "undefined") {
        url += "&lang=" + encodeURIComponent(window.pll_current_language);
      }

      return url;
    }

    function loadBundles(page, force) {
      var safePage = parseInt(page, 10) || 1;
      var isForce = force === true;

      if (safePage < 1) safePage = 1;
      if (state.total_pages && safePage > state.total_pages) safePage = state.total_pages;

      if (state.loading) return;
      if (!ensureAjaxGlobals()) return;

      if (!isForce && safePage === state.page) return;

      state.page = safePage;
      state.loading = true;

      abortPending();

      $list.empty();
      $pagination.empty();
      hideNoResult();

      showLoader();

      var url = buildUrl(state.page);

      var controller = new AbortController();
      state.pending = controller;

      fetch(url, { method: "GET", signal: controller.signal })
        .then(function (response) {
          if (!response.ok) throw new Error("Bad response");
          return response.json();
        })
        .then(function (data) {
          renderResponse(data, state.page);
          requestAnimationFrame(function () {
            scrollToBundlesTop();
          });
          $(document).trigger("masterstudy:bundles:updated", [data]);
        })
        .catch(function (err) {
          if (err && (err.name === "AbortError" || err.code === 20)) return;
          alert((cfg.strings && cfg.strings.error) || "Error");
        })
        .finally(function () {
          state.loading = false;
          hideLoader();
          state.pending = null;
        });
    }

    function refreshCurrent() {
      loadBundles(state.page, true);
    }

    // Pagination: number
    $root.on("click", ".masterstudy-pagination__item-block", function (e) {
      e.preventDefault();

      var page = parseInt($(this).data("id"), 10) || 1;
      if (page < 1) page = 1;
      if (state.total_pages && page > state.total_pages) page = state.total_pages;

      if (page === state.page) return;

      loadBundles(page, true);
    });

    // Pagination: prev/next (use state.page, not DOM)
    $root.on(
      "click",
      ".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next",
      function (e) {
        e.preventDefault();

        var isNext = $(this).hasClass("masterstudy-pagination__button-next");
        var nextPage = isNext ? state.page + 1 : state.page - 1;

        if (nextPage < 1) nextPage = 1;
        if (state.total_pages && nextPage > state.total_pages) nextPage = state.total_pages;

        if (nextPage === state.page) return;

        loadBundles(nextPage, true);
      }
    );

    if (state.total_pages > 1) {
      initPagination(state.page, state.total_pages);
    }

    if (!$list.children().length) {
      showNoResult();
    }

    function scrollToBundlesTop() {
      var el = $list.get(0);
      if (!el) return;

      var top = el.getBoundingClientRect().top + window.pageYOffset - 60;

      window.scrollTo({
        top: top,
        behavior: "smooth",
      });
    }

    var MODAL = {
      btn: ".masterstudy-bundle-instructor-actions__modal-btn",
      modal: ".masterstudy-bundle-instructor-actions__modal",
      show: "masterstudy-bundle-instructor-actions__modal_show",
    };

    function closeAllActionModals() {
      $(MODAL.modal).removeClass(MODAL.show);
    }

    function getModalForBtn($btn) {
      var $card = $btn.closest(".masterstudy-bundle-card");
      if (!$card.length) return $();

      return $card.find(MODAL.modal).first();
    }

    $root.on("click", MODAL.btn, function (e) {
      e.preventDefault();
      e.stopPropagation();

      var $btn = $(this);
      var $modal = getModalForBtn($btn);
      if (!$modal.length) return;

      var isOpen = $modal.hasClass(MODAL.show);

      closeAllActionModals();

      if (!isOpen) {
        $modal.addClass(MODAL.show);
      }
    });

    $root.on("click", MODAL.modal, function (e) {
      e.stopPropagation();
    });

    $(document).on("click", function (e) {
      if ($(e.target).closest(MODAL.btn + "," + MODAL.modal).length) return;
      closeAllActionModals();
    });

    $(document).on("keydown", function (e) {
      if (e.key === "Escape" || e.keyCode === 27) {
        closeAllActionModals();
      }
    });

    // Actions: delete / toggle-status
    function getBundleId($el) {
      var direct = parseInt($el.data("bundle-id"), 10);
      if (direct) return direct;

      var fromCard = parseInt($el.closest("[data-bundle-id]").data("bundle-id"), 10);
      if (fromCard) return fromCard;

      return 0;
    }

    $root.on("click", '[data-bundle-action="delete"]', function (e) {
      e.preventDefault();

      var $btn = $(this);
      var bundleId = getBundleId($btn);
      if (!bundleId) return;

      var msg =
        (cfg.strings && cfg.strings.delete_confirm) ||
        "Do you really want to delete this bundle?";

      if (!confirm(msg)) return;

      if (
        !ensureAjaxGlobals() ||
        typeof window.stm_lms_nonces["stm_lms_delete_bundle"] === "undefined"
      )
        return;

      showLoader();

      $.get(window.stm_lms_ajaxurl, {
        action: "stm_lms_delete_bundle",
        nonce: window.stm_lms_nonces["stm_lms_delete_bundle"],
        bundle_id: bundleId,
      }).always(function () {
        hideLoader();
        refreshCurrent();
      });
    });

    $root.on("click", '[data-bundle-action="toggle-status"]', function (e) {
      e.preventDefault();

      var $btn = $(this);
      var bundleId = getBundleId($btn);
      if (!bundleId) return;

      if (
        !ensureAjaxGlobals() ||
        typeof window.stm_lms_nonces["stm_lms_change_bundle_status"] === "undefined"
      )
        return;

      if ($btn.data("loading")) return;
      $btn.data("loading", true);

      var oldText = $.trim($btn.text());

      showLoader();

      $.get(window.stm_lms_ajaxurl, {
        action: "stm_lms_change_bundle_status",
        nonce: window.stm_lms_nonces["stm_lms_change_bundle_status"],
        bundle_id: bundleId,
      })
        .done(function (resp) {
          if (resp !== "OK") {
            alert(resp);
            return;
          }

          var moveToDrafts =
            (cfg.strings && cfg.strings.move_to_drafts) || "Move to drafts";
          var publish = (cfg.strings && cfg.strings.publish) || "Publish";

          var nextText = oldText === moveToDrafts ? publish : moveToDrafts;
          $btn.text(nextText);
        })
        .always(function () {
          hideLoader();
          $btn.data("loading", false);
        });
    });
  });
})(jQuery);