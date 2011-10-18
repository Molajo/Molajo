<?php defined('_JEXEC') or die;
/**
* @package		Template Framework for Molajo 1.5
* @author		Joomla Engineering http://joomlaengineering.com
* @copyright	Copyright (C) 2010, 2011 Matt Thomas | Joomla Engineering. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Load Joomla filesystem package
jimport('joomla.filesystem.file');

// Load template logic
$logicFile				= JPATH_THEMES.'/'.$this->template.'/elements/logic.php';
if(JFile::exists($logicFile)) {
	include $logicFile;
}

// Mobile device detection
if(JFile::exists($mdetectFile)) {
	include_once $mdetectFile;
	// Instantiate the mobile object class
	$uagent_obj 		= new uagent_info();
	$isMobile 			= $uagent_obj->DetectMobileLong();
	$isTablet			= $uagent_obj->DetectTierTablet();
}

// Check if mobile device detecion is turned on and, test if visitor is a mobile device, and if so, load mobile sub-template
if (( $mdetect && $isMobile ) || ( $mdetect && $detectTablets && $isTablet )) {
	if(JFile::exists($mTemplate)) {
	 	include_once $mTemplate;
	}
}

// If mobile detection is off, or visitor is not a mobile device, check for alternate index file and load it if it exists
$results = $layoutOverride->getIncludeFile ();

if ($results) {
    $alternateIndexFile = $results;
	include_once $alternateIndexFile;	
} else {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />
</head>

<body class="<?php echo $columnLayout; if($useStickyFooter) echo ' sticky-footer'; echo ' '.$currentComponent; if($articleId) echo ' article-'.$articleId; if ($itemId) echo ' item-'.$itemId; if($catId) echo ' category-'.$catId; if($sectionId) echo ' section-'.$sectionId; ?>">

	<div id="footer-push">
			<a id="page-top" name="page-top"></a>
			<?php if ($headerAboveCount) : ?>
				<div id="header-above" class="clearfix">						
					<?php if ($this->countModules('header-above-1')) : ?>
						<div id="header-above-1" class="<?php echo $headerAboveClass ?>">
							<jdoc:include type="modules" name="header-above-1" style="jexhtml" />
						</div><!-- end header-above-1 -->								
					<?php endif; ?>
					
					<?php if ($this->countModules('header-above-2')) : ?>
						<div id="header-above-2" class="<?php echo $headerAboveClass ?>">
							<jdoc:include type="modules" name="header-above-2" style="jexhtml" />
						</div><!-- end header-above-2 -->
					<?php endif; ?>
					
					<?php if ($this->countModules('header-above-3')) : ?>
						<div id="header-above-3" class="<?php echo $headerAboveClass ?>">
							<jdoc:include type="modules" name="header-above-3" style="jexhtml" />
						</div><!-- end header-above-3 -->								
					<?php endif; ?>
					
					<?php if ($this->countModules('header-above-4')) : ?>
						<div id="header-above-4" class="<?php echo $headerAboveClass ?>">
							<jdoc:include type="modules" name="header-above-4" style="jexhtml" />
						</div><!-- end header-above-4 -->								
					<?php endif; ?>
					
					<?php if ($this->countModules('header-above-5')) : ?>
						<div id="header-above-5" class="<?php echo $headerAboveClass ?>">
							<jdoc:include type="modules" name="header-above-5" style="jexhtml" />
						</div><!-- end header-above-5 -->								
					<?php endif; ?>
					
					<?php if ($this->countModules('header-above-6')) : ?>
						<div id="header-above-6" class="<?php echo $headerAboveClass ?>">
							<jdoc:include type="modules" name="header-above-6" style="jexhtml" />
						</div><!-- end header-above-6 -->								
					<?php endif; ?>
				</div><!-- end header-above -->
			<?php endif; ?>				
		
		<div id="header" class="clear clearfix">
			<div class="gutter clearfix">

				<div class="date-container">
					<span class="date-weekday"><?php	$now = &MolajoFactory::getDate(); echo $now->toFormat('%A').','; ?></span>
					<span class="date-month"><?php 		$now = &MolajoFactory::getDate(); echo $now->toFormat('%B'); ?></span>
					<span class="date-day"><?php 		$now = &MolajoFactory::getDate(); echo $now->toFormat('%d').','; ?></span>
					<span class="date-year"><?php 		$now = &MolajoFactory::getDate(); echo $now->toFormat('%Y'); ?></span>
				</div>
				
				<?php if ($showDiagnostics) : ?>
					<ul id="diagnostics">
						<li><?php echo $currentComponent; ?></li>
						<?php if($view)			echo '<li>'.$view.' view</li>'; ?>						
						<?php if($articleId)	echo '<li>article-'.$articleId.'</li>'; ?>
						<?php if($itemId)		echo '<li>item-'.$itemId.'</li>'; ?>
						<?php if($catId)		echo '<li>category-'.$catId.'</li>'; ?>
						<?php if($sectionId) 	echo '<li>section-'.$sectionId.'</li>'; ?>
						<?php if($catId) {							
								echo '<li>Parent Category '.$parentCategory.'</li>';							
								echo '<li>Ancestor Categories:';		
								$results = getAncestorCategories($catId);
									if (count($results) > 0) {
										foreach ($results as $item) {
											echo ' '.$item->id.',';
										}			
									}
								echo'</li>';
								} ?>
					</ul>
				<?php endif; ?>	

				<h1 id="logo"><a href="<?php echo $this->baseurl ?>/" title="<?php echo $app->getCfg('sitename');?>"><?php echo $app->getCfg('sitename');?></a></h1>
				
				<?php if ($this->countModules('header')) : ?>
					<jdoc:include type="modules" name="header" style="jexhtml" />	
				<?php endif; ?>		
				
				<ul id="access">
				  <li>Jump to:</li>
				  <li><a href="<?php $url->setFragment('content'); echo $url->toString();?>" class="to-content">Content</a></li>					
				  <?php if ($this->countModules('nav')) : ?>
					<li><a href="<?php $url->setFragment('nav'); echo $url->toString();?>" class="to-nav">Navigation</a></li>
				  <?php endif; ?>					
				  <?php if ($contentBelowCount) : ?>
					<li><a href="<?php $url->setFragment('additional'); echo $url->toString();?>" class="to-additional">Additional Information</a></li>
				  <?php endif; ?>
				</ul>				
				
				<?php if ($enableSwitcher) : ?>
					<ul id="style-switch">
						<li><a href="#" onclick="setActiveStyleSheet('wireframe'); return false;" title="Wireframe">Wireframe</a></li>
						<li><a href="#" onclick="setActiveStyleSheet('diagnostic'); return false;" title="Diagnostic">Diagnostic Mode</a></li>
						<li><a href="#" onclick="setActiveStyleSheet('normal'); return false;" title="Normal">Normal Mode</a></li>
					</ul>
				<?php endif; ?>	

			</div><!--end gutter -->
		</div><!-- end header-->
		   
		<div id="body-container">

			<?php if ($headerBelowCount) : ?>
				<div id="header-below" class="clearfix">						
					<?php if ($this->countModules('header-below-1')) : ?>
						<div id="header-below-1" class="<?php echo $headerBelowClass ?>">
							<jdoc:include type="modules" name="header-below-1" style="jexhtml" module-class="gutter"/>
						</div><!-- end header-below-1 -->								
					<?php endif; ?>
					
					<?php if ($this->countModules('header-below-2')) : ?>
						<div id="header-below-2" class="<?php echo $headerBelowClass ?>">
							<jdoc:include type="modules" name="header-below-2" style="jexhtml" module-class="gutter"/>
						</div><!-- end header-below-2 -->
					<?php endif; ?>
					
					<?php if ($this->countModules('header-below-3')) : ?>
						<div id="header-below-3" class="<?php echo $headerBelowClass ?>">
							<jdoc:include type="modules" name="header-below-3" style="jexhtml" module-class="gutter"/>
						</div><!-- end header-below-3 -->
					<?php endif; ?>
					
					<?php if ($this->countModules('header-below-4')) : ?>
						<div id="header-below-4" class="<?php echo $headerBelowClass ?>">
							<jdoc:include type="modules" name="header-below-4" style="jexhtml" module-class="gutter"/>
						</div><!-- end header-below-4 -->
					<?php endif; ?>
					
					<?php if ($this->countModules('header-below-5')) : ?>
						<div id="header-below-5" class="<?php echo $headerBelowClass ?>">
							<jdoc:include type="modules" name="header-below-5" style="jexhtml" module-class="gutter"/>
						</div><!-- end header-below-5 -->
					<?php endif; ?>
					
					<?php if ($this->countModules('header-below-6')) : ?>
						<div id="header-below-6" class="<?php echo $headerBelowClass ?>">
							<jdoc:include type="modules" name="header-below-6" style="jexhtml" module-class="gutter"/>
						</div><!-- end header-below-6 -->
					<?php endif; ?>											
				</div><!-- end header-below -->
			<?php endif; ?>
		
			<?php if ($this->countModules('breadcrumbs')) : ?>		
				<jdoc:include type="module" name="breadcrumbs" />			
			<?php endif; ?>		
			
			<?php if ($this->countModules('nav')) : ?>
				<div id="nav" class="clear clearfix">
					<jdoc:include type="modules" name="nav" style="raw" />
				</div><!-- end nav-->
			<?php endif; ?>
	  
			<div id="content-container" class="clear clearfix">    

				<?php if ($navBelowCount) : ?>
					<div id="nav-below" class="clearfix">						
						<?php if ($this->countModules('nav-below-1')) : ?>
							<div id="nav-below-1" class="<?php echo $navBelowClass ?>">
								<jdoc:include type="modules" name="nav-below-1" style="jexhtml" module-class="gutter" />
							</div><!-- end nav-below-1 -->								
						<?php endif; ?>
						
						<?php if ($this->countModules('nav-below-2')) : ?>
							<div id="nav-below-2" class="<?php echo $navBelowClass ?>">
								<jdoc:include type="modules" name="nav-below-2" style="jexhtml" module-class="gutter" />
							</div><!-- end nav-below-2 -->
						<?php endif; ?>
						
						<?php if ($this->countModules('nav-below-3')) : ?>
							<div id="nav-below-3" class="<?php echo $navBelowClass ?>">
								<jdoc:include type="modules" name="nav-below-3" style="jexhtml" module-class="gutter" />
							</div><!-- end nav-below-3 -->
						<?php endif; ?>
						
						<?php if ($this->countModules('nav-below-4')) : ?>
							<div id="nav-below-4" class="<?php echo $navBelowClass ?>">
								<jdoc:include type="modules" name="nav-below-4" style="jexhtml" module-class="gutter" />
							</div><!-- end nav-below-4 -->
						<?php endif; ?>
						
						<?php if ($this->countModules('nav-below-5')) : ?>
							<div id="nav-below-5" class="<?php echo $navBelowClass ?>">
								<jdoc:include type="modules" name="nav-below-5" style="jexhtml" module-class="gutter" />
							</div><!-- end nav-below-5 -->
						<?php endif; ?>
						
						<?php if ($this->countModules('nav-below-6')) : ?>
							<div id="nav-below-6" class="<?php echo $navBelowClass ?>">
								<jdoc:include type="modules" name="nav-below-6" style="jexhtml" module-class="gutter" />
							</div><!-- end nav-below-6 -->
						<?php endif; ?>													
					</div><!-- end nav-below -->
				<?php endif; ?>
				
				<div id="load-first" class="clearfix">
					<a id="content" name="content"></a>     
					<div id="content-main">
						<div class="gutter">
						
							<?php if ($contentAboveCount) : ?>
								<div id="content-above" class="clearfix">						
									<?php if ($this->countModules('content-above-1')) : ?>
										<div id="content-above-1" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-above-1" style="jexhtml" module-class="gutter" />
										</div><!-- end content-above-1 -->								
									<?php endif; ?>
							
									<?php if ($this->countModules('content-above-2')) : ?>
										<div id="content-above-2" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-above-2" style="jexhtml" module-class="gutter" />
										</div><!-- end content-above-2 -->
									<?php endif; ?>
									
									<?php if ($this->countModules('content-above-3')) : ?>
										<div id="content-above-3" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-above-3" style="jexhtml" module-class="gutter" />
										</div><!-- end content-above-3 -->
									<?php endif; ?>
									
									<?php if ($this->countModules('content-above-4')) : ?>
										<div id="content-above-4" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-above-4" style="jexhtml" module-class="gutter" />
										</div><!-- end content-above-4 -->
									<?php endif; ?>

									<?php if ($this->countModules('content-above-5')) : ?>
										<div id="content-above-5" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-above-5" style="jexhtml" module-class="gutter" />
										</div><!-- end content-above-5 -->
									<?php endif; ?>
									
									<?php if ($this->countModules('content-above-6')) : ?>
										<div id="content-above-6" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-above-6" style="jexhtml" module-class="gutter" />
										</div><!-- end content-above-6 -->
									<?php endif; ?>									
								</div><!-- end content-above -->
							<?php endif; ?>
					  
							<?php if ($this->getBuffer('message')) : ?>
								<jdoc:include type="message" />
							<?php endif; ?>

							<jdoc:include type="component" />
								
							<?php if ($contentBelowCount) : ?>
								<div id="content-below" class="clearfix">						
									<?php if ($this->countModules('content-below-1')) : ?>
										<div id="content-below-1" class="<?php echo $contentBelowClass ?>">
											<jdoc:include type="modules" name="content-below-1" style="jexhtml" module-class="gutter" />
										</div><!-- end content-below-1 -->								
									<?php endif; ?>
						
									<?php if ($this->countModules('content-below-2')) : ?>
										<div id="content-below-2" class="<?php echo $contentBelowClass ?>">
											<jdoc:include type="modules" name="content-below-2" style="jexhtml" module-class="gutter" />
										</div><!-- end content-below-2 -->
									<?php endif; ?>
									
									<?php if ($this->countModules('content-below-3')) : ?>
										<div id="content-below-3" class="<?php echo $contentBelowClass ?>">
											<jdoc:include type="modules" name="content-below-3" style="jexhtml" module-class="gutter" />
										</div><!-- end content-below-3 -->
									<?php endif; ?>
									
									<?php if ($this->countModules('content-below-4')) : ?>
										<div id="content-below-4" class="<?php echo $contentBelowClass ?>">
											<jdoc:include type="modules" name="content-below-4" style="jexhtml" module-class="gutter" />
										</div><!-- end content-below-4 -->
									<?php endif; ?>
									
									<?php if ($this->countModules('content-below-5')) : ?>
										<div id="content-below-5" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-below-5" style="jexhtml" module-class="gutter" />
										</div><!-- end content-below-5 -->
									<?php endif; ?>
									
									<?php if ($this->countModules('content-below-6')) : ?>
										<div id="content-below-6" class="<?php echo $contentAboveClass ?>">
											<jdoc:include type="modules" name="content-below-6" style="jexhtml" module-class="gutter" />
										</div><!-- end content-below-6 -->
									<?php endif; ?>									
								</div><!-- end content-below -->
							<?php endif; ?>
							
						</div><!--end gutter -->        
					</div><!-- end content-main -->
					
					<?php if ($columnGroupAlphaCount) : ?>
						<div id="column-group-alpha" class="clearfix">
							<?php if ($this->countModules('column-1')) : ?>
								<div id="column-1" class="<?php echo $columnGroupAlphaClass ?>">
									<div class="gutter clearfix">
										<jdoc:include type="modules" name="column-1" style="jexhtml" />
									</div><!--end gutter -->
								</div><!-- end column-1 -->
							<?php endif; ?>
							<?php if ($this->countModules('column-2')) : ?>
								<div id="column-2" class="<?php echo $columnGroupAlphaClass ?>">
									<div class="gutter clearfix">
										<jdoc:include type="modules" name="column-2" style="jexhtml" />
									</div><!--end gutter -->
								</div><!-- end column-2 -->
							<?php endif; ?>
						</div><!-- end column-group-alpha -->
					<?php endif; ?>

				</div><!-- end load-first -->
		
					<?php if ($columnGroupBetaCount) : ?>
						<div id="column-group-beta" class="clearfix">
							<?php if ($this->countModules('column-3')) : ?>
								<div id="column-3" class="<?php echo $columnGroupBetaClass ?>">
									<div class="gutter clearfix">
										<jdoc:include type="modules" name="column-3" style="jexhtml" />
									</div><!--end gutter -->
								</div><!-- end column-2 -->								
							<?php endif; ?>
							<?php if ($this->countModules('column-4')) : ?>
								<div id="column-4" class="<?php echo $columnGroupBetaClass ?>">
									<div class="gutter clearfix">
										<jdoc:include type="modules" name="column-4" style="jexhtml" />
									</div><!--end gutter -->
								</div><!-- end column-4 -->
							<?php endif; ?>
						</div><!-- end column-group-beta -->
					<?php endif; ?>
			
				<?php if ($footerAboveCount) : ?>
					<div id="footer-above" class="clearfix">						
						<?php if ($this->countModules('footer-above-1')) : ?>
							<div id="footer-above-1" class="<?php echo $footerAboveClass ?>">
								<jdoc:include type="modules" name="footer-above-1" style="jexhtml" module-class="gutter" />
							</div><!-- end footer-above-1 -->								
						<?php endif; ?>			   
						<?php if ($this->countModules('footer-above-2')) : ?>
							<div id="footer-above-2" class="<?php echo $footerAboveClass ?>">
								<jdoc:include type="modules" name="footer-above-2" style="jexhtml" module-class="gutter" />
							</div><!-- end footer-above-2 -->
						<?php endif; ?>					
						<?php if ($this->countModules('footer-above-3')) : ?>
							<div id="footer-above-3" class="<?php echo $footerAboveClass ?>">
								<jdoc:include type="modules" name="footer-above-3" style="jexhtml" module-class="gutter" />
							</div><!-- end footer-above-3 -->
						<?php endif; ?>					
						<?php if ($this->countModules('footer-above-4')) : ?>
							<div id="footer-above-4" class="<?php echo $footerAboveClass ?>">
								<jdoc:include type="modules" name="footer-above-4" style="jexhtml" module-class="gutter" />
							</div><!-- end footer-above-4 -->
						<?php endif; ?>					
						<?php if ($this->countModules('footer-above-5')) : ?>
							<div id="footer-above-5" class="<?php echo $footerAboveClass ?>">
								<jdoc:include type="modules" name="footer-above-5" style="jexhtml" module-class="gutter" />
							</div><!-- end footer-above-5 -->
						<?php endif; ?>						
						<?php if ($this->countModules('footer-above-6')) : ?>
							<div id="footer-above-6" class="<?php echo $footerAboveClass ?>">
								<jdoc:include type="modules" name="footer-above-6" style="jexhtml" module-class="gutter" />
							</div><!-- end footer-above-6 -->
						<?php endif; ?>											
					</div><!-- end footer-above -->
				<?php endif; ?>

			</div><!-- end content-container -->
		</div><!-- end body-container -->
	</div><!-- end footer-push -->
    
	<div id="footer" class="clear clearfix">
		<div class="gutter clearfix">
			
			<a id="to-page-top" href="<?php $url->setFragment('page-top'); echo $url->toString();?>" class="to-additional">Back to Top</a>
			
			<?php if ($this->countModules('syndicate')) : ?>			
			<div id="syndicate">
				<jdoc:include type="modules" name="syndicate" />
			</div>
			<?php endif; ?>

			<?php if ($this->countModules('footer')) : ?>
				<jdoc:include type="modules" name="footer" style="jexhtml" />
			<?php endif; ?>

		</div><!--end gutter -->
	</div><!-- end footer -->

	<?php if ($this->countModules('debug')) : ?>
		<jdoc:include type="modules" name="debug" style="raw" />
	<?php endif; ?>	  
	
	<?php if ($this->countModules('analytics')) : ?>
		<jdoc:include type="modules" name="analytics" />
	<?php endif; ?>
	
	</body>
</html>
<?php }?>
