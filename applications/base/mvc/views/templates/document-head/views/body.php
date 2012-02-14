<?php
/**
 * @package     Molajo
 * @subpackage  View
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
if ($this->row->type == 'metadata'): ?>

<?php
    elseif ($this->row->type == 'stylesheet_links'):
?>

    <link rel="stylesheet" href="<?php echo $this->row->url; ?>"<?php if ((int)Services::Configuration()->get('html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ($this->row->media != null): ?> type="<?php echo $this->row->media; ?>"<?php endif; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes;?><?php endif; ?><?php echo $end; ?>
<?php
    elseif ($this->row->type == 'javascript_links'): ?>

    <script src="<?php echo $this->row->url; ?>" <?php if ((int)Services::Configuration()->get('html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ((int)$this->row->defer == 1): ?>defer="defer" <?php endif; ?><?php if ((int)$this->row->async == 1): ?>async="async" <?php endif; ?>/></script>
<?php

/** stylesheet_declarations */
elseif ($this->row->type == 'stylesheet_declarations'):
?>
    <style<?php if ((int)Services::Configuration()->get('html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>" <?php endif; ?>>
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

/** stylesheet_declarations */
elseif ($this->row->type == 'script_declarations'):
?>

    <script<?php if ((int)Services::Configuration()->get('html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?>>
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
    </script>
<?php
endif;
