<?php
/**
 * @package     Molajo
 * @subpackage  Views
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
if ($this->row->type == 'js'):
?>
<script src="<?php echo $this->row->url; ?>"<?php if ((int)Services::Configuration()->get('html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ((int)$this->row->defer == 1): ?> defer="defer"<?php endif; ?><?php if ((int)$this->row->async == 1): ?> async="async"<?php endif; ?>></script><?php echo chr(10) . chr(13); ?>
<?php
elseif ($this->row->type == 'js_declarations'):
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
</script><?php echo chr(10) . chr(13); ?>
<?php
endif;
