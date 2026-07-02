<?php
/**
 * Form template.
 *
 * @package woodmart
 *
 * @var Admin $admin        Admin instance.
 */

?>
<div class="wd-add-custom-label">
	<?php
	$admin->get_template(
		'popup',
		array(
			'btn_text'   => '',
			'title_text' => esc_html__( 'Create custom label', 'woodmart' ),
			'content'    => $admin->get_form(),
		)
	);
	?>
</div>
