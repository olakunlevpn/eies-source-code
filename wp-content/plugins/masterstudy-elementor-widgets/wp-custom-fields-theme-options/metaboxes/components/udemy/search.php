<div>

    <div class="stm_lms_udemy_search" v-bind:class="{'loading' : loading}">
        <input v-model="search_name" type="text" v-on:keyup="search"/>

        <table v-if="typeof courses != 'string'">
            <thead>
                <tr>
                    <th class="course_id"><?php esc_html_e('Course ID', 'masterstudy-elementor-widgets'); ?></th>
                    <th class="course_title"><?php esc_html_e('Title', 'masterstudy-elementor-widgets'); ?></th>
                    <th class="stm_lms_actions"><?php esc_html_e('Actions', 'masterstudy-elementor-widgets'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(course, key) in courses" v-bind:class="{'loading' : course.lms_publishing}">
                    <td class="course_id">
                        <span v-if="course.id">[<label>{{course.id}}</label>]</span>
                    </td>
                    <td class="course_title">
                        <a v-bind:href="'https://www.udemy.com' + course.url" target="_blank">
                            {{course.title}}
                        </a>
                    </td>
                    <td class="stm_lms_actions">

                        <div v-if="!course.imported"
                             class="stm_lms_udemy_import_course"
                             @click="importCourse(course.id, key)"
                             v-bind:class="{'loading' : course.loading}">
                            <i v-if="!course.loading" class="lnr lnr-download"></i>
                            <i v-if="course.loading" class="lnr lnr-sync"></i>
                            <span v-if="!course.loading_text"><?php esc_html_e('Import Course', 'masterstudy-elementor-widgets'); ?></span>
                            <span v-else>{{course.loading_text}}</span>
                        </div>

                        <div v-else class="stm_lms_action__edit">
                            <a v-bind:href="course.stm_lms_url"  target="_blank" class="stm_lms_udemy_import_course imported">
                                <span>{{course.loading_text}}</span>
                            </a>
                            <a v-bind:href="course.stm_lms_url_edit"  target="_blank" class="stm_lms_udemy_import_course edit">
                                <span><?php esc_html_e('Edit', 'masterstudy-elementor-widgets'); ?></span>
                            </a>
                            <div class="stm_lms_udemy_import_course publish" @click="publish(key, course.id)">
                                <span v-if="!course.lms_published"><?php esc_html_e('Publish', 'masterstudy-elementor-widgets'); ?></span>
                                <span v-else>{{course.lms_published}}</span>
                            </div>
                        </div>

                    </td>
                </tr>
            </tbody>
        </table>

        <div v-else>
            <div id="setting-error-tgmpa" class="update-nag settings-error notice">
                <p style="margin-bottom: 0;">{{courses}}</p>
            </div>
        </div>

    </div>

</div>