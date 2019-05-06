<?php
if(!$meta){
	$meta = array(
		'meta_title' => 'Лучшие интернет магазины со скидками, подарками и бесплатной доставкой',
		'title' => 'Интернет магазины со скидками',
		'meta_description' => 'Покупайте с промокодами и получайте скидки в лучших интернет магазинах'
	);
}

require_once $_SERVER['DOCUMENT_ROOT'] .'/functions/n2w.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/templates/inc/header.php';
?>

<!--MAIN/-->
<div class="main">
	<div class="row row_wrp">
		<div class="col-3">
			<?php include_once $_SERVER['DOCUMENT_ROOT'] .'/templates/inc/sidebar.php'; ?>
		</div>
		<div class="col-9 p-0">
			<div class="row">
				<div class="col-12">
					<h1 class="title"><?php echo $meta['title'];?></h1>
				</div>
			</div>

			<div class="row tile">
			<?php
			if (!empty($content['shops'])) {
				foreach ($content['shops'] as $item) {
					echo '<div class="col-3 md-col-4">';
					include $_SERVER['DOCUMENT_ROOT'] .'/templates/inc/shop-item.php';
					echo '</div>';
				}
			} else {
				echo '<div class="col-12">На данный момент, в этой категории нет магазинов.</div>';
			}
			?>
			</div>
			<div class="row">
				<div class="col-12 p-x-0">
					<ul class="paginate">
					<?php echo $lemon -> getPagenav(); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!--/MAIN-->

<?php require_once $_SERVER['DOCUMENT_ROOT'] .'/templates/inc/footer.php'; ?>