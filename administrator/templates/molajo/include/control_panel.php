<div id="content-box">
    <div class="border">
        <div class="padding">
            <div id="element-box">
                <jdoc:include type="message" />
                <div class="t">
                    <div class="t">
                        <div class="t"></div>
                    </div>
                </div>
                <div class="m" >
                <div class="adminform">
                    <div class="control-panel-left">
                        <?php if ($this->countModules('icon')>1):?>
                            <?php echo JHtml::_('sliders.start', 'position-icon', array('useCookie' => 1));?>
                            <jdoc:include type="modules" name="icon" style="sliders" />
                            <?php echo JHtml::_('sliders.end');?>
                        <?php else:?>
                            <jdoc:include type="modules" name="icon" />
                        <?php endif;?>
                    </div>
                    <div class="control-panel-right">
                        <jdoc:include type="component" />
                    </div>
                </div>
                    <div class="clr"></div>
                </div>
                <div class="b">
                    <div class="b">
                        <div class="b"></div>
                    </div>
                </div>
            </div>
            <noscript>
                <?php echo  JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
            </noscript>
            <div class="clr"></div>
        </div>
    </div>
</div>