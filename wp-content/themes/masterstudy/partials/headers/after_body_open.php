<?php do_action( 'masterstudy_body_start' ); ?>

<!-- Searchform -->
<?php
get_template_part( 'partials/searchform' );
$isPreloaderActive = stm_option( 'preloader' );
if ( $isPreloaderActive ) {

	?>
	<div class="ms_lms_loader_bg">
		<div class="ms_lms_loader"></div>
	</div>
	<?php

}
?>
<div id="wrapper">

	<?php do_action( 'masterstudy_before_header' ); ?>
