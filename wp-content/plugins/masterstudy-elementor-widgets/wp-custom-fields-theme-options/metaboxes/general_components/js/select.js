Vue.component('wpcfto_select', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    data: function () {
        return {
            value : '',
        }
    },
    template: ` 
        <div class="wpcfto_generic_field">
            <div class="stm-lms-admin-select wpcfto_generic_field_flex_input">  
                <label v-html="field_label"></label>
                <select v-bind:name="field_name"
                        v-model="value"
                        v-bind:id="field_id">
                    <option v-for="(option, key) in fields['options']" v-bind:value="key">{{ option }}</option>
                </select>
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