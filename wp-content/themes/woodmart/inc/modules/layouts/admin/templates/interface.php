<?php
/**
 * Interface template.
 *
 * @package woodmart
 *
 * @var Admin $admin Admin instance.
 */

use XTS\Modules\Layouts\Admin;

?>

<div class="xts-add-layout">
	<?php
	$admin->get_template(
		'popup',
		array(
			'btn_text'      => '',
			'title_text'    => esc_html__( 'Create layout', 'woodmart' ),
			'content'       => $admin->get_form(),
			'create_layout' => true,
		)
	);
	?>

	<?php $admin->print_condition_template(); ?>
	<?php $admin->print_tabs(); ?>
</div>
