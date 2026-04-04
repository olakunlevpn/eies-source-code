Vue.component('v-select', VueSelect.VueSelect);
Vue.component('stm-user-search', {
    data: function () {
        return {
            options: [],
            timeOut: null,
        }
    },
    mounted: function () {
    },
    methods: {
        onSearch(search, loading) {
            var vm = this;
            clearTimeout(this.timeOut);
            this.timeOut = setTimeout(function () {
                loading(true);
                vm.search(loading, search, vm);
            }, 250);
        },
        search: (loading, search, vm) => {
            vm.$http.get(stm_payout_url_data['url'] + '/stm-lms-user/search', {params: {search: search}}).then(function (response) {
                loading(false);
                vm.options = response.body
            });
        },
    },
    props: {
        user: {
            default: {
                id: null,
                name: '',
                email: '',
            }
        },
    },
    watch: {
        user: {
            handler(val) {
                if (this.user != null)
                    this.$emit('stm-user-search', this.user)
            },
            deep: true
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    new Vue({
        el: '.stm-user-search-app',
        data: {
            user: null
        },
        methods: {
            selectUser: function (user) {
                this.user = user;
            }
        }
    })
});

