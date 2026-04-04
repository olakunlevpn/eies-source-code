<div>

    <div class="stm-lms-addons" v-bind:class="{'loading' : loading}">
        <div class="stm-lms-addon" v-for="(addon, key) in addons_list" v-bind:class="{'active' : addons[key]}">
            <div class="stm-lms-addon__image">
                <img v-bind:src="addon.url"/>
            </div>
            <div class="stm-lms-addon__install">

                <div class="stm-lms-admin-checkbox section_2-enable_courses_filter">
                    <label @click.prevent="enableAddon(key)">
                        <div class="stm-lms-admin-checkbox-wrapper" v-bind:class="{'active' : addons[key]}">

                            <div class="stm-lms-checkbox-switcher"></div>
                            <input type="checkbox" name="enable_courses_filter" id="section_2-enable_courses_filter">
                        </div>

                        <span>{{addon.name}}</span>
                    </label>
                </div>

                <a v-bind:href="addon.settings" target="_blank" v-if="addons[key] && addon.settings">
                    <i class="fa fa-cog"></i>
                </a>

            </div>
        </div>
    </div>
</div>