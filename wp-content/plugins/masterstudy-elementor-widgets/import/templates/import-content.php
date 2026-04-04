<div class="dialog-message dialog-lightbox-message">
    <div class="dialog-content dialog-lightbox-content">
        <div id="elementor-template-library-templates">

            <div id="elementor-template-library-templates-container" v-if="!loading">

                <?php STM_Elementor_Template_Library::includeTemplate('single-import-template'); ?>

            </div>


        </div>
    </div>

    <?php STM_Elementor_Template_Library::includeTemplate('import-loader'); ?>

</div>