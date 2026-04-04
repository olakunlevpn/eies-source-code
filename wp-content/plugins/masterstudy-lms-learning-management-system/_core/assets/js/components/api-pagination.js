"use strict";

function initializePagination(currentPage, totalPages) {
  var itemWidth = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 50;
  var pagesContainer = jQuery(".masterstudy-pagination");
  var pagesWrapper = pagesContainer.find(".masterstudy-pagination__wrapper");
  var pagesList = pagesContainer.find(".masterstudy-pagination__list");
  var scrollButtonNext = pagesContainer.find(".masterstudy-pagination__button-next");
  var scrollButtonPrev = pagesContainer.find(".masterstudy-pagination__button-prev");
  var numericFields = ["max_visible_pages", "total_pages", "current_page", "item_width"];
  if (typeof pages_data === 'undefined') {
    var pages_data = {
      'max_visible_pages': 5,
      'total_pages': totalPages,
      'current_page': currentPage,
      'is_queryable': false,
      'item_width': itemWidth
    };
  }
  numericFields.forEach(function (field) {
    pages_data[field] = parseInt(pages_data[field]);
  });
  var containerWidth = pagesWrapper.data('width'),
    currentPosition = 0,
    centeredPage = Math.round(pages_data.max_visible_pages / 2),
    maxPosition = pages_data.item_width * (totalPages - pages_data.max_visible_pages),
    noScroll = totalPages <= pages_data.max_visible_pages;
  if (containerWidth) {
    pagesWrapper.css("width", containerWidth);
  }
  prevNextButtonState(pagesContainer, currentPage, totalPages);
  setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
  centerActivePage(pagesWrapper, pagesList, currentPage);
  scrollButtonNext.off('click').on('click', function (e) {
    e.preventDefault();
    if (currentPage >= totalPages) return;
    currentPage += 1;
    prevNextButtonState(pagesContainer, currentPage, totalPages);
    setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
    centerActivePage(pagesWrapper, pagesList, currentPage);
    if (pages_data.is_queryable) updatePageQueryParam(currentPage);
  });
  scrollButtonPrev.off('click').on('click', function (e) {
    e.preventDefault();
    if (currentPage <= 1) return;
    currentPage -= 1;
    prevNextButtonState(pagesContainer, currentPage, totalPages);
    setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
    centerActivePage(pagesWrapper, pagesList, currentPage);
    if (pages_data.is_queryable) updatePageQueryParam(currentPage);
  });
  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }
  function centerActivePage(pagesWrapper, pagesList, pageNumber) {
    var activeBlock = pagesList.find("[data-id=\"".concat(pageNumber, "\"]"));
    if (!activeBlock.length) return;
    var wrapperWidth = pagesWrapper[0].getBoundingClientRect().width;
    var blockRect = activeBlock[0].getBoundingClientRect();
    var listRect = pagesList[0].getBoundingClientRect();
    var blockCenter = blockRect.left - listRect.left + blockRect.width / 2;
    var targetLeft = blockCenter - wrapperWidth / 2;
    var maxScroll = Math.max(0, pagesList[0].scrollWidth - wrapperWidth);
    targetLeft = clamp(targetLeft, 0, maxScroll);
    pagesList.stop(true).animate({
      left: -targetLeft + "px"
    }, 120);
  }
  jQuery(".masterstudy-pagination__item-block").off('click').on('click', function () {
    currentPage = parseInt(jQuery(this).data("id"), 10);
    prevNextButtonState(pagesContainer, currentPage, totalPages);
    setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
    centerActivePage(pagesWrapper, pagesList, currentPage);
    if (pages_data.is_queryable) {
      updatePageQueryParam(currentPage);
    }
  });
}
function calculateInitialPosition(currentPage, centeredPage, totalPages, maxPosition) {
  if (currentPage <= centeredPage) {
    return 0;
  } else if (currentPage > totalPages - centeredPage) {
    return maxPosition;
  } else {
    return (currentPage - centeredPage) * pages_data.item_width;
  }
}
function setCurrentPage(pagesList, currentPage, className) {
  pagesList.find("[data-id=\"".concat(currentPage, "\"]")).parent().siblings().removeClass(className);
  pagesList.find("[data-id=\"".concat(currentPage, "\"]")).parent().addClass(className);
}
function updateButtonState(scrollButtonNext, scrollButtonPrev, currentPage, totalPages) {
  scrollButtonPrev.toggleClass("masterstudy-pagination__button_disabled", currentPage === 1 || totalPages === 1);
  scrollButtonNext.toggleClass("masterstudy-pagination__button_disabled", currentPage === totalPages || totalPages === 1);
}
function scrollPageList(currentPage, centeredPage, maxPosition, pagesList) {
  var currentPosition = 0;
  if (currentPage > centeredPage && currentPage < pages_data.total_pages - centeredPage + 1) {
    currentPosition = (currentPage - centeredPage) * pages_data.item_width;
  } else if (currentPage <= centeredPage) {
    currentPosition = 0;
  } else {
    currentPosition = maxPosition;
  }
  pagesList.animate({
    left: -currentPosition + "px"
  }, 50);
}
function calculateCurrentPosition(currentPage, centeredPage, maxPosition, noScroll, totalPages) {
  if (currentPage < centeredPage) {
    return 0;
  } else if (currentPage > totalPages - centeredPage + 1) {
    return noScroll ? 0 : maxPosition;
  } else {
    return (currentPage - centeredPage) * pages_data.item_width;
  }
}
function updatePageQueryParam(pageNumber) {
  var currentUrl = window.location.href;
  var urlParams = new URLSearchParams(window.location.search);
  var queryName = "page";
  if (urlParams.has(queryName)) {
    urlParams.set(queryName, pageNumber);
  } else {
    urlParams.append(queryName, pageNumber);
  }
  var queryUrl = currentUrl.split("?")[0] + "?" + urlParams.toString();
  window.history.replaceState({}, document.title, queryUrl);
  window.location.href = queryUrl;
}
function prevNextButtonState(container, currentPage, totalPages) {
  var btnClassPrev = '.masterstudy-pagination__button-prev';
  var btnClassNext = '.masterstudy-pagination__button-next';
  container.find(btnClassPrev).toggleClass("masterstudy-pagination__button_disabled", currentPage === 1 || totalPages === 1);
  container.find(btnClassNext).toggleClass("masterstudy-pagination__button_disabled", currentPage === totalPages || totalPages === 1);
}
jQuery(document).ready(function () {
  if (typeof pages_data !== 'undefined') {
    initializePagination(parseInt(pages_data.current_page), parseInt(pages_data.total_pages), parseInt(pages_data.item_width));
  }
});