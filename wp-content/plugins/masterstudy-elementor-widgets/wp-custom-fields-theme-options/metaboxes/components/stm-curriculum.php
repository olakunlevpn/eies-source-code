<div class="stm-curriculum" v-bind:class="{\'loading\': loading}">

    <div class="stm-curriculum-list list_2" v-if="items.length > 0">
        <draggable v-model="items">
            <div class="stm-curriculum-single" v-for="(item, item_key) in items" :key="item.id"
                 v-bind:class="{\'section\' : isNaN(item.id)}">
                <div class="stm-curriculum-single-name">
                    Section
                    <input type="text" v-model="items[item_key][\'title\']"
                           @blur="changeTitle(item.id, items[item_key][\'title\'], item_key)">
                </div>
                <div class="stm-curriculum-single-actions">
                    <a target="_blank" v-if="!isNaN(item.id)"
                       v-bind:href="\'<?php echo esc_url(admin_url()); ?>post.php?post=\' + item.id + \'&action=edit\'"
                       class="fa fa-pen stm_lms_edit_item_action"></a>
                    <i class="far fa-trash-alt"
                       @click="confirmDelete(item_key, \'<?php esc_html_e('Do you really want to delete this item?', 'wp-custom-fields-theme-options') ?>\')"></i>
                </div>
            </div>
        </draggable>
    </div>

    <div class="stm_lms_curriculum_box">

        <label><?php esc_html_e('Search lesson or quiz', 'wp-custom-fields-theme-options'); ?></label>
        <v-select v-model="search"
                  label="title"
                  :options="options"
                  @search="onSearch">
        </v-select>

        <div class="container">
            <label><?php esc_html_e('Add course data', 'wp-custom-fields-theme-options'); ?></label>

            <div class="row">
                <div class="column">
                    <div class="stm-lms-icon_input">
                        <input type="text"
                               v-model="add_new_section"
                               id="stm_lms_add_new_section"
                               v-bind:class="{\'shake-it\' : sectionEmpty}"
                               @keydown.enter.prevent="createSection()"
                               v-bind:placeholder="\'<?php esc_html_e('Enter new section title', 'wp-custom-fields-theme-options'); ?>\'"/>
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
                               v-bind:class="{\'shake-it\' : lessonEmpty}"
                               @keydown.enter.prevent="callFunction(createItem, \'stm-lessons\', \'add_new_lesson\')"
                               v-bind:placeholder="\'<?php esc_html_e('Enter new lesson title', 'wp-custom-fields-theme-options'); ?>\'"/>
                        <i class="stm-lms-icon stm-lms-icon-add"
                           @click="callFunction(createItem, \'stm-lessons\', \'add_new_lesson\')">+</i>
                    </div>
                </div>

                <div class="column column-50">
                    <div class="stm-lms-icon_input">
                        <input type="text"
                               v-model="add_new_quiz"
                               id="stm_lms_add_new_quiz"
                               v-bind:class="{\'shake-it\' : quizEmpty}"
                               @keydown.enter.prevent="callFunction(createItem, \'stm-quizzes\',  \'add_new_quiz\')"
                               v-bind:placeholder="\'<?php esc_html_e('Enter new quiz title', 'wp-custom-fields-theme-options'); ?>\'"/>
                        <i class="stm-lms-icon stm-lms-icon-add"
                           @click="callFunction(createItem, \'stm-quizzes\',  \'add_new_quiz\')">+</i>
                    </div>
                </div>

            </div>

            <?php if (class_exists('STM_LMS_Assignments')): ?>
                <div class="row">
                    <div class="column">
                        <div class="stm-lms-icon_input">
                            <input type="text"
                                   v-model="add_new_assignment"
                                   id="stm_lms_add_new_assignment"
                                   @keydown.enter.prevent="callFunction(createItem, \'stm-assignments\', \'add_new_assignment\')"
                                   v-bind:placeholder="\'<?php esc_html_e('Enter new assignment title', 'wp-custom-fields-theme-options'); ?>\'"/>
                            <i class="stm-lms-icon stm-lms-icon-add"
                               @click="callFunction(createItem, \'stm-assignments\', \'add_new_assignment\')">+</i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <?php do_action('stm_lms_curriculum_box'); ?>
    <div class="test"></div>

</div>