<script>

	Vue.component('v-select', VueSelect.VueSelect);
	Vue.component('stm-curriculum', {
		props: ['posts', 'stored_ids'],
		data: function () {
			return {
				items: [],
				search: '',
				add_new_lesson: '',
				add_new_section: '',
				add_new_quiz: '',
				add_new_assignment: '',
				options: [],
				loading: true,
				ids: [],
				list: [
					{name: "John"},
					{name: "Joao"},
					{name: "Jean"}
				],
				sectionEmpty: false,
				lessonEmpty: false,
				quizEmpty: false,
				timeout: ''
			}
		},
		template: `
		<div class="stm-curriculum" v-bind:class="{'loading': loading}">

			<div class="stm-curriculum-list list_3" v-if="items.length > 0">
				<draggable v-model="items">
					<div class="stm-curriculum-single" v-for="(item, item_key) in items" :key="item.id"
						 v-bind:class="{'section' : isNaN(item.id)}">
						<div class="stm-curriculum-single-name">
							<input type="text" v-model="items[item_key]['title']"
								   @blur="changeTitle(item.id, items[item_key]['title'], item_key)">
						</div>
						<div class="stm-curriculum-single-actions">
							<a target="_blank" v-if="!isNaN(item.id)"
							   v-bind:href="'<?php echo esc_url( admin_url() ); ?>post.php?post=' + item.id + '&action=edit'"
							   class="fa fa-pen stm_lms_edit_item_action"></a>
							<i class="far fa-trash-alt"
							   @click="confirmDelete(item_key, '<?php esc_html_e( 'Do you really want to delete this item?', 'wp-custom-fields-theme-options' ); ?>')"></i>
						</div>
					</div>
				</draggable>
			</div>

			<div class="stm_lms_curriculum_box">

				<label><?php esc_html_e( 'Search lesson or quiz', 'wp-custom-fields-theme-options' ); ?></label>
				<v-select v-model="search"
						  label="title"
						  :options="options"
						  @search="onSearch">
				</v-select>

				<div class="container">
					<label><?php esc_html_e( 'Add course data', 'wp-custom-fields-theme-options' ); ?></label>

					<div class="row">
						<div class="column">
							<div class="stm-lms-icon_input">
								<input type="text"
									   v-model="add_new_section"
									   id="stm_lms_add_new_section"
									   v-bind:class="{'shake-it' : sectionEmpty}"
									   @keydown.enter.prevent="createSection()"
									   v-bind:placeholder="'<?php esc_html_e( 'Enter new section title', 'wp-custom-fields-theme-options' ); ?>'"/>
								<i class="stm-lms-icon stm-lms-icon-add" @click="createSection()">+</i>
							</div>
						</div>
					</div>

					<div class="row">

						<div class="column column-50">
							<div class="stm-lms-icon_input">
								<input type="text"
									   v-model="add_new_lesson"
									   id="stm_lms_add_new_lesson"
									   v-bind:class="{'shake-it' : lessonEmpty}"
									   @keydown.enter.prevent="callFunction(createItem, 'stm-lessons', 'add_new_lesson')"
									   v-bind:placeholder="'<?php esc_html_e( 'Enter new lesson title', 'wp-custom-fields-theme-options' ); ?>'"/>
								<i class="stm-lms-icon stm-lms-icon-add"
								   @click="callFunction(createItem, 'stm-lessons', 'add_new_lesson')">+</i>
							</div>
						</div>

						<div class="column column-50">
							<div class="stm-lms-icon_input">
								<input type="text"
									   v-model="add_new_quiz"
									   id="stm_lms_add_new_quiz"
									   v-bind:class="{'shake-it' : quizEmpty}"
									   @keydown.enter.prevent="callFunction(createItem, 'stm-quizzes',  'add_new_quiz')"
									   v-bind:placeholder="'<?php esc_html_e( 'Enter new quiz title', 'wp-custom-fields-theme-options' ); ?>'"/>
								<i class="stm-lms-icon stm-lms-icon-add"
								   @click="callFunction(createItem, 'stm-quizzes',  'add_new_quiz')">+</i>
							</div>
						</div>

					</div>

					<?php if ( class_exists( 'STM_LMS_Assignments' ) ) : ?>
						<div class="row">
							<div class="column">
								<div class="stm-lms-icon_input">
									<input type="text"
										   v-model="add_new_assignment"
										   id="stm_lms_add_new_assignment"
										   @keydown.enter.prevent="callFunction(createItem, 'stm-assignments', 'add_new_assignment')"
										   v-bind:placeholder="'<?php esc_html_e( 'Enter new assignment title', 'wp-custom-fields-theme-options' ); ?>'"/>
									<i class="stm-lms-icon stm-lms-icon-add"
									   @click="callFunction(createItem, 'stm-assignments', 'add_new_assignment')">+</i>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</div>

			</div>

			<?php do_action( 'stm_lms_curriculum_box' ); ?>

		</div>
		`,
		created: function () {

			if (this.stored_ids) {

				var url = stm_wpcfto_ajaxurl + '?action=stm_curriculum&nonce=' + stm_wpcfto_nonces['stm_curriculum'] + '&post_types=' + this.posts.join(',') + '&posts_per_page=-1&orderby=post__in&ids=' + encodeURIComponent(this.stored_ids);

				if (typeof stm_lms_manage_course_id !== 'undefined') {
					url += '&course_id=' + stm_lms_manage_course_id;
				}

				this.getPosts(url, 'items');
			} else {
				this.isLoading(false);
			}

			this.updateIds();

		},
		methods: {
			emitMethod(item) {
				STM_LMS_EventBus.$emit('STM_LMS_Curriculum_item', item);
			},
			isLoading(isLoading) {
				this.loading = isLoading;
			},
			onSearch(search) {
				var exclude = this.ids.join(',');
				this.getPosts(stm_wpcfto_ajaxurl + '?action=stm_curriculum&nonce=' + stm_wpcfto_nonces['stm_curriculum'] + '&exclude_ids=' + exclude + '&s=' + search + '&post_types=' + this.posts.join(','), 'options');
			},
			getPosts(url, variable) {
				var vm = this;
				vm.isLoading(true);
				this.$http.get(url).then(function (response) {

					var items_data = [];

					response.body.forEach(function(item){
						items_data.push({
							id : item.id,
							title : decodeEntities(item.title),
							post_type : item.post_type,
						})
					});

					vm[variable] = items_data;

					vm.isLoading(false);
				});
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
			updateIds() {
				var vm = this;
				vm.ids = [];
				this.items.forEach(function (value, key) {
					vm.$set(value, 'title', value.title.replace(/stm_lms_amp/g, "&"));
					vm.ids.push(value.id);
				});
				vm.$emit('get-ids', vm.ids);
			},
			confirmDelete(item_key, message) {
				var r = confirm(message);
				if (!r) return;
				this.items.splice(item_key, 1);
			},
			callFunction(functionName, item, model) {
				functionName(item, model);
			},
			changeSection(item) {
				console.log(item);
			},
			createItem(item, model) {
				var vm = this;

				vm.quizEmpty = vm.lessonEmpty = false;
				clearTimeout(vm.timeout);
				if (this[model] === '') {

					var itemName = (model === 'add_new_quiz') ? 'quizEmpty' : 'lessonEmpty';
					vm[itemName] = true;
					vm.timeout = setTimeout(function () {
						vm[itemName] = false;
					}, 800);

					return false;
				}

				if (this[model] === '') return false;
				vm.isLoading(true);

				var subrequest = '?action=stm_curriculum_create_item&nonce=' +
					stm_wpcfto_nonces['stm_curriculum_create_item'] +
					'&post_type=' + item +
					'&title=' + encodeURIComponent(this[model]);

				var url = stm_wpcfto_ajaxurl + subrequest;
				this.$http.get(url).then(function (response) {
					if (typeof response.body.status === 'undefined') this.items.push(response.body);
					vm[model] = '';
					vm.isLoading(false);
				});
			},
			createSection() {
				var vm = this;

				vm.sectionEmpty = false;
				clearTimeout(vm.timeout);
				if (this.add_new_section === '') {

					vm.sectionEmpty = true;
					vm.timeout = setTimeout(function () {
						vm.sectionEmpty = false;
					}, 800);

					return false;
				}

				this.items.push({
					id: vm.add_new_section,
					title: vm.add_new_section
				});

			},
			changeTitle(post_id, title, itemKey) {
				if (isNaN(post_id)) {
					this.items[itemKey]['id'] = title;
					this.updateIds();
				} else {
					this.$http.get(stm_wpcfto_ajaxurl + '?action=stm_save_title&nonce=' + stm_wpcfto_nonces['stm_save_title'] + '&title=' + title + '&id=' + post_id);
				}
			},
		},
		watch: {
			search: function (value) {
				if (typeof value === 'object' && value !== null && !this.containsObject(value, this.items)) {
					this.items.push(value);
				}
			},
			items: function () {
				this.updateIds();
			}
		}
	});


	var decodeEntities = (function() {
		// this prevents any overhead from creating the object each time
		var element = document.createElement('div');

		function decodeHTMLEntities (str) {
			if(str && typeof str === 'string') {
				// strip script/html tags
				str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
				str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
				element.innerHTML = str;
				str = element.textContent;
				element.textContent = '';
			}

			return str;
		}

		return decodeHTMLEntities;
	})();
</script>
