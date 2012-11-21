<?php
use Molajo\Service\Services;

/**
 *
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<p>
    <a href="<?php echo $this->row->catalog_id_url; ?>"><strong><?php echo $this->row->title; ?></strong></a>
    <?php if ($this->parameters['display_snippet'] == 1) {
    echo '<br />' . $this->row->content_text_snippet;
}
    if ($this->parameters['display_author_name'] == 1) {
        echo '<br />' . '<strong>' . Services::Language()->translate('Written by') . ':</strong> ' . $this->row->author_full_name;
    }
    if ($this->parameters['display_published_date'] == 1) {
        echo '<br />' . '<strong>' . Services::Language()->translate('Published') . ':</strong> ' . $this->row->start_publishing_datetime_pretty_date;
    } ?>
</p>
