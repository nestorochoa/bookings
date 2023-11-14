<script>
	var base_url = '<? echo base_url() ?>';
</script>


<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-responsive-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="#">
				<?php echo $basic_var['company_name'] ?>
			</a>
			<div class="nav-collapse collapse navbar-responsive-collapse">
				<?
				$level_bef = 1;
				$entry = 3;
				$time = 0;

				$mem = '';

				echo '<ul  class="nav">';
				foreach ($basic_var['recordset_menu'] as $item) {
					$idMenu = $item->id_menu;
					$counter = array_reduce($basic_var['recordset_menu'], function ($carry, $item) use ($idMenu) {
						$carry += str_starts_with($item->id_menu, $idMenu) && strlen($idMenu) + 3 === strlen($item->id_menu) && 1 || 0;
						return $carry;
					}, 0);
					$classli = '';
					$classa = '';
					$arrow = '';
					if ($counter > 0) {
						$classli = 'class="dropdown"';
						$classa = 'class="dropdown-toggle" data-toggle="dropdown"';
						$arrow = '<b class="caret"></b>';
					}

					$level = strlen($item->id_menu) / $entry;

					if ($time == 1) {
						if ($level_bef == $level && $time == 1) {
							echo '</li>';
						}
						if ($level_bef < $level && $time == 1) {

							echo  '<ul  class="dropdown-menu">';
						}
						if ($level_bef > $level) {
							for ($i = 1; $i <= $level; $i++) {
								echo '</li></ul>';
							}
							echo  '</li>';
						}
					} else {
						$time = 1;
					}



					echo '<li ' . $classli . '><a href="' . base_url() . $item->link . '" ' . $classa . '>' . $item->description .  $arrow . '</a>';
					$level_bef = strlen($item->id_menu) / $entry;
				}
				if ($time == 1) {
					if ($level == 1) {
						echo '</li>';
					} else {
						for ($i = 1; $i <= $level; $i++) {
							echo '</li></ul>';
						}
						echo  '</li>';
					}
				}
				echo '</ul>';



				?>
				<ul class="nav pull-right">
					<li><a href="<?php echo base_url() . 'log_out' ?>">Log out</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>