Vue.component('wpcfto_radio', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    data: function () {
        return {
            value : '',
        }
    },
    template: ` 
        <div class="wpcfto_generic_field" v-bind:class="field_id">
            <div class="stm-lms-admin-select" v-bind:id="field_id">
                <label v-html="field_label"></label>
                <div class="stm-lms-radio">
                    <label v-for="(option, key) in fields['options']">
                        <input type="radio"
                               v-bind:name="field_name"
                                v-model="value"
                               v-bind:value="key"/>
                        {{ option }}
                    </label>
                </div>
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