<?php
/**
 * @package     Molajo
 * @subpackage  Style Guide
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>

<h4><?php echo MolajoTextHelper::_('MOLAJO_STYLEGUIDE_BLOCKQUOTE'); ?></h4>

<blockquote>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante venenatis dapibus posuere
        velit aliquet.</p>
    <small>Dr. Julius Hibbert</small>
</blockquote>

<address>
    <strong>Twitter, Inc.</strong><br/>
    795 Folsom Ave, Suite 600<br/>
    San Francisco, CA 94107<br/>
    <abbr title="Phone">P:</abbr> (123) 456-7890<br/>
    <a mailto="">first.last@gmail.com</a>
</address>


<h4>Unordered</h4>
<ul>
    <li>Lorem ipsum dolor sit amet</li>
    <li>Consectetur adipiscing elit</li>
    <li>Integer molestie lorem at massa</li>

    <li>Facilisis in pretium nisl aliquet</li>
    <li>Nulla volutpat aliquam velit
        <ul>
            <li>Phasellus iaculis neque</li>
            <li>Purus sodales ultricies</li>
            <li>Vestibulum laoreet porttitor sem</li>
            <li>Ac tristique libero volutpat at</li>

        </ul>
    </li>
    <li>Faucibus porta lacus fringilla vel</li>
    <li>Aenean sit amet erat nunc</li>
    <li>Eget porttitor lorem</li>
</ul>
</div>

<div class="span4">
    <h4>Unstyled <code>&lt;ul.unstyled&gt;</code></h4>
    <ul class="unstyled">
        <li>Lorem ipsum dolor sit amet</li>
        <li>Consectetur adipiscing elit</li>
        <li>Integer molestie lorem at massa</li>

        <li>Facilisis in pretium nisl aliquet</li>
        <li>Nulla volutpat aliquam velit
            <ul>
                <li>Phasellus iaculis neque</li>
                <li>Purus sodales ultricies</li>
                <li>Vestibulum laoreet porttitor sem</li>
                <li>Ac tristique libero volutpat at</li>

            </ul>
        </li>
        <li>Faucibus porta lacus fringilla vel</li>
        <li>Aenean sit amet erat nunc</li>
        <li>Eget porttitor lorem</li>
    </ul>
</div>

<div class="span4">
    <h4>Ordered <code>&lt;ol&gt;</code></h4>
    <ol>
        <li>Lorem ipsum dolor sit amet</li>
        <li>Consectetur adipiscing elit</li>
        <li>Integer molestie lorem at massa</li>

        <li>Facilisis in pretium nisl aliquet</li>
        <li>Nulla volutpat aliquam velit</li>
        <li>Faucibus porta lacus fringilla vel</li>
        <li>Aenean sit amet erat nunc</li>
        <li>Eget porttitor lorem</li>
    </ol>

</div>
<div class="span4">
    <h4>Description <code>dl</code></h4>
    <dl>
        <dt>Description lists</dt>
        <dd>A description list is perfect for defining terms.</dd>
        <dt>Euismod</dt>

        <dd>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</dd>
        <dd>Donec id elit non mi porta gravida at eget metus.</dd>
        <dt>Malesuada porta</dt>
        <dd>Etiam porta sem malesuada magna mollis euismod.</dd>
    </dl>
</div>
</div>

<h4>Large</h4>

<ul class="media-grid">
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/330x230" alt="">
        </a>
    </li>
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/330x230" alt="">
        </a>

    </li>
</ul>

<h4>Medium</h4>

<ul class="media-grid">
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/210x150" alt="">
        </a>

    </li>
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/210x150" alt="">
        </a>
    </li>
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/210x150" alt="">

        </a>
    </li>
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/210x150" alt="">
        </a>
    </li>
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/210x150" alt="">
        </a>
    </li>
</ul>

<h4>Small</h4>
<ul class="media-grid">
    <li>
        <a href="#">

            <img class="thumbnail" src="http://placehold.it/90x90" alt="">
        </a>
    </li>
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/90x90" alt="">
        </a>
    </li>
    <li>
        <a href="#">
            <img class="thumbnail" src="http://placehold.it/90x90" alt="">
        </a>
    </li>
</ul>