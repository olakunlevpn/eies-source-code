"use strict";

stm_lms_components['back'] = {
  template: '#stm-lms-dashboard-back',
  methods: {
    goBack: function goBack() {
      var ref = document.referrer;
      if (ref && ref.includes('/wp-admin/admin.php?page=manage_students')) {
        window.location.assign(ref);
      } else {
        this.$router.go(-1);
      }
    }
  }
};