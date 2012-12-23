<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die; ?>

<h3><?php echo $this->row->title; ?></h3>

<h6><?php echo Services::Language()->translate('Written by'); ?>
    <a href="#">
        <?php echo $this->row->created_by_full_name; ?>
    </a>
    <?php echo Services::Language()->translate(' on '); ?>
    <?php echo $this->row->start_publishing_datetime_day_name
        . ', ' . $this->row->start_publishing_datetime_month_name
        . ' ' . $this->row->start_publishing_datetime_dd
        . ', '. $this->row->start_publishing_datetime_ccyy; ?>.
</h6>

<?php if ($this->row->image2 == '') {
} else { ?>
<figure class="float_right image2">
    <img src="<?php echo $this->row->image2; ?>" alt="">
    <?php if ($this->row->image_caption2 == '') {
} else { ?>
    <figcaption><?php echo $this->row->image_caption2; ?></figcaption>
    <?php } ?>
</figure>
<?php }

if ($this->row->content_text_introductory == '') {
} else { ?>
<div class="introductory"><?php echo $this->row->content_text_introductory; ?></div>
<?php }

if ($this->row->image3 == '') {
} else { ?>
<figure class="float_left image3">
    <img src="<?php echo $this->row->image3; ?>" alt="">
    <?php if ($this->row->image_caption3 == '') {
} else { ?>
    <figcaption><?php echo $this->row->image_caption3; ?></figcaption>
    <?php } ?>
</figure>
<?php }

echo $this->row->content_text_fulltext;
