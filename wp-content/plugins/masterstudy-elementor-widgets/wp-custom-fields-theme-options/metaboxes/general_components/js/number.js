Vue.component('wpcfto_number', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value', 'field_data'],
    data: function () {
        return {
            value: '',
            step : 1
        }
    },
    template: `
        <div class="wpcfto_generic_field wpcfto_generic_field_flex_input wpcfto_generic_field__number">
            <label v-html="field_label"></label>
            <input type="number"
                v-bind:name="field_name"
                v-bind:placeholder="field_data.placeholder"
                v-bind:id="field_id"
                :step="step"
                v-model="value"
            />
        </div>
    `,
    mounted: function () {
        this.value = this.field_value;
        if(typeof this.field_data.step !== 'undefined') this.step = this.field_data.step;
    },
    methods: {},
    watch: {
        value: function (value) {
            this.$emit('wpcfto-get-value', value);
        }
    }
});