<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="container">
    <doc:include type="modules" name="header" wrap="header"/>
    <doc:include type="message"/>
    <section>
        <doc:include type="modules" name="menu" wrap="none"/>
        <doc:include type="component"/>
    </section>
    <doc:include type="modules" name="footer" wrap="footer"/>
</div>

<body id="-page" class="">

<div class="row">
    <div class="four columns">
        <h1><a href="../">Foundation</a></h1>
    </div>
    <div class="eight columns hide-on-phones">
        <strong class="right">
            <a href="../grid.php">Features</a>
            <a href="../case-soapbox.php">Case Studies</a>
            <a href="index.php">Documentation</a>

            <a href="http://github.com/zurb/foundation">Github</a>
            <a href="../files/foundation-download.zip" class="small blue nice button src-download">Download</a>

        </strong>
    </div>
</div>
</div>
<!-- /ZURBar -->
<div class="container">

    <div class="row">
        <div class="twelve columns">
            <div class="foundation-header">
                <h1><a href="index.php">Foundation Docs</a></h1>
                <h4 class="subheader">Rapid prototyping and building library from ZURB.</h4>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="two columns">
            <dl class="nice tabs vertical hide-on-phones">
                <dd><a href="index.php">Getting Started</a></dd>
                <dd><a href="grid.php">Grid</a></dd>
                <dd><a href="buttons.php">Buttons</a></dd>
                <dd><a href="forms.php">Forms</a></dd>

                <dd><a href="layout.php">Layout</a></dd>
                <dd><a href="ui.php">UI</a></dd>
                <dd><a href="orbit.php">Orbit</a></dd>
                <dd><a href="reveal.php" class="active">Reveal</a></dd>
                <dd><a href="gems.php">Gems</a></dd>
                <dd><a href="qa.php">QA</a></dd>

            </dl>
        </div>
        <div class="six columns">
            <h3>Reveal</h3>
            <h4 class="subheader">Reveal is our new modal plugin. We kept it light-weight, simple, and totally flexible
                (there's a 'your mom' joke in there somewhere). Go ahead, <a href="" data-reveal-id="testModal">see what
                    a default Reveal modal looks like.</a></h4>
            <hr/>

            <h4>Using Reveal</h4>

            <p>Reveal is a cinch to hook up - just include the JS and CSS. You can either call it in the JS or just
                include a new "data-reveal-id" parameter. If you need detailed steps check out the <a
                        href="http://www.zurb.com/playground/reveal-modal-plugin">playground for Reveal</a>, but here
                are the steps to get it started:</p>
            <ol>
                <li>The markup goes something like this:<br/><br/>
                    <script src="http://snipt.net/embed/abdf882c25e08d9ba219fe33f17591fe"></script>
                    <br/>
                </li>
                <li>

                    Activate Reveal...but there are two ways to do this glorious action. The first is to attach a
                    handler to something (button most likely) then call Reveal: <br/><br/>
                    <script src="http://snipt.net/embed/c723edab0ed473c55a27af5dce37abfe"></script>
                    <br/>
                    <strong>OR</strong> the new hotness option is to just add a data-reveal-id to the object which you
                    want to fire the modal when clicked...<br/><br/>
                    <script src="http://snipt.net/embed/896416888c9bf045d01aca39f64df7b7"></script>
                    <br/>
                    This will launch the modal with the ID "myModal2" without attaching a handler or calling the plugin
                    (since the plugin is always listening for this). You can also pass any of the parameters simply by
                    putting a data-nameOfParameter="value" (i.e. data-animation="fade")
                </li>

            </ol>
            <hr/>
            <h4>Options</h4>
            <script src="http://snipt.net/embed/190995aac581e583e72e9c2bd6bc1794"></script>
            <br/>

            <p>Options can be used on the "data-reveal-id" implementation too, just do it like this:</p>
            <script src="http://snipt.net/embed/34db731ca7ab2b9eabe5ac5dd381ea28"></script>

        </div>

        <div class="four columns">
            <div class="panel hide-on-phones">
                <h4>Get Foundation</h4>

                <p>Download Foundation here to get started quickly. Includes the base file structure, CSS, JS, and
                    required images.</p>

                <p>
                    <a href="../files/foundation-download.zip" class="nice radius blue button mobile src-download">Download
                        Foundation</a>
                </p>

            </div>


            <!-- ==========================
                   ZURBjobs
                   =============================== -->

            <div class="jobs hide-on-phones">
                <h5>Looking for Product Engineering or<br/>Design jobs? Check out:</h5>
                <script type="text/javascript"
                        src="http://www.zurb.com/jobs/widgets/jobs.js?limit=3&variation=foundation-sidebar"></script>
                <a id="via" href="http://zurbjobs.com">via&nbsp;<span class="jobs-link">ZURBjobs</span></a>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="twelve columns">
            <dl class="nice tabs mobile show-on-phones">
                <dd><a href="index.php">Getting Started</a></dd>
                <dd><a href="grid.php">Grid</a></dd>

                <dd><a href="buttons.php">Buttons</a></dd>
                <dd><a href="forms.php">Forms</a></dd>
                <dd><a href="layout.php">Layout</a></dd>
                <dd><a href="ui.php">UI</a></dd>
                <dd><a href="orbit.php">Orbit</a></dd>
                <dd><a href="reveal.php" class="active">Reveal</a></dd>

                <dd><a href="qa.php">QA</a></dd>
            </dl>
        </div>
    </div>


    <div id="testModal" class="reveal-modal">
        <h2>Awww yeah, modal dialog!</h2>

        <p class="lead">Yeah it's just the best.</p>

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ultrices aliquet placerat. Duis pulvinar orci et
            nisi euismod vitae tempus lorem consectetur. Duis at magna quis turpis mattis venenatis eget id diam. </p>
        <a class="close-reveal-modal">&#215;</a>
        <a href="" class="nice radius button">This is a Button</a>
    </div>

    <!-- Das Footer -->
    <footer class="row">
        <section class="five columns">
            <h6><strong>Made by ZURB</strong></h6>

            <p>Foundation is made by <a href="http://www.zurb.com/">ZURB</a>, an <a
                    href="http://www.zurb.com/words/design-process">interaction design and strategy company</a> located
                in Campbell, California. We've put over 10 years of experience building web products, services and
                websites into this framework. <a href="../about.php">Foundation Info and Goodies &rarr;</a></p>
        </section>

        <section class="three columns">
            <h6><strong>Using Foundation?</strong></h6>

            <p>Let us know how you're using Foundation and we might feature you as an example! <a
                    href="mailto:foundation@zurb.com?subject=I'm%20using%20Foundation">Get in touch &rarr;</a></p>

        </section>

        <section class="four columns">
            <h6><strong>Need some help?</strong></h6>

            <p>For quick answers or help <a href="mailto:foundation@zurb.com">email us &rarr;</a></p>

            <ul class="block-grid three-up">
                <li>
                    <!-- Place this tag where you want the +1 button to render -->

                    <g:plusone size="medium"></g:plusone>

                    <!-- Place this render call where appropriate -->
                    <script>
                        (function() {
                            var po = document.createElement('script');
                            po.type = 'text/javascript';
                            po.async = true;
                            po.src = 'https://apis.google.com/js/plusone.js';
                            var s = document.getElementsByTagName('script')[0];
                            s.parentNode.insertBefore(po, s);
                        })();
                    </script>
                </li>
                <li>

                    <iframe allowtransparency="true" frameborder="0" scrolling="no"
                            src="http://platform.twitter.com/widgets/follow_button.html?screen_name=foundationzurb"
                            style="width:80px; height:20px;"></iframe>
                </li>
                <li>

                    <iframe src="http://www.facebook.com/plugins/like.php?app_id=273982815961057&amp;href=foundation.zurb.com&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font=lucida+grande&amp;height=21"
                            scrolling="no" frameborder="0"
                            style="border:none; overflow:hidden; width:90px; height:21px;"
                            allowTransparency="true"></iframe>
                </li>
</div>
</section>
</footer>
<!-- /Das Footer -->
</div>

<script>

    var _gaq = _gaq || [];
    _gaq.push(
            ['_setAccount', 'UA-2195009-2'],
            ['_trackPageview'],
            ['b._setAccount', 'UA-2195009-27'],
            ['b._trackPageview']
    );

    (function() {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();
</script>

</body>