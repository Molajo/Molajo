<?php
/**
 * @package     Molajo
 * @subpackage  Head
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Builds recursive aspects of the header considering HTML5
 *
 *  Note: Avoid horizontal space outside of the PHP sections
 *      because it will be reflected in the header section
 */

/** metadata */
if ($this->row->type == 'metadata'):
?>
<?php

/** links */
elseif ($this->row->type == 'links'):
?>
<link <?php echo $this->row->relation; ?>="<?php echo $this->row->relation_type; ?>" href="<?php echo $this->row->url; ?>" <?php echo $this->row->attributes; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes.' ';?><?php endif; ?>/>
<?php

    elseif ($this->row->type == 'stylesheet_links'):
?>
    <link rel="stylesheet" href="<?php echo $this->row->url; ?>" <?php if ($this->parameters->get('html5', true) === false): ?>type="<?php echo $this->row->mimetype; ?>" <?php endif; ?><?php if ($this->row->media != null): ?>type="<?php echo $this->row->media; ?>" <?php endif; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes.' ';?><?php endif; ?>/>
<?php

/** stylesheet_declarations */
elseif ($this->row->type == 'styles'):
?>
    <style<?php if ($this->parameters->get('html5', true) === false): ?> type="<?php echo $this->row->mimetype; ?>" <?php endif; ?>>
    <?php
        if ($this->row->mimetype == 'text/html') :
        else : ?>
        <![CDATA[
    <?php
        endif;
        echo $this->row->content;
        if ($this->row->mimetype == 'text/html') :
        else : ?>
        ]]>
    <?php
        endif; ?>
    </style>
<?php

/** javascript_links */
elseif ($this->row->type == 'javascript_links'):
?>
    <script src="<?php echo $this->row->url; ?>" <?php if ($this->parameters->get('html5', true) === false): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if (trim($this->row->async) != ''): ?>async="async" <?php endif; ?>/></script>
<?php

/** javascript declarations */
elseif ($this->row->type == 'script'):
?>
<?php endif; ?>