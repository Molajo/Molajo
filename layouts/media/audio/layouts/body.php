<?php
/**
 * @package     Molajo
 * @subpackage  Audio
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

$this->audio_file_loader .= '    AudioPlayer.embed("audioplayer_'.$this->id.'", {soundFile: "'.JURI::base().$this->audio_folder.'/'.$this->audio_file.'"});'."\n";
?>
<p id="audioplayer_<?php echo $this->id; ?>">
    <?php echo $this->audio_label; ?>
</p>
