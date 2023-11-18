<script>
	var base_url = '<? echo base_url() ?>';
</script>


<nav class="navbar navbar-expand-lg bg-body-tertiary">

	<div class="container-fluid">
		<a class="navbar-brand" href="#">
			<?php echo $basic_var['company_name'] ?>
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<?
			$level_bef = 1;
			$entry = 3;
			$time = 0;

			$mem = '';

			echo '<ul  class="mb-2 navbar-nav me-auto mb-lg-0">';
			foreach ($basic_var['recordset_menu'] as $item) {
				$idMenu = $item->id_menu;
				$counter = array_reduce($basic_var['recordset_menu'], function ($carry, $item) use ($idMenu) {
					$carry += str_starts_with($item->id_menu, $idMenu) && strlen($idMenu) + 3 === strlen($item->id_menu) && 1 || 0;
					return $carry;
				}, 0);
				$classli = 'nav-item ';
				$classa = " href=\"" . base_url() . $item->link . "\" class='nav-link'";
				$arrow = '';
				if ($counter > 0) {
					$classli .= ' dropdown';
					$classa = 'class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"';
				}

				$level = strlen($item->id_menu) / $entry;

				if ($time == 1) {
					if ($level_bef == $level && $time == 1) {
						echo '</li>';
					}
					if ($level_bef < $level && $time == 1) {

						echo  '<ul class="dropdown-menu">';
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



				echo "<li class=\"$classli\">";
				echo "<a   $classa  >" . $item->description .  "</a>";

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
				<li class="nav-item"><a class="nav-link" href="<?php echo base_url() . 'log_out' ?>">Log out</a></li>
			</ul>
		</div>
	</div>

</nav>