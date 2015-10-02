<html>
    <head>
        <title>Guru ThinkMobiles</title>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php wp_head(); ?>
    </head>
    <body>
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
                    <div id="slide-course">
                        <div class="logo-block">
                            <p class="logo"><b>ThinkMobiles</p>
                        </div>
                        <div class="slide">
                            <h1>It School</h1>
                            <div class="block-btn">
                                <div class="slide-btn">
                                    <span class="number">1&#47;7</span>
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