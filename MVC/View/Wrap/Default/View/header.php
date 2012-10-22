<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

if ((int) Services::Registry()->get('Configuration', 'application_html5', 1) == 1
    && ($this->parameters['wrap_view_title'] == 'Article'
        || $this->parameters['wrap_view_title'] == 'Aside'
        || $this->parameters['wrap_view_title'] == 'Footer'
        || $this->parameters['wrap_view_title'] == 'Header'
        || $this->parameters['wrap_view_title'] == 'Hgroup'
        || $this->parameters['wrap_view_title'] == 'Nav'
        || $this->parameters['wrap_view_title'] == 'Section')
):
    $headerType = $this->parameters['wrap_view_title']; else :
    $headerType = 'div';
endif;
$headerType = strtolower($headerType);

$headerId = trim($this->parameters['wrap_view_css_id']);
if ($headerId == '') :
else :
    $headerId = ' id="' . $headerId . '"';
endif;

$headerClass = trim($this->parameters['wrap_view_css_class']);
if ($headerClass == '') :
else :
    $headerClass = ' class="' . $headerClass . '"';
endif;

$headerRole = trim($this->parameters['wrap_view_role']);
if ($headerRole == '') :
else :
    $headerRole = ' role="' . $headerRole . '"';
endif;

$headerProperty = trim($this->parameters['wrap_view_property']);
if ($headerProperty == '') :
else :
    $headerProperty = ' property="' . $headerProperty . '"';
endif;
?>
<<?php echo trim($headerType . $headerId . $headerClass . $headerRole . $headerProperty); ?>>
<?php
$headingLevel = $this->parameters['wrap_view_header_level'];
if ((int) Services::Registry()->get('Configuration', 'application_html5', 1) == 1):
    if ($this->parameters['wrap_view_show_title'] === true
        && $this->parameters['wrap_view_show_subtitle'] === true
    ) : ?>
    <hgroup>
<?php endif;
endif;

if ($this->parameters['wrap_view_show_title'] === true) :  ?>
    <h<?php echo $headingLevel; ?>>
        <?php echo $this->row->title; ?>
    </h<?php echo $headingLevel++; ?>>
    <?php
endif;

if ($this->parameters['wrap_view_show_subtitle'] === true) :  ?>
    <h<?php echo $headingLevel; ?>>
        <?php echo $this->row->subtitle; ?>
    </h<?php echo $headingLevel++; ?>>
    <?php
endif;

if ((int) Services::Registry()->get('Configuration', 'application_html5', 1) == 1) :
    if ($this->parameters['wrap_view_show_title'] === true
        && $this->parameters['wrap_view_show_subtitle'] === true
    ) : ?>
    </hgroup>
<?php endif;
endif;
