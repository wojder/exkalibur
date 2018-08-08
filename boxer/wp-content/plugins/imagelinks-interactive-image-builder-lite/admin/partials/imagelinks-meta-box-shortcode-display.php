<?php
/**
 * This file is used to markup the meta box aspects of the plugin.
 *
 * @since      1.0.0
 * @package    imagelinks
 * @subpackage imagelinks/admin/partials
 */
?>

<?php 
	$post = get_post();
	$id = $post->ID;
	$slug = $post->post_name;	
?>

<p><?php echo __('To use this imagelinks in your posts or pages use the following shortcode:', 'imagelinks'); ?></p>
<p><code>[imagelinks id="<?php echo $id; ?>"]</code><?php ($slug ? 'or' : '') ?></p>
<?php if ( $slug ) { ?>
<p><code>[imagelinks slug="<?php echo $slug; ?>"]</code></p>
<?php } ?>