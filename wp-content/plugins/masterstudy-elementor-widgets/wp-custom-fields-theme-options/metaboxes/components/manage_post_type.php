<div>
    <div class="stm_lms_manage_post_type" v-bind:class="{'loading' : loading}">


        <div class="stm_lms_manage_post_type__filter">
            <div class="pagination" v-if="total > 1">
                <a href="#"
                   v-for="page in pages"
                   v-on:click.prevent="switchPage(page)"
                   v-bind:class="{'active' : page == current_page}">{{page}}</a>
            </div>

            <div class="stm_lms_manage_post_type__btn update_all" @click="updating_all = !updating_all; updateAll();" v-bind:class="{'loading' : updating_all}">
                <i v-if="updating_all" class="lnr lnr-sync"></i>
                <span v-if="!updating_all"><?php esc_html_e('Update All', 'wp-custom-fields-theme-options'); ?></span>
                <span v-else><?php esc_html_e('Pause update', 'wp-custom-fields-theme-options'); ?></span>
            </div>

            <div class="filter">
                <select v-model="filter" v-on:change="switchStatus()">
                    <option value=""><?php esc_html_e('All', 'masterstudy-elementor-widgets'); ?></option>
                    <option value="publish"><?php esc_html_e('Published', 'masterstudy-elementor-widgets'); ?></option>
                    <option value="draft"><?php esc_html_e('Draft', 'masterstudy-elementor-widgets'); ?></option>
                    <option value="trash"><?php esc_html_e('Trash', 'masterstudy-elementor-widgets'); ?></option>
                </select>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th><?php esc_html_e('Course', 'masterstudy-elementor-widgets'); ?></th>
                <th class="stm_lms_manage_post_type__actions"><?php esc_html_e('Manage actions', 'masterstudy-elementor-widgets'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(post, index) in posts" v-bind:class="{'loading' : post.loading}">
                <td class="course_title">
                    <a v-bind:href="post.url" v-html="post.title" target="_blank"></a>
                </td>
                <td class="stm_lms_manage_post_type__actions">

                    <div class="stm_lms_actions">

                        <div class="stm_lms_udemy_import_course"
                             @click="updateCourse(post)"
                             v-bind:class="{'loading' : post.loading}"
                             :ref="'importCourse' + post.id">
                            <i v-if="post.loading" class="lnr lnr-sync"></i>
                            <span v-if="!post.loading_text"><?php esc_html_e('Update Course', 'masterstudy-elementor-widgets'); ?></span>
                            <span v-else>{{post.loading_text}}</span>
                        </div>

                        <a v-bind:href="post.edit_link" target="_blank" class="stm_lms_manage_post_type__btn edit">
                            <?php esc_html_e('Edit', 'masterstudy-elementor-widgets'); ?>
                        </a>

                        <div v-if="post.status != 'publish'"
                             @click.prevent="changeStatus(index, post.id, 'publish')"
                             class="stm_lms_manage_post_type__btn publish">
                            <?php esc_html_e('Publish', 'masterstudy-elementor-widgets'); ?>
                        </div>

                        <div v-else class="stm_lms_manage_post_type__actions_set">
                            <div class="stm_lms_manage_post_type__btn draft"
                                 @click.prevent="changeStatus(index, post.id, 'draft')">
                                <?php esc_html_e('Draft', 'masterstudy-elementor-widgets'); ?>
                            </div>
                            <div class="stm_lms_manage_post_type__btn trash"
                                 @click.prevent="changeStatus(index, post.id, 'trash')">
                                <?php esc_html_e('Trash', 'masterstudy-elementor-widgets'); ?>
                            </div>
                        </div>

                    </div>

                </td>
            </tr>
            </tbody>
        </table>

        <div class="stm_lms_manage_post_type__filter">
            <div class="pagination" v-if="total > 1">
                <a href="#"
                   v-for="page in pages"
                   v-on:click.prevent="switchPage(page)"
                   v-bind:class="{'active' : page == current_page}">{{page}}</a>
            </div>

            <div class="filter">
                <select v-model="filter" v-on:change="switchStatus()">
                    <option value=""><?php esc_html_e('All', 'masterstudy-elementor-widgets'); ?></option>
                    <option value="publish"><?php esc_html_e('Published', 'masterstudy-elementor-widgets'); ?></option>
                    <option value="draft"><?php esc_html_e('Draft', 'masterstudy-elementor-widgets'); ?></option>
                    <option value="trash"><?php esc_html_e('Trash', 'masterstudy-elementor-widgets'); ?></option>
                </select>
            </div>
        </div>


    </div>
</div>