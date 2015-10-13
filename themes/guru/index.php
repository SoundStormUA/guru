<?php get_header(); ?>
    <div id="main">
	<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-N76PJF"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-N76PJF');</script>
		<!-- End Google Tag Manager -->
        <div id="content">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<p><?php the_content(__('(more...)')); ?></p>
				<?php endwhile; else: ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>
		</div>
    </div>
    <div id="delimiter"></div>
<?php get_footer(); ?>