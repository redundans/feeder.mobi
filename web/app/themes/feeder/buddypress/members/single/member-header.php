<?php
/**
 * BuddyPress - Users Header
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<div id="item-header-avatar">
	<a href="<?php bp_displayed_user_link(); ?>">

		<?php bp_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<?php esc_html_e( 'Logged in as', 'feeder' ); ?> <span class="user-nicename"><?php esc_html_e( bp_get_displayed_user_username() ); ?></span>

	<?php bp_nouveau_member_hook( 'before', 'header_meta' ); ?>

	<?php if ( bp_nouveau_member_has_meta() ) : ?>
		<div class="item-meta">

			<?php bp_nouveau_member_meta(); ?>

		</div><!-- #item-meta -->
	<?php endif; ?>

	<?php bp_nouveau_member_header_buttons( array( 'container_classes' => array( 'member-header-actions' ) ) ); ?>
</div><!-- #item-header-content -->
