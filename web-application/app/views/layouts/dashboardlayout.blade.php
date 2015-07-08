<!doctype html>
<html>
    <head>
        <meta charset="utf-8">



        <title>Smart Bus</title>
        <!-- css section start -->

        {{ HTML::style('assets/css/style.css') }}
        {{ HTML::style('assets/css/jquery.jscrollpane.css') }}
        {{ HTML::style('assets/css/jquery.datetimepicker.css') }}
        {{ HTML::style('assets/css/jquery.dataTables.css') }}


        <!-- js section start -->

        {{ HTML::script('assets/js/jquery-1.10.2.min.js') }}
        {{ HTML::script('assets/js/nav_script.js') }}
        {{ HTML::script('assets/js/jquery.datetimepicker.js') }}
        {{ HTML::script('assets/js/jquery.dataTables.js') }}
        {{ HTML::script('assets/js/jquery-ui.js') }}
        {{ HTML::style('assets/css/jquery-ui.css') }}
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
                    <div class="main-navigation">
                        <div class="scrroll-navigation navigation-scroll" style="width:200px;">
                            <ul class="navigation menu-main">
                                <li><a href="{{ URL::to('home'); }}" title="Dashboard"><span class="icon dashboard-icon"></span>Dashboard</a></li>
                                <?php
                                if (Auth::user()->usertype == 1 || Auth::user()->usertype == 2) {
                                    ?>
                                    <li class="Mastervisible"><a href="#" title="Schools"><span class="icon class-icon"></span>Master</a>
                                        <ul>
                                            <?php if (Auth::user()->usertype != 2) { ?>
                                                <li><a href="{{ URL::to('general'); }}">Add School</a></li>
                                            <?php } ?>
                                            <li><a href="{{ URL::to('class'); }}">Grade</a> </li>
                                            <!--<li><a href="{{ URL::to('batch'); }}">Batch</a> </li>-->
                                            <li><a href="{{ URL::to('gradesection'); }}">Grade Section</a> </li>
                                            <li><a href="{{ URL::to('languagesection'); }}">Language</a> </li>

                                    </li>
                                </ul>
                                </li>
                            <?php } if (Auth::user()->usertype == 1 || Auth::user()->usertype == 2) {
                                ?>
                                <li  class="Studentvisible"><a href="#" title="Schools"><span class="icon class-icon"></span>Student</a>
                                    <ul>
                                        <li><a href="{{ URL::to('studentadmission'); }}">Student Admission</a></li>    
                                        <li><a href="{{ URL::to('studentlist'); }}">Student List</a></li>  		
                                    </ul>
                                </li>
                                <?php
                            }
                            if (Auth::user()->usertype != 2) {
                                ?>
                                <li  class="Transportvisible"><a href="#" title="Schools"><span class="icon bus-icon"></span>Transport</a>
                                    <ul>
                                        <li><a href="{{ URL::to('vehicle'); }}">Add Vehicle</a></li>
                                        <li><a href="{{ URL::to('route'); }}">Add Routes</a></li>
                                        <li><a href="{{ URL::to('destination'); }}">Add Destination</a></li>
                                        <li><a href="{{ URL::to('driver'); }}">Add Driver</a></li>
                                        <li><a href="{{ URL::to('timing'); }}">Add Timing</a></li>
                                        <li><a href="{{ URL::to('allocation'); }}">Add Allocation</a>
                                        </li>
                                    </ul>
                                </li>
<?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    <header class="header" id="header"> 
                        <!-- header container start -->
                        <div class="align-center">
                            <div class="col-left">
                                <div class="logo-section"> <a href="#">
<?php
if (Auth::user()->usertype == 2) {
    $schoolid = Auth::user()->schoolid;
    $schoollogo = GeneralSettingModel::where('id', $schoolid)->get()->toArray();
    if (!empty($schoollogo)) {
        ?>
                                                {{ HTML::image('assets/uploads/uploadschoollogo/'.$schoollogo[0]['UploadLogo'], 'Smart Bus') }}
                                                <?php
                                            } else {
                                                ?>
                                                {{ HTML::image('assets/images/logo.png', 'Smart Bus') }}
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            {{ HTML::image('assets/images/logo.png', 'Smart Bus') }}
                                        <?php } ?>
                                    </a></div>
                            </div>
                            <div class="col-right">
                                <div class="logged-user-detail">
                                    <div class="logged-user-panel">
                                        <p class="logged-user"><span class="icon icon-proifle"></span>{{ Auth::user()->UserName; }}</p>
                                        <span class="log-tog-btn"></span> </div>
                                    <div class="user-tog-box">
                                        <div class="profile-section">
                                            <div class="profile-icon">{{ HTML::image('assets/images/profile-inner.png', 'Profile picture') }}</div>
                                            <p class="pro-name">{{ Auth::user()->FirstName.' '.Auth::user()->LastName; }}</p>
                                        </div>
                                        <div class="logged-lister">
                                            <ul>
                                                <li><a href="{{ URL::to('profile'); }}"><span class="icon pro-icon"></span>Profile</a></li>
<?php
if (Auth::user()->usertype == 2) {
    $schoolid = Auth::user()->schoolid;
    ?>
                                                    <li><a href="<?php echo url(); ?>/schooledit/<?php echo $schoolid; ?>"><span class="icon pro-icon"></span>School Information</a></li>
                                                <?php } ?>
                                                <li><a href="{{ URL::to('changepassword'); }}"><span class="icon pass-icon"></span>Change password</a></li>
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
                            <div id="dialog-confirm"></div>
                            <script>
                                function fnOpenNormalDialog() {
                                    var url = $(this).attr("id");
                                    $("#dialog-confirm").html("Delete records?");
                                    var buttonsConfig = [
                                        {
                                            text: "Ok",
                                            "class": "ok",
                                            click: function () {
                                                $(this).dialog('close');
                                                window.location.href = url;
                                            }
                                        },
                                        {
                                            text: "Cancel",
                                            "class": "cancel",
                                            click: function () {
                                                $(this).dialog('close');
                                            }
                                        }
                                    ];
                                    // Define the Dialog and its properties.
                                    $("#dialog-confirm").dialog({
                                        resizable: false,
                                        modal: true,
                                        title: "MTI(Malden Taxi & Malden Trans Inc)",
                                        height: 250,
                                        width: 400,
                                        buttons: buttonsConfig,
                                    });
                                }

                                $('.btnOpenDialog').click(fnOpenNormalDialog);

                            </script>
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