<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo html_escape(isset($title) ? $title.' — ERP Dashboard' : 'ERP Dashboard'); ?></title>
	<link rel="stylesheet" href="<?php echo base_url('assets/css/app.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/css/dashboard.css'); ?>">
</head>
<body class="dashboard-body">
	<div class="dashboard-layout">
		<aside class="dashboard-sidebar">
			<a class="brand" href="<?php echo site_url('products'); ?>">ERP Dashboard</a>
			<nav>
				<a href="<?php echo site_url('dashboard/products'); ?>" class="<?php echo (isset($title) && $title === 'Manage Products') ? 'active' : ''; ?>">Products</a>
				<?php if ($current_user && in_array('manage_bills', $current_user->permissions, TRUE)): ?>
				<a href="<?php echo site_url('dashboard/bills/create'); ?>" class="<?php echo (isset($title) && $title === 'Create Bill') ? 'active' : ''; ?>">Create Bill</a>
				<?php endif; ?>
				<a href="<?php echo site_url('products'); ?>">View Catalog</a>
				<a href="<?php echo site_url('bills'); ?>">View Bills</a>
			</nav>
			<div class="sidebar-footer">
				<span><?php echo html_escape($current_user->first_name); ?></span>
				<a href="<?php echo site_url('logout'); ?>">Logout</a>
			</div>
		</aside>
		<div class="dashboard-main">
			<header class="dashboard-header">
				<h1><?php echo html_escape(isset($title) ? $title : 'Dashboard'); ?></h1>
			</header>
			<div class="dashboard-content">
				<?php echo $content; ?>
			</div>
		</div>
	</div>

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
