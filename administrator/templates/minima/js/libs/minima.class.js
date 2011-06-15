/** 
 * @package     Minima
 * @author      Henrik Hussfelt, Marco Barbosa
 * @copyright   Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 *				This Class is mainly for code-readability.
 */

var MinimaClass = new Class({
	Implements: [Options],

    options: {
    },

	element: {
		systemMessage: null
	},

	initialize: function(options, elements){
    	// Set options
    	this.setOptions(options);
    	// Set elements
    	this.element.systemMessage = elements.systemMessage;
    },

	showSystemMessage: function() {
	    // system-message fade
	    if (this.element.systemMessage && this.element.systemMessage.getElement("ul li:last-child")) {
	    	var _this = this;
	        var hideAnchor = new Element('a', {
	            'href': '#',
	            'id': 'hide-system-message',
	            'html': 'hide',
	            'events': {
	                'click': function(e){
	                    _this.element.systemMessage.dissolve({duration: 'short'})
	                }
	            }
	        });
	        // inject hideAnchor in the system-message container
	        this.element.systemMessage.show().getElement("ul li:last-child").adopt(hideAnchor);
	    };
	},

	makeRowsClickable: function() {
		// get the toggle element
        var toggle = $$('input[name=checkall-toggle]');
        // now remove the horrible onClick event
        //toggle.set("onclick",null);
        // add the real click event
        toggle.addEvent('click', function(){
            var rows = $$('.adminlist tbody tr');
            rows.toggleClass('selected');
        });

        $$('.adminlist tbody tr input[type=checkbox]').each(function(element){

        	// get parent
            var parent = element.getParent('tr');

            // get boxchecked
            var boxchecked = $$('input[name=boxchecked]');

            // add click event
            element.addEvent('click', function(event){
                event && event.stopPropagation();

                if (element.checked) {
                    parent.addClass('selected');
                } else {
                    parent.removeClass('selected');
                }
            });

            // add click event
            parent.addEvent('click', function(){
                if (element.checked) {
                    element.set('checked', false);
                    boxchecked.set('value',0)
                }else{
                    element.set('checked', true);
                    boxchecked.set('value', 1);
                }
                element.fireEvent('click');
            });

        });

        // highlight the sorting column
        $$('.adminlist th img').getParent('th').addClass('active');
	}
});