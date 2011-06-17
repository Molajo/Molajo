/** 
 * @package			Minima
 * @author			Marco Barbosa
 * @contributors	Henrik Hussfelt
 * @copyright		Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license			GNU General Public License version 2 or later; see LICENSE.txt
 */

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
    
    // ElementFilter by David Walsh (http://davidwalsh.name/plugin-element-filter)
	//ElementFilter=new Class({Implements:[Options,Events],options:{cache:true,caseSensitive:false,ignoreKeys:[13,27,32,37,38,39,40],matchAnywhere:true,property:"text",trigger:"keyup",onStart:$empty,onShow:$empty,onHide:$empty,onComplete:$empty},initialize:function(c,b,a){this.setOptions(a);this.observeElement=document.id(c);this.elements=$$(b);this.matches=this.elements;this.misses=[];this.listen();},listen:function(){this.observeElement.addEvent(this.options.trigger,function(a){if(this.observeElement.value.length){if(!this.options.ignoreKeys.contains(a.code)){this.fireEvent("start");this.findMatches(this.options.cache?this.matches:this.elements);this.fireEvent("complete");}}else{this.findMatches(this.elements,false);}}.bind(this));},findMatches:function(f,b){var e=this.observeElement.value;var a=this.options.matchAnywhere?e:"^"+e;var g=this.options.caseSensitive?"":"i";var c=new RegExp(a,g);var d=[];f.each(function(i){var h=(b==undefined?c.test(i.get(this.options.property)):b);if(h){if(!i.retrieve("showing")){this.fireEvent("show",[i]);}d.push(i);i.store("showing",true);}else{if(i.retrieve("showing")){this.fireEvent("hide",[i]);}i.store("showing",false);}return true;}.bind(this));return d;}}),

    // ScrollSpy by David Walsh (http://davidwalsh.name/js/scrollspy)
    ScrollSpy=new Class({Implements:[Options,Events],options:{container:window,max:0,min:0,mode:"vertical"},initialize:function(a){this.setOptions(a);this.container=document.id(this.options.container);this.enters=this.leaves=0;this.inside=false;this.listener=function(d){var b=this.container.getScroll(),c=b[this.options.mode=="vertical"?"y":"x"];if(c>=this.options.min&&(this.options.max==0||c<=this.options.max)){if(!this.inside){this.inside=true;this.enters++;this.fireEvent("enter",[b,this.enters,d])}this.fireEvent("tick",[b,this.inside,this.enters,this.leaves,d])}else{if(this.inside){this.inside=false;this.leaves++;this.fireEvent("leave",[b,this.leaves,d])}}this.fireEvent("scroll",[b,this.inside,this.enters,this.leaves,d])};this.addListener()},start:function(){this.container.addEvent("scroll",this.listener.bind(this))},stop:function(){this.container.removeEvent("scroll",this.listener.bind(this))},addListener:function(){this.start()}});

    // Iphone checkboxes by David Walsh (http://davidwalsh.name/iphone-checkboxes-mootools)
    //(function(a){this.IPhoneCheckboxes=new Class({Implements:[Options],options:{checkedLabel:"ON",uncheckedLabel:"OFF",background:"#fff",containerClass:"iPhoneCheckContainer",labelOnClass:"iPhoneCheckLabelOn",labelOffClass:"iPhoneCheckLabelOff",handleClass:"iPhoneCheckHandle",handleBGClass:"iPhoneCheckHandleBG",handleSliderClass:"iPhoneCheckHandleSlider",elements:"input[type=checkbox].check"},initialize:function(b){this.setOptions(b);this.elements=$$(this.options.elements);this.elements.each(function(c){this.observe(c)},this)},observe:function(e){e.set("opacity",0);var d=new Element("div",{"class":this.options.containerClass}).inject(e.getParent());e.inject(d);var g=new Element("div",{"class":this.options.handleClass}).inject(d);var c=new Element("div",{"class":this.options.handleBGClass,style:this.options.background}).inject(g);var i=new Element("div",{"class":this.options.handleSliderClass}).inject(g);var b=new Element("label",{"class":this.options.labelOffClass,text:this.options.uncheckedLabel}).inject(d);var f=new Element("label",{"class":this.options.labelOnClass,text:this.options.checkedLabel}).inject(d);var h=d.getSize().x-39;e.offFx=new Fx.Tween(b,{property:"opacity",duration:200});e.onFx=new Fx.Tween(f,{property:"opacity",duration:200});d.addEvent("mouseup",function(){var l=!e.checked;var j=(l?h:0);var k=(l?34:0);c.hide();new Fx.Tween(g,{duration:100,property:"left",onComplete:function(){c.setStyle("left",k).show()}}).start(j);if(l){e.offFx.start(0);e.onFx.start(1)}else{e.offFx.start(1);e.onFx.start(0)}e.set("checked",l)});if(e.checked){b.set("opacity",0);f.set("opacity",1);g.setStyle("left",h);c.setStyle("left",34)}else{f.set("opacity",0);c.setStyle("left",0)}}})})(document.id);

    // MINIMA CLASSES
    // ==================================================

	// MinimaPanelClass by Henrik Hussfelt, Marco Barbosa
	MinimaPanelClass=new Class({Implements:[Options],panelStatus:{"true":"active","false":"inactive"},panel:null,options:{prev:"",next:"",panelList:"",panelPage:"",panelWrapper:"",toIncrement:0,increment:900},maxRightIncrement:null,panelSlide:null,numberOfExtensions:null,initialize:function(a){this.setOptions(a);this.panel=new Fx.Slide.Mine(this.options.panelWrapper,{mode:"vertical",transition:Fx.Transitions.Pow.easeOut}).hide();if(this.options.next){this.panelSlide=new Fx.Tween(this.options.panelList,{duration:500,transition:"back:in:out"});this.numberOfExtensions=this.options.panelList.getChildren("li").length;this.options.panelList.setStyle("width",Math.round(this.numberOfExtensions/9)*this.options.increment);this.maxRightIncrement=-Math.ceil(this.options.panelPage.getChildren().length*this.options.increment-this.options.increment);this.showButtons()}},helloWorld:function(){alert("cool")},doPrevious:function(){if(this.options.toIncrement<0){this.options.next.show();this.options.toIncrement+=this.options.increment;this.panelSlide.pause();this.panelSlide.start("margin-left",this.options.toIncrement);this.options.panelPage.getFirst(".current").removeClass("current").getPrevious("li").addClass("current");this.showButtons()}},doNext:function(){if(this.options.toIncrement>this.maxRightIncrement){this.options.prev.show();this.options.toIncrement-=this.options.increment;this.panelSlide.pause();this.panelSlide.start("margin-left",this.options.toIncrement);this.options.panelPage.getFirst(".current").removeClass("current").getNext("li").addClass("current");this.showButtons()}},changeToPage:function(b){var a=b.id.substr("panel-pagination-".length);this.panelSlide.pause();this.options.toIncrement=Math.ceil(0-this.options.increment*a);this.panelSlide.start("margin-left",this.options.toIncrement);this.options.panelPage.getFirst(".current").removeClass("current");b.addClass("current");this.showButtons()},showButtons:function(){if(this.options.toIncrement==0){this.options.prev.hide()}else{this.options.prev.show()}if(this.options.toIncrement==this.maxRightIncrement){this.options.next.hide()}else{this.options.next.show()}}}),

	// MinimaTabsClass by Henrik Hussfelt, Marco Barbosa
	MinimaTabsClass=new Class({Implements:[Options],options:{},elements:{tabs:null,content:null},initialize:function(a,b){this.setOptions(a);this.elements=b},showFirst:function(){this.elements.content.pick().removeClass("hide")},hideAllContent:function(){this.elements.content.addClass("hide")},addTabsAction:function(){this.elements.tabs.each(function(b,a){b.addEvents({click:function(c){c.stop();this.elements.tabs.removeClass("active");this.elements.tabs[a].addClass("active");this.elements.content.addClass("hide");this.elements.content[a].removeClass("hide")}.bind(this)})}.bind(this))}}),

    // MinimaClass by Henrik Hussfelt, Marco Barbosa
    MinimaClass=new Class({Implements:[Options],options:{},element:{systemMessage:null},initialize:function(a,b){this.setOptions(a);this.element.systemMessage=b.systemMessage},showSystemMessage:function(){if(this.element.systemMessage&&this.element.systemMessage.getElement("ul li:last-child")){var b=this;var a=new Element("a",{href:"#",id:"hide-system-message",html:"hide",events:{click:function(c){b.element.systemMessage.dissolve({duration:"short"})}}});this.element.systemMessage.show().getElement("ul li:last-child").adopt(a)}},makeRowsClickable:function(){var a=$$("input[name=checkall-toggle]");a.addEvent("click",function(){var b=$$(".adminlist tbody tr");b.toggleClass("selected")});$$(".adminlist tbody tr input[type=checkbox]").each(function(b){var c=b.getParent("tr");var d=$$("input[name=boxchecked]");b.addEvent("click",function(e){e&&e.stopPropagation();if(b.checked){c.addClass("selected")}else{c.removeClass("selected")}});c.addEvent("click",function(){if(b.checked){b.set("checked",false);d.set("value",0)}else{b.set("checked",true);d.set("value",1)}b.fireEvent("click")})});$$(".adminlist th img").getParent("th").addClass("active")}}),

    MinimaToolbarClass=new Class({Implements:[Options],options:{toolbar:$("toolbar"),toolbarElements:$$(".toolbar-list li a"),label:null},bulkActionsArray:new Array(),bulkNonActionsArray:new Array(),minima:null,initialize:function(a){this.minima=document.id(this.options.minima)||document.id("minima");this.setOptions(a)},doToolbar:function(){this.sortItems();this.fixToolbar()},sortItems:function(){var a=this;if(this.options.toolbarElements.length){this.options.toolbarElements.each(function(b){if(b.get("onclick")!=null&&b.get("onclick").contains("if")){a.bulkActionsArray.push(b.getParent("li"))}else{if(b.get("class")!="divider"){a.bulkNonActionsArray.push(b.getParent("li"))}}})}},fixToolbar:function(){var f=this;if(this.bulkActionsArray.length>1){var d=new Element("ul#actions").hide(),c=new Element("li",{id:"bulkActions",events:{click:function(g){this.toggleReveal(d,{duration:200,styles:["border"]});$$(f.minima.getElement("#bulkActions > a:first-child"),f).switchClass("active","inactive")},outerClick:function(){d.dissolve({duration:250});f.minima.getElement("#bulkActions > a:first-child").set("class","inactive")}}}),a=new Element("a[text= "+this.options.label+"]"),b=new Element("span.arrow");this.bulkActionsArray=this.bulkActionsArray.sort(function(h,g){if(h.get("text").toLowerCase()<g.get("text").toLowerCase()){return -1}if(h.get("text").toLowerCase()==g.get("text").toLowerCase()){return 0}return 1});this.bulkActionsArray.each(function(h,g){d.grab(h)});var e=($("toolbar-new"))?"ul > li#toolbar-new":"ul > li";c.inject($("toolbar").getElement(e),"after");c.adopt(a.grab(b),d)}}});

;


window.addEvent('domready', function() {

    // instanciate
    //var chx = new IPhoneCheckboxes();

    // Initiate some global variables
    // ------------------------------- 

    // get the language strings
    var 
        language = MooTools.lang.get('Minima');
    // DOM variables                
        contentTop = $('content-top'),
        toolbar = $('toolbar'),
        topHead = $('tophead'),
        minima = $('minima'),
        subMenu =  $('submenu'),
        itemForm = $('item-form'),
    // Initiate MimimaClass
        Minima = new MinimaClass({},{systemMessage: $('system-message')}),
        MinimaToolbar = new MinimaToolbarClass(
            {
                'toolbar' : toolbar, // toolbar parent
                'toolbarElements' : minima.getElements('.toolbar-list li a'), // list of the anchor elements
                'label' : language['actionBtn']
            }
        )
    ;
    // ------------------------------- 

    // Trigger actions
 
    // Show system message
    Minima.showSystemMessage();

    // Make whole row clickable, if there are any    
    if (minima.getElements('.adminlist').length) {
    	Minima.makeRowsClickable();
    };

    // TOOLBAR
    // =============================
    MinimaToolbar.doToolbar();
    if(toolbar) toolbar.show();

    // FIXES
	// =============================
    new Fx.SmoothScroll({
        links: '#topLink'
    });    

    // add id #adminlist to .adminlist
    var adminlist = $$('.adminlist');
    if (adminlist.length && adminlist.get('id') != 'adminlist') adminlist.set('id','adminlist');

    // add aditional tabs to #submenu position
    if (subMenu) subMenu.addClass('minimaTabs');            
    
    if (subMenu && itemForm) {
        // Start tabs actions, create instances of class
    	var MinimaTabsHorizontal = new MinimaTabsClass({}, {'tabs': $$('.minimaTabs a'), 'content': itemForm.getChildren('div')}),
        	MinimaTabsVertical = new MinimaTabsClass({}, {'tabs': $$('#vertical-tabs a'), 'content': $('tabs').getChildren('.panelform')});

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

    // fixed content-box header when scrolling    
    scrollSize = document.getScrollSize().y - document.getSize().y;    
    tableHead = minima.getElements('table.adminlist thead');
    
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
                //$$(contentTop,tableHead).setStyle('width', topHead.getSize().x - 40).addClass('fixed');
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
    
    // ------------------------------- 

    // PANEL TAB
    // ==================================================

    // tabs wrapper
    var tabsWrapper = $('panel-wrapper');

    if (tabsWrapper) {
	    // fixing wrapper bug - thanks to d_mitar
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

        // search-filter to filter the components
        /*var searchTerm = $('search-term');

        if (searchTerm) {
            var myFilter = new ElementFilter('search-term', '#panel-list li a', {
                trigger: 'keyup',
                cache: false,
                onShow: function(element) {
                    element.show();
                    element.set('morph',{
                        onComplete: function() {
                            element.setStyle('background-color','#fff');
                        }
                    });
                    element.morph({'background-color':'#a5faa9'});
                },
                onHide: function(element) {
                    element.hide();
                    element.set('morph',{
                        onComplete: function() {
                            element.setStyle('background-color','#fff');
                        }
                    });
                    element.morph({'background-color':'#fac3a5'});
                },
                onComplete: function(element) {
                    console.log(element);
                    //showButtons();
                }
            });
        }*/

        var extra = $('more')
            extraLists = $('list-content'),
            openPanel = $('panel-tab'),
            listWrapper = $('list-wrapper');

        // open the panel slide
        openPanel.addEvents({
            'click': function(){
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
        }

        // turn off list when click outside
        listWrapper.addEvent('outerClick', function(){
            hideLists();
        });        

        // turn off list when clicking a link
        extraLists.getElements("a").addEvent('click', function(){
            hideLists();
        });

        // slide up panel when clicking a link
        minima.getElements('#panel-list li').addEvent('click', function(){            
            Panel.panel.toggle();
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

    }// end of if(tabsWrapper)

});
