Vue.component('v-select', VueSelect.VueSelect);
Vue.component('wpcfto_autocomplete', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    data: function () {
        return {
            ids: [],
            items: [],
            search: '',
            options: [],
            loading: true,
            value: ''
        }
    },
    template: `
        <div class="wpcfto_generic_field__autocomplete_wrapper">
            
            <label v-html="field_label"></label>
        
            <div class="wpcfto_generic_field wpcfto_generic_field__autocomplete">
               
                <div class="stm-autocomplete-search" v-bind:class="{'loading': loading}">
          
                    <div class="v-select-search">

                        <i class="fa fa-plus"></i>
    
                        <v-select label="title"
                                  v-model="search"
                                  @input="setSelected($event)"
                                  :options="options"
                                  @search="onSearch($event)">
                        </v-select>
    
                        <span class="v-select-search-label">
                            Add {{field_label}}
                        </span>
    
                    </div>
                   
                    <ul class="stm-lms-autocomplete">
                        <li v-for="(item, index) in items" v-if="typeof item !== 'string'">
                            <div class="item-wrapper">
                                <img v-bind:src="item.image" v-if="item.image">
                                <div class="item-data">
                                    <span class="item-label" v-html="field_label"></span>
                                    <span v-html="item.title"></span>
                                </div>
                            </div>
                            <i class="lnr lnr-cross" @click="removeItem(index)"></i>
                        </li>
                    </ul>
                    
                    
                    <input type="hidden"
                           v-bind:name="field_name"
                           v-model="value"/>
                
                </div>
            </div>
            
        </div>
    `,
    created: function () {
        if (this.field_value) {
            this.getPosts(stm_wpcfto_ajaxurl + '?action=stm_curriculum&nonce=' + stm_wpcfto_nonces['stm_curriculum'] + '&posts_per_page=-1&orderby=post__in&ids=' + this.field_value + '&post_types=' + this.fields.post_type.join(','), 'items');
        } else {
            this.isLoading(false);
        }
    },
    methods: {
        isLoading(isLoading) {
            this.loading = isLoading;
        },
        setSelected(value) {

            this.items.push(value);

            /*Reset options*/
            this.$set(this, 'options', []);
            this.$set(this, 'search', '');
        },
        onSearch(search) {
            var _this = this;
            var exclude = _this.ids.join(',');
            var post_types = _this.fields['post_type'].join(',');
            _this.getPosts(
                stm_wpcfto_ajaxurl + '?action=stm_curriculum&nonce=' +
                stm_wpcfto_nonces['stm_curriculum'] +
                '&exclude_ids=' + exclude +
                '&s=' + search +
                '&post_types=' + post_types,
                'options'
            );
        },
        getPosts(url, variable) {
            var vm = this;
            vm.isLoading(true);

            /*Adding field ID to filters then*/

            url += '&name=' + vm.field_name;

            this.$http.get(url).then(function (response) {
                vm[variable] = response.body;
                vm.isLoading(false);
            });

        },
        updateIds() {
            var vm = this;
            vm.ids = [];

            this.items.forEach(function (value, key) {
                vm.ids.push(value.id);
            });
            vm.$set(this, 'value', vm.ids);
            vm.$emit('wpcfto-get-value', vm.ids);
        },
        callFunction(functionName, item, model) {
            functionName(item, model);
        },
        containsObject(obj, list) {
            var i;
            for (i = 0; i < list.length; i++) {
                if (list[i]['id'] === obj['id']) {
                    return true;
                }
            }

            return false;
        },
        removeItem(index) {
            this.items.splice(index, 1);
        }
    },
    watch: {
        items: function () {
            this.updateIds();
        }
    }
})