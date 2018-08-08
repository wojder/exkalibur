<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    ImageLinks
 * @subpackage ImageLinks/includes
 */
class ImageLinks_Activator {
	public static function activate() {
		//==============================================
		// update from version 1.3.5 to 1.4.0
		global $wpdb;

		$rows = $wpdb->get_results("SELECT * FROM ". $wpdb->postmeta . " WHERE post_id IN (SELECT ID FROM " . $wpdb->posts . " WHERE post_type = 'imgl_item') AND meta_key = 'imgl-meta-ui-cfg'", OBJECT);
		foreach ($rows as $row) {
			$post_id = $row->post_id;
			$meta_id = $row->meta_id;
			$meta_value = $row->meta_value;
			
			$json = unserialize($meta_value);
			if($json == null) {
				$meta_value = serialize(json_decode(html_entity_decode($meta_value)));
				
				update_post_meta($post_id, 'imgl-meta-ui-cfg', $meta_value);
			}
		}
		
		$rows = $wpdb->get_results("SELECT * FROM ". $wpdb->postmeta . " WHERE post_id IN (SELECT ID FROM " . $wpdb->posts . " WHERE post_type = 'imgl_item') AND meta_key = 'imgl-meta-imagelinks-cfg'", OBJECT);
		foreach ($rows as $row) {
			$post_id = $row->post_id;
			$meta_id = $row->meta_id;
			$meta_value = $row->meta_value;
			
			$json = unserialize($meta_value);
			if($json == null) {
				$meta_value = serialize(json_decode(html_entity_decode($meta_value)));
				
				update_post_meta($post_id, 'imgl-meta-imagelinks-cfg', $meta_value);
			}
		}
		//==============================================
	}
}
