<?php
/**
 * This file is used to markup the meta box aspects of the plugin.
 *
 * @since      1.0.0
 * @package    imagelinks
 * @subpackage imagelinks/admin/partials
 */
?>
<script type="text/javascript">
	var _imageLinksAppData = window.imageLinksAppData || {};
	if(_imageLinksAppData) {
		_imageLinksAppData.path = '<?php echo plugin_dir_url( dirname(dirname(__FILE__)) ); ?>';
		_imageLinksAppData.ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		_imageLinksAppData.uploadUrl = '<?php $upload_dir = wp_upload_dir(); echo $upload_dir['baseurl']; ?>';
		_imageLinksAppData.uiCfg = '<?php echo addslashes(json_encode(unserialize(get_post_meta( get_the_ID(), 'imgl-meta-ui-cfg', true )))); ?>';
	}
</script>

<!-- imgl-ui-wrap -->
<div id="imgl-ui-wrap" x-ng-app="ngImageLinksApp" x-ng-controller="ngImageLinksAppController">
	<input type="hidden" id="imgl-ui-meta-image-url" name="imgl-meta-image-url" value="">
	<input type="hidden" id="imgl-ui-meta-ui-cfg" name="imgl-meta-ui-cfg" value="">
	<input type="hidden" id="imgl-ui-meta-imagelinks-cfg" name="imgl-meta-imagelinks-cfg" value="">
	
	<div id="imgl-ui-loading" class="imgl-ui-loading-main">
		<div class="imgl-ui-loading-progress"></div>
	</div>
	<div id="imgl-ui-workspace" class="imgl-ui-clearfix" x-workspace x-init="appData.fn.workspace.init">
		<div id="imgl-ui-screen">
			<div id="imgl-ui-image-loading" x-ng-class="{'imgl-ui-active': appData.image.isLoading}">
				<i class="fa fa-spinner fa-pulse fa-fw"></i>
			</div>
			<div id="imgl-ui-canvas" x-ng-class="{'imgl-ui-active': appData.image.show, 'imgl-ui-target-tool': appData.targetTool}" x-canvas x-init="appData.fn.canvas.init">
				<img id="imgl-ui-canvas-image" x-ng-src="{{appData.image.show ? appData.fn.getImageFullPath(appData, appData.config) : ''}}" x-ng-style="appData.canvas.style" data-pin-nopin="true">
				<div id="imgl-ui-hotspots">
					<div x-ng-repeat="hotspot in appData.config.hotspots | isset:'isVisible'">
						<div class="imgl-ui-hotspot" x-ng-class="{'imgl-ui-active': hotspot.isSelected}" x-ng-style="hotspot.style" x-hotspot x-init="appData.fn.hotspots.init" x-data="hotspot" tabindex="1">
							<div class="imgl-ui-hotspot-label">{{(hotspot.config.title ? hotspot.config.title : hotspot.id)}}</div>
							<div class="line pos-n"></div>
							<div class="line pos-e"></div>
							<div class="line pos-s"></div>
							<div class="line pos-w"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="imgl-ui-tabs">
			<div class="imgl-ui-tab" x-ng-class="{'imgl-ui-active': appData.config.tabPanel.general.isActive}" x-tab-panel-item x-id="general" x-init="appData.fn.tabPanelItemInit"><i class="fa fa-fw fa-cog"></i><?php echo __('General', 'imagelinks'); ?></div>
			<div class="imgl-ui-tab" x-ng-class="{'imgl-ui-active': appData.config.tabPanel.hotspots.isActive}" x-tab-panel-item x-id="hotspots" x-init="appData.fn.tabPanelItemInit"><i class="fa fa-fw fa-dot-circle-o"></i><?php echo __('Hotspots', 'imagelinks'); ?><div class="imgl-ui-label">{{appData.config.hotspots.length}}</div></div>
			<div class="imgl-ui-cmd-setimage" x-select-image x-id="image" x-init="appData.fn.selectImageInit"><?php echo __('Set Image', 'imagelinks'); ?></div>
			<div class="imgl-ui-cmd-preview" x-ng-click="appData.fn.preview(appData);"><?php echo __('Preview', 'imagelinks'); ?></div>
			<div class="imgl-ui-cmd-load" x-ng-click="appData.fn.storage.loadFromFile(appData);">Load From File</div>
			<div class="imgl-ui-cmd-save" x-ng-click="appData.fn.storage.saveToFile(appData);">Save To File</div>
			<input id="imgl-ui-load-from-file" type="file" style="display:none;" />
		</div>
		<div id="imgl-ui-tab-data">
			<!-- general section -->
			<div class="imgl-ui-section" x-ng-class="{'imgl-ui-active': appData.config.tabPanel.general.isActive}">
				<div class="imgl-ui-config">
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.imageUrl}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.imageUrl = !appData.config.foldedSections.imageUrl;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Set the full url for the image.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Image Url', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-control">
								<input class="imgl-ui-text imgl-ui-long" type="text" x-ng-model="appData.config.imageUrl">
							</div>
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.imageUrlLocal"></div>
								<label><?php echo __('URL is local', 'imagelinks'); ?></label>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.imageSize}">
						<div class="imgl-ui-block-header"  x-ng-click="appData.config.foldedSections.imageSize = !appData.config.foldedSections.imageSize;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Set image custom width and height.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Image Size', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-control">
								<select class="imgl-ui-select" x-ng-model="appData.config.imageSize">
									<option value="none"><?php echo __('Default', 'imagelinks'); ?></option>
									<option value="fixed"><?php echo __('Fixed Size', 'imagelinks'); ?></option>
								</select>
							</div>
							
							<div class="imgl-ui-inline-group" x-ng-if="!(appData.config.imageSize=='none')"> 
								<div class="imgl-ui-control">
									<input class="imgl-ui-number" x-ng-model="appData.config.imageWidth">
									<div class="imgl-ui-label"><?php echo __('Width', 'imagelinks'); ?> (auto|value[px,cm,%,etc]|initial|inherit)</div>
								</div>
								<div class="imgl-ui-control">
									<input class="imgl-ui-number" x-ng-model="appData.config.imageHeight">
									<div class="imgl-ui-label"><?php echo __('Height', 'imagelinks'); ?> (auto|value[px,cm,%,etc]|initial|inherit)</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.theme}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.theme = !appData.config.foldedSections.theme;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Choose a theme from the list.<br><br>Note:<br>You can create your own theme too and add it in the plugin folder for later use.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Theme', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-control">
								<select class="imgl-ui-select" x-ng-model="appData.config.theme">
									<option value="imgl-theme-default"><?php echo __('default', 'imagelinks'); ?></option>
									<?php 
										$plugin_path = plugin_dir_path( dirname(dirname(__FILE__)) );
										$path = $plugin_path . 'lib/imagelinks.theme.*.css';
										$files = glob( $path );
										foreach($files as $file) {
											$file = strtolower(basename($file));
											$matches = array();
											
											if(preg_match('/^imagelinks.theme.(.*).css?/', $file, $matches)) {
												$theme = $matches[1];
												if($theme !== 'default' ) {
													echo '<option value="imgl-theme-' . $theme . '">' . $theme . '</option>';
												}
											}
										}
									?>
								</select>
							</div>
							
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.hotSpotBelowPopover"></div>
								<label><?php echo __('Hotspots are below the popover window', 'imagelinks'); ?></label>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.mobile}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.mobile = !appData.config.foldedSections.mobile;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Enable or disable the animation in the mobile browsers.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Mobile Animation', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.config.mobile"></div>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.popoverCfg}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.popoverCfg = !appData.config.foldedSections.popoverCfg;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Set popover settings. They are common for all popover instances.<br><br>Note:<br>We recommend do not change the popover template without having some knowledge.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Popover Settings', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-label"><?php echo __('Show Popovers', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.config.popover"></div>
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Popover Placement', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<select class="imgl-ui-select" x-ng-model="appData.config.popoverPlacement">
									<option value="top"><?php echo __('top', 'imagelinks'); ?></option>
									<option value="bottom"><?php echo __('bottom', 'imagelinks'); ?></option>
									<option value="left"><?php echo __('left', 'imagelinks'); ?></option>
									<option value="right"><?php echo __('right', 'imagelinks'); ?></option>
									<option value="top-left"><?php echo __('top-left', 'imagelinks'); ?></option>
									<option value="top-right"><?php echo __('top-right', 'imagelinks'); ?></option>
									<option value="bottom-left"><?php echo __('bottom-left', 'imagelinks'); ?></option>
									<option value="bottom-right"><?php echo __('bottom-right', 'imagelinks'); ?></option>
								</select>
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Popover Show Trigger', 'imagelinks'); ?></div>
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-control">
									<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.popoverShowTriggerHover"></div>
									<label><?php echo __('Hover', 'imagelinks'); ?></label>
								</div>
							</div>
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-control">
									<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.popoverShowTriggerClick"></div>
									<label><?php echo __('Click', 'imagelinks'); ?></label>
								</div>
							</div>
							<br>
							
							<div class="imgl-ui-label"><?php echo __('Popover Hide Trigger', 'imagelinks'); ?></div>
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-control">
									<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.popoverHideTriggerLeave"></div>
									<label><?php echo __('Leave', 'imagelinks'); ?></label>
								</div>
							</div>
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-control">
									<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.popoverHideTriggerClick"></div>
									<label><?php echo __('Click', 'imagelinks'); ?></label>
								</div>
							</div>
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-control">
									<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.popoverHideTriggerBodyClick"></div>
									<label><?php echo __('Body', 'imagelinks'); ?></label>
								</div>
							</div>
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-control">
									<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.config.popoverHideTriggerManual"></div>
									<label><?php echo __('Manual', 'imagelinks'); ?></label>
								</div>
							</div>
							<br>
							
							<div class="imgl-ui-label"><?php echo __('Popover Show CSS3 Class', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<button class="imgl-ui-button" type="button" x-ng-click="appData.fn.selectPopoverShowClass(appData)">GET</button>
								<input class="imgl-ui-text" type="text" x-ng-model="appData.config.popoverShowClass" x-ng-model-options="{updateOn: 'change blur'}">
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Popover Hide CSS3 Class', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<button class="imgl-ui-button" type="button" x-ng-click="appData.fn.selectPopoverHideClass(appData)">GET</button>
								<input class="imgl-ui-text" type="text" x-ng-model="appData.config.popoverHideClass" x-ng-model-options="{updateOn: 'change blur'}">
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Popover HTML Template', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<textarea class="imgl-ui-textarea" cols="40" rows="5" x-ng-model="appData.config.popoverTemplate"></textarea>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block"  x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.customCSS}">
						<div class="imgl-ui-block-header"  x-ng-click="appData.config.foldedSections.customCSS = !appData.config.foldedSections.customCSS;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Enter any custom css you want to apply on this imagelins.<br><br>Note:<br>Please do not use <b>&lt;style&gt;...&lt;/style&gt;</b> tag with Custom CSS.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Custom CSS', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.config.customCSS"></div>
							</div>
							
							<div class="imgl-ui-control" x-ng-if="appData.config.customCSS">
								<textarea class="imgl-ui-textarea" cols="40" rows="20" x-ng-model="appData.config.customCSSContent" placeholder="<?php echo __('Enter custom CSS here', 'imagelinks'); ?>"></textarea>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block"  x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.customJS}">
						<div class="imgl-ui-block-header"  x-ng-click="appData.config.foldedSections.customJS = !appData.config.foldedSections.customJS;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Enter any custom javascript code you want to execute after the imagelinks load.<br><br>Note:<br>Please do not use <b>&lt;script&gt;...&lt;/script&gt;</b> tag with Custom JavaScript.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Custom JavaScript', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.config.customJS"></div>
							</div>
							
							<div class="imgl-ui-control" x-ng-if="appData.config.customJS">
								<textarea class="imgl-ui-textarea" cols="40" rows="20" x-ng-model="appData.config.customJSContent" placeholder="<?php echo __('Enter custom JavaScript here', 'imagelinks'); ?>"></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /end general section -->
			
			<!-- hotspots section -->
			<div class="imgl-ui-section" x-ng-class="{'imgl-ui-active': appData.config.tabPanel.hotspots.isActive}">
				<div class="imgl-ui-item-list-wrap">
					<div class="imgl-ui-item-commands">
						<div class="imgl-ui-item-command" x-ng-click="appData.fn.hotspots.add(appData)"><i class="fa fa-fw fa-plus-square"></i></div>
						<div class="imgl-ui-item-command" x-ng-click="appData.fn.hotspots.copySelected(appData)"><i class="fa fa-fw fa-clone"></i></div>
						<div class="imgl-ui-item-command" x-ng-click="appData.fn.hotspots.upSelected(appData)"><i class="fa fa-fw fa-arrow-up"></i></div>
						<div class="imgl-ui-item-command" x-ng-click="appData.fn.hotspots.downSelected(appData)"><i class="fa fa-fw fa-arrow-down"></i></div>
						<div class="imgl-ui-item-command" x-ng-click="appData.fn.hotspots.removeSelected(appData)"><i class="fa fa-fw fa-trash"></i></div>
					</div>
					<ul class="imgl-ui-item-list">
						<li class="imgl-ui-item" x-ng-repeat="hotspot in appData.config.hotspots track by hotspot.id" x-ng-class="{'imgl-ui-active': hotspot.isSelected}" x-ng-click="appData.fn.hotspots.select(appData, hotspot)" title="{{(hotspot.config.title ? hotspot.config.title : hotspot.id)}}">
							<span class="imgl-ui-icon"></span>
							<span class="imgl-ui-name">{{(hotspot.config.title ? hotspot.config.title : hotspot.id)}}</span>
							<span class="imgl-ui-visible" x-ng-click="hotspot.isVisible=!hotspot.isVisible;" x-ng-class="{'imgl-ui-off': !hotspot.isVisible}"></span>
						</li>
					</ul>
				</div>
				<div class="imgl-ui-config">
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-folded': appData.config.foldedSections.targetTool}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.targetTool = !appData.config.foldedSections.targetTool;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Use the target tool to quick create a hotspot and it\'s location on the image.<br><br>When the target tool is ON click on the image and you will get a new hotspot.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Target Tool State', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.targetTool"></div>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-hidden': !appData.hotspot.selected, 'imgl-ui-folded': appData.config.foldedSections.hotspotLocation}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.hotspotLocation = !appData.config.foldedSections.hotspotLocation;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Use this options to set the hotspot\'s starting x and y location.<br><br>If you want to change the location of the selected hotspot, just click on the hotspot and drag it or use arrow keys.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Hotspot Location', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-label"><?php echo __('X %', 'imagelinks'); ?></div>
								<div class="imgl-ui-control">
									<input class="imgl-ui-number" type="number" step="any" x-ng-model="appData.hotspot.selected.config.x">
								</div>
							</div>
							
							<div class="imgl-ui-inline-group">
								<div class="imgl-ui-label"><?php echo __('Y %', 'imagelinks'); ?></div>
								<div class="imgl-ui-control">
									<input class="imgl-ui-number" type="number" step="any" x-ng-model="appData.hotspot.selected.config.y">
								</div>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-hidden': !appData.hotspot.selected, 'imgl-ui-folded': appData.config.foldedSections.hotspotCfg}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.hotspotCfg = !appData.config.foldedSections.hotspotCfg;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Use this option to set hotspot settings. You can define your own style for hotspot with images, icons, text and etc.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Hotspot Settings', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-label"><?php echo __('Title', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<input class="imgl-ui-text imgl-ui-long" type="text" x-ng-model="appData.hotspot.selected.config.title">
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Hotspot Image (otherwise the plugin uses a theme icon)', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<div class="imgl-ui-image" x-ng-class="{'imgl-ui-active': appData.hotspot.selected.config.image.url}" x-ng-click="appData.fn.selectImage(appData, appData.hotspot.selected.config.image);">
									<div class="imgl-ui-image-data" x-ng-style="{'background-image': 'url(' + appData.fn.getImageUrl(appData, appData.hotspot.selected.config.image) + ')'}"></div>
									<div class="imgl-ui-image-edit" x-ng-click="appData.fn.setImageUrlConfirm(appData, appData.hotspot.selected.config.image);$event.stopPropagation();"></div>
									<div class="imgl-ui-image-clear" x-ng-click="appData.hotspot.selected.config.image.url=null;$event.stopPropagation();"></div>
									<div class="imgl-ui-image-label"><?php echo __('Image', 'imagelinks'); ?></div>
								</div>
							</div>
							
							<div x-ng-if="(appData.hotspot.selected.config.image.url ? true : false)">
								<div class="imgl-ui-inline-group">
									<div class="imgl-ui-label"><?php echo __('Image Custom Width (px)', 'imagelinks'); ?></div>
									<div class="imgl-ui-control">
										<input class="imgl-ui-number" type="number" min="0" x-ng-model="appData.hotspot.selected.config.image.width">
									</div>
								</div>
								
								<div class="imgl-ui-inline-group">
									<div class="imgl-ui-label"><?php echo __('Image Custom Height (px)', 'imagelinks'); ?></div>
									<div class="imgl-ui-control">
										<input class="imgl-ui-number" type="number" min="0" x-ng-model="appData.hotspot.selected.config.image.height">
									</div>
								</div>
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Link URL', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<input class="imgl-ui-text imgl-ui-long" type="text" x-ng-model="appData.hotspot.selected.config.link">
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Open Link in New Window', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.hotspot.selected.config.linkNewWindow"></div>
							</div>
							
							<div class="imgl-ui-accordion">
								<div class="imgl-ui-accordion-toggle"><?php echo __('Advanced Options', 'imagelinks'); ?></div>
								<div class="imgl-ui-accordion-data">
									<div class="imgl-ui-control">
										<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Enable/disable hotspot custom style.<br><br>Note:<br>You can define your own style for hotspot with images, icons, text and etc..', 'imagelinks'); ?></div></div>
										<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.hotspot.selected.config.custom"></div>
										<div class="imgl-ui-label"><?php echo __('Custom style', 'imagelinks'); ?></div>
									</div>
									
									<div class="imgl-ui-control" x-ng-if="appData.hotspot.selected.config.custom">
										<input class="imgl-ui-text imgl-ui-long" type="text" placeholder="<?php echo __('Hotspot Class Name', 'imagelinks'); ?>" x-ng-model="appData.hotspot.selected.config.customClassName">
										<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Set custom classes for a hotspot element.', 'imagelinks'); ?></div></div>
									</div>
									
									<div class="imgl-ui-control" x-ng-if="appData.hotspot.selected.config.custom">
										<textarea class="imgl-ui-textarea" cols="40" rows="5" placeholder="<?php echo __('Hotspot HTML Content', 'imagelinks'); ?>" x-ng-model="appData.hotspot.selected.config.customContent"></textarea>
										<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Set content for a hotspot element, if you want to make it complex.', 'imagelinks'); ?></div></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="imgl-ui-block" x-ng-class="{'imgl-ui-hidden': !appData.hotspot.selected, 'imgl-ui-folded': appData.config.foldedSections.hotspotPopoverCfg}">
						<div class="imgl-ui-block-header" x-ng-click="appData.config.foldedSections.hotspotPopoverCfg = !appData.config.foldedSections.hotspotPopoverCfg;">
							<div class="imgl-ui-helper"><div class="imgl-ui-tooltip"><?php echo __('Use this options to set popover settings.', 'imagelinks'); ?></div></div>
							<div class="imgl-ui-title"><?php echo __('Popover Settings', 'imagelinks'); ?></div>
							<div class="imgl-ui-state"></div>
						</div>
						<div class="imgl-ui-block-data">
							<div class="imgl-ui-label"><?php echo __('Show Popover', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-toggle" x-ng-model="appData.hotspot.selected.config.popover"></div>
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Popover Content', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<button class="imgl-ui-button" type="button" x-ng-click="appData.fn.editorShow(appData)"><?php echo __('Edit in the editor', 'imagelinks'); ?></button>
							</div>
							<div class="imgl-ui-control">
								<textarea class="imgl-ui-textarea" cols="40" rows="5" x-ng-model="appData.hotspot.selected.config.popoverContent"></textarea>
							</div>
							
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.hotspot.selected.config.popoverHtml"></div>
								<label><?php echo __('Popover Content is HTML', 'imagelinks'); ?></label>
							</div>
							
							<div class="imgl-ui-control">
								<div x-checkbox class="imgl-ui-standard" x-ng-model="appData.hotspot.selected.config.popoverShow"></div>
								<label><?php echo __('Show on Load', 'imagelinks'); ?></label>
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Popover Placement', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<select class="imgl-ui-select" x-ng-model="appData.hotspot.selected.config.popoverPlacement">
									<option value="default"><?php echo __('default', 'imagelinks'); ?></option>
									<option value="top"><?php echo __('top', 'imagelinks'); ?></option>
									<option value="bottom"><?php echo __('bottom', 'imagelinks'); ?></option>
									<option value="left"><?php echo __('left', 'imagelinks'); ?></option>
									<option value="right"><?php echo __('right', 'imagelinks'); ?></option>
									<option value="top-left"><?php echo __('top-left', 'imagelinks'); ?></option>
									<option value="top-right"><?php echo __('top-right', 'imagelinks'); ?></option>
									<option value="bottom-left"><?php echo __('bottom-left', 'imagelinks'); ?></option>
									<option value="bottom-right"><?php echo __('bottom-right', 'imagelinks'); ?></option>
								</select>
							</div>
							
							<div class="imgl-ui-label"><?php echo __('Popover Custom Width (px)', 'imagelinks'); ?></div>
							<div class="imgl-ui-control">
								<input class="imgl-ui-number" type="number" min="0" x-ng-model="appData.hotspot.selected.config.popoverWidth" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /end hotspots section -->
		</div>
	</div>
	<div class="imgl-ui-modals">
	</div>
	<div id="imgl-ui-preview-wrap" x-ng-class="{'imgl-ui-active': appData.preview}">
		<div id="imgl-ui-preview-inner">
			<div id="imgl-ui-preview-canvas" x-ng-class="{'imgl-ui-active': appData.image.show}" >
				<img id="imgl-ui-preview-image" x-ng-src="{{appData.image.show ? appData.uploadUrl + appData.config.imageUrl : ''}}" x-ng-style="appData.config.imageSize == 'none' ? appData.canvas.style : {width: appData.config.imageWidth + 'px', height: appData.config.imageHeight + 'px'}" data-pin-nopin="true">
			</div>
		</div>
		<button type="button" id="imgl-ui-preview-close" aria-label="Close" x-ng-click="appData.fn.previewClose(appData);"><span aria-hidden="true">&times;</span></button>
	</div>
	<div id="imgl-ui-editor-wrap" x-ng-class="{'imgl-ui-active': appData.editor}">
		<div id="imgl-ui-editor-inner">
			<?php 
				// Manual double binding for x-ng-model="appData.hotspot.selected.config.popoverContent"
				$settings = array(
					'wpautop' => false,
					'editor_height' => 300 // In pixels, takes precedence and has no default value
				);
				wp_editor('', 'imgluieditor', $settings);
			?>
		</div>
		<button type="button" id="imgl-ui-editor-close" aria-label="Close" x-ng-click="appData.fn.editorClose(appData);"><span aria-hidden="true">&times;</span></button>
	</div>
</div>
<!-- /end imgl-ui-wrap -->