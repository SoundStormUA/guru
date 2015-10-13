<?php include  (get_stylesheet_directory() . '/templates/header404.php');?>
    <div id="main">
        <div id="content">
			<p class="notFound">404</p>
			<p class="notFoundText">Ooops,page not found</p>
			<button class="notFoundButton"><a href="<?php echo get_home_url();?>">Home</a></button>
		</div>
    </div>
    <div id="delimiter"></div>
<?php get_footer(); ?>
