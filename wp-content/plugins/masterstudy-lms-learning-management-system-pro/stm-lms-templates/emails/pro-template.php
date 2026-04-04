<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName.
/**
 * Email template
 *
 * @var $subject
 * @var $message
 */

?>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<!-- Facebook sharing information tags -->
<meta property="og:title" content="Masterstudy LMS Email Template">

<title>Masterstudy LMS Email Template</title>
<center style="margin: 0;padding: 0;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; font-style: normal;font-weight: 500;font-size: 15px;line-height: 26px;color: #808C98;background-color: <?php echo ( ! empty( $email_manager['stm_lms_email_template_branding'] ) ) ? esc_html( $outside_bg ) : 'white'; ?>;">
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable" style="margin: 0;padding: 0;height: 100% !important;width: 100% !important;">
		<tbody>
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" id="templateContainer" style="border: 1px solid <?php echo ( ! empty( $email_manager['stm_lms_email_template_branding'] ) ) ? '#DDDDDD' : 'white'; ?>; margin-top: 40px;background-color: #FFFFFF;">
						<tbody>
						<?php
						if ( ! empty( $subject ) ) {
							?>
							<tr class="columnOneContent courseTitle">
								<td>
									<h2 style="color: black;text-align: center;margin-bottom: 30px; max-width: 680px; padding: 0 10px; "><?php echo esc_html( $subject ); ?></h2>
								</td>
							</tr>
							<?php
						}
						?>
						<tr class="columnOneContent courseBody">
							<td>
								<div class="courseContentBody" style="max-width: 460px;margin: 0 auto;text-align: center;margin-bottom: 30px !important;">
								<?php echo $message; // phpcs:ignore?>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
					<!-- // End Template Body \\ -->
				</td>
			</tr>
		</tbody>
	</table>
	<br>
</center>
