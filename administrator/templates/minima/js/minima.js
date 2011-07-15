/** 
 * @package			Minima
 * @author			Marco Barbosa
 * @contributors	Henrik Hussfelt
 * @copyright		Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license			GNU General Public License version 2 or later; see LICENSE.txt
 */

    // EXTRAS
    // ==================================================
    // outerClick function
    (function(){var b;var a=function(f){var d=$(f.target);var c=d.getParents();b.each(function(g){var e=g.element;if(e!=d&&!c.contains(e)){g.fn.call(e,f)}})};Element.Events.outerClick={onAdd:function(c){if(!b){document.addEvent("click",a);b=[]}b.push({element:this,fn:c})},onRemove:function(c){b=b.filter(function(d){return d.element!=this||d.fn!=c},this);if(!b.length){document.removeEvent("click",a);b=null}}}})();

    // switchClass function
    Element.implement('switchClass', function(a, b){ var toggle = this.hasClass(a); this.removeClass(toggle ? a : b).addClass(toggle ? b : a); return this; });

    // extending Selector for a visible boolean
    $extend(Selectors.Pseudo,{visible:function(){if(this.getStyle("visibility")!="hidden"&&this.isVisible()&&this.isDisplayed()){return this}}});

    // toggle for reveal or dissolve
    Element.implement('toggleReveal', function(el, options) {
        return el.isDisplayed() ? el.dissolve(options) : el.reveal(options);
    });
    
