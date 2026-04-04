"use strict";

new Vue({
  el: '#stm_lms_user_manage',
  data: {
    users: [],
    loading: true,
    direction: 'DESC',
    currentPage: 1,
    total: 1,
    aiToggle: false,
    historyModal: {
      status: false,
      history: []
    }
  },
  mounted: function mounted() {
    this.getUsers();
    this.$refs.aiToggle.classList.toggle('active', this.aiToggle);
  },
  computed: {
    computedUsers: function computedUsers() {
      if (this.direction === 'ASC') {
        return this.users.slice().sort(function (a, b) {
          return a.submission_time - b.submission_time;
        });
      } else {
        return this.users.slice().sort(function (a, b) {
          return b.submission_time - a.submission_time;
        });
      }
    }
  },
  methods: {
    getUsers: function getUsers() {
      var $this = this;
      var url = stm_lms_ajaxurl + '?action=stm_lms_get_users_submissions&nonce=' + stm_lms_nonces['stm_lms_get_users_submissions'] + '&page=' + $this.currentPage;
      $this.loading = true;
      this.$http.get(url).then(function (response) {
        var res = response.body;
        $this.loading = false;
        $this.users = res.users;
        $this.total = Math.ceil(parseInt(res.total) / 20);
        $this.aiToggle = res.ai_enabled_for_all;
        this.$refs.aiToggle.classList.toggle('active', this.aiToggle);
      });
    },
    updateUserStatus: function updateUserStatus(userId, key, action) {
      var $this = this;
      var url = stm_lms_ajaxurl + '?action=stm_lms_update_user_status&nonce=' + stm_lms_nonces['stm_lms_update_user_status'] + '&status=' + action + '&message=' + $this.computedUsers[key].message + '&user_id=' + userId;
      $this.loading = true;
      $this.$http.get(url).then(function (response) {
        var res = response.body;
        $this.$set($this.computedUsers[key], 'submission_history', res);
        $this.$set($this.computedUsers[key], 'status', action);
        $this.loading = false;
      });
    },
    showHistory: function showHistory(key) {
      var $this = this;
      $this.$set($this.historyModal, 'status', true);
      $this.$set($this.historyModal, 'history', $this.computedUsers[key].submission_history);
    },
    banUser: function banUser(userId, value) {
      var $this = this;
      var url = stm_lms_ajaxurl + '?action=stm_lms_ban_user&nonce=' + stm_lms_nonces['stm_lms_ban_user'] + '&user_id=' + userId + '&banned=' + value;
      $this.loading = true;
      $this.$http.get(url).then(function (response) {
        $this.loading = false;
      });
    },
    toggleUserAI: function toggleUserAI(userId, value) {
      var $this = this;
      var url = stm_lms_ajaxurl + '?action=stm_lms_toggle_user_ai_access&nonce=' + stm_lms_nonces['stm_lms_ban_user'] + '&user_id=' + userId + '&ai_enabled=' + value;
      $this.loading = true;
      $this.$http.get(url).then(function (response) {
        $this.loading = false;
      });
    },
    toggleAI: function toggleAI() {
      var $this = this;
      var url = stm_lms_ajaxurl + '?action=stm_lms_toggle_users_ai_access&nonce=' + stm_lms_nonces['stm_lms_ban_user'] + '&ai_enabled=' + $this.aiToggle;
      $this.loading = true;
      $this.$http.get(url).then(function (response) {
        $this.loading = false;
        $this.aiToggle = !$this.aiToggle;
        $this.getUsers();
      });
    },
    changePage: function changePage(page) {
      var $this = this;
      $this.currentPage = page;
      $this.getUsers();
    }
  }
});