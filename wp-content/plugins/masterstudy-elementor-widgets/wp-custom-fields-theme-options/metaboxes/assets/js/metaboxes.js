(function ($) {
    $(document).ready(function () {

        $('[data-vue]').each(function () {

            let $this = $(this);

            let data_var = $this.attr('data-vue');
            let data_source = $this.attr('data-source');

            new Vue({
                el: $(this)[0],
                data: function () {
                    return {
                        loading: false,
                        data: '',
                    }
                },
                mounted: function () {
                    this.getSettings();
                },
                methods: {
                    changeTabFromAnchor : function() {
                        var _this = this;
                        var hash = window.location.hash;
                        var hashParts = hash.split('#');
                        if(typeof hashParts[1] !== 'undefined') {
                            Vue.nextTick(function(){
                                _this.changeTab(hashParts[1]);
                            });
                        }
                    },
                    changeTab: function (tab) {
                        let $tab = $('#' + tab);
                        $tab.closest('.stm_metaboxes_grid__inner').find('.stm-lms-tab').removeClass('active');
                        $tab.addClass('active');

                        let $section = $('div[data-section="' + tab + '"]');
                        $tab.closest('.stm_metaboxes_grid__inner').find('.stm-lms-nav').removeClass('active');
                        $section.addClass('active');

                        history.pushState(null, null, '#' + tab);

                    },
                    getSettings: function () {
                        var _this = this;
                        _this.loading = true;

                        this.$http.get(stm_wpcfto_ajaxurl + '?action=stm_wpcfto_get_settings&source=' + data_source + '&name=' + data_var).then(function (r) {
                            _this.$set(_this, 'data', r.body);
                            _this.loading = false;

                            this.changeTabFromAnchor();
                        });

                    },
                    saveSettings: function (id) {
                        var vm = this;
                        vm.loading = true;
                        this.$http.post(stm_wpcfto_ajaxurl + '?action=stm_save_settings&nonce=' + stm_wpcfto_nonces['stm_save_settings'] + '&name=' + id, JSON.stringify(vm.data)).then(function (response) {
                            vm.loading = false;
                        });
                    },
                    initOpen(field) {
                        if(typeof field.opened === 'undefined') {
                            this.$set(field, 'opened', !!(field.value));
                        }
                    },
                    openField(field) {

                        var opened = !field.opened;


                        this.$set(field, 'opened', opened);

                        if(!field.opened) {
                            this.$set(field, 'value', '');
                        }
                    },
                    enableAddon($event, option) {
                        var _this = this;
                        Vue.nextTick(function() {
                            (function($) {


                                var currentItem = $($event.target);

                                currentItem.addClass('loading');

                                var url = stm_wpcfto_ajaxurl + '?action=stm_lms_enable_addon&addon=' + option;

                                _this.$http.get(url).then(function (response) {
                                    currentItem.removeClass('loading');
                                    var $container = $('.stm_lms_addon_group_settings_' + option);
                                    $container.each(function(){
                                        var $this = $(this);
                                        $this.removeClass('is_pro is_pro_in_addon');
                                        $this.find('.field_overlay').remove();
                                        $this.find('.pro-notice').remove();
                                    });
                                });

                            })(jQuery);
                        });
                    }
                },
            });

        });

    });
})(jQuery);

function WpcftoIsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}