					<ul class="nav-bar">
      				<li><input type="search"></li>
						<?php foreach($filterable AS $filter): ?>
						<li class="has-flyout"><a href=""><?php echo ucfirst($filter) ?></a>
							<div class="flyout">
	
								<ul>
									<li><a href="">This is a link in a UL.</a></li>
									<li><a href="">This is a link in a UL.</a></li>
									<li><a href="">This is a link in a UL.</a></li>
								</ul>
							</div>
						</li>
						<?php endforeach ?>
      				</ul>
