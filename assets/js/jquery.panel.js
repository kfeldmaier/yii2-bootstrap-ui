	/**
	 * The Panel plugin extends the Bootstrap 3 Panel with additional functionality
	 * like collapse and xhr content.
	 * 
	 * <h3>Options</h3>
	 * <table>
	 *   <tr>
	 *     <th>Name</th>
	 *     <th>Type</th>
	 *     <th>Description</th>
	 *   </tr>
	 *   <tr>
	 *     <td>collapsible</td>
	 *     <td>Boolean</td>
	 *     <td>When true, adds an chevron icon to the header to toggle between collapsed and uncollapsed state.</td>
	 *   </tr><tr>
	 *     <td>collapsed</td>
	 *     <td>null|Boolean</td>
	 *     <td>Defines the initial state of the panel. Ignored if <code>collapsible</code> is <code>false</code>. If no <code>collapsed</code> is defined,
	 *		but an url is defined, the panel starts collapsed and uncollapses as soon as any content is received.</td>
	 *   </tr><tr>
	 *     <td>url</td>
	 *     <td>string</td>
	 *     <td>An url to load contents via XHR.</td>
	 *   </tr><tr>
	 *     <td>autorefresh</td>
	 *     <td>null|number</td>
	 *     <td>Interval in seconds to reload content via given <code>url</code>.</td>
	 *   </tr>
	 * </table>
	 * 
	 * <h3>Usage Example</h3>
	 * <p>Define a Bootstrap Panel anywhere in the page.</p>
	 * <p><strong>Example 1</strong> With default configuration:
	 * <code>
	 * <script type="text/javascript">
	 *   $(function(){
	 *		$('.panel').panel();
	 *   })
	 * </script>
	 * </code></p>
	 * <p><strong>Example 2</strong> With full configuration:
	 * <code>
	 * <script type="text/javascript">
	 *   $(function(){
	 *		$('.panel').panel({
	 *			url: '/user/profile?id=0815',
	 *			collapsible: true,
	 *			collapsed: false,
	 *			autorefresh: 10
	 *		});
	 *   })
	 * </script>
	 * </code></p>
	 * <p>Instead of configuring the plugin with javascript, all parameters
	 * can be passed using data- attributes.</p>
	 * <code>
	 * <div class="panel panel-default" data-url="/user/profile?id=0815" data-collapsible="true" data-collapsed="false">
	 *   (...)
	 * </div>
	 * </code>
	 * <script type="text/javascript">
	 *   $(function(){
	 *		$('.panel').panel();
	 *   })
	 * </script>
	 * </p>
	 * 
	 * <p>If the <code>url</code> is specified, there are some special behaviours to consider:
	 *   <ul>
	 *     <li>Define an element inside the footer, give it the class 'panel-reload' and it will be used as reload button.</li>
	 *   </ul>
	 * </p>
	 *
	 * <h3>Events</h3>
	 * <table>
	 *   <tr>
	 *     <th>Event type</th>
	 *     <th>Description</th>
	 *     <th>Event Properties</th>
	 *   </tr><tr>
	 *     <td>panel-complete</td>
	 *     <td>The event type of the completion of the panel. If the panel uses an url for loading contents using XHR, loading will appear right after this event.</td>
	 *     <td>collapsed (true|false)</td>
	 *   </tr><tr>
	 *     <td>panel-abort</td>
	 *     <td>The event type of the abort of the current XHR request.</td>
	 *     <td></td>
	 *   </tr><tr>
	 *     <td>panel-error-content</td>
	 *     <td>The event when a XHR request fails. </td>
	 *     <td>jqXHR, textStatus, errorThrown</td>
	 *   </tr><tr>
	 *     <td>panel-before-content</td>
	 *     <td>The event right after a successfull XHR request before the content is updated</td>
	 *     <td>data (HTML)</td>
	 *   </tr><tr>
	 *     <td>panel-after-content</td>
	 *     <td>The event right after a successfull XHR request after the content has been updated</td>
	 *     <td>data (HTML)</td>
	 *   </tr><tr>
	 *     <td>panel-idle-changed</td>
	 *     <td>The event whenever a XHR navigation target changes its idle state. Idle means loading.</td>
	 *     <td>idle (true|false)</td>
	 *   </tr>
	 * </table>
	 *
	 * jQuery Panel
	 * Copyright 2015 Kai Feldmaier (kai.feldmaier@gmail.com)
	 * Licensed under MIT (http://opensource.org/licenses/MIT)
	 */
