<html>
    <head>
		<link rel="icon" href="<?php echo get_stylesheet_directory_uri() . '/img/favicon.ico';?>" type="image/x-icon" />
		<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri() . '/img/favicon.ico';?>" type="image/x-icon" />
        <title>Guru ThinkMobiles</title>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php wp_head(); ?>
    </head>
    <body>
	<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-N76PJF"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-N76PJF');</script>
	<!-- End Google Tag Manager -->
        <div id="wrapper" class="wrap">
            <div id="wrap-bg" class="page">
                <a href="#0" class="cd-top"></a>
                <div id="header">
                    <div id="top-menu-container">
                        <nav id="site-navigation" class="navigation main-navigation" role="navigation">
                            <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => false ) ) ?>
                            <!--<select class="language" name="set-language"><option value="ua">UA</option><option value="en">EN</option><option value="ru">RU</option></select>-->
                        </nav>
                    </div>
                    <div id="slide-course" class="animated-header">
                        <div class="logo-block">
                            <p class="logo"><b>ThinkMobiles</p>
                        </div>
                        <div class="slide">
                            <h1>It School</h1>
                            <div class="block-btn">
                                <div class="slide-btn">
                                    <span class="number">1&#47;8</span>
                                    <a class="next" id="arrow-prev" href="#"></a>
                                    <a class="prev" id="arrow-next" href="#"></a>
                                </div>
                            </div>
                            <div class="registration-block">
                                <a class="btn-registration  register" href="#">Реєструйся</a>
                            </div>
                        </div>
                    </div>
                </div>