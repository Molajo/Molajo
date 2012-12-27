<?php
/**
 * @package     Niambie
 * @copyright   2013 Amy Stephen. All rights reserved.
 * @license     MIT
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die; ?>
<section itemscope itemtype="http://data-vocabulary.org/Person">
    <h1 itemprop="name">
        <?php echo $this->row->author_full_name; ?>
    </h1>
    <?php if ($this->row->author_parameters_display_gravatar == 1) { ?>
    <p>
        <img class="gravatar float_left" src="<?php echo $this->row->author_email_gravatar; ?>" itemprop="photo"
             alt="[<?php echo $this->row->author_full_name; ?>]">
    </p>
    <?php } ?>
    <p>
        <a href="<?php echo $this->row->author_catalog_sef_request; ?>"
           title="<?php echo Services::Language()->translate('Posts by '); ?>
            <span itemprop=" name">
        <?php echo $this->row->author_full_name; ?>
        </span>
        </a>
    </p>
    <?php if ($this->row->author_parameters_display_phone == 1) { ?>
    <p itemprop="tel">
        <?php echo $this->row->author_phone; ?>
    </p>
    <?php } ?>
    <?php if ($this->row->author_parameters_display_email == 1) { ?>
    <h5>
        <?php echo $this->row->author_email; ?>
    </h5>
    <?php } ?>
    <p>
        <?php echo $this->row->author_about_me;  ?>
    </p>
</section>
