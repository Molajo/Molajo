<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;

/** swf player and parameters **/
$js = "window.addEvent('domready', function() { " . "\n";
$js .= ' AudioPlayer.setup("' . JURI::base() . 'media/molajo/audio/player.swf' . '", {   ' . "\n";

/** options **/
$js .= ' loop: "' . $this->parameters->get('audio_loop', "no") . '", ' . "\n";
$js .= ' animation: "' . $this->parameters->get('audio_animation', "yes") . '", ' . "\n";
$js .= ' remaining: "' . $this->parameters->get('audio_remaining', "no") . '", ' . "\n";
$js .= ' noinfo: "' . $this->parameters->get('audio_noinfo', "no") . '", ' . "\n";
$js .= ' initialvolume: ' . $this->parameters->get('audio_initialvolume', 60) . ', ' . "\n";
$js .= ' buffer: ' . $this->parameters->get('audio_buffer', 5) . ', ' . "\n";
$js .= ' encode: "' . $this->parameters->get('audio_encode', "no") . '", ' . "\n";
$js .= ' checkpolicy: "' . $this->parameters->get('audio_checkpolicy', "no") . '", ' . "\n";
$js .= ' rtl: "' . $this->parameters->get('audio_rtl', "no") . '", ' . "\n";

/** flash player options **/
$js .= ' width: ' . $this->parameters->get('audio_width', 290) . ', ' . "\n";
$js .= ' transparentpagebg: "' . $this->parameters->get('audio_transparentpagebg', "no") . '", ' . "\n";
$js .= ' pagebg: "' . $this->parameters->get('audio_pagebg', "") . '", ' . "\n";

/** colors **/
$js .= ' bg: "' . $this->parameters->get('audio_bg', "E5E5E5") . '", ' . "\n";
$js .= ' leftbg: "' . $this->parameters->get('audio_leftbg', "CCCCCC") . '", ' . "\n";
$js .= ' lefticon: "' . $this->parameters->get('audio_lefticon', "333333") . '", ' . "\n";
$js .= ' voltrack: "' . $this->parameters->get('audio_voltrack', "F2F2F2") . '", ' . "\n";
$js .= ' volslider: "' . $this->parameters->get('audio_volslider', "666666") . '", ' . "\n";
$js .= ' rightbg: "' . $this->parameters->get('audio_rightbg', "B4B4B4") . '", ' . "\n";
$js .= ' rightbghover: "' . $this->parameters->get('audio_rightbghover', "999999") . '", ' . "\n";
$js .= ' righticon: "' . $this->parameters->get('audio_righticon', "333333") . '", ' . "\n";
$js .= ' righticonhover: "' . $this->parameters->get('audio_righticonhover', "FFFFFF") . '", ' . "\n";
$js .= ' loader: "' . $this->parameters->get('audio_loader', "009900") . '", ' . "\n";
$js .= ' track: "' . $this->parameters->get('audio_track', "FFFFFF") . '", ' . "\n";
$js .= ' tracker: "' . $this->parameters->get('audio_tracker', "DDDDDD") . '", ' . "\n";
$js .= ' border: "' . $this->parameters->get('audio_border', "CCCCCC") . '", ' . "\n";
$js .= ' skip: "' . $this->parameters->get('audio_skip', "666666") . '", ' . "\n";
$js .= ' text: "' . $this->parameters->get('audio_text', "333333") . '" ' . "\n";

/** swf player end **/
$js .= ' });' . "\n";

Services::Assets()->addScriptDeclaration($js);

/** head for individual mp3 **/
$loader = "window.addEvent('domready', function() { " . "\n";
foreach ($list as $item) {
    $loader .= 'AudioPlayer.embed("audioplayer' . $this->row->id . '", {soundFile: "' . $this->row->file_link . '"});'; ?>
<p id="audioplayer_<?php echo $this->id; ?>"><?php echo $this->audio_label; ?></p>
<?php
}
$loader .= ' });';
Services::Assets()->addScriptDeclaration($loader);

?>
</audio>
</div>
<?php
