var stm_lms_get_image_mixin = {
    data: function () {
        return {
            image_url: '',
        }
    },
    created() {
        if (this.$options.propsData.fields.type === 'image') {
            this.get_image_url(this.$options.propsData.field_value);
        }
    },
    methods: {
        get_image_url(image_id) {
            this.$http.get(stm_wpcfto_ajaxurl + '?action=stm_lms_get_image_url&nonce=' + stm_wpcfto_nonces['get_image_url'] + '&image_id=' + image_id).then(function (response) {
                this.image_url = response.body;
            });
        },
        wpcfto_checkURL(url) {
            return (url.match(/\.(jpeg|jpg|gif|png)$/) != null);
        }
    }
};