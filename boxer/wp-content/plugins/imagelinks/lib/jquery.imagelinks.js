/*!
  ImageLinks - jQuery Interactive Image
  @name jquery.imagelinks.js
  @description A jQuery plugin for creating an interactive image for news, posters, albums and etc
  @version 1.4.4
  @author Max Lawrence 
  @site http://www.avirtum.com
  @copyright (c) 2016 Max Lawrence (http://www.avirtum.com)
*/

(function($) {
	"use strict";
	
	/*
	* Pointer Events Polyfill: Adds support for the style attribute "pointer-events: none" to browsers without this feature (namely, IE).
	* (c) 2013, Kent Mewhort, licensed under BSD. See LICENSE.txt for details.
	*/
	// constructor
	function PointerEventsPolyfill(options) {
		// set defaults
		this.options = {
			selector: '*',
			mouseEvents: ['click','dblclick','mousedown','mouseup'],
			usePolyfillIf: function(){
				if(navigator.appName == 'Microsoft Internet Explorer')
				{
					var agent = navigator.userAgent;
					if (agent.match(/MSIE ([0-9]{1,}[\.0-9]{0,})/) != null){
						var version = parseFloat( RegExp.$1 );
						if(version < 11) {
							return true;
						}
					}
				}
				return false;
			}
		};
		if(options){
			var obj = this;
			$.each(options, function(k,v){
				obj.options[k] = v;
			});
		}
		if(this.options.usePolyfillIf())
			this.register_mouse_events();
	}

	// singleton initializer
	PointerEventsPolyfill.initialize = function(options) {
		if(PointerEventsPolyfill.singleton == null) {
			PointerEventsPolyfill.singleton = new PointerEventsPolyfill(options);
		}
		return PointerEventsPolyfill.singleton;
	};

	// handle mouse events w/ support for pointer-events: none
	PointerEventsPolyfill.prototype.register_mouse_events = function() {
		// register on all elements (and all future elements) matching the selector
		$(document).on(this.options.mouseEvents.join(" "), this.options.selector, function(e) {
			if($(this).css('pointer-events') == 'none') {
				// peak at the element below
				var origDisplayAttribute = $(this).css('display');
				$(this).css('display','none');

				var underneathElem = document.elementFromPoint(e.clientX, e.clientY);

				if(origDisplayAttribute) {
					$(this).css('display', origDisplayAttribute);
				} else {
					$(this).css('display','');
				}

				// fire the mouse event on the element below
				e.target = underneathElem;
				$(underneathElem).trigger(e);

				return false;
			}
			return true;
		});
	};
	PointerEventsPolyfill.initialize({selector:'.imgl-popover'});


	/*
	* ImageLinks Plugin Stuff
	*/
	var Util = (
		function() {
			function Util() {
			}

			Util.prototype.css2json = function(css) {
				var s = {};
				if (!css) return s;
				if (css instanceof CSSStyleDeclaration) {
					for (var i in css) {
						if ((css[i]).toLowerCase) {
							s[(css[i]).toLowerCase()] = (css[css[i]]);
						}
					}
				} else if (typeof css == "string") {
					css = css.split(";");
					for (var i in css) {
						var l = css[i].split(":");
						if(l.length == 2) {
							s[l[0].toLowerCase().trim()] = (l[1].trim());
						}
					}
				}
				return s;
			};

			Util.prototype.isMobile = function(agent) {
				return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(agent);
			};

			Util.prototype.animationEvent = function() {
				var el = document.createElement("fakeelement");

				var animations = {
					"animation"      : "animationend",
					"MSAnimationEnd" : "msAnimationEnd",
					"OAnimation"     : "oAnimationEnd",
					"MozAnimation"   : "mozAnimationEnd",
					"WebkitAnimation": "webkitAnimationEnd"
				}

				for (var i in animations){
					if (el.style[i] !== undefined){
						return animations[i];
					}
				}
			};

			return Util;
		}()
	);

	var ITEM_DATA_NAME = "imglnks",
	INSTANCE_COUNTER = 0;

	function ImageLinks(container, config) {
		this.container = null;
		this.config = null;
		this.controls = {};
		this.hotSpots = [];
		this.popover = false;
		this.popoverTemplate = null;
		this.popoverCloseManual = false;
		this.popoverClosePopover = false;
		this.imgWidth = 0;
		this.imgHeight = 0;
		this.zindex = 4;
		this.hotSpotSetup = false;
		this.id = INSTANCE_COUNTER++;
		this.ready = false;
		
		this.init(container, config);
	};

	ImageLinks.prototype = {
		VERSION: "1.4.3",
		
		//=============================================
		// Properties & methods (is shared for all instances)
		//=============================================
		defaults: {
			theme: "imgl-default", // CSS styles for controls, change it to match your own theme
			popover: true, // enable or disable the build-in popover system
			popoverTemplate: "<div class='imgl-popover'><div class='imgl-close'></div><div class='imgl-arrow'></div><div class='imgl-content'></div></div>", // base HTML to use when creating the popover
			popoverPlacement: "top", // set the position of the popover (top, bottom, left, right, top-right, top-left, bottom-right, bottom-left)
			popoverShowTrigger: "hover", // specify how popover is triggered (click, hover)
			popoverHideTrigger: "leave", // specify how popover is hidden (click, leave, bodyclick, manual)
			popoverShowClass: null, // specify the css3 animation class for the popover show
			popoverHideClass: null, // specify the css3 animation class for the popover hide
			hotSpotBelowPopover: true, // specify the z-order of the hotSpot against the popover
			hotSpots: [], // specify an array of hot spots that can be links to news, posters, albums and other media content etc
			// the definition of a hotSpot
			// x: 0 // specify the x position of the hot spot’s location in % [0;1]
			// y: 0 // specify the y position of the hot spot’s location in % [0;1]
			// className: null // specify additional css classes for the hotspot
			// content: null // if set, the value is displayed as the hotspot's content
			// imageUrl: null, // url for the hotspot image
			// link: null, // if set, the hotspot is a link
			// linkNewWindow: false, // if set, open link in new window
			// popover: true, // enable or disable the popover
			// popoverShow: false, // show the popover content when the scene's loaded
			// popoverLazyload: true, // enable or disable lazy load for the popover content
			// popoverHtml: true // specify the type of the popover content
			// popoverWidth: 100 // specify the width of the popover (px)
			// popoverContent: null // if set, the value is displayed as the popover's content, it can be text or HTML content, or a method - function myfunc()
			// popoverSelector: null, // specify the selector for select a single element with a content for the popover
			// popoverPlacement: "top" // set the position of the popover (top, bottom, left, right, top-right, top-left, bottom-right, bottom-left)
			// userData: null // specify the user data that is associated with the hotspot, useful when the popoverContent is a method
			hotSpotSetup: false, // set or disable manual setup of hotspots in the current image
			mobile: false, // enable or disable the animation in the mobile browsers
			onLoad: null // function() {} fire after the imagelinks was loaded
		},

		//=============================================
		// Methods
		//=============================================
		init: function(container, config) {
			this.container = container;
			this.config = config;

			this.checkAnimation();
			this.destroy();
			this.applyImage();
		},

		create: function() {
			this.applyDOM();
			this.applyHandlers();
			this.applyPopoverSupport();
			this.applyHotSpots();
			this.updateHotSpots();
			
			if (typeof this.config.onLoad == "function") { // make sure the callback is a function
				this.config.onLoad.call(this);
			}
			
			this.ready = true;
		},

		checkAnimation: function() {
			var disabled = !this.config.mobile && this.util().isMobile(navigator.userAgent);
			if(this.util().animationEvent() == undefined || disabled) {
				this.config.popoverShowClass = null;
				this.config.popoverHideClass = null;
			}
		},

		applyImage: function() {
			if(!this.container.is("img")) {
				console.error("Cannot load image, the container should be an 'img' element");
				return;
			}
			
			
			this.container.get(0).onload = $.proxy(function ( xhr ) {
				if(!this.ready) {
					return;
				}
				
				this.updateLayout();
			}, this);
			
			
			var imageSrc = this.container.data("imgl-src") || this.container.attr("src");
			if(imageSrc) {
				var image = new Image();
				
				image.onload = $.proxy(function ( xhr ) {
					this.imgWidth = image.width;
					this.imgHeight = image.height;
					this.create();
				}, this);
				image.onerror = $.proxy(function ( xhr ) {
					console.error("Cannot load image '" + imageSrc + "'");
				}, this);
				
				image.src = imageSrc;
			} else {
				console.error("Cannot load image, the parameter 'image' must be defined");
			}
		},

		applyDOM: function() {
			this.container.wrap("<div class='imgl'></div>");
			this.container.wrap("<div class='imgl-img'></div>");
			
			this.controls.$img = this.container.closest(".imgl-img");
			this.controls.$main = this.container.closest(".imgl");
			
			// a little hack for IE, animationstart doesn't work
			this.controls.$img.detach().css({ top: -9999, left: -9999, width: 0, position: "absolute"});
			this.controls.$img.appendTo( "body" );
			setTimeout($.proxy(function() {
				this.controls.$img.detach().removeAttr("style");
				this.controls.$img.prependTo(this.controls.$main);
			}, this), 1);
			
			this.controls.$view = $("<div class='imgl-view'></div>");
			this.controls.$view.addClass(this.config.theme).attr("id", this.getUID("imgl"));
			
			if( this.config.hotSpotSetup ) {
				this.controls.$view.addClass("imgl-hotspot-setup").attr("tabindex", 1);
			}
			
			this.controls.$hotspots = $("<div class='imgl-hotspots'></div>");
			this.controls.$view.append(this.controls.$hotspots);
			
			this.controls.$main.append(this.controls.$view);
		},

		resetDOM: function() {
			this.controls.$view.remove();
			this.container.unwrap(".imgl-img");
			this.container.unwrap(".imgl");
		},

		applyHandlers: function() {
			if( this.config.hotSpotSetup ) {
				this.controls.$view.on( "click.imgl", $.proxy(this.onViewClick, this) );
				this.controls.$view.on( "keydown.imgl", $.proxy(this.onViewKeyDown, this) );
				this.controls.$view.on( "keyup.imgl", $.proxy(this.onViewKeyUp, this) );
			}
			
			// a little hack, detect when a parent element change the 'display:none' to something else
			this.controls.$img.on("animationstart.imgl MSAnimationStart.imgl webkitAnimationStart.imgl", $.proxy(this.onAnimationStart, this) );
			
			$(window).on( "load.imgl-" + this.id, $.proxy(this.onLoad, this) );
			$(window).on( "resize.imgl-" + this.id, $.proxy(this.onResize, this) );
		},

		resetHandlers: function() {
			this.controls.$view.off( "click.imgl"  );
			this.controls.$view.off( "keydown.imgl" );
			this.controls.$view.off( "keyup.imgl" );
			
			$(window).off( "load.imgl-" + this.id );
			$(window).off( "resize.imgl-" + this.id );
		},

		applyHotSpots: function() {
			var hotSpots = JSON.parse(JSON.stringify(this.config.hotSpots)); // clone object without reference
			for (var i = 0, len = hotSpots.length; i < len; i++) {
				var hotSpot = hotSpots[i];
				
				if(hotSpot.className) {
					hotSpot.$el = $("<div class='imgl-hotspot-custom " + hotSpot.className + "'></div>");
				} else if(hotSpot.imageUrl) {
					hotSpot.$el = $("<div class='imgl-hotspot-custom'></div>");
				} else {
					hotSpot.$el = $("<div class='imgl-hotspot'></div>");
				}
				
				if(hotSpot.imageUrl) {
					var style = "style='";
					if(hotSpot.imageWidth || hotSpot.imageHeight) {
						style = style + (hotSpot.imageWidth ? "width:" + hotSpot.imageWidth + "px;" : "") + (hotSpot.imageHeight ? "height:" + hotSpot.imageHeight + "px;" : "");
					}
					style = style + "'";
					
					var $data = $("<div class='imgl-hotspot-image'></div>");
					hotSpot.$image = $("<img src='" + hotSpot.imageUrl + "' alt=''" + style + ">");
					hotSpot.$el.append($data.append(hotSpot.$image));
				}
				
				if(hotSpot.link) {
					var $data = $("<div class='imgl-hotspot-link'></div>").append("<a href='" + hotSpot.link + "' target='" + (hotSpot.linkNewWindow ? "_blank" : "_self") + "' rel='nofollow'>");
					hotSpot.$el.append($data);
				}
				
				if(hotSpot.content) {
					var $data = $("<div class='imgl-hotspot-data'></div>").append(hotSpot.content);
					hotSpot.$el.append($data);
				}
				
				if(!(typeof hotSpot.popoverLazyload === "boolean")) {
					hotSpot.popoverLazyload = true;
				}
				
				if(!(typeof hotSpot.popoverShow === "boolean")) {
					hotSpot.popoverShow = false;
				}
				
				
				hotSpot.id = this.getUID("hotspot");
				hotSpot.$el.attr("id", hotSpot.id);
				hotSpot.$popover = null;
				hotSpot.visible = true;
				
				// restore the reference if 'popoverContent' is a function
				if(typeof this.config.hotSpots[i].popoverContent == "function") {
					hotSpot.popoverContent = this.config.hotSpots[i].popoverContent;
				}
				
				if(hotSpot.popoverContent || hotSpot.popoverSelector) {
					var triggers = this.config.popoverShowTrigger.trim().split(' ');
					for (var j = triggers.length; j--;) {
						var trigger = triggers[j];
						
						if (trigger == "click") {
							hotSpot.$el.on("click.imgl", $.proxy(this.onHotSpotClick, this, hotSpot) );
							hotSpot.$el.on("touchstart.imgl", $.proxy(this.onHotSpotClick, this, hotSpot) );
							hotSpot.$el.on("touchend.imgl", $.proxy(this.onHotSpotTouchEnd, this, hotSpot) );
						} else if (trigger == "hover") {
							hotSpot.$el.on("mouseenter.imgl", $.proxy(this.onHotSpotEnter, this, hotSpot) );
							hotSpot.$el.on("touchstart.imgl", $.proxy(this.onHotSpotClick, this, hotSpot) );
							hotSpot.$el.on("touchend.imgl", $.proxy(this.onHotSpotTouchEnd, this, hotSpot) );
						}
					}

					var triggers = this.config.popoverHideTrigger.trim().split(' ');
					for (var j = triggers.length; j--;) {
						var trigger = triggers[j];

						if (trigger == "click") {
							hotSpot.$el.on("click.imgl", $.proxy(this.onPopoverHide, this, hotSpot) );
							hotSpot.$el.on("touchstart.imgl", $.proxy(this.onPopoverHide, this, hotSpot) );
						} else if (trigger == "bodyclick") {
							$("body").add(this.controls.$view).on("mousedown.imgl touchstart.imgl", $.proxy(this.onPopoverHide, this, hotSpot) );
						} else if (trigger == "leave") {
							hotSpot.$el.on("mouseleave.imgl", $.proxy(this.onHotSpotLeave, this, hotSpot) );
							$("body").add(this.controls.$view).on("touchstart.imgl", $.proxy(this.onPopoverHide, this, hotSpot) );
						} else if (trigger == "popover") {
							this.popoverClosePopover = true;
						} else if (trigger == "manual") {
							this.popoverCloseManual = true;
						}
					}
				}
				
				this.controls.$hotspots.append(hotSpot.$el);
				
				if(this.popover && (!hotSpot.popoverLazyload || hotSpot.popoverShow)) {
					this.createPopover(hotSpot);
				}
			}
			
			this.hotSpots = this.hotSpots.concat(hotSpots);
		},

		resetHotSpots: function() {
			this.controls.$hotspots.children().fadeTo("slow", 0, function() {$(this).remove()});
		},

		updateHotSpots: function() {
			// check if hotSpot point is in the view
			var w = this.container.width(),
			h = this.container.height();
			
			if(w == 0 || h == 0) {
				this.controls.$view.css({"display":"none"});
			} else {
				this.controls.$view.css({"display":"block"});
			}

			var rect = this.container.get(0).getBoundingClientRect(),
			top = 0,
			left = 0,
			marginTop = parseInt(this.container.css("margin-top"), 10), // manually read paddings because getBoundingClientRect includes difference
			marginLeft = parseInt(this.container.css("margin-left"), 10),
			paddingTop = parseInt(this.container.css("padding-top"), 10),
			paddingLeft = parseInt(this.container.css("padding-left"), 10),
			borderTop = parseInt(this.container.css("border-top-width"), 10),
			borderLeft = parseInt(this.container.css("border-left-width"), 10);

			// we must check for NaN for ie 8/9
			if (isNaN(paddingTop))  paddingTop  = 0;
			if (isNaN(paddingLeft)) paddingLeft = 0;
			if (isNaN(marginTop))   marginTop   = 0;
			if (isNaN(marginLeft))  marginLeft  = 0;
			if (isNaN(borderTop))   borderTop   = 0;
			if (isNaN(borderLeft))  borderLeft  = 0;

			top  += (marginTop + paddingTop + borderTop);
			left += (marginLeft + paddingLeft + borderLeft);
			
			this.controls.$view.css({top: top, left: left, width: w, height: h});
			
			for(var i = this.hotSpots.length; i--;) {
				var hotSpot = this.hotSpots[i],
				width = hotSpot.$el.width(),
				height = hotSpot.$el.height(),
				left = w * hotSpot.x,
				top = h * hotSpot.y,
				left = Math.round(left - width/2),
				top = Math.round(top - height/2);
				
				hotSpot.$el.css({left: left, top: top});
				
				//if(hotSpot.$image) {
				//	var offset = {
				//		top: Math.round((height - hotSpot.$image.height()) / 2),
				//		left: Math.round((width - hotSpot.$image.width()) / 2)
				//	}
				//	
				//	hotSpot.$image.css({'margin-top':offset.top + 'px', 'margin-left':offset.left + 'px'});
				//}
			}
		},

		applyPopoverSupport: function() {
			this.popover = this.config.popover;
			if(!this.popover) {
				return;
			}

			var template = $(this.config.popoverTemplate);
			if (template.length != 1) {
				this.popover = false;
				console.error("'popoverTemplate' option must consist of exactly 1 top-level element!");
				return;
			}
			this.popoverTemplate = this.config.popoverTemplate;
		},

		createPopover: function(hotSpot) {
			// popover doesn't exist, let's create it
			if(!hotSpot.$popover) {
				hotSpot.$popover = $(this.popoverTemplate);
				
				var popoverId = this.getUID("popover");
				hotSpot.$popover.attr("id", popoverId);
				
				if(this.popoverCloseManual) {
					hotSpot.$popover.addClass("imgl-close");
					hotSpot.$popover.find(".imgl-close").on("click.imgl", $.proxy(this.onPopoverHide, this, hotSpot) );
				}
				
				hotSpot.$popover.on("click.imgl", $.proxy(this.onPopoverClick, this, hotSpot) )
			}
			
			var $popover = hotSpot.$popover;
			if(!$popover.hasClass("imgl-active") || hotSpot.$popover.hasClass(this.config.popoverHideClass) ) {
				
				var content = this.getPopoverContent(hotSpot);
				$popover.find(".imgl-content").children().detach().end()[ // maintain js events
					(hotSpot.popoverHtml ? (typeof content == "string" ? "html" : "append") : "text")
				](content);
			
				$popover.detach().css({ top: -9999, left: -9999, width: ""});
				$popover.removeClass(this.config.popoverHideClass);
				$popover.removeClass(this.config.popoverShowClass);
				
				if(hotSpot.popoverWidth) {
					$popover.css({"max-width": hotSpot.popoverWidth, "min-width": hotSpot.popoverWidth});
				}
				
				this.liftupPopover(hotSpot);
				
				$popover.appendTo(this.controls.$view);
				$popover.css({width: $popover[0].offsetWidth});
			}
		},

		showPopover: function(hotSpot) {
			if(!this.popover || !hotSpot.visible || !hotSpot.popover || (!hotSpot.popoverContent && !hotSpot.popoverSelector)) {
				return;
			}
			
			this.createPopover(hotSpot);
			
			// place the popover on the view
			var placement = this.config.popoverPlacement;
			if(hotSpot.popoverPlacement) {
				placement = hotSpot.popoverPlacement;
			}
			
			var pos = this.getPopoverPosition(hotSpot),
			$popover = hotSpot.$popover,
			popoverWidth  = $popover[0].offsetWidth,
			popoverHeight = $popover[0].offsetHeight;
			
			// check free space for the popover window
			//placement = placement == "bottom" && (pos.bottom + popoverHeight) > (window.pageYOffset + window.innerHeight) ? "top"    :
			//            placement == "top"    && (pos.top    - popoverHeight) < (window.pageYOffset)                      ? "bottom" :
			//            placement == "right"  && (pos.right  + popoverWidth)  > (window.pageXOffset + window.innerWidth)  ? "left"   :
			//            placement == "left"   && (pos.left   - popoverWidth)  < (window.pageXOffset)                      ? "right"  :
			//            placement;

			var offset = this.getPopoverOffset(placement, pos, popoverWidth, popoverHeight);
			this.applyPopoverPlacement(hotSpot, offset, placement);


			// make the popover active
			if(!$popover.hasClass("imgl-active")) {
				$popover.removeClass(this.config.popoverHideClass);
				$popover.addClass(this.config.popoverShowClass);
				
				if( this.config.popoverShowClass ) {
					$popover.css("visibility", "visible"); // little hack to prevent incorrect position of the popover
					$popover.addClass("imgl-active").addClass(this.config.popoverShowClass);
					
					$popover.one(this.util().animationEvent(), $.proxy(function(e) {
						var $popover = $(e.target);
						if(!$popover.hasClass(this.config.popoverHideClass)) {
							$popover.removeClass(this.config.popoverShowClass);
						}
						$popover.css("visibility", "");
					}, this) );
				} else {
					$popover.addClass("imgl-active");
				}
			}
			
			if(this.popoverClosePopover) {
				for (var i = 0, len = this.hotSpots.length; i < len; i++) {
					var hs = this.hotSpots[i];
					if(hs.id != hotSpot.id && hs.$popover && hs.$popover.hasClass("imgl-active")) {
						this.hidePopover(hs);
					}
				}
			}
		},

		updatePopovers: function() {
			for(var i = this.hotSpots.length; i--;) {
				var hotSpot = this.hotSpots[i];
				if( hotSpot.$popover && hotSpot.$popover.hasClass("imgl-active") ) {
					this.showPopover(hotSpot);
				}
			}
		},

		showPopovers: function() {
			for(var i = this.hotSpots.length; i--;) {
				var hotSpot = this.hotSpots[i];
				if(hotSpot.popover && hotSpot.popoverShow) {
					this.showPopover(hotSpot);
				}
			}
		},

		getUID: function(prefix) {
			do prefix += ~~(Math.random() * 1000000);
			while (document.getElementById(prefix));
			return prefix;
		},

		getPopoverContent: function (hotSpot) {
			if(hotSpot.popoverContent) {
				return (typeof hotSpot.popoverContent == "function" ? hotSpot.popoverContent.call(hotSpot) : hotSpot.popoverContent);
			} else if(hotSpot.popoverSelector) {
				var el = $(hotSpot.popoverSelector);
				return el.html();
			}
			return "";
		},

		getPopoverPosition: function (hotSpot) {
			var $el = hotSpot.$el,
			el = $el.get(0);
			
			var rect = el.getBoundingClientRect(),
			offset = $el.offset();
			
			var result = $.extend({}, rect, offset);
			result.top  = result.top  + result.height/2;
			result.left = result.left + result.width/2;
			
			return result;
		},

		getPopoverOffset: function(placement, pos, popoverWidth, popoverHeight) {
			return placement == "bottom"       ? { top: pos.top,                     left: pos.left - popoverWidth / 2 } :
				   placement == "top"          ? { top: pos.top - popoverHeight,     left: pos.left - popoverWidth / 2 } :
				   placement == "left"         ? { top: pos.top - popoverHeight / 2, left: pos.left - popoverWidth } :
				   placement == "right"        ? { top: pos.top - popoverHeight / 2, left: pos.left } :
				   placement == "bottom-left"  ? { top: pos.top,                     left: pos.left - popoverWidth } :
				   placement == "bottom-right" ? { top: pos.top,                     left: pos.left } :
				   placement == "top-left"     ? { top: pos.top - popoverHeight,     left: pos.left - popoverWidth} :
				   placement == "top-right"    ? { top: pos.top - popoverHeight,     left: pos.left } : 
				/* placement == "top" */         { top: pos.top - popoverHeight,     left: pos.left - popoverWidth / 2 };
		},

		applyPopoverPlacement: function (hotSpot, offset, placement) {
			var $popover = hotSpot.$popover,
			popoverWidth  = $popover[0].offsetWidth,
			popoverHeight = $popover[0].offsetHeight;
			
			// manually read margins because getBoundingClientRect includes difference
			var marginTop = parseInt($popover.css("margin-top"), 10),
			marginLeft = parseInt($popover.css("margin-left"), 10),
			marginBottom = parseInt($popover.css("margin-bottom"), 10),
			marginRight = parseInt($popover.css("margin-right"), 10);

			// we must check for NaN for ie 8/9
			if (isNaN(marginTop))    marginTop  = 0;
			if (isNaN(marginLeft))   marginLeft = 0;
			if (isNaN(marginBottom)) marginBottom = 0;
			if (isNaN(marginRight))  marginRight = 0;

			offset.top  += (marginTop  - marginBottom);
			offset.left += (marginLeft - marginRight);
			
			// $.fn.offset doesn't round pixel values
			// so we use setOffset directly with our own function B-0
			$.offset.setOffset($popover[0], $.extend({
				using: function (props) {
					$popover.css({
						top: Math.round(props.top),
						left: Math.round(props.left)
					})
				}
			}, offset), 0);
			
			
			var classes = ["top", "left", "bottom", "right", "top-left", "top-right", "bottom-left", "bottom-right"];
			for(var i = classes.length; i--;) {
				if(classes[i] == placement) {
					classes.splice(i, 1);
					break;
				}
			}
			for(var i = classes.length; i--;) {
				$popover.removeClass("imgl-popover-" + classes[i]);
			}
		  
			$popover.addClass("imgl-popover-" + placement);
		},

		hidePopover: function(hotSpot) {
			if( hotSpot.$popover && (hotSpot.$popover.hasClass("imgl-active") || hotSpot.$popover.hasClass(this.config.popoverShowClass)) ) {
				hotSpot.$popover.removeClass(this.config.popoverShowClass);
				
				hotSpot.$popover.css("z-index", "");
				hotSpot.$el.css("z-index", "");
				
				if( this.config.popoverHideClass ) {
					hotSpot.$popover.addClass(this.config.popoverHideClass);
					hotSpot.$popover.one(this.util().animationEvent(), $.proxy(function(e) {
						var $popover = $(e.target);
						if(!$popover.hasClass(this.config.popoverShowClass)) {
							$popover.removeClass(this.config.popoverHideClass);
							this.retachPopover($popover);
						}
						
					}, this) );
				} else {
					this.retachPopover(hotSpot.$popover);
				}
			}
		},

		retachPopover: function($popover) {
			$popover.removeClass("imgl-active");
			$popover.detach(); // little hack to force close all media queries
			$popover.get(0).offsetHeight;
			//$popover.css({ top: -9999, left: -9999, width: ""});
			
			$popover.appendTo(this.controls.$view);
			
			if(this.controls.$view.find(".imgl-popover.imgl-active").length == 0) {
				this.controls.$view.find(".imgl-hotspot, .imgl-popover").css("z-index", "");
				this.zindex = 4;
			}
		},

		liftupPopover: function(hotSpot) {
			if(this.hotSpots.length > 1 || hotSpot.popoverShow) {
				if(this.config.hotSpotBelowPopover) {
					hotSpot.$el.css("z-index", this.zindex+1);
					hotSpot.$popover.css("z-index", this.zindex+2);
				} else {
					hotSpot.$el.css("z-index", this.zindex+2);
					hotSpot.$popover.css("z-index", this.zindex+1);
				}
				this.zindex = this.zindex+2;
			}
		},

		updateLayout: function() {
			this.updateHotSpots();
			this.updatePopovers();
		},

		onAnimationStart: function(e) {
			this.updateLayout();
		},

		onLoad: function(e) {
			this.updateLayout();
			setTimeout($.proxy(this.showPopovers, this), 400);
		},

		onResize: function(e) {
			this.updateLayout();
		},

		onHotSpotClick: function(hotSpot, e) {
			if( !hotSpot.$popover || !hotSpot.$popover.hasClass("imgl-active") ) {
				e.stopImmediatePropagation(); // prevent close the popover
			}
			this.showPopover(hotSpot);
		},

		onHotSpotEnter: function(hotSpot, e) {
			this.showPopover(hotSpot);
		},
		
		onHotSpotTouchStart: function(hotSpot, e) {
			this.showPopover(hotSpot);
		},
		
		onHotSpotTouchEnd: function(hotSpot, e) {
			if($(e.target).is('a')) {
				// prevent delay and simulated mouse events
				e.preventDefault();
				
				// trigger the actual behavior we bound to the 'click' event
				e.target.click();
			}
		},

		onHotSpotLeave: function(hotSpot, e) {
			if(!hotSpot.$popover) {
				return;
			}
			
			var target = e.toElement || e.relatedTarget;
			if(hotSpot.$popover.has(target).length === 0 && !hotSpot.$popover.is(target) && !hotSpot.$el.is(target) ) {
				this.hidePopover(hotSpot);
			} else {
				hotSpot.$popover.one("mouseleave.imgl", $.proxy(this.onHotSpotLeave, this, hotSpot) );
			}
		},

		onPopoverClick: function(hotSpot, e) {
			if(!hotSpot.$popover) {
				return;
			}
			
			this.liftupPopover(hotSpot);
		},

		onPopoverHide: function(hotSpot, e) {
			if(!hotSpot.$popover) {
				return;
			}
			
			if(hotSpot.$popover.has(e.target).length === 0 || $(e.target).hasClass("imgl-close")) {
				if($(e.target).hasClass("imgl-close")) {
					e.stopImmediatePropagation();
				}
				
				this.hidePopover(hotSpot);
			}
		},

		onViewClick: function(e) {
			if( this.hotSpotSetup ) {
				var parentOffset = this.controls.$view.offset(),
				x = e.pageX - parentOffset.left,
				y = e.pageY - parentOffset.top,
				rect = this.controls.$view.get(0).getBoundingClientRect(),
				w = rect.right - rect.left,
				h = rect.bottom - rect.top,
				xfactor = x / w,
				yfactor = y / h;
				
				console.log("x:" + xfactor + ", y:" + yfactor);
			}
		},

		onViewKeyDown: function(e) {
			if(e.ctrlKey) {
				this.controls.$view.focus();
				this.hotSpotSetup = true;
			}
		},

		onViewKeyUp: function(e) {
			this.hotSpotSetup = false;
		},

		destroy: function() {
			if( !this.controls.$main ) {
				return;
			}
			
			this.resetHotSpots();
			this.resetHandlers();
			this.resetDOM();
		},

		util: function() {
			return this._util != null ? this._util : this._util = new Util();
		},
	};

	//=============================================
	// Init jQuery Plugin
	//=============================================
	/**
	* @param CfgOrCmd - config object or command name
	* @param CmdArgs - some commands may require an argument
	* List of methods:
	* $("#imagelinks").imagelinks("instance")
	* $("#imagelinks").imagelinks("resize")
	* $("#imagelinks").imagelinks("destroy")
	*/
	$.fn.imagelinks = function(CfgOrCmd, CmdArgs) {
		if (CfgOrCmd == "instance") {
			var container = $(this),
			instance = container.data(ITEM_DATA_NAME);
			
			if (!instance) {
				console.error("Calling 'instance' method on not initialized instance is forbidden");
				return;
			}
			
			
			return instance;
		}
		
		return this.each(function() {
			var container = $(this),
			instance = container.data(ITEM_DATA_NAME),
			options = $.isPlainObject(CfgOrCmd) ? CfgOrCmd : {};
			
			if (CfgOrCmd == "destroy") {
				if (!instance) {
					console.error("Calling 'destroy' method on not initialized instance is forbidden");
				}

				container.removeData(ITEM_DATA_NAME);
				instance.destroy();

				return;
			}
			
			if (CfgOrCmd == "resize") {
				if (!instance) {
					console.error("Calling 'resize' method on not initialized instance is forbidden");
				}

				this.updateLayout();

				return;
			}
			
			if (instance) {
				var config = $.extend({}, instance.config, options);
				instance.init(container, config);
			} else {
				var config = $.extend({}, ImageLinks.prototype.defaults, options);
				instance = new ImageLinks(container, config);
				container.data(ITEM_DATA_NAME, instance);
			}
		});
	}
})(window.jQuery);