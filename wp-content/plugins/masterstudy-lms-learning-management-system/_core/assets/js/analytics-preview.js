"use strict";

(function ($) {
  $(document).ready(function () {
    var originalSrc = $('#masterstudy-analytics-preview-video').attr('src');
    $('[data-id="analytics-watch-video"]').click(function (event) {
      event.preventDefault();
      var currentSrc = $('#masterstudy-analytics-preview-video').attr('src');
      if (!currentSrc) {
        $('#masterstudy-analytics-preview-video').attr('src', originalSrc);
      }
      $('.masterstudy-analytics-preview-page__popup').addClass('masterstudy-analytics-preview-page__popup_show');
    });
    $('.masterstudy-analytics-preview-page__popup').click(function (event) {
      if (!$(event.target).closest('.masterstudy-analytics-preview-page__popup-video').length) {
        $('#masterstudy-analytics-preview-video').attr('src', '');
        $('.masterstudy-analytics-preview-page__popup').removeClass('masterstudy-analytics-preview-page__popup_show');
      }
    });
  });
})(jQuery);