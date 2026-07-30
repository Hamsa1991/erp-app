<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login — ERP</title>
	<link rel="stylesheet" href="<?php echo base_url('assets/css/app.css'); ?>">
</head>
<body class="auth-page">
	<div class="auth-card">
		<h1>ERP Login</h1>
		<?php if ( ! empty($error)): ?>
		<div class="alert alert-error"><?php echo html_escape($error); ?></div>
		<?php endif; ?>
		<form method="post" action="<?php echo site_url('login/submit'); ?>">
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" id="email" class="form-control" name="email" required autofocus>
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" class="form-control" name="password" required>
			</div>
			<button type="submit" class="btn btn-primary btn-block">Sign In</button>
		</form>
		<p class="auth-hint">Default admin: admin@erp.local / admin123</p>
	</div>
</body>
</html>
