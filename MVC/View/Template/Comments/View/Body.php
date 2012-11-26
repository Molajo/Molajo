<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageURL = Services::Registry()->get('Plugindata', 'page_url');
?>
<article class="<?php //echo trim('comment' . ' ' . $this->row->first_row . ' ' . $this->row->last_row . ' ' . $this->row->even_or_odd_row  . ' ' . $this->row->css_class); ?>"
         id="<?php echo $this->row->alias; ?>">
    <div class="comment-meta">
        <a class="permalink" href="<?php echo $pageURL; ?>#<?php echo $this->row->alias; ?>">#</a>
        <?php echo Services::Language()->translate('On') . ' ' ?>
            <time datetime="<?php echo $this->row->created_datetime; ?>" pubdate>
                <?php echo $this->row->created_datetime_month_name
                . ' ' . $this->row->created_datetime_day_number
                . ', ' . $this->row->created_datetime_ccyy
                . ', ' . Services::Language()->translate('at')
                . ' ' . $this->row->created_datetime_time; ?>
            </time>,
        <?php if ($this->row->website == '') {
                } else { ?>
                    <a href="<?php echo $this->row->website; ?>" rel='external nofollow' class='url'>
            <?php } ?>
            <?php echo $this->row->visitor_name; ?>
        <?php if ($this->row->website == '') {
                } else { ?>
                </a>
            <?php }
        echo Services::Language()->translate('said:') . ' ';  ?>
        <?php if ($this->row->title == '') {
        } else { ?>
        <header>
            <h5><?php echo $this->row->title; ?></h5>
        </header>
        <?php } ?>
    </div>
    <img alt="" src="http://1.gravatar.com/avatar/37372c3acc31affca29886c1a9cbc13b?s=48&amp;d=http%3A%2F%2F1.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D48&amp;r=G" class="gravatar float_right" height="48" width="48" />
    <div class="comment-text">
        <p class="comment-text"><?php echo $this->row->content_text; ?></p>
    </div>
</article>
