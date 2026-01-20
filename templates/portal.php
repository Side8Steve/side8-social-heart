<?php
$posts = get_posts(
	array(
		'post_type' => 'post',
		'posts_per_page' => 10,
		'orderby' => 'date',
		'order' => 'DESC',
	)
);
?>
<div class="side8-portal">
	<header class="side8-portal__header">
		<h2><?php esc_html_e( 'Side 8 Social Heart', 'side8-social-heart' ); ?></h2>
		<p><?php esc_html_e( 'Prepare and submit social content for approval.', 'side8-social-heart' ); ?></p>
	</header>
	<section class="side8-portal__panel">
		<h3><?php esc_html_e( 'Create Submission', 'side8-social-heart' ); ?></h3>
		<form class="side8-portal__form" data-side8-form>
			<label>
				<span><?php esc_html_e( 'Select post', 'side8-social-heart' ); ?></span>
				<select name="post_id" required>
					<option value=""><?php esc_html_e( 'Choose a post', 'side8-social-heart' ); ?></option>
					<?php foreach ( $posts as $post_item ) : ?>
						<option value="<?php echo esc_attr( $post_item->ID ); ?>"><?php echo esc_html( $post_item->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label>
				<span><?php esc_html_e( 'Caption', 'side8-social-heart' ); ?></span>
				<textarea name="caption" rows="4" placeholder="<?php esc_attr_e( 'Write a caption...', 'side8-social-heart' ); ?>"></textarea>
			</label>
			<fieldset>
				<legend><?php esc_html_e( 'Channels', 'side8-social-heart' ); ?></legend>
				<label><input type="checkbox" name="channels[]" value="facebook" /> Facebook</label>
				<label><input type="checkbox" name="channels[]" value="instagram" /> Instagram</label>
				<label><input type="checkbox" name="channels[]" value="linkedin" /> LinkedIn</label>
			</fieldset>
			<button type="submit" class="side8-portal__button"><?php esc_html_e( 'Submit for approval', 'side8-social-heart' ); ?></button>
		</form>
		<div class="side8-portal__notice" data-side8-notice aria-live="polite"></div>
	</section>
	<section class="side8-portal__panel">
		<h3><?php esc_html_e( 'Recent Activity', 'side8-social-heart' ); ?></h3>
		<ul class="side8-portal__activity" data-side8-activity>
			<li><?php esc_html_e( 'Loading activity...', 'side8-social-heart' ); ?></li>
		</ul>
	</section>
</div>
