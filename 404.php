<?php if (!defined('ABSPATH')) {
    exit;
}  ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>
<body class="antialiased">
	<div class="md:flex min-h-screen">
		<div class="w-full md:w-1/2 flex items-center justify-center">
			<div class="max-w-sm m-8">
				<div class="text-5xl md:text-15xl text-ink border-primary border-b">404</div>
				<div class="w-16 h-1 bg-gold my-3 md:my-6"></div>
				<p class="text-ink text-2xl md:text-3xl font-light mb-8"><?php _e( 'Sorry, the page you are looking for could not be found.', 'renaissance' ); ?></p>
				<a href="<?php echo get_bloginfo( 'url' ); ?>" class="btn-primary">
					<?php _e( 'Go Home', 'renaissance' ); ?>
				</a>
			</div>
		</div>
	</div>
</body>
</html>