var 
    // PLUGINS
    // ==================================================
    // ScrollSpy by David Walsh (http://davidwalsh.name/js/scrollspy)
    ScrollSpy=new Class({Implements:[Options,Events],options:{container:window,max:0,min:0,mode:"vertical"},initialize:function(a){this.setOptions(a);this.container=document.id(this.options.container);this.enters=this.leaves=0;this.inside=false;this.listener=function(d){var b=this.container.getScroll(),c=b[this.options.mode=="vertical"?"y":"x"];if(c>=this.options.min&&(this.options.max==0||c<=this.options.max)){if(!this.inside){this.inside=true;this.enters++;this.fireEvent("enter",[b,this.enters,d])}this.fireEvent("tick",[b,this.inside,this.enters,this.leaves,d])}else{if(this.inside){this.inside=false;this.leaves++;this.fireEvent("leave",[b,this.leaves,d])}}this.fireEvent("scroll",[b,this.inside,this.enters,this.leaves,d])};this.addListener()},start:function(){this.container.addEvent("scroll",this.listener.bind(this))},stop:function(){this.container.removeEvent("scroll",this.listener.bind(this))},addListener:function(){this.start()}});

    // Iphone checkboxes by David Walsh (http://davidwalsh.name/iphone-checkboxes-mootools)
    //(function(a){this.IPhoneCheckboxes=new Class({Implements:[Options],options:{checkedLabel:"ON",uncheckedLabel:"OFF",background:"#fff",containerClass:"iPhoneCheckContainer",labelOnClass:"iPhoneCheckLabelOn",labelOffClass:"iPhoneCheckLabelOff",handleClass:"iPhoneCheckHandle",handleBGClass:"iPhoneCheckHandleBG",handleSliderClass:"iPhoneCheckHandleSlider",elements:"input[type=checkbox].check"},initialize:function(b){this.setOptions(b);this.elements=$$(this.options.elements);this.elements.each(function(c){this.observe(c)},this)},observe:function(e){e.set("opacity",0);var d=new Element("div",{"class":this.options.containerClass}).inject(e.getParent());e.inject(d);var g=new Element("div",{"class":this.options.handleClass}).inject(d);var c=new Element("div",{"class":this.options.handleBGClass,style:this.options.background}).inject(g);var i=new Element("div",{"class":this.options.handleSliderClass}).inject(g);var b=new Element("label",{"class":this.options.labelOffClass,text:this.options.uncheckedLabel}).inject(d);var f=new Element("label",{"class":this.options.labelOnClass,text:this.options.checkedLabel}).inject(d);var h=d.getSize().x-39;e.offFx=new Fx.Tween(b,{property:"opacity",duration:200});e.onFx=new Fx.Tween(f,{property:"opacity",duration:200});d.addEvent("mouseup",function(){var l=!e.checked;var j=(l?h:0);var k=(l?34:0);c.hide();new Fx.Tween(g,{duration:100,property:"left",onComplete:function(){c.setStyle("left",k).show()}}).start(j);if(l){e.offFx.start(0);e.onFx.start(1)}else{e.offFx.start(1);e.onFx.start(0)}e.set("checked",l)});if(e.checked){b.set("opacity",0);f.set("opacity",1);g.setStyle("left",h);c.setStyle("left",34)}else{f.set("opacity",0);c.setStyle("left",0)}}})})(document.id);

    // MINIMA CLASSES
    // ==================================================
	// MinimaPanelClass by Henrik Hussfelt, Marco Barbosa
	MinimaPanelClass=new Class({Implements:[Options],panelStatus:{"true":"active","false":"inactive"},panel:null,options:{prev:"",next:"",panelList:"",panelPage:"",panelWrapper:"",toIncrement:0,increment:900},maxRightIncrement:null,panelSlide:null,numberOfExtensions:null,initialize:function(a){this.setOptions(a);this.panel=new Fx.Slide.Mine(this.options.panelWrapper,{mode:"vertical",transition:Fx.Transitions.Pow.easeOut}).hide();if(this.options.next){this.panelSlide=new Fx.Tween(this.options.panelList,{duration:500,transition:"back:in:out"});this.numberOfExtensions=this.options.panelList.getChildren("li").length;this.options.panelList.setStyle("width",Math.round(this.numberOfExtensions/9)*this.options.increment);this.maxRightIncrement=-Math.ceil(this.options.panelPage.getChildren().length*this.options.increment-this.options.increment);this.showButtons()}},helloWorld:function(){alert("cool")},doPrevious:function(){if(this.options.toIncrement<0){this.options.next.show();this.options.toIncrement+=this.options.increment;this.panelSlide.pause();this.panelSlide.start("margin-left",this.options.toIncrement);this.options.panelPage.getFirst(".current").removeClass("current").getPrevious("li").addClass("current");this.showButtons()}},doNext:function(){if(this.options.toIncrement>this.maxRightIncrement){this.options.prev.show();this.options.toIncrement-=this.options.increment;this.panelSlide.pause();this.panelSlide.start("margin-left",this.options.toIncrement);this.options.panelPage.getFirst(".current").removeClass("current").getNext("li").addClass("current");this.showButtons()}},changeToPage:function(b){var a=b.id.substr("panel-pagination-".length);this.panelSlide.pause();this.options.toIncrement=Math.ceil(0-this.options.increment*a);this.panelSlide.start("margin-left",this.options.toIncrement);this.options.panelPage.getFirst(".current").removeClass("current");b.addClass("current");this.showButtons()},showButtons:function(){if(this.options.toIncrement==0){this.options.prev.hide()}else{this.options.prev.show()}if(this.options.toIncrement==this.maxRightIncrement){this.options.next.hide()}else{this.options.next.show()}}}),

	// MinimaTabsClass by Henrik Hussfelt, Marco Barbosa
    MinimaTabsClass=new Class({Implements:[Options],options:{},elements:{tabs:null,content:null},initialize:function(a,b){this.setOptions(a);this.elements=b},moveTabs:function(a){a.inject($("content"),"top")},showFirst:function(){this.elements.content.pick().removeClass("hide")},hideAllContent:function(){this.elements.content.addClass("hide")},addTabsAction:function(){var a=this;this.elements.tabs.each(function(c,b){c.addEvents({click:function(d){d.stop();a.elements.tabs.removeClass("active");a.elements.tabs[b].addClass("active");a.elements.content.addClass("hide");a.elements.content[b].removeClass("hide")}})})}});

    // MinimaClass by Henrik Hussfelt, Marco Barbosa
    MinimaClass=new Class({Implements:[Options],options:{},elements:{systemMessage:null,jformTitle:null},minima:null,initialize:function(a,b){this.minima=document.id(this.options.minima)||document.id("minima");this.setOptions(a);this.elements=b},showSystemMessage:function(){if(this.elements.systemMessage&&this.elements.systemMessage.getElement("ul li:last-child")){var b=this,a=new Element("a",{href:"#",id:"hide-system-message",html:"hide",events:{click:function(c){b.elements.systemMessage.dissolve({duration:"short"})}}});this.elements.systemMessage.show().getElement("ul li:last-child").adopt(a)}},dynamicTitle:function(){var b=this.minima.getElement(".pagetitle h2"),a=$("jform_alias"),c=this;if(this.elements.jformTitle.get("value")!=""){b.set("html",this.elements.jformTitle.get("value"))}this.elements.jformTitle.addEvent("keyup",function(d){if(c.elements.jformTitle.get("value")!=""){b.set("html",this.get("value"))}if(c.minima.hasClass("no-id")&&a){a.set("value",this.get("value").standardize().replace(/\s+/g,"-").replace(/[^-\w]+/g,"").toLowerCase())}})},makeRowsClickable:function(){var a=$$("input[name=checkall-toggle]");a.addEvent("click",function(){var b=$$(".adminlist tbody tr");b.toggleClass("selected")});$$(".adminlist tbody tr input[type=checkbox]").each(function(b){var c=b.getParent("tr"),d=$$("input[name=boxchecked]");b.addEvent("click",function(e){e&&e.stopPropagation();if(b.checked){c.addClass("selected")}else{c.removeClass("selected")}});c.addEvent("click",function(){if(b.checked){b.set("checked",false);d.set("value",0)}else{b.set("checked",true);d.set("value",1)}b.fireEvent("click")})});$$(".adminlist th img").getParent("th").addClass("active")}});

    // MinimaToolbarClass by Marco Barbosa
    MinimaToolbarClass=new Class({Implements:[Options],options:{toolbar:$("toolbar"),toolbarElements:$$(".toolbar-list li a"),label:null},bulkActionsArray:new Array(),bulkNonActionsArray:new Array(),minima:null,initialize:function(a){this.minima=document.id(this.options.minima)||document.id("minima");this.setOptions(a)},doToolbar:function(){this.sortItems();this.fixToolbar()},sortItems:function(){var a=this;if(this.options.toolbarElements.length){this.options.toolbarElements.each(function(b){if(b.get("onclick")!=null&&b.get("onclick").contains("if")){a.bulkActionsArray.push(b.getParent("li"))}else{if(b.get("class")!="divider"){a.bulkNonActionsArray.push(b.getParent("li"))}}})}},fixToolbar:function(){var f=this;if(this.bulkActionsArray.length>1){var d=new Element("ul#actions").hide(),c=new Element("li",{id:"bulkActions",events:{click:function(g){this.toggleReveal(d,{duration:200,styles:["border"]});$$(f.minima.getElement("#bulkActions > a:first-child"),f).switchClass("active","inactive")},outerClick:function(){d.dissolve({duration:250});f.minima.getElement("#bulkActions > a:first-child").set("class","inactive")}}}),a=new Element("a[text= "+this.options.label+"]"),b=new Element("span.arrow");this.bulkActionsArray=this.bulkActionsArray.sort(function(h,g){if(h.get("text").toLowerCase()<g.get("text").toLowerCase()){return -1}if(h.get("text").toLowerCase()==g.get("text").toLowerCase()){return 0}return 1});this.bulkActionsArray.each(function(h,g){d.grab(h)});var e=($("toolbar-new"))?"ul > li#toolbar-new":"ul > li";c.inject($("toolbar").getElement(e),"after");c.adopt(a.grab(b),d)}}});

    // MinimaFilterbarClass by Marco Barbosa
    MinimaFilterBarClass=new Class({Implements:[Options],options:{},minima:null,filterStatusLabels:{"true":"Show search & filters","false":"Hide search & filters"},isActive:false,pageTitle:"",elements:{filterBar:null},filterSlide:null,filterAnchor:null,initialize:function(a,b,c){this.minima=$(this.options.minima)||$("minima");this.setOptions(a);this.elements=b;if(c.length){this.setLabelsLanguage(c.hideFilter,c.showFilter)}},setLabelsLanguage:function(b,a){if(b.length&&a.length){this.filterStatusLabels["true"]=b;this.filterStatusLabels["false"]=a}},createSlideElements:function(){var a=this;this.filterSlide=new Fx.Slide(this.elements.filterBar).hide();this.filterAnchor=new Element("a",{href:"#minima",id:"open-filter",html:a.filterStatusLabels["false"],events:{click:function(c){var b=$("filter_search");c.stop();a.filterSlide.toggle();this.toggleClass("active");if(this.hasClass("active")&&b){b.focus()}if($("content-top").hasClass("fixed")){window.scrollTo(0,0)}}}})},fixAnchor:function(){this.minima.getElement(".pagetitle").grab(this.filterAnchor)},onFilterSelected:function(){var a=this;filterBar.getElements("input, select").each(function(b){var c=b.get("value");if(c){a.isActive=true;a.pageTitle+=(b.get("tag").toLowerCase()=="select")?b.getElement("option:selected").get("html").toLowerCase()+" ":a.pageTitle+=c.toLowerCase()+" ";a.addFiltersToTitle()}})},addFiltersToTitle:function(){var a=minima.getElement(".pagetitle h2");if(this.pageTitle.length){a.set("html",a.get("html")+"<em>( "+this.pageTitle+")</em>")}},doFilterBar:function(){var a=this;this.createSlideElements();this.fixAnchor();this.onFilterSelected();if(this.isActive){this.filterSlide.show();this.filterAnchor.set("html",this.filterStatusLabels[this.filterSlide.open]).addClass("active")}this.filterSlide.addEvent("complete",function(){a.filterAnchor.set("html",a.filterStatusLabels[a.filterSlide.open])});this.elements.filterBar.show()}})

