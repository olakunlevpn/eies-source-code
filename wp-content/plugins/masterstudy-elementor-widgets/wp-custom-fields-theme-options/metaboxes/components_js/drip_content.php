<script>
    Vue.component('v-select', VueSelect.VueSelect);
    Vue.component('stm-autocomplete-drip-content', {
        props: ['posts', 'stored_ids', 'label'],
        data: function () {
            return {
                options: [],
                items: [],
                test: [
                    {
                        "parent": {"id": 2520, "title": "Feature lesson 1", "post_type": "stm-lessons"},
                        "childs": [{"id": 2450, "title": "asdasdasd", "post_type": "stm-lessons"}, {
                            "id": 2448,
                            "title": "adasd asda sdas asd",
                            "post_type": "stm-quizzes"
                        }]
                    },
                    {
                        "parent": {"id": 2303, "title": "Stream", "post_type": "stm-lessons"},
                        "childs": [{"id": 2448, "title": "adasd asda sdas asd", "post_type": "stm-quizzes"}, {
                            "id": 2101,
                            "title": "TEacher lesson",
                            "post_type": "stm-lessons"
                        }, {"id": 2303, "title": "Stream", "post_type": "stm-lessons"}]
                    }
                ]
            }
        },
        created: function () {
            this.isLoading(false);
            if (this.stored_ids) {
                this.items = JSON.parse(this.stored_ids);
            }
        },
        methods: {
            isLoading(isLoading) {
                this.loading = isLoading;
            },
            onSearch($event, exclude_items) {
                var _this = this;
                var post_types = _this.posts.join(',');
                console.log(exclude_items);
                var exclude = exclude_items.map(a => a.id).join(',');
                var url = stm_wpcfto_ajaxurl + '?action=stm_curriculum&nonce=' + stm_wpcfto_nonces['stm_curriculum'] + '&s=' + $event + '&post_types=' + post_types;
                if(exclude) url += '&exclude_ids=' + exclude;
                _this.$http.get(url).then(function (response) {
                    _this.$set(_this, 'options', response.body);
                });
            },
            setSelected(value, item, key) {
                this.$set(item, key, value);

                /*Reset options*/
                this.$set(this, 'options', []);
                this.$set(item, 'search', '');
            },
            addNewParent() {
                this.items.push({
                    parent: {},
                    childs: []
                });
            },
            setValue(value) {

            }
        },
        watch: {
            items: {
                deep: true,
                handler: function () {
                    this.$emit('autocomplete-ids', JSON.stringify(this.items));
                }
            }
        }
    })
</script>