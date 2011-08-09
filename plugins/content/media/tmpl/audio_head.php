<?php
/**
 * @version     $id: audio.php
 * @package     Molajo
 * @subpackage  Responses Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$document =& MolajoFactory::getDocument();
$document->addStyleSheet(JURI::base().'media/molajo/audio/audio.css' );
$document->addScript(JURI::base().'media/molajo/audio/audio-player.js' );

/** swf player and parameters **/
$js = "window.addEvent('domready', function() { "."\n";
$js .= ' AudioPlayer.setup("'.JURI::base().'media/molajo/audio/player.swf'.'", {   '."\n";

/** options **/
$js .= ' loop: "'.$this->systemParams->def('audio_loop', "no").'", '."\n";
$js .= ' animation: "'.$this->systemParams->def('audio_animation', "yes").'", '."\n";
$js .= ' remaining: "'.$this->systemParams->def('audio_remaining', "no").'", '."\n";
$js .= ' noinfo: "'.$this->systemParams->def('audio_noinfo', "no").'", '."\n";
$js .= ' initialvolume: '.$this->systemParams->def('audio_initialvolume', 60).', '."\n";
$js .= ' buffer: '.$this->systemParams->def('audio_buffer', 5).', '."\n";
$js .= ' encode: "'.$this->systemParams->def('audio_encode', "no").'", '."\n";
$js .= ' checkpolicy: "'.$this->systemParams->def('audio_checkpolicy', "no").'", '."\n";
$js .= ' rtl: "'.$this->systemParams->def('audio_rtl', "no").'", '."\n";

/** flash player options **/
$js .= ' width: '.$this->systemParams->def('audio_width', 290).', '."\n";
$js .= ' transparentpagebg: "'.$this->systemParams->def('audio_transparentpagebg', "no").'", '."\n";
$js .= ' pagebg: "'.$this->systemParams->def('audio_pagebg', "").'", '."\n";

/** colors **/
$js .= ' bg: "'.$this->systemParams->def('audio_bg', "E5E5E5").'", '."\n";
$js .= ' leftbg: "'.$this->systemParams->def('audio_leftbg', "CCCCCC").'", '."\n";
$js .= ' lefticon: "'.$this->systemParams->def('audio_lefticon', "333333").'", '."\n";
$js .= ' voltrack: "'.$this->systemParams->def('audio_voltrack', "F2F2F2").'", '."\n";
$js .= ' volslider: "'.$this->systemParams->def('audio_volslider', "666666").'", '."\n";
$js .= ' rightbg: "'.$this->systemParams->def('audio_rightbg', "B4B4B4").'", '."\n";
$js .= ' rightbghover: "'.$this->systemParams->def('audio_rightbghover', "999999").'", '."\n";
$js .= ' righticon: "'.$this->systemParams->def('audio_righticon', "333333").'", '."\n";
$js .= ' righticonhover: "'.$this->systemParams->def('audio_righticonhover', "FFFFFF").'", '."\n";
$js .= ' loader: "'.$this->systemParams->def('audio_loader', "009900").'", '."\n";
$js .= ' track: "'.$this->systemParams->def('audio_track', "FFFFFF").'", '."\n";
$js .= ' tracker: "'.$this->systemParams->def('audio_tracker', "DDDDDD").'", '."\n";
$js .= ' border: "'.$this->systemParams->def('audio_border', "CCCCCC").'", '."\n";
$js .= ' skip: "'.$this->systemParams->def('audio_skip', "666666").'", '."\n";
$js .= ' text: "'.$this->systemParams->def('audio_text', "333333").'" '."\n";

/** swf player end **/
$js .= ' });'."\n";
$js .= ' });'."\n";
$document->addScriptDeclaration($js);

/** head for individual mp3 **/
$this->audio_file_loader = "window.addEvent('domready', function() { "."\n";