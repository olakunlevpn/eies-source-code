Vue.component('wpcfto_checkbox', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    data: function () {
        return {
            value : '',
        }
    },
    template: `
        <div class="wpcfto_generic_field">
            <div class="stm-lms-admin-checkbox" v-bind:class="field_id">
               
               <label>
               
                    <div class="stm-lms-admin-checkbox-wrapper" v-bind:class="{'active' : value}">
                        <div class="stm-lms-checkbox-switcher"></div>
                        <input type="checkbox"
                               :name="field_name"
                               v-bind:id="field_id"
                               v-model="value"/>
                    </div>
                    
                    <span v-html="field_label"></span>
                
                </label>
                
            </div>
        </div>
    `,
    mounted: function () {
        this.value = this.field_value;

    },
    methods: {},
    watch: {
        value: function (value) {
            this.$emit('wpcfto-get-value', value);
        }
    }
});