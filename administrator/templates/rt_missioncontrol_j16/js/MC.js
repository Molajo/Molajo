/**
 * @package MissionControl Admin Template - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

(function(){
	
	var MC = this.MC = {
		
		init: function(){
			if (this.MC.Notice) this.MC.Notice.shake.delay(500, this.MC.Notice.shake, 3);
			SelectBoxes.init();
			MC.fixIOS();
		},
		
		fixIOS: function(){
			var menu = $('mctrl-menu');
			if (menu){
				var children = menu.getElements('li');
				if (children.length){
					children.addEvent('mouseenter', function(e){ new Event(e).stop(); });
				}
			}
		}
		
	};
	
	
	var SelectBoxes = this.MC.SelectBoxes = {
		
		init: function(){
			this.selects = $$('.dropdown select');
			
			this.selects.each(function(sel){	
				sel.getParent().addEvent('mouseenter', function(e) {new Event(e).stop();});
				this.build(sel);
			}, this);
		},
		
		build: function(sel){
			var selected = new Element('a', {'class': 'mc-dropdown-selected'}).inject(sel, 'before');
			var list = new Element('ul', {'class': 'mc-dropdown'}).inject(selected, 'after');
			
			sel.setStyle('display', 'none');
			
			sel.getChildren().each(function(option, i){
				var active = option.getProperty('selected') || false;
				var lnk = new Element('a', {'href': '#'}).setText(option.getText());
				var opt = new Element('li').inject(list).adopt(lnk);
				
				opt.addEvent('click', function(e){
					new Event(e).stop();

					sel.selectedIndex = i;
					selected.getFirst().setText(option.getText());
					
					sel.fireEvent('change');
				});
				
				opt['mcselected'] = active;
				opt['mcvalue'] = option.getValue() || '';
				
				if (active) selected.setHTML('<span class="select-active">' + option.getText() + '</span>');
			});
			
			var arrow = new Element('span', {'class': 'select-arrow'}).setHTML('&#x25BE;').inject(selected);
		}
		
	};
	

	window.addEvent('domready', MC.init);
	
})();