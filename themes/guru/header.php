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
            <div id="wrap-bg">
                <div id="header">
                    <div id="top-menu-container">
                        <nav id="site-navigation" class="navigation main-navigation" role="navigation">
            	            <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => false ) ); ?>
                        </nav>
                    </div>
                    <div id="slide-course">
                        <div class="logo-block">
                            <p class="logo"><b>THINK</b>MOBILES</p>
                        </div>
                        <div class="slide">
                            <h3>It School</h3>
                            <div class="block-btn">
                                <div class="slide-btn">
                                    <span class="first">1&#47;6</span>
                                     <a class="next" href="#"></a>
                                     <a class="prev" href="#"></a>
                                </div>
                            </div>
                            <div class="registration-block">
                                <a class="btn-registration" href="#">Registration</a>
                            </div>
                        </div>
                    </div>
                </div>