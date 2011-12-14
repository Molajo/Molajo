<?php
/**
 * @version     $id: audio.php
 * @package     Molajo
 * @subpackage  Responses Component
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$document =& MolajoFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'media/audio/audio.css');
$document->addScript(JURI::base() . 'media/audio/audio-player.js');

/** swf player and parameters **/
$js = "window.addEvent('domready', function() { " . "\n";
$js .= ' AudioPlayer.setup("' . JURI::base() . 'media/audio/player.swf' . '", {   ' . "\n";

/** options **/
$js .= ' loop: "' . $this->systemParameters->def('audio_loop', "no") . '", ' . "\n";
$js .= ' animation: "' . $this->systemParameters->def('audio_animation', "yes") . '", ' . "\n";
$js .= ' remaining: "' . $this->systemParameters->def('audio_remaining', "no") . '", ' . "\n";
$js .= ' noinfo: "' . $this->systemParameters->def('audio_noinfo', "no") . '", ' . "\n";
$js .= ' initialvolume: ' . $this->systemParameters->def('audio_initialvolume', 60) . ', ' . "\n";
$js .= ' buffer: ' . $this->systemParameters->def('audio_buffer', 5) . ', ' . "\n";
$js .= ' encode: "' . $this->systemParameters->def('audio_encode', "no") . '", ' . "\n";
$js .= ' checkpolicy: "' . $this->systemParameters->def('audio_checkpolicy', "no") . '", ' . "\n";
$js .= ' rtl: "' . $this->systemParameters->def('audio_rtl', "no") . '", ' . "\n";

/** flash player options **/
$js .= ' width: ' . $this->systemParameters->def('audio_width', 290) . ', ' . "\n";
$js .= ' transparentpagebg: "' . $this->systemParameters->def('audio_transparentpagebg', "no") . '", ' . "\n";
$js .= ' pagebg: "' . $this->systemParameters->def('audio_pagebg', "") . '", ' . "\n";

/** colors **/
$js .= ' bg: "' . $this->systemParameters->def('audio_bg', "E5E5E5") . '", ' . "\n";
$js .= ' leftbg: "' . $this->systemParameters->def('audio_leftbg', "CCCCCC") . '", ' . "\n";
$js .= ' lefticon: "' . $this->systemParameters->def('audio_lefticon', "333333") . '", ' . "\n";
$js .= ' voltrack: "' . $this->systemParameters->def('audio_voltrack', "F2F2F2") . '", ' . "\n";
$js .= ' volslider: "' . $this->systemParameters->def('audio_volslider', "666666") . '", ' . "\n";
$js .= ' rightbg: "' . $this->systemParameters->def('audio_rightbg', "B4B4B4") . '", ' . "\n";
$js .= ' rightbghover: "' . $this->systemParameters->def('audio_rightbghover', "999999") . '", ' . "\n";
$js .= ' righticon: "' . $this->systemParameters->def('audio_righticon', "333333") . '", ' . "\n";
$js .= ' righticonhover: "' . $this->systemParameters->def('audio_righticonhover', "FFFFFF") . '", ' . "\n";
$js .= ' loader: "' . $this->systemParameters->def('audio_loader', "009900") . '", ' . "\n";
$js .= ' track: "' . $this->systemParameters->def('audio_track', "FFFFFF") . '", ' . "\n";
$js .= ' tracker: "' . $this->systemParameters->def('audio_tracker', "DDDDDD") . '", ' . "\n";
$js .= ' border: "' . $this->systemParameters->def('audio_border', "CCCCCC") . '", ' . "\n";
$js .= ' skip: "' . $this->systemParameters->def('audio_skip', "666666") . '", ' . "\n";
$js .= ' text: "' . $this->systemParameters->def('audio_text', "333333") . '" ' . "\n";

/** swf player end **/
$js .= ' });' . "\n";
$js .= ' });' . "\n";
$document->addScriptDeclaration($js);

/** head for individual mp3 **/
$this->audio_file_loader = "window.addEvent('domready', function() { " . "\n";