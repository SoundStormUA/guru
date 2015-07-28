<html>
    <head>
        <title>Guru ThinkMobiles theme</title>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php wp_head(); ?>
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <div id="top-menu-container">
                    <nav id="site-navigation" class="navigation main-navigation" role="navigation">
            	        <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => false ) ); ?>
                    </nav>
                </div>
            </div>