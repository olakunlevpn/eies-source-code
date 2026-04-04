"use strict";

(function ($) {
  $(document).ready(function () {
    var classes = ['post-type-stm-courses', 'post-type-stm-lessons', 'post-type-stm-quizzes', 'post-type-stm-questions', 'post-type-stm-assignments', 'post-type-stm-google-meets', 'post-type-stm-user-assignment', 'post-type-stm-reviews', 'post-type-stm-orders', 'post-type-stm-ent-groups', 'post-type-stm-payout', 'taxonomy-stm_lms_course_taxonomy', 'taxonomy-stm_lms_question_taxonomy', 'stm-lms_page_stm-lms-online-testing', 'admin_page_stm_lms_scorm_settings', 'toplevel_page_stm-lms-dashboard'];
    var setupSettingsMenu = function setupSettingsMenu() {
      var $settingsParent = $('.stm-lms-settings-menu-title').closest('li');
      $settingsParent.addClass('stm-lms-settings-menu');
      $settingsParent.nextAll('li').addClass('stm-lms-pro-addons-menu');
    };
    var setupUsersMenu = function setupUsersMenu() {
      var $instructorsParent = $('.stm-lms-instructors-menu-title').closest('li');
      var $studentsParent = $('.stm-lms-students-menu-title').closest('li');
      $instructorsParent.addClass('stm-lms-instructors-menu');
      $studentsParent.addClass('stm-lms-students-menu');
    };
    var setupTemplatesMenu = function setupTemplatesMenu() {
      var $templates = $('.stm-lms-templates-menu-title');
      var $settings_parent = $('.stm-lms-settings-menu-title').closest('li');
      $settings_parent.next('li').addClass('stm-lms-help-support');
      if ($templates.length === 0) {
        $settings_parent.addClass('stm-lms-settings-menu');
      }
      var $templatesParent = $templates.closest('li');
      if (!$templatesParent.length) return;
      var li_addon_last = $('li.stm-lms-pro-addons-menu:last');
      $templatesParent.addClass('stm-lms-templates-menu');
      $templatesParent.next('li').addClass('stm-lms-addons-page-menu');
      $templatesParent.nextAll('li').addClass('stm-lms-pro-addons-menu');
      if (li_addon_last.find('span.stm-lms-unlock-pro-btn').length) {
        li_addon_last.addClass('upgrade');
      }
    };
    var updateDemoLink = function updateDemoLink() {
      var link = document.querySelector('a[href="admin.php?page=masterstudy-starter-demo-import"]');
      if (link) {
        link.target = "_blank";
        link.href = "https://stylemixthemes.com/wordpress-lms-plugin/starter-templates/";
      }
    };
    var highlightMenu = function highlightMenu() {
      if ($('body').is("." + classes.join(', .'))) {
        $('#adminmenu > li').removeClass('wp-has-current-submenu wp-menu-open').find('.wp-submenu').css('margin-right', 0);
        $('#toplevel_page_stm-lms-settings').addClass('wp-has-current-submenu wp-menu-open').removeClass('wp-not-current-submenu');
        $('.toplevel_page_stm-lms-settings').addClass('wp-has-current-submenu').removeClass('wp-not-current-submenu');
      }
    };

    // unlock banner slider
    var slidePosition = 0;
    var numOfSlide = $("#unlock-slider-slide-holder > div").size();
    $("#unlock-slider-slide-holder").css("width", numOfSlide * 100 + "%");
    $(".unlock-slider-slide").css("width", 100 / numOfSlide + "%");
    for (var a = 0; a < numOfSlide; a++) {
      $('#unlock-slider-slide-nav').append(' <a href="javascript: void(0)" class="unlock-slider-slide-nav-bt' + (a === 0 ? ' active' : '') + '">  </a> ');
    }
    $('body').on('click', '.unlock-slider-slide-nav-bt', function () {
      moveSlide($(this));
      clearInterval(autoPlaySlideInter);
    });
    function moveSlide(thisa) {
      var thisindex = $('#unlock-slider-slide-nav a').index(thisa);
      $('#unlock-slider-slide-holder').css("margin-left", '-' + thisindex + '00%');
      $('#unlock-slider-slide-nav a').removeClass('active');
      thisa.addClass('active');
    }
    function autoPlaySlide() {
      slidePosition++;
      if (slidePosition == numOfSlide) {
        slidePosition = 0;
      }
      moveSlide($("#unlock-slider-slide-nav").children(".unlock-slider-slide-nav-bt:eq(" + slidePosition + ")"));
    }
    var autoPlaySlideInter = setInterval(autoPlaySlide, 4000);
    setupSettingsMenu();
    setupUsersMenu();
    setupTemplatesMenu();
    updateDemoLink();
    highlightMenu();
  });
  $(window).on('load', function () {
    if (!$('body').hasClass('post-type-stm-questions')) return;
    var originalTitle = $('#titlediv input').val();
    var observer = new MutationObserver(function (mutationsList, observer) {
      var $editor = $('#editorquestion_title .ql-editor');
      if ($editor.length) {
        $editor.html(originalTitle);
        observer.disconnect();
      }
      $('#section_question_settings .ql-toolbar').each(function () {
        $(this).find('.ql-color, .ql-blockquote').each(function () {
          $(this).parent().remove();
        });
      });
    });
    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  });
})(jQuery);