<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    imagelinks
 * @subpackage imagelinks/public
 */
class ImageLinks_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 */
	private $version;


	/**
	 * The post type of this plugin.
	 *
	 * @since 1.0.0
	 */
	private $post_type;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param      string    $plugin_name  The name of the plugin.
	 * @param      string    $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $post_type ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->post_type = $post_type;
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$plugin_url = plugin_dir_url( dirname(__FILE__) );
		
		wp_enqueue_style( $this->plugin_name . '_imagelinks',    $plugin_url . 'lib/imagelinks.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_imagelinks_wp', $plugin_url . 'lib/imagelinks.wp.css', array(), $this->version, 'all' );
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.3.0
	 */
	public function register_scripts() {
		wp_register_script($this->plugin_name . '-imagelinks', plugin_dir_url( dirname(__FILE__) ) . 'lib/jquery.imagelinks.min.js', array( 'jquery' ), $this->version, true);
	}
	
	/**
	 * Print the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.3.0
	 */
	public function print_scripts() {
		global $imagelink_plugin_shortcode_used;

		if ( ! $imagelink_plugin_shortcode_used )
			return;

		wp_print_scripts($this->plugin_name . '-imagelinks');
	}


	/**
	 * Init the edit screen of the plugin post type item
	 *
	 * @since 1.0.0
	 */
	public function public_init() {
		add_shortcode( $this->plugin_name, array( $this , 'shortcode') );
	}
	
	/**
	 * Helper function
	 *
	 * @since 1.3.5
	 */
	private function isLocal($name, $json, $jsonGlobal) {
		if( $jsonGlobal && property_exists($json, 'global') && $json->global->$name ) {
			return false;
		}
		return true;
	}
	
	/**
	 * Helper function
	 *
	 * @since 1.3.5
	 */
	private function getValue($name, $json, $jsonGlobal, $type = 's', $echo = true, $onlyLocal = false) {
		$value = null;
		
		if( !$onlyLocal && $jsonGlobal && property_exists($json, 'global') && $json->global->$name ) {
			if( property_exists($jsonGlobal, $name) ) {
				$value = $jsonGlobal->$name;
			} else {
				return;
			}
		} else if ( property_exists($json, $name) ) {
			$value = $json->$name;
		} else {
			return;
		}
		
		if(!$echo) {
			return $value;
		}
		
		if($type == 's' && !is_null($value)) {
			echo $name . ': "' . $value . '",' . PHP_EOL;
		}
		
		if($type == 'n' && !is_null($value) ) {
			echo $name . ': ' . $value . ',' . PHP_EOL;
		}
		
		if($type == 'b') {
			echo $name . ': ' . ($value ? 'true' : 'false'). ',' . PHP_EOL;
		}
	}
	
	/**
	 * Shortcode output for the plugin
	 *
	 * @since 1.0.0
	 */
	public function shortcode( $atts ) {
		extract(
			shortcode_atts(
				array(
					'id'     => 0,
					'slug'   => '',
					'url'    => NULL,
					'alt'    => NULL,
					'width'  => NULL,
					'height' => NULL,
					'class'  => NULL
				), $atts
			)
		);
		
		if ( !$id && !$slug ) {
			return __('Invalid imagelinks shortcode attributes', $this->plugin_name);
		}
		
		if ( !$id ) {
			$obj = get_page_by_path( $slug, OBJECT, $this->post_type );
			if ( $obj ) {
				$id = $obj->ID;
			} else {
				return __('Invalid imagelinks slug attribute', $this->plugin_name);
			}
		}
		?>
<?php // common settings
		// global was set during the rendering of the page
		global $imagelink_plugin_shortcode_used;
		$imagelink_plugin_shortcode_used = true;
		
		$upload_dir = wp_upload_dir();
		$baseurl = $upload_dir['baseurl'];
		
		$json = unserialize(get_post_meta( $id, 'imgl-meta-imagelinks-cfg', true ));
		$jsonGlobal = false;
		
		
		$imageUrl    = ($json->imageUrl && $json->imageUrlLocal ? $baseurl . $json->imageUrl : $json->imageUrl);
		$imageSize   = $this->getValue('imageSize', $json, $jsonGlobal, 's', false);
		$imageWidth  = $this->getValue('imageWidth', $json, $jsonGlobal, 's', false);
		$imageHeight = $this->getValue('imageHeight', $json, $jsonGlobal, 's', false);
		
		$imageWidthStyle  = ($width  != NULL ? 'width:'  . $width  . ';' : (($imageSize == 'fixed' && $imageWidth  > 0) ? 'width:'  . $imageWidth  . ';' : NULL));
		$imageHeightStyle = ($height != NULL ? 'height:' . $height . ';' : (($imageSize == 'fixed' && $imageHeight > 0) ? 'height:' . $imageHeight . ';' : NULL));
		
		// generate unique prefix for $id to avoid clashes with multiple same shortcode use
		$prefix  = strtolower(wp_generate_password( 5, false ));
		$id_data = 'imgl-data-' . $prefix . '-' . $id;
		$func    = 'imgl_init_' . $prefix . '_' . $id;
		$id      = 'imgl-' . $prefix . '-' . $id;
		
		// turn on buffering 
		ob_start();
		?>
<?php
	echo '<!-- imagelinks begin -->' . PHP_EOL;
?>
<?php // theme and effects CSS styles
	echo '<style>' . PHP_EOL;
	
	$value = $this->getValue('theme', $json, $jsonGlobal, 's', false);
	
	if(preg_match('/^imgl-theme-(.*)?/', $value, $matches)) {
		$theme = $matches[1];
		echo '@import url("' . plugin_dir_url( dirname(__FILE__) ) . 'lib/imagelinks.theme.' . $theme . '.css?ver=' . $this->plugin_name . '");' . PHP_EOL;
	} else {
		$json->theme = 'imgl-theme-default';
		echo '@import url("' . plugin_dir_url( dirname(__FILE__) ) . 'lib/imagelinks.theme.default.css?ver=' . $this->plugin_name . '");' . PHP_EOL;
	}
	
	if($this->getValue('popoverShowClass', $json, $jsonGlobal, 's', false) || $this->getValue('popoverHideClass', $json, $jsonGlobal, 's', false)) {
		echo '@import url("' . plugin_dir_url( dirname(__FILE__) ) . 'lib/effect.css?ver=' . $this->plugin_name .  '");' . PHP_EOL;
	}

	echo '</style>' . PHP_EOL;
?>
<?php // custom CSS styles
	if( $this->getValue('customCSS', $json, $jsonGlobal, 'b', false) ) { 
		echo '<style>' . PHP_EOL;
		echo $this->getValue('customCSSContent', $json, $jsonGlobal, 's', false, $this->isLocal('customCSS', $json, $jsonGlobal)). PHP_EOL;
		echo '</style>' . PHP_EOL;
	}
?>
<?php // HTML data for scene titles and popover content
	echo '<div id="' . $id_data . '" style="display:none;">' . PHP_EOL;
	
	$index = 0;
	foreach($json->hotSpots as $hotspot) {
		if( property_exists($hotspot, 'popoverContent') ) {
			echo '<div id="imgl-data-' . $prefix . '-popover-' . $index . '">';
			echo do_shortcode($hotspot->popoverContent);
			echo '</div>' . PHP_EOL;
		}
		$index = $index + 1;
	}
	
	echo '</div>' . PHP_EOL;
?>
<?php // HTML markup
	echo ($class != NULL ? '<div class=\'' . $class . '\'/>' : '');
	
	if( $imageWidthStyle || $imageHeightStyle ) {
		echo '<img id="' . $id . '" src="' . $imageUrl . '" data-imgl-src="' . $imageUrl . '" alt="' . $alt . '" style="' . $imageWidthStyle . $imageHeightStyle . '">';
	} else {
		echo '<img id="' . $id . '" src="' . $imageUrl . '" data-imgl-src="' . $imageUrl . '" alt="' . $alt . '">';
	}
	
	echo ($class != NULL ? '</div>' : '');
?>
<?php // JavaScript code
	echo '<script type="text/javascript">' . PHP_EOL;
	echo 'function ' . $func . '() {'. PHP_EOL;
		echo 'jQuery( "#' . $id . '" ).imagelinks({' . PHP_EOL;
			$this->getValue('theme', $json, $jsonGlobal, 's');
			$this->getValue('popover', $json, $jsonGlobal, 'b');
			if($this->getValue('popoverTemplate', $json, $jsonGlobal, 's', false)) {
				$content = $json->popoverTemplate;
				$content = addslashes($content);
				$content = str_replace(array("\n", "\t", "\r"),'', $content);
				echo 'popoverTemplate: "' . $content . '",' . PHP_EOL;
			}
			$this->getValue('popoverPlacement', $json, $jsonGlobal, 's');
			$this->getValue('popoverShowTrigger', $json, $jsonGlobal, 's');
			$this->getValue('popoverHideTrigger', $json, $jsonGlobal, 's');
			$this->getValue('popoverShowClass', $json, $jsonGlobal, 's');
			$this->getValue('popoverHideClass', $json, $jsonGlobal, 's');
			$this->getValue('hotSpotBelowPopover', $json, $jsonGlobal, 'b');
			$this->getValue('mobile', $json, $jsonGlobal, 'b');
			if($this->getValue('customJS', $json, $jsonGlobal, 'b', false)) {
				echo 'onLoad: function() {' . PHP_EOL;
				echo $this->getValue('customJSContent', $json, $jsonGlobal, 's', false, $this->isLocal('customJS', $json, $jsonGlobal)) . PHP_EOL;
				echo '},' . PHP_EOL;
			}
			if($this->getValue('hotSpots', $json, false, 'b', false)) {
				echo 'hotSpots: [' . PHP_EOL;
				
				$index = 0;
				foreach($json->hotSpots as $hotspot) {
					echo '{' . PHP_EOL;
						$this->getValue('x', $hotspot, false, 'n');
						$this->getValue('y', $hotspot, false, 'n');
						$this->getValue('className', $hotspot, false, 's');
						$this->getValue('content', $hotspot, false, 's');
						$this->getValue('link', $hotspot, false, 's');
						$this->getValue('linkNewWindow', $hotspot, false, 'b');
						$this->getValue('imageUrl', $hotspot, false, 's');
						$this->getValue('imageWidth', $hotspot, false, 'n');
						$this->getValue('imageHeight', $hotspot, false, 'n');
						$this->getValue('popover', $hotspot, false, 'b');
						$this->getValue('popoverHtml', $hotspot, false, 'b');
						echo 'popoverLazyload: false,' . PHP_EOL;
						echo 'popoverSelector: "#imgl-data-' . $prefix . '-popover-' . $index . '",' . PHP_EOL;
						$this->getValue('popoverShow', $hotspot, false, 'b');
						$this->getValue('popoverPlacement', $hotspot, false, 's');
						$this->getValue('popoverWidth', $hotspot, false, 'n');
					echo '},' . PHP_EOL;
					
					$index = $index + 1;
				}
				
				echo ']' . PHP_EOL;
			}
		echo '});' . PHP_EOL;
		
		
		//[lite]
		echo 'function imgl_setInfoLabel' . $prefix . '() {' . PHP_EOL;
		echo 'var $el = jQuery( "#' . $id . '").closest(".imgl");' . PHP_EOL;
		echo 'if($el.length) {' . PHP_EOL;
		echo 'var link = document.createElement("a");' . PHP_EOL;
		echo 'link.setAttribute("href", "https://wordpress.org/plugins/imagelinks-interactive-image-builder-lite/");' . PHP_EOL;
		echo 'link.setAttribute("target", "_blank");' . PHP_EOL;
		echo 'link.setAttribute("rel", "nofollow");' . PHP_EOL;
		echo 'link.className = "imgl-btn-info";' . PHP_EOL;
		echo '$el.append(link);' . PHP_EOL;
		echo 'return true;' . PHP_EOL;
		echo '}' . PHP_EOL;
		echo 'return false' . PHP_EOL;
		echo '};' . PHP_EOL;
		echo 'var timerId' . $prefix . ' = setInterval(function(){' . PHP_EOL;
		echo 'if(imgl_setInfoLabel' . $prefix . '()) {' . PHP_EOL;
		echo 'clearInterval( timerId' . $prefix . ' );' . PHP_EOL;
		echo '}' . PHP_EOL;
		echo '}, 3000);' . PHP_EOL;
		
	echo '}' . PHP_EOL;
	echo 'if(window.attachEvent) {window.attachEvent("onload",' . $func . ')}' . PHP_EOL;
	echo 'else if(window.addEventListener) {window.addEventListener("load",' . $func . ', false)}' . PHP_EOL;
	echo '</script>' . PHP_EOL;
?>
<?php
	echo '<!-- imagelinks end -->' . PHP_EOL;
?>
<?php
		// get the buffered content into a var
		$output = ob_get_contents();

		// clean buffer
		ob_end_clean();

		return $output;
	}
}
