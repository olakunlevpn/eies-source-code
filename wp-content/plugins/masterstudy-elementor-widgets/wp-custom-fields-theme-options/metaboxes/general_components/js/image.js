Vue.component('wpcfto_image', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    mixins: [stm_lms_get_image_mixin],
    data: function () {
        return {
            value: '',
            media_modal: '',
            image_url: ''
        }
    },
    mounted: function () {
        var vm = this;
        vm.value = vm.field_value;
    },
    template: `
        <div class="wpcfto_generic_field">
        
            <label v-html="field_label"></label>
        
            <div class="stm-lms-image">
                <div class="image-field" v-if="image_url">
                    <img v-bind:src="image_url" v-if="wpcfto_checkURL(image_url)"/>
                    <div class="image-field-file" v-else>
                        <i class="fa fa-file-alt"></i>
                        {{image_url}}
                    </div>
                </div>
                <div class="actions">
                    <div class="button" v-if="!image_url" @click="addImage()">
                        Add File
                    </div>
                    <div class="button" v-if="image_url" @click="removeImage()">
                        Remove File
                    </div>
                    <div class="button" v-if="image_url" @click="addImage()">
                        Replace File
                    </div>
                </div>
            </div>
           
            <input type="hidden"
                   v-bind:name="field_name"
                   v-model="value" />
            
           
        </div>
    `,
    methods: {
        addImage: function () {
            this.media_modal = wp.media({
                frame: 'select',
                multiple: false,
                editing: true,
            });

            this.media_modal.on('select', function (value) {
                var attachment = this.media_modal.state().get('selection').first().toJSON();

                this.value = attachment.id;
                this.image_url = attachment.url;

            }, this);

            this.media_modal.open();
        },
        removeImage: function () {
            this.value = this.image_url = '';
        }
    },
    watch: {
        value: function (value) {
            this.$emit('wpcfto-get-value', value);
        },
    }
});