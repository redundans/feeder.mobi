<?php
/**
 * BuddyPress - Members Home
 *
 * @since   1.0.0
 * @version 3.0.0
 *
 * @package Feeder.
 */

?>

	<?php bp_nouveau_member_hook( 'before', 'home_content' ); ?>

	<div id="item-header" role="complementary" data-bp-item-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>" data-bp-item-component="members" class="users-header single-headers">

		<?php bp_nouveau_member_header_template_part(); ?>

	</div><!-- #item-header -->

	<div class="bp-wrap">

		<div id="item-body" class="item-body">

			<?php bp_nouveau_member_template_part(); ?>

		</div><!-- #item-body -->
	</div><!-- // .bp-wrap -->

	<?php bp_nouveau_member_hook( 'after', 'home_content' ); ?>
