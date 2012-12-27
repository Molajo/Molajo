<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;
$application_html5 = $this->row->application_html5;
$end               = $this->row->end;
?>
    <link href="<?php echo $this->row->url; ?>"
          rel="<?php echo $this->row->relation; ?>"<?php echo $this->row->attributes; ?><?php echo $end;
