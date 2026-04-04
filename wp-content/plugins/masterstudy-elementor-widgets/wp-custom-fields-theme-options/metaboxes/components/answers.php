<?php stm_lms_register_style('admin/questions'); ?>

<div>

    <!--Wrapper-->
    <div class="stm_lms_questions_v2_wrapper">
        <div class="stm_lms_curriculum_v2 stm_lms_questions_v2">
            <div class="stm-lms-questions">

                <!--Simple Question-->
                <transition name="slide-fade">
                    <div class="stm-lms-questions-single stm-lms-questions-single_choice"
                         v-if="choice == 'single_choice' && questions.length">

                        <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">

                            <div class="stm-lms-questions-single_input">
                                <input type="text"
                                       v-model="questions[k]['text']"
                                       placeholder="<?php esc_html_e('Enter answer', 'masterstudy-elementor-widgets'); ?>"
                                       v-bind:size="inputSize(questions[k]['text'].length)"/>
                            </div>

                            <div class="actions">

                                <div class="actions_single actions_single_info"
                                     v-bind:class="infoClass(questions[k]['explain'])">
                            <span>
                                <i class="fa fa-info"></i> +
                            </span>
                                    <div class="actions_single_info_popup">
                                        <span><?php esc_html_e('Question Explanation', 'masterstudy-elementor-widgets'); ?></span>
                                        <textarea v-model="questions[k]['explain']"
                                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'wp-custom-fields-theme-options') ?>">
                            </textarea>
                                    </div>
                                </div>

                                <div class="actions_single actions_single_delete">
                                    <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                                </div>

                                <div class="actions_single actions_single_answer">
                                    <label class="stm_lms_radio" v-bind:class="{'active' : v.isTrue}">
                                        <span><?php esc_html_e('Correct', 'masterstudy-elementor-widgets'); ?></span>
                                        <input type="radio"
                                               v-bind:name="choice + '_' + origin"
                                               v-model="correctAnswer"
                                               v-bind:value="v.text"
                                               @change="isAnswer()"/>
                                        <i></i>
                                    </label>
                                </div>

                            </div>

                        </div>

                    </div>
                </transition>

                <!--Multi Answer Question-->
                <transition name="slide-fade">
                    <div class="stm-lms-questions-single stm-lms-questions-multi_choice"
                         v-if="choice == 'multi_choice' && questions.length">

                        <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">


                            <div class="stm-lms-questions-single_input">
                                <input type="text"
                                       v-model="questions[k]['text']"
                                       placeholder="<?php esc_html_e('Enter answer', 'masterstudy-elementor-widgets'); ?>"
                                       v-bind:size="inputSize(questions[k]['text'].length)"/>
                            </div>

                            <div class="actions">

                                <div class="actions_single actions_single_info"
                                     v-bind:class="infoClass(questions[k]['explain'])">
                            <span>
                                <i class="fa fa-info"></i> +
                            </span>
                                    <div class="actions_single_info_popup">
                                        <span><?php esc_html_e('Question Explanation', 'masterstudy-elementor-widgets'); ?></span>
                                        <textarea v-model="questions[k]['explain']"
                                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'wp-custom-fields-theme-options') ?>">
                            </textarea>
                                    </div>
                                </div>

                                <div class="actions_single actions_single_delete">
                                    <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                                </div>

                                <div class="actions_single actions_single_answer">
                                    <label class="stm_lms_checkbox" v-bind:class="{'active' : v.isTrue}">
                                        <span><?php esc_html_e('Correct', 'masterstudy-elementor-widgets'); ?></span>
                                        <input type="checkbox" v-bind:name="choice" v-model="correctAnswers[v.text]"
                                               v-bind:value="v.text" @change="isAnswers()"/>
                                        <i class="fa fa-check"></i>
                                    </label>
                                </div>

                            </div>


                        </div>

                    </div>
                </transition>

                <!--True False Question-->
                <transition name="slide-fade">

                    <div class="stm-lms-questions-single stm-lms-questions-true_false"
                         v-if="choice == 'true_false' && questions.length">

                        <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">

                            <label class="stm_lms_radio" v-bind:class="{'active' : v.isTrue}">
                                <input type="radio"
                                       v-model="correctAnswer"
                                       v-bind:value="v.text"
                                       @change="isAnswer()"/>
                                <i></i>
                                <span>{{ v.text }}</span>
                            </label>
                        </div>

                    </div>
                </transition>

                <!--Item Match Question-->
                <transition name="slide-fade">

                    <div class="stm-lms-questions-single stm-lms-questions-item_match"
                         v-if="choice == 'item_match' && questions.length">

                        <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">


                            <div class="stm-lms-questions-single_input">

                                <div class="row"
                                     v-bind:class="{'has-question' : typeof questions[k]['question'] !== 'undefined' && questions[k]['question'].length, 'has-answer' : typeof questions[k]['text'] !== 'undefined' && questions[k]['text'].length}">
                                    <div class="column column-match">
                                        <div class="border">
                                            <h6><?php esc_html_e('Question', 'wp-custom-fields-theme-options'); ?></h6>
                                            <input type="text"
                                                   placeholder="<?php esc_html_e('Add question...', 'masterstudy-elementor-widgets'); ?>"
                                                   v-model="questions[k]['question']"/>
                                        </div>
                                    </div>
                                    <div class="column column-answer">
                                        <div class="border">
                                            <h6><?php esc_html_e('Match', 'wp-custom-fields-theme-options'); ?></h6>
                                            <input type="text"
                                                   placeholder="<?php esc_html_e('Add answer...', 'masterstudy-elementor-widgets'); ?>"
                                                   v-model="questions[k]['text']"/>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="actions">

                                <div class="actions_single actions_single_info"
                                     v-bind:class="infoClass(questions[k]['explain'])">
                            <span>
                                <i class="fa fa-info"></i> +
                            </span>
                                    <div class="actions_single_info_popup">
                                        <span><?php esc_html_e('Question Explanation', 'masterstudy-elementor-widgets'); ?></span>
                                        <textarea v-model="questions[k]['explain']"
                                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'wp-custom-fields-theme-options') ?>">
                            </textarea>
                                    </div>
                                </div>

                                <div class="actions_single actions_single_delete">
                                    <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                                </div>

                            </div>


                        </div>

                    </div>
                </transition>

                <!--Keywords Question-->
                <transition name="slide-fade">
                    <div class="stm-lms-questions-single stm-lms-questions-keywords"
                         v-if="choice == 'keywords' && questions.length">

                        <div class="stm-lms-questions-single_answer stm-lms-questions-single_keyword"
                             v-for="(v,k) in questions">

                            <div class="stm-lms-questions-single_input">
                                <input type="text"
                                       v-model="questions[k]['text']"
                                       placeholder="<?php esc_html_e('Enter answer', 'masterstudy-elementor-widgets'); ?>"
                                       v-bind:size="inputSize(questions[k]['text'].length)"/>
                            </div>

                            <div class="actions">

                                <div class="actions_single actions_single_info"
                                     v-bind:class="infoClass(questions[k]['explain'])">
                            <span>
                                <i class="fa fa-info"></i> +
                            </span>
                                    <div class="actions_single_info_popup">
                                        <span><?php esc_html_e('Question Explanation', 'masterstudy-elementor-widgets'); ?></span>
                                        <textarea v-model="questions[k]['explain']"
                                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'wp-custom-fields-theme-options') ?>">
                            </textarea>
                                    </div>
                                </div>

                                <div class="actions_single actions_single_delete">
                                    <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                                </div>

                            </div>

                        </div>

                    </div>
                </transition>

                <!--Fill the Gap Question-->
                <transition name="slide-fade">
                    <div class="stm-lms-questions-single stm-lms-questions-fill_the_gap"
                         v-if="choice == 'fill_the_gap' && questions.length">

                        <div class="stm-lms-questions-single_fill_the_gap">
                    <textarea v-model="questions[0]['text']"
                              placeholder="<?php esc_html_e('Enter text, separate answers with "|" symbol', 'wp-custom-fields-theme-options') ?>">

                    </textarea>

                            <h4>
                                <i class="fa fa-info"></i>
                                <strong>Example:</strong>
                                Deborah was angry at her son. Her son didn't <strong>|listen|</strong> to her.
                                Her son was 16 years old. Her son <strong>|thought|</strong> he knew everything.
                                Her son <strong>|yelled|</strong> at Deborah.
                            </h4>
                        </div>

                    </div>
                </transition>

                <?php if (get_post_type() !== 'stm-questions'): ?>
                    <!--Question Bank-->
                    <transition name="slide-fade">
                        <div class="stm-lms-questions-single stm-lms-questions-question_bank"
                             v-if="choice == 'question_bank'">


                            <div class="bank_data" v-if="typeof questions[0] !== 'undefined'">

                                <div class="bank_data__single">
                                    <span><?php esc_html_e('Questions:', 'masterstudy-elementor-widgets'); ?></span>
                                    <strong class="bank_category__single">
                                        {{questions[0]['number']}}
                                    </strong>
                                </div>

                                <div class="bank_data__single">
                                    <span><?php esc_html_e('Categories:', 'masterstudy-elementor-widgets'); ?></span>
                                    <strong class="bank_category__single"
                                            v-for="category in questions[0]['categories']">
                                        {{category.name}}
                                    </strong>
                                </div>

                            </div>

                        </div>
                    </transition>

                <?php endif; ?>

                <div class="stm_lms_answers_container"
                     v-if="choice === 'single_choice' || choice === 'multi_choice' || choice === 'item_match' || choice === 'keywords' || (choice === 'fill_the_gap' && questions.length < 1)">
                    <div class="stm_lms_answers_container__input">
                        <input type="text"
                               v-model="new_answer"
                               v-bind:class="{'shake-it' : isEmpty}"
                               @keydown.enter.prevent="addAnswer()"
                               placeholder="<?php esc_html_e('Enter new Answer', 'wp-custom-fields-theme-options'); ?>"/>

                        <div class="stm_lms_answers_container__submit">
                            <a class="button" @click="addAnswer()">
                                <?php STM_Metaboxes::print_svg('assets/svg/enter.svg'); ?>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>