;


window.addEvent('domready', function() {

    // instanciate
    //var chx = new IPhoneCheckboxes();

    // Initiate some global variables
    // -------------------------------     
    var 
        // get the language strings
        language        = MooTools.lang.get('Minima');
        // DOM variables                
        contentTop      = $('content-top'),
        toolbar         = $('toolbar'),
        topHead         = $('tophead'),
        minima          = $('minima'),
        subMenu         = $('submenu'),
        itemForm        = $('item-form'),
        filterBar       = $('filter-bar'),
        // Initiate MimimaClass
        Minima          = new MinimaClass({},{
                                  systemMessage : $('system-message'),
                                  jformTitle    : $('jform_title')
                              }),
        // Initiate MinimaToolbar
        MinimaToolbar   = new MinimaToolbarClass({
                                  'toolbar'         : toolbar, // toolbar parent
                                  'toolbarElements' : minima.getElements('.toolbar-list li a'), // list of the anchor elements
                                  'label'           : language['actionBtn']
                              }),
        MinimaFilterBar = new MinimaFilterBarClass({}, {
                                  'filterBar' : filterBar
                              }, {
                                  'hideFilter' : language['hideFilter'],
                                  'showFilter' : language['showFilter']
                              })
    ;

    // ------------------------------- 

    // TRIGGERS
    // =============================    
    // smooth scroll when clicking "back to top"
    new Fx.SmoothScroll({
        links: '#topLink'
    });    

    // fix the filterbar
    if (filterBar) {        
        MinimaFilterBar.doFilterBar();
    }

    // Show system message (if applicable)
    Minima.showSystemMessage();

    // make title dynamic if we have one
    if ($('jform_title')) {
        Minima.dynamicTitle();
    };

    // Make whole row clickable, if there are any    
    if (minima.getElements('.adminlist').length) {
    	Minima.makeRowsClickable();
    };

    // TOOLBAR
    // =============================
    // fix the toolbar
    MinimaToolbar.doToolbar();

    // show it afterwards
    if (toolbar) {
        toolbar.show();
    };

    // TABS
    // =============================
    if (subMenu && itemForm) {
        
        // Start tabs actions, create instances of class
    	var MinimaTabsHorizontal = new MinimaTabsClass({}, {
                'tabs'    : subMenu.getElements('a'), 
                'content' : itemForm.getChildren('div')
            }),
        MinimaTabsVertical = new MinimaTabsClass({}, {
                'tabs'    : minima.getElements('.vertical-tabs a'), 
                'content' : $('tabs').getChildren('.panelform')
            });

        if (subMenu.hasClass('out')) {
            MinimaTabsHorizontal.moveTabs(subMenu);   
        }

    	// Add tabs for horizontal submenu
        // Hide all content elements
        MinimaTabsHorizontal.hideAllContent();
        // Show the first
        MinimaTabsHorizontal.showFirst();
        // Add onClick
        MinimaTabsHorizontal.addTabsAction();

        // Add tabs for vertical menu
        // Hide all content elements
        MinimaTabsVertical.hideAllContent();
        // Show the first
        MinimaTabsVertical.showFirst();
        // Add onClick
        MinimaTabsVertical.addTabsAction();
    };    

    // SCROLLING
    // =============================
    // fixed content-box header when scrolling    
    var scrollSize = document.getScrollSize().y - document.getSize().y;    
    
    /* scrollspy instance */    
    new ScrollSpy({
        // the minimum ammount of scrolling before it triggers
        min: 200, 
        onEnter: function() {            
            // we are in locked mode, must fix positioning
            if (scrollSize > 400) {
                if (document.body.hasClass('locked')) {
                    contentTop.setStyle('left', (topHead.getSize().x - 1140) / 2);
                };
                contentTop.setStyle('width', topHead.getSize().x - 40).addClass('fixed');
            };
        },
        onLeave: function() {
            if (scrollSize > 400) {
                contentTop.removeClass('fixed');
                if(document.body.hasClass('locked')) {
                    contentTop.setStyle('width', '100%');
                };
            };
        }
    }); 
    
    // PANEL TAB
    // ==================================================
    // tabs wrapper
    var tabsWrapper = $('panel-wrapper'),
        extra       = $('more')
        extraLists  = $('list-content'),
        openPanel   = $('panel-tab'),
        listWrapper = $('list-wrapper');

    if (tabsWrapper) {

	    // Fixing wrapper bug
	    Fx.Slide.Mine = new Class({
	        Extends: Fx.Slide,
	        initialize: function(el, options) {
	            this.parent(el, options);
	            this.wrapper = this.element.getParent();
	        }
	    });

		// Create a Panel instance
		var Panel = new MinimaPanelClass({
				panelWrapper: $('panel-wrapper'),
				prev: $('prev'),
				next: $('next'),
				panelList: $('panel-list'),
				panelPage: $('panel-pagination')
		});

		// Setup click event for previous
		$('prev').addEvent('click', function() {
			Panel.doPrevious();
		});
		// Setup click event for previous
		$('next').addEvent('click', function() {
			Panel.doNext();
		});

		// Fix panel pagination
		$('panel-pagination').getChildren("li").addEvent('click', function() {
			// Send ID to changepage as this contains pagenumber
			Panel.changeToPage(this);
		});

        // Open the panel slide
        openPanel.addEvents({
            'click': function(){                
                //minima.getElements("#shortcuts .parent").getChildren('.sub').dissolve({duration: 200}).removeClass('hover');
                minima.getElements("#shortcuts .parent").removeClass('hover');
        		Panel.panel.toggle();
            }/*,
            'outerClick' : function(){
                //Panel.panel.slideOut();
            }*/
        });

        // change status on toggle complete
        Panel.panel.addEvent('complete', function() {
            openPanel.set('class', Panel.panelStatus[Panel.panel.open]);
        });

        // slide up panel when clicking a link
        minima.getElements('#panel-list li').addEvent('click', function(){            
            Panel.panel.toggle();
        });

    }; // end of if(tabsWrapper)


    // dropdown menu
    extra.addEvent('click', function(){            
        this.switchClass('active','inactive');            
        //extraLists.toggle();
        this.toggleReveal(extraLists, {heightOverride: '155',duration: 250});
    });

    var hideLists = function() {
        extra.set('class','inactive');
        listWrapper.removeClass('active');
        extraLists.dissolve();            
    };

    // turn off list when click outside
    listWrapper.addEvent('outerClick', function(){
        hideLists();
    });        

    // turn off list when clicking a link
    extraLists.getElements("a").addEvent('click', function(){
        hideLists();
    });

    minima.getElements('#shortcuts .parent').each(function(li) {             
        // add events to the list elements
        li.addEvents({
           'click' : function() {                    
                // show or hide when click on the arrow                    
                this.toggleReveal(this.getChildren('.sub')[0]).toggleClass('hover');                    
                this.getElement('a').toggleClass('hover');
           },
           'outerClick' : function() {
                // hide when clicking outside or on a different element                    
                this.getChildren('.sub').dissolve({duration: 200}).removeClass('hover');
                this.getElement('a').removeClass('hover');
           }
        });            
    });

    // dashboard icons actions
    if (minima.hasClass('com_cpanel')) {        
        minima.getElements('.box-icon').addEvent('click', function() {        
            this.toggleClass('hover').getParent('nav').toggleReveal(this.getNext('ul'));
        });
    }

});
