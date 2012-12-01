<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<article class="blog">
    <h3>
        <a href="<?php echo $this->row->catalog_sef_request; ?>">
            <?php echo $this->row->title; ?>
        </a>
    </h3>
    <h6><?php echo Services::Language()->translate('Written by'); ?>
        <a href="#">
            <?php echo $this->row->author_full_name; ?>
        </a>
        <?php echo Services::Language()->translate(' on '); ?>
        <?php echo $this->row->start_publishing_datetime_day_name
            . ', ' . $this->row->start_publishing_datetime_month_name
            . ' ' . $this->row->start_publishing_datetime_dd
            . ', ' . $this->row->start_publishing_datetime_ccyy; ?>.
    </h6>

    <?php if ($this->row->image1 == '') {
} else {
    ?>
    <figure class="float_left image1">
        <img src="<?php echo $this->row->image1; ?>" alt="">
        <?php if ($this->row->image_caption1 == '') {
    } else {
        ?>
        <figcaption><?php echo $this->row->image_caption1; ?></figcaption>
        <?php } ?>
    </figure>
    <?php
}
    if ($this->row->content_text_introductory == '') {
    } else {
        ?>
        <div class="introductory"><?php echo $this->row->content_text_introductory; ?></div>
        <?php } ?>
    <p class="readmore"><a href="<?php echo $this->row->catalog_sef_request; ?>"
                           class="<?php echo $this->get('template_view_css_class'); ?>">Read more &rarr;</a></p>
</article>
