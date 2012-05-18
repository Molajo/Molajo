<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;

/**
 *  Builds recursive aspects of the header considering HTML5
 *
 *  Note: Avoid horizontal space outside of the PHP sections
 *      because it will be reflected in the header section
 */

if ($this->row->type == 'metadata'): ?>
    <meta name="<?php echo $this->row->name; ?>" content="<?php echo $this->row->content; ?>"<?php echo $end; ?>
<?php
elseif ($this->row->type == 'links'):
?>
    <link href="<?php echo $this->row->url; ?>" rel="<?php echo $this->row->relation; ?>"<?php echo $this->row->attributes; ?><?php echo $end; ?>
<?php
elseif ($this->row->type == 'css'):
?>
    <link rel="stylesheet" href="<?php echo $this->row->url; ?>"<?php if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ($this->row->media != null): ?> type="<?php echo $this->row->media; ?>"<?php endif; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes; ?><?php endif; ?><?php echo $end; ?>
<?php
elseif ($this->row->type == 'js'):
?>
    <script src="<?php echo $this->row->url; ?>"<?php if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ((int)$this->row->defer == 1): ?> defer="defer"<?php endif; ?><?php if ((int)$this->row->async == 1): ?> async="async"<?php endif; ?>></script><?php echo chr(10) . chr(13); ?>
<?php
elseif ($this->row->type == 'css_declarations'):
?>
    <style<?php if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php echo $end; ?>
<?php if ($page_mimetype == 'text/html') :
else : ?>
<![CDATA[
<?php endif;
echo $this->row->content . chr(10) . chr(13);
if ($page_mimetype == 'text/html') :
else : ?>
]]>
<?php
endif; ?>
     </style><?php echo chr(10) . chr(13); ?>
<?php
elseif ($this->row->type == 'js_declarations'): ?>
<script<?php if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?>>
<?php
if ($page_mimetype == 'text/html') :
else : ?>
<![CDATA[
<?php
endif;
echo '    ' . trim($this->row->content) . chr(10) . chr(13);
if ($page_mimetype == 'text/html') :
else : ?>
]]>
<?php
endif; ?>
</script><?php echo chr(10) . chr(13); ?>
<?php
endif;