( function( $ ) {
					 
	var Panel = function( element, options )
	{
		var __e = $(element);
		var __o = this;
					
		/**
		 * @private
		 * Settings NS 
		 */
		var __s = $.extend({
						debug: false,
						url: null,
						collapsible: true,
						collapsed: null,
						autorefresh: null,
						refreshable: true
					}, options || {} );
					
		/**
		 * @private
		 * Class Variables NS 
		 */
		var __c = {
			body: null,
			header: null,
			collapsed: null,
			collapseBtn: null,
			collapseIcon: null,
			reloadBtn: null,
			idleIcon: null,
			idle: false,
			autorefreshIv: null
		};

		this.abort = function()
		{
			this.debug('Panel->abort');
			if (!__c.xhr)
				return;

			__c.xhr.abort();
			__trigger('panel-abort');

			__setIdle(false);
		};

		/**
		 * Check either plugin is loading or not
		 */
		this.isIdle = function()
		{
			return __c.idle;
		};

		this.loadContents = function()
		{
			if (this.isIdle())
				this.abort();

			this.debug('Panel->loadContents');

			__setIdle(true);

			__c.xhr = $.ajax({
				url: __s.url,
				dataType: 'html',
				context: this,
				error: function(jqXHR, textStatus, errorThrown)
				{
					__o.debug('Panel->loadContents', jqXHR, textStatus, errorThrown);
					__setIdle(false);
					__c.xhr = null;
					__trigger('panel-error-content', {jqXHR:jqXHR, textStatus:textStatus, errorThrown:errorThrown});
					__c.body.html(textStatus +'<br>\n' +errorThrown);
				},
				success: function(data)
				{
					__o.debug('Panel->loadContents', data);
					__setIdle(false);
					__c.xhr = null;
					__trigger('panel-before-content', {data:data});
					__c.body.html(data);
					if (!__o.isCollapsedDefined())
						__o.collapse(false);
					__trigger('panel-after-content', {data:data});
					__e.scrollTop();
					__o.refreshInterval();
				}
			});
		};

		this.refreshInterval = function()
		{
			if (isNaN(__s.autorefresh) || (!isNaN(__s.autorefresh) && __s.autorefresh <= 1))
				return;
			
			clearInterval(__s.autorefreshIv);

			__c.autorefreshIv = setTimeout(function(){
				__o.loadContents();
			}, __s.autorefresh*1000);
		};

		/*
		 * PROTECTED  
		 */
		this.isCollapsed = function()
		{
			return __c.collapsed;
		};

		/*
		 * PROTECTED  
		 */
		this.isCollapsedDefined = function()
		{
			return !(__s.collapsed === null);
		};

		/**
		 *  
		 */
		this.collapse = function(collapse)
		{
			if (collapse == __c.collapsed)
				return;

			this.debug('Panel->collapse', collapse);

			__c.collapsed = collapse;

			if (!__c.body)
				return;

			if (__c.collapsed)
			{
				__c.body.slideUp();
				if (__c.collapseIcon && __c.collapseIcon.hasClass('glyphicon-chevron-up'))
					__c.collapseIcon.removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
			} else {
				__c.body.slideDown();
				if (__c.collapseIcon && __c.collapseIcon.hasClass('glyphicon-chevron-down'))
					__c.collapseIcon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
			}
		};
		
		this.debug = function()
		{
			if (!__s.debug || !window.console || !window.console.log)
				return;

			window.console.log(arguments);
		};	

		/**
		 * @private
		 */
		var __setIdle = function(value)
		{
			if (value === __c.idle)
				return;

			__c.idle = value;

			if (__c.idle)
			{
				if (__c.reloadBtn)
					__c.reloadBtn.attr("disabled", "disabled");

				if (__c.body)
					__c.body.fadeTo("fast" , .5);

				if (__c.idleIcon)
					__c.idleIcon.show();
			} else {
				if (__c.reloadBtn)
					__c.reloadBtn.removeAttr("disabled");

				if (__c.body)
					__c.body.fadeTo("slow" , 1);

				if (__c.idleIcon)
					__c.idleIcon.hide();
			}

			__o.debug('Panel->__setIdle', __c.idle);

			__trigger('panel-idle-changed', {idle:__c.idle});
		};

		/**
		 * @private
		 */
		var __trigger = function(type, args)
		{
			var event = jQuery.Event(type);

			for (var key in args)
				event[key] = args[key];

			__e.trigger(event); //type, args);
		};

		/**
		 *  Constructor
		 */
		var __construct = function()
		{
			// all settings can be overridden 
			// using data- attributes
			for (var key in __s)
				if (__e.attr('data-'+key) !== undefined )
					__s[key] = __e.attr('data-'+key);

			__c.body = __e.find('.panel-body').first();

			if (__o.isCollapsedDefined())
			{
				__c.collapsed = __s.collapsed;
			} else {
				__c.collapsed = __s.url === null ? false : true;
			}	

			if (__s.collapsible)
			{			
				__c.collapseBtn = __e.find('.btn-panel-toggle-collapse').first();
				__c.collapseIcon = __e.find('.panel-collapse-icon').first();
				
				if (__c.collapseBtn)
					__c.collapseBtn.on('click', function(event){
						event.preventDefault();
						if (__o.isCollapsed())
						{
							__o.collapse(false);
						} else {
							__o.collapse(true);
						}
					});
			}

			__trigger('panel-complete', {collapsed:__o.isCollapsed()});

			if (__s.url)
			{
				__c.idleIcon = __e.find('.panel-idle-icon').first();
				if (__c.idleIcon)
					__c.idleIcon.hide();

				__c.reloadBtn = __e.find('.btn-panel-reload');
				
				if (__c.reloadBtn)
					__c.reloadBtn.on('click', function(event){
						event.preventDefault();
						__o.loadContents();
					});

				__o.loadContents();
			}
				
		}
		
		/**
		 *  @private
		 */
		var __debug = function ( $obj ) 
		{
			if ( window.console && window.console.log )
				window.console.log(arguments);
		};
		
		__construct();

	};

	$.fn.panel = function (options)
	{
		return this.each(function()
		{
			var e = $(this);
			if (e.data('panel')) 
				return;
			var p = new Panel(this, options);
			e.data('panel', p);
		});
	};
	
})( jQuery );
