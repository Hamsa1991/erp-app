<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo html_escape(isset($title) ? $title.' — ERP' : 'ERP'); ?></title>
	<link rel="stylesheet" href="<?php echo base_url('assets/css/app.css'); ?>">
	<?php if (isset($layout) && $layout === 'dashboard'): ?>
	<link rel="stylesheet" href="<?php echo base_url('assets/css/dashboard.css'); ?>">
	<?php endif; ?>
</head>
<body>
	<header class="site-header">
		<div class="container header-inner">
			<a class="brand" href="<?php echo site_url('products'); ?>">ERP System</a>
			<nav class="main-nav">
				<a href="<?php echo site_url('products'); ?>">Products</a>
				<a href="<?php echo site_url('warehouses'); ?>">Warehouses</a>
				<a href="<?php echo site_url('bills'); ?>">Bills</a>
				<?php if ($current_user && in_array('manage_products', $current_user->permissions, TRUE)): ?>
				<a href="<?php echo site_url('dashboard/products'); ?>">Dashboard</a>
				<?php endif; ?>
			</nav>
			<div class="user-menu">
				<?php if ($current_user): ?>
				<span><?php echo html_escape($current_user->first_name.' '.$current_user->last_name); ?></span>
				<a class="btn btn-sm btn-outline" href="<?php echo site_url('logout'); ?>">Logout</a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main class="container page-content">
		<?php echo $content; ?>
	</main>

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script>
		window.APP = {
			baseUrl: '<?php echo base_url(); ?>',
			siteUrl: '<?php echo site_url(); ?>'
		};
	</script>
	<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
	<?php if ( ! empty($page_script)): ?>
	<script src="<?php echo base_url('assets/js/'.$page_script); ?>"></script>
	<?php endif; ?>
</body>
</html>
