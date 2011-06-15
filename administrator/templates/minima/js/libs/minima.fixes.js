
window.addEvent('domready', function() { 

    // FIXES
	// =============================
  
    var 
        adminlist = $$('.adminlist'),
        subMenu = $('submenu'),
        jformTitle = $('jform_title'),
        itemForm = $('item-form'),
        minima = $('minima'),
        filterBar = $('filter-bar'),
        language = MooTools.lang.get('Minima');

    if (adminlist.length && adminlist.get('id') != 'adminlist') adminlist.set('id','adminlist');    

    // position aditional tabs to #submenu position
    if (subMenu) subMenu.addClass('minimaTabs');            

    // some overrides have tabs that are out of place   
    if ((subMenu && subMenu.hasClass('out')) || (subMenu && itemForm)) {        
        // hide the tabs content
        //if (itemForm) itemForm.getChildren('div').addClass('hide');
        // position the tabs on the right place
        subMenu.inject($('content'),'top');        
    }; // end of subMenu

    // FIXME must see if this is necessary
    // fix padding when there are no tabs
    if (!filterBar  && $$('.adminlist')) $$('.adminlist').addClass('padTop');

    // change the h2 title dynamically
    if (jformTitle) {
        // set the title of the page with the jform_title
        if(jformTitle.get("value") != "") $$('.pagetitle h2').set('html', jformTitle.get("value"));
        // change while typing it
        jformTitle.addEvent('keyup', function(event){
            // show h2 with the title typed
            if(jformTitle.get("value") != "") $$('.pagetitle h2').set('html', this.get("value"));
            //fix alias automatically, removing extra chars, all lower cased
            $('jform_alias').set( 'value', this.get("value").standardize().replace(/\s+/g, '-').replace(/[^-\w]+/g, '').toLowerCase() );
        });
    }; // end jform_title

    // make filter-bar a slide    
    if (filterBar) {
       
        // status of the filter, if it's on or off
        var 
            // the status of the filter
            filterStatus = {
                'true':  language['closeFilter'],
                'false': language['showFilter']
            },
            // the Fx slide
            filterSlide = new Fx.Slide(filterBar).hide(),		
            // the "open filter" anchor
            filterAnchor = new Element('a', {
                'href': '#minima',
                'id': 'open-filter',
                'html': language['showFilter'],
                'events': {
                    'click': function(e){
                        e.stop();
                        filterSlide.toggle();
                        this.toggleClass("active");                    
                        if (this.hasClass("active")) {
                          $('filter_search').focus();  
                        } 
                        if (contentTop.hasClass('fixed')) {
                            window.scrollTo(0,0);                        
                        }
                    }
                }
            });
		
		// show filter if it's being used
        // -------------------------------
        var filterActive = false,
            pageTitle = "";

        // FIXME not detecting correctly
        // we must find out if any of the filters are in use (selected)
        
        filterBar.getElements('input, select').each(function(el) {
        	var elValue = el.get('value');        	
        	// if any filter is selected
            if (elValue) {    		
                // set to active
                filterActive = true;
                // add the selected filters to the pageTitle
                pageTitle += ( el.get('tag').toLowerCase() == "select" ) ?
                    el.getElement("option:selected").get("html").toLowerCase() + " " : pageTitle += elValue.toLowerCase() + " ";
            }
        });
                
        // if filter is active then show #filter-bar
        if (filterActive) {
        		filterSlide.show(); 
        		filterAnchor.set('html', filterStatus[filterSlide.open]).addClass("active"); 
        }      		

        
        // and change <h2> showing the selected filters
        var h2Title = $$('.pagetitle').getElement('h2');

        if (pageTitle) h2Title.set( 'html', h2Title.get('html') + "<em>( "+pageTitle+")</em>" );
        // -------------------------------

        // change status on toggle complete
        filterSlide.addEvent('complete', function() {
            filterAnchor.set('html', filterStatus[filterSlide.open]);
        });

        // add the filter anchor next to pagetitle
        $$('.pagetitle').grab(filterAnchor);
        //$$('.pagetitle h2').inject(filterAnchor, 'before');
        
        // hidden to avoid flicker, show it back after done fixing it
        filterBar.show();
        
    } //end filter-bar  



});