<?php defined('_JEXEC') or die;
/**
* @package		Template Framework for Molajo 1.6
* @author		Joomla Engineering http://joomlaengineering.com
* @copyright	Copyright (C) 2010 Matt Thomas | Joomla Engineering. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * JFormFieldGooglewebfont
 *
 * Provides list of Google Web Fonts
 *
 * @static
 * @package		Molajo
 * @subpackage  HTML
 * @since		1.6
 */
class JFormFieldGooglewebfont extends JFormFieldList
{
    /**
     * Field Type
     *
     * @var		string
     * @since	1.6
     */
    public $type = 'Googlewebfont';

    /**
     * getOptions
     *
     * Generates list options
     *
     * @return	array	The field option objects.
     * @since	1.6
     */
    protected function getOptions()
    {
        $options	= array();

        $options[]	= MolajoHTML::_('select.option', '', '- None Selected -');
        $options[]	= MolajoHTML::_('select.option', 'Aclonica', 'Aclonica');
		$options[]	= MolajoHTML::_('select.option', 'Allan', 'Allan');
		$options[]	= MolajoHTML::_('select.option', 'Allerta', 'Allerta');
		$options[]	= MolajoHTML::_('select.option', 'Allerta+Stencil', 'Allerta Stencil');
		$options[]	= MolajoHTML::_('select.option', 'Amaranth', 'Amaranth');
		$options[]	= MolajoHTML::_('select.option', 'Annie+Use+Your+Telescope', 'Annie Use Your Telescope');
		$options[]	= MolajoHTML::_('select.option', 'Anonymous+Pro', 'Anonymous Pro');
		$options[]	= MolajoHTML::_('select.option', 'Anton', 'Anton');
		$options[]	= MolajoHTML::_('select.option', 'Architects+Daughter', 'Architects Daughter');
		$options[]	= MolajoHTML::_('select.option', 'Arimo', 'Arimo');
		$options[]	= MolajoHTML::_('select.option', 'Artifika', 'Artifika');
		$options[]	= MolajoHTML::_('select.option', 'Arvo', 'Arvo');
		$options[]	= MolajoHTML::_('select.option', 'Astloch', 'Astloch');
		$options[]	= MolajoHTML::_('select.option', 'Bangers', 'Bangers');
		$options[]	= MolajoHTML::_('select.option', 'Bentham', 'Bentham');
		$options[]	= MolajoHTML::_('select.option', 'Bevan', 'Bevan');
		$options[]	= MolajoHTML::_('select.option', 'Bigshot+One', 'Bigshot One');
		$options[]	= MolajoHTML::_('select.option', 'Brawler', 'Brawler');
		$options[]	= MolajoHTML::_('select.option', 'Buda', 'Buda');
		$options[]	= MolajoHTML::_('select.option', 'Cabin', 'Cabin');
		$options[]	= MolajoHTML::_('select.option', 'Cabin+Sketch', 'Cabin Sketch');
		$options[]	= MolajoHTML::_('select.option', 'Calligraffitti', 'Calligraffitti');
		$options[]	= MolajoHTML::_('select.option', 'Candal', 'Candal');
		$options[]	= MolajoHTML::_('select.option', 'Cantarell', 'Cantarell');
		$options[]	= MolajoHTML::_('select.option', 'Cardo', 'Cardo');
		$options[]	= MolajoHTML::_('select.option', 'Carter+One', 'Carter One');
		$options[]	= MolajoHTML::_('select.option', 'Caudex', 'Caudex');
		$options[]	= MolajoHTML::_('select.option', 'Cedarville+Cursive', 'Cedarville Cursive');
		$options[]	= MolajoHTML::_('select.option', 'Cherry+Cream+Soda', 'Cherry Cream Soda');
		$options[]	= MolajoHTML::_('select.option', 'Chewy', 'Chewy');
		$options[]	= MolajoHTML::_('select.option', 'Coda', 'Coda');
		$options[]	= MolajoHTML::_('select.option', 'Coming+Soon', 'Coming Soon');
		$options[]	= MolajoHTML::_('select.option', 'Copse', 'Copse');
		$options[]	= MolajoHTML::_('select.option', 'Corben', 'Corben');
		$options[]	= MolajoHTML::_('select.option', 'Cousine', 'Cousine');
		$options[]	= MolajoHTML::_('select.option', 'Covered+By+Your+Grace', 'Covered By Your Grace');
		$options[]	= MolajoHTML::_('select.option', 'Crafty+Girls', 'Crafty Girls');
		$options[]	= MolajoHTML::_('select.option', 'Crimson+Text', 'Crimson Text');
		$options[]	= MolajoHTML::_('select.option', 'Crushed', 'Crushed');
		$options[]	= MolajoHTML::_('select.option', 'Cuprum', 'Cuprum');
		$options[]	= MolajoHTML::_('select.option', 'Damion', 'Damion');
		$options[]	= MolajoHTML::_('select.option', 'Dancing+Script', 'Dancing Script');
		$options[]	= MolajoHTML::_('select.option', 'Dawning+of+a+New+Day', 'Dawning of a New Day');
		$options[]	= MolajoHTML::_('select.option', 'Didact+Gothic', 'Didact Gothic');
		$options[]	= MolajoHTML::_('select.option', 'Droid+Sans', 'Droid Sans');
		$options[]	= MolajoHTML::_('select.option', 'Droid+Sans+Mono', 'Droid Sans Mono');
		$options[]	= MolajoHTML::_('select.option', 'Droid+Serif', 'Droid Serif');
		$options[]	= MolajoHTML::_('select.option', 'EB+Garamond', 'EB Garamond');
		$options[]	= MolajoHTML::_('select.option', 'Expletus+Sans', 'Expletus Sans');
		$options[]	= MolajoHTML::_('select.option', 'Fontdiner+Swanky', 'Fontdiner Swanky');
		$options[]	= MolajoHTML::_('select.option', 'Francois+One', 'Francois One');
		$options[]	= MolajoHTML::_('select.option', 'Geo', 'Geo');
		$options[]	= MolajoHTML::_('select.option', 'Goudy+Bookletter+1911', 'Goudy Bookletter 1911');
		$options[]	= MolajoHTML::_('select.option', 'Gruppo', 'Gruppo');
		$options[]	= MolajoHTML::_('select.option', 'Holtwood+One+SC', 'Holtwood One SC');
		$options[]	= MolajoHTML::_('select.option', 'Homemade+Apple', 'Homemade Apple');
		$options[]	= MolajoHTML::_('select.option', 'IM+Fell', 'IM Fell');
		$options[]	= MolajoHTML::_('select.option', 'Inconsolata', 'Inconsolata');
		$options[]	= MolajoHTML::_('select.option', 'Indie+Flower', 'Indie Flower');
		$options[]	= MolajoHTML::_('select.option', 'Irish+Grover', 'Irish Grover');
		$options[]	= MolajoHTML::_('select.option', 'Josefin+Sans', 'Josefin Sans');
		$options[]	= MolajoHTML::_('select.option', 'Josefin+Slab', 'Josefin Slab');
		$options[]	= MolajoHTML::_('select.option', 'Judson', 'Judson');
		$options[]	= MolajoHTML::_('select.option', 'Jura', 'Jura');
		$options[]	= MolajoHTML::_('select.option', 'Just+Another+Hand', 'Just Another Hand');
		$options[]	= MolajoHTML::_('select.option', 'Just+Me+Again+Down+Here', 'Just Me Again Down Here');
		$options[]	= MolajoHTML::_('select.option', 'Kameron', 'Kameron');
		$options[]	= MolajoHTML::_('select.option', 'Kenia', 'Kenia');
		$options[]	= MolajoHTML::_('select.option', 'Kranky', 'Kranky');
		$options[]	= MolajoHTML::_('select.option', 'Kreon', 'Kreon');
		$options[]	= MolajoHTML::_('select.option', 'Kristi', 'Kristi');
		$options[]	= MolajoHTML::_('select.option', 'La+Belle+Aurore', 'La Belle Aurore');
		$options[]	= MolajoHTML::_('select.option', 'Lato', 'Lato');
		$options[]	= MolajoHTML::_('select.option', 'League+Script', 'League Script');
		$options[]	= MolajoHTML::_('select.option', 'Lekton', 'Lekton');
		$options[]	= MolajoHTML::_('select.option', 'Limelight', 'Limelight');
		$options[]	= MolajoHTML::_('select.option', 'Lobster', 'Lobster');
		$options[]	= MolajoHTML::_('select.option', 'Lora', 'Lora');
		$options[]	= MolajoHTML::_('select.option', 'Luckiest+Guy', 'Luckiest Guy');
		$options[]	= MolajoHTML::_('select.option', 'Maiden+Orange', 'Maiden Orange');
		$options[]	= MolajoHTML::_('select.option', 'Mako', 'Mako');
		$options[]	= MolajoHTML::_('select.option', 'Maven+Pro', 'Maven Pro');
		$options[]	= MolajoHTML::_('select.option', 'Meddon', 'Meddon');
		$options[]	= MolajoHTML::_('select.option', 'MedievalSharp', 'MedievalSharp');
		$options[]	= MolajoHTML::_('select.option', 'Megrim', 'Megrim');
		$options[]	= MolajoHTML::_('select.option', 'Merriweather', 'Merriweather');
		$options[]	= MolajoHTML::_('select.option', 'Metrophobic', 'Metrophobic');
		$options[]	= MolajoHTML::_('select.option', 'Michroma', 'Michroma');
		$options[]	= MolajoHTML::_('select.option', 'Miltonian', 'Miltonian');
		$options[]	= MolajoHTML::_('select.option', 'Molengo', 'Molengo');
		$options[]	= MolajoHTML::_('select.option', 'Monofett', 'Monofett');
		$options[]	= MolajoHTML::_('select.option', 'Mountains+of+Christmas', 'Mountains of Christmas');
		$options[]	= MolajoHTML::_('select.option', 'Muli', 'Muli');
		$options[]	= MolajoHTML::_('select.option', 'Neucha', 'Neucha');
		$options[]	= MolajoHTML::_('select.option', 'Neuton', 'Neuton');
		$options[]	= MolajoHTML::_('select.option', 'News+Cycle', 'News Cycle');
		$options[]	= MolajoHTML::_('select.option', 'Nobile', 'Nobile');
		$options[]	= MolajoHTML::_('select.option', 'Nova', 'Nova');
		$options[]	= MolajoHTML::_('select.option', 'Nunito', 'Nunito');
		$options[]	= MolajoHTML::_('select.option', 'OFL+Sorts+Mill+Goudy+TT', 'OFL Sorts Mill Goudy TT');
		$options[]	= MolajoHTML::_('select.option', 'Old+Standard+TT', 'Old Standard TT');
		$options[]	= MolajoHTML::_('select.option', 'Open+Sans', 'Open Sans');
		$options[]	= MolajoHTML::_('select.option', 'Orbitron', 'Orbitron');
		$options[]	= MolajoHTML::_('select.option', 'Oswald', 'Oswald');
		$options[]	= MolajoHTML::_('select.option', 'Over+the+Rainbow', 'Over the Rainbow');
		$options[]	= MolajoHTML::_('select.option', 'PT+Sans', 'PT Sans');
		$options[]	= MolajoHTML::_('select.option', 'PT+Serif', 'PT Serif');
		$options[]	= MolajoHTML::_('select.option', 'Pacifico', 'Pacifico');
		$options[]	= MolajoHTML::_('select.option', 'Paytone+One', 'Paytone One');
		$options[]	= MolajoHTML::_('select.option', 'Permanent+Marker', 'Permanent Marker');
		$options[]	= MolajoHTML::_('select.option', 'Philosopher', 'Philosopher');
		$options[]	= MolajoHTML::_('select.option', 'Play', 'Play');
		$options[]	= MolajoHTML::_('select.option', 'Playfair+Display', 'Playfair Display');
		$options[]	= MolajoHTML::_('select.option', 'Podkova', 'Podkova');
		$options[]	= MolajoHTML::_('select.option', 'Puritan', 'Puritan');
		$options[]	= MolajoHTML::_('select.option', 'Quattrocento', 'Quattrocento');
		$options[]	= MolajoHTML::_('select.option', 'Quattrocento+Sans', 'Quattrocento Sans');
		$options[]	= MolajoHTML::_('select.option', 'Radley', 'Radley');
		$options[]	= MolajoHTML::_('select.option', 'Raleway', 'Raleway');
		$options[]	= MolajoHTML::_('select.option', 'Reenie+Beanie', 'Reenie Beanie');
		$options[]	= MolajoHTML::_('select.option', 'Rock+Salt', 'Rock Salt');
		$options[]	= MolajoHTML::_('select.option', 'Rokkitt', 'Rokkitt');
		$options[]	= MolajoHTML::_('select.option', 'Ruslan+Display', 'Ruslan Display');
		$options[]	= MolajoHTML::_('select.option', 'Schoolbell', 'Schoolbell');
		$options[]	= MolajoHTML::_('select.option', 'Shadows+Into+Light', 'Shadows Into Light');
		$options[]	= MolajoHTML::_('select.option', 'Shanti', 'Shanti');
		$options[]	= MolajoHTML::_('select.option', 'Sigmar+One', 'Sigmar One');
		$options[]	= MolajoHTML::_('select.option', 'Six+Caps', 'Six Caps');
		$options[]	= MolajoHTML::_('select.option', 'Slackey', 'Slackey');
		$options[]	= MolajoHTML::_('select.option', 'Smythe', 'Smythe');
		$options[]	= MolajoHTML::_('select.option', 'Sniglet', 'Sniglet');
		$options[]	= MolajoHTML::_('select.option', 'Special+Elite', 'Special Elite');
		$options[]	= MolajoHTML::_('select.option', 'Sue+Ellen+Francisco', 'Sue Ellen Francisco');
		$options[]	= MolajoHTML::_('select.option', 'Sunshiney', 'Sunshiney');
		$options[]	= MolajoHTML::_('select.option', 'Swanky+and+Moo+Moo', 'Swanky and Moo Moo');
		$options[]	= MolajoHTML::_('select.option', 'Syncopate', 'Syncopate');
		$options[]	= MolajoHTML::_('select.option', 'Tangerine', 'Tangerine');
		$options[]	= MolajoHTML::_('select.option', 'Tenor+Sans', 'Tenor Sans');
		$options[]	= MolajoHTML::_('select.option', 'Terminal+Dosis+Light', 'Terminal Dosis Light');
		$options[]	= MolajoHTML::_('select.option', 'The+Girl+Next+Door', 'The Girl Next Door');
		$options[]	= MolajoHTML::_('select.option', 'Tinos', 'Tinos');
		$options[]	= MolajoHTML::_('select.option', 'Ubuntu', 'Ubuntu');
		$options[]	= MolajoHTML::_('select.option', 'Ultra', 'Ultra');
		$options[]	= MolajoHTML::_('select.option', 'UnifrakturCook', 'UnifrakturCook');
		$options[]	= MolajoHTML::_('select.option', 'UnifrakturMaguntia', 'UnifrakturMaguntia');
		$options[]	= MolajoHTML::_('select.option', 'Unkempt', 'Unkempt');
		$options[]	= MolajoHTML::_('select.option', 'VT323', 'VT323');
		$options[]	= MolajoHTML::_('select.option', 'Vibur', 'Vibur');
		$options[]	= MolajoHTML::_('select.option', 'Vollkorn', 'Vollkorn');
		$options[]	= MolajoHTML::_('select.option', 'Waiting+for+the+Sunrise', 'Waiting for the Sunrise');
		$options[]	= MolajoHTML::_('select.option', 'Wallpoet', 'Wallpoet');
		$options[]	= MolajoHTML::_('select.option', 'Walter+Turncoat', 'Walter Turncoat');
		$options[]	= MolajoHTML::_('select.option', 'Wire+One', 'Wire One');
		$options[]	= MolajoHTML::_('select.option', 'Yanone+Kaffeesatz', 'Yanone Kaffeesatz');
		$options[]	= MolajoHTML::_('select.option', 'Zeyada', 'Zeyada');

        return $options;

    }
}