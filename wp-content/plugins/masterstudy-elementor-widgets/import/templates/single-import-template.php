<div class="elementor-template-library-template elementor-template-library-template-remote"
     v-bind:class="['elementor-template-library-template-' + template.type, 'elementor-template-library-template-' + template.subtype]"
     v-for="template in templates">

    <div class="elementor-template-library-template-body">

        <img class="elementor-template-library-template-screenshot" v-bind:src="template.thumbnail"/>

    </div>

    <div class="elementor-template-library-template-footer">

        <a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button" @click="importTemplate(template.id)">
            <i class="eicon-file-download" aria-hidden="true"></i>
            <span class="elementor-button-title"><?php esc_html_e("Insert", 'masterstudy-elementor-widgets'); ?></span>
        </a>

        <div class="elementor-template-library-template-name" v-html="template.title"></div>

    </div>

</div>