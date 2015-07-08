<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Smart Bus</title>
        <!-- css section start -->
        {{ HTML::style('assets/css/style.css') }}
        {{ HTML::style('assets/css/jquery.jscrollpane.css') }}
        {{ HTML::style('assets/css/jquery.datetimepicker.css') }}

        <!-- js section start -->

        {{ HTML::script('assets/js/jquery-1.10.2.min.js') }}
        {{ HTML::script('assets/js/nav_script.js') }}
        {{ HTML::script('assets/js/jquery.datetimepicker.js') }}
    </head>
    <body class="nav-fixed-close">
        <div class="main-total-container">
            <div class="main-content-container"> <!-- main content container start -->
                <div class="left_column sidebar_left col-left">
                    <div class="nav_locker"> <!--- nav locker section start -->
                        <div class="onOff_tog">
                            <div class="nav-toggler"> </div>
                            <span class="navEx-on"> <span class="lock-nav"> Lock </span> <span class="gird_nav"> Grid </span> </span> </div>
                    </div>
                    <!--- nav locker section end -->
                </div>
                <div class="right_column">
                    <header class="header" id="header"> 
                        <!-- header container start -->
                        <div class="align-center">
                            <div class="col-left">
                                <div class="logo-section"> <a href="#">{{ HTML::image('assets/images/logo.png', 'Smart Bus') }}</a> </div>
                            </div>
                            <div class="col-right">
                                <div class="logged-user-detail">
                                    <div class="logged-user-panel">
                                        <p class="logged-user"><span class="icon icon-proifle"></span>John Cena</p>
                                        <span class="log-tog-btn"></span> </div>
                                    <div class="user-tog-box">
                                        <div class="profile-section">
                                            <div class="profile-icon">{{ HTML::image('assets/images/profile-inner.png', 'Profile picture') }}</div>
                                            <p class="pro-name">John Cena</p>
                                        </div>
                                        <div class="logged-lister">
                                            <ul>
                                                <li><a href="#"><span class="icon pro-icon"></span>Profile</a></li>
                                                <li><a href="#"><span class="icon pass-icon"></span>Change password</a></li>
                                                <li><a href="{{ URL::to('logout'); }}"><span class="icon logout-icon"></span>Logout</a>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="scroll-wrapper">
                        @yield('body')
                        <div class="site-footer">
                            <p>© Malden Taxi & Malden Trans Inc. Powered by: <a href="http://www.bizarresoftware.in/" target="_new">BIZARRE Software Solutions Pvt Ltd</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ HTML::script('assets/js/jquery.jscrollpane.min.js') }}
        {{ HTML::script('assets/js/custom_script.js') }}
    </body>
</html>