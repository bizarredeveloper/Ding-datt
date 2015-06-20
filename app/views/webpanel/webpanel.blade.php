@extends('header.header')
<?php
$assets_path = "assets/inner/";
?>
@section('includes')

<script>
    function loadmore(tab)
    {
        $("#" + tab + " .crsl-item").show();
        $("#" + tab + " #navbtns").html("");
    }
</script>
@stop
@section('body')
{{ Form::hidden('pagename','webpanel', array('id'=> 'pagename')) }}
<script>
    $(document).ready(function () {
        loadtap_content();
    });
    function loadtap_content()
    {
        var main_tab = $("input:radio[name=tab]:checked").attr('id');
        var sub_tab = $("input:radio[name=subtab]:checked").attr('id');  //alert(sub_tab);
        if (main_tab == "tab1")
        {
            var tsearch = $("#tab-body-1 #tsearch").val();
            var interest = $('#tab-body-1 #interestid_1').val();
        }
        else if (main_tab == "tab2")
        {
            var tsearch = $("#tab-body-2 #tsearch").val();
            var interest = $('#tab-body-2 #interestid_2').val();
        }
        else if (main_tab == "tab3")
        {
            var tsearch = $("#tab-body-3 #tsearch").val();
            var interest = $('#tab-body-3 #interestid_3').val();
        }

        var dataString = 'main_tab=' + main_tab + '&sub_tab=' + sub_tab + "&tsearch=" + tsearch + "&interest=" + interest; //alert(dataString);
        $.ajax({
            type: "POST",
            url: "loadcontest_list",
            data: dataString,
            success: function (data) {  //alert(data);  alert(sub_tab);
                var content = data.split("||");
                if (main_tab == "tab1")
                {
                    if (content[1] == 0) {

                        $("#tab-body-1 .crsl-wrap").html('<div class="centertext">No contest available</div>');
                    } else {


                        $("#tab-body-1 .crsl-wrap").html(content[0]);
                        if (content[1] > 14)
                            $("#tab-body-1 #navbtns").html("<a href=\"#\" onclick=\"loadmore('tab-body-1')\">Load More...</a>");
                        else
                            $("#tab-body-1 #navbtns").html("");
                    }

                    if (sub_tab == "tab7")
                        $("#tab-body-1 #subtab").val("private");
                    else if (sub_tab == "tab4")
                        $("#tab-body-1 #subtab").val("current");
                    else if (sub_tab == "tab5")
                        $("#tab-body-1 #subtab").val("upcoming");
                    else if (sub_tab == "tab6")
                        $("#tab-body-1 #subtab").val("archive");
                }
                else if (main_tab == "tab2")
                {
                    if (content[1] == 0) {

                        $("#tab-body-2 .crsl-wrap").html('<div class="centertext">No contest available</div>');
                    } else {

                        $("#tab-body-2 .crsl-wrap").html(content[0]);
                        if (content[1] > 14)
                            $("#tab-body-2 #navbtns").html("<a href=\"#\" onclick=\"loadmore('tab-body-2')\">Load More...</a>");
                        else
                            $("#tab-body-2 #navbtns").html("");

                    }
                    if (sub_tab == "tab7")
                        $("#tab-body-2 #subtab").val("private");
                    else if (sub_tab == "tab4")
                        $("#tab-body-2 #subtab").val("current");
                    else if (sub_tab == "tab5")
                        $("#tab-body-2 #subtab").val("upcoming");
                    else if (sub_tab == "tab6")
                        $("#tab-body-2 #subtab").val("archive");
                }
                else if (main_tab == "tab3")
                {
                    if (content[1] == 0) {

                        $("#tab-body-3 .crsl-wrap").html('<div class="centertext">No contest available</div>');
                    } else {

                        $("#tab-body-3 .crsl-wrap").html(content[0]);
                        if (content[1] > 14)
                            $("#tab-body-3 #navbtns").html("<a href=\"#\" onclick=\"loadmore('tab-body-3')\">Load More...</a>");
                        else
                            $("#tab-body-3 #navbtns").html("");

                    }

                    if (sub_tab == "tab7")
                        $("#tab-body-3 #subtab").val("private");
                    else if (sub_tab == "tab4")
                        $("#tab-body-3 #subtab").val("current");
                    else if (sub_tab == "tab5")
                        $("#tab-body-3 #subtab").val("upcoming");
                    else if (sub_tab == "tab6")
                        $("#tab-body-3 #subtab").val("archive");
                }
            }
        });
    }
    function loadtap_content_mobile()
    {
        var main_tab = $('#mobileselected').val();
        var sub_tab = $('#mobilesubtabselected').val();

        if (main_tab == 'photo') {
            main_tab = 'tab1';
            var tsearch = $("#tab-body-1 #tsearch").val();
        }
        else if (main_tab == 'video') {
            main_tab = 'tab2';
            var tsearch = $("#tab-body-2 #tsearch").val();
        }
        else if (main_tab == 'topic') {
            main_tab = 'tab3';
            var tsearch = $("#tab-body-3 #tsearch").val();
        }

        if (sub_tab == 'private')
            sub_tab = 'tab7';
        else if (sub_tab == 'current')
            sub_tab = 'tab4';
        else if (sub_tab == 'upcoming')
            sub_tab = 'tab5';
        else if (sub_tab == 'archive')
            sub_tab = 'tab6';

        var dataString = 'main_tab=' + main_tab + '&sub_tab=' + sub_tab + "&tsearch=" + tsearch;  //alert(dataString);
        $.ajax({
            type: "POST",
            url: "loadcontest_list",
            data: dataString,
            success: function (data) {   //alert(data);
                var content = data.split("||");  //alert(content[0]);
                if (main_tab == "tab1")
                {
                    //alert(content[0]);
                    $(".crsl-wrap").html("");
                    $(".crsl-wrap").html(content[0]);
                    if (content[1] > 10)
                        $("#tab-body-1 #navbtns").html("<a href=\"#\" onclick=\"loadmore('tab-body-1')\">Load More...</a>");
                    else
                        $("#tab-body-1 #navbtns").html("");
                }
                else if (main_tab == "tab2")
                {
                    $(".crsl-wrap").html("");
                    $(".crsl-wrap").html(content[0]);
                    if (content[1] > 10)
                        $("#tab-body-2 #navbtns").html("<a href=\"#\" onclick=\"loadmore('tab-body-2')\">Load More...</a>");
                    else
                        $("#tab-body-2 #navbtns").html("");

                }
                else if (main_tab == "tab3")
                {
                    console.log(content[0]);
                    $(".crsl-wrap").html("");
                    $(".crsl-wrap").html(content[0]);
                    if (content[1] > 10)
                        $("#tab-body-3 #navbtns").html("<a href=\"#\" onclick=\"loadmore('tab-body-3')\">Load More...</a>");
                    else
                        $("#tab-body-3 #navbtns").html("");

                }
            }
        });
    }
</script>
<?php
if (isset($inputs['tab'])) {
    $tab = $inputs['tab'];
} else {
    $tab = "photo";
}
if (isset($inputs['subtab'])) {
    $subtab = $inputs['subtab'];
} else {
    $subtab = "private";
}
?>
<!--img src="assets/images/bell.png" class="bell" width="80px" height="80px" style="float:right; margin-right:15px;"/-->
<div class="tabs-wrapper">
    <input type="radio" name="tab" id="tab1" class="tab-head" onclick="loadtap_content();" <?php if ($tab == "photo") {
    echo "checked";
} ?>/>
    <label for="tab1" id="txt_photocontest">Photo Contest</label>
    <input type="radio" name="tab" id="tab2" class="tab-head" onclick="loadtap_content();" <?php if ($tab == "video") {
    echo "checked";
} ?> />
    <label for="tab2" id="txt_videocontest">Video Contest</label>
    <input type="radio" name="tab" id="tab3" class="tab-head" onclick="loadtap_content();" <?php if ($tab == "topic") {
    echo "checked";
} ?>/>
    <label for="tab3" id="txt_topicocontest">Topic Contest</label>

    <div class="mbblk">
        <select class="radius sel_lang" id="mobileselected" onchange="loadtap_content_mobile();">
            <option value="photo"  <?php if ($tab == "photo") {
    echo "selected";
} ?> class="txt_photocontest">Photo Contest</option>     
            <option value="video" <?php if ($tab == "video") {
    echo "selected";
} ?> class="txt_videocontest">Video Contest</option>
            <option value="topic" <?php if ($tab == "topic") {
    echo "selected";
} ?> class="txt_topicocontest" >Topic Contest</option>
        </select>

        <select class="radius sel_lang" id="mobilesubtabselected" onchange="loadtap_content_mobile();">
            <option value="private"  <?php if ($tab == "private") {
    echo "selected";
} ?> class="txt_privatecontest">Private</option>     
            <option value="current" <?php if ($tab == "current") {
    echo "selected";
} ?> class="txt_currentcontest">Current</option>
            <option value="upcoming" <?php if ($tab == "upcoming") {
    echo "selected";
} ?> class="txt_upcommingcontest">Upcoming</option>
            <option value="archive" <?php if ($tab == "archive") {
    echo "selected";
} ?> class="txt_archivecontest">Archive</option>
        </select>
    </div>


    <div class="con_cat_right">
        <input type="radio" name="subtab" id="tab7" class="tab-head1" <?php if ($subtab == "private") {
    echo "checked";
} ?> onclick="loadtap_content();"/>
        <label for="tab7" id="txt_privatecontest">Private</label>
        <input type="radio" name="subtab" id="tab4" class="tab-head1" <?php if ($subtab == "current") {
    echo "checked";
} ?> onclick="loadtap_content();"/>
        <label for="tab4" id="txt_currentcontest">Current</label>
        <input type="radio" name="subtab" id="tab5" class="tab-head1" <?php if ($subtab == "upcoming") {
    echo "checked";
} ?> onclick="loadtap_content();"/>
        <label for="tab5" id="txt_upcommingcontest">Upcoming</label>
        <input type="radio" name="subtab" id="tab6" class="tab-head1" <?php if ($subtab == "archive") {
    echo "checked";
} ?> onclick="loadtap_content();"/>
        <label for="tab6" id="txt_archivecontest">Archive</label>
    </div>

    <div class="tab-body-wrapper">
        <div id="tab-body-1" class="tab-body">
            <div id="p">
                <div class="con_hed_blk">
                    <div class="con_head">
                        <h1><span class="txt_contestlist">Contest List</span></h1>
                    </div>
                    <div class="con_search">
                        <form name="tab1-search"  action="{{ URL::to('webpanel') }}" method="post" >
                            <div class="mb_con_search" style="vertical-align:top;margin:0; padding:0;">
                                <input type="hidden" name="tab" value="photo">
                                <input type="hidden" id="subtab" name="subtab" value="private">
                <?php $interestDetails = InterestCategoryModel::where('status', 1)->lists('Interest_name', 'Interest_id');
                ?>									
                                {{ Form::select('interest', array('Select category')+$interestDetails,isset($inputs['interest'])?$inputs['interest']:'',array('id'=> 'interestid_1','class'=>'radius sel_lang'))}} 

                                <input type="text" name="tsearch1" id="tsearch" value="{{ isset($inputs['tsearch1'])?$inputs['tsearch1']:'' }}" class="pch_searchcontest" placeholder="Search Contest" />
                                <input class="search_btn" type="submit" value="" />
                            </div>
                        </form>
                    </div>
                </div>
                        <?php
                        $currentdate = date('Y-m-d H:i:s');
                        $photocontest = contestModel::where(function($query) {
                                            $query->where(function($query) {
                                                $currentdate = date('Y-m-d H:i:s');
                                                $query->where('conteststartdate', '<=', $currentdate);
                                                $query->where('contestenddate', '>=', $currentdate);
                                            });
                                            $query->orWhere(function($query) {
                                                $currentdate = date('Y-m-d H:i:s');
                                                $query->where('votingstartdate', '<=', $currentdate);
                                                $query->where('votingenddate', '>=', $currentdate);
                                            });
                                        })
                                        ->where('contesttype', 'p')->where('status', '1')->where('visibility', 'u')->get();
                        $contestcount = count($photocontest);
                        ?>
                <div class="clrscr"></div>
                <div class="crsl-items_p" data-navigation="navbtns">
                    <div class="crsl-wrap">
                        <?php
                        for ($i = 0; $i < $contestcount; $i++) {   }
                        ?>
                    </div><!-- @end .crsl-wrap -->
                </div><!-- @end .crsl-items --> 
                <nav class="slidernav">
                    <div id="navbtns" class="clearfix">
<?php
if ($contestcount > 10) {
    ?>
                            <a href="#" onclick="loadmore('tab-body-1')">Load More...</a>
                                    <?php
                                }
                                ?>
                    </div>
                </nav> 

            </div><!-- @end #w -->
        </div>
        <div id="tab-body-2" class="tab-body">
            <div id="v">
                <div class="con_hed_blk">
                    <div class="con_head">
                        <h1><span class="txt_contestlist">Contest List</span></h1>
                    </div>
                    <div class="con_search">
                        <form name="tab2-search"  action="{{ URL::to('webpanel') }}" method="post" >
                            <div class="mb_con_search" style="vertical-align:top;margin:0; padding:0;">
                                <input type="hidden" name="tab" value="video">
                                <input type="hidden"id="subtab" name="subtab" value="private">

                <?php $interestDetails = InterestCategoryModel::where('status', 1)->lists('Interest_name', 'Interest_id');
                ?>									
                                {{ Form::select('interest', array('Select category')+$interestDetails,isset($inputs['interest'])?$inputs['interest']:'',array('id'=> 'interestid_2','class'=>'radius sel_lang'))}} 

                                <input type="text" name="tsearch2" id="tsearch" value="{{ isset($inputs['tsearch2'])?$inputs['tsearch2']:'' }}" class="pch_searchcontest" placeholder="Search Contest" />
                                <input class="search_btn" type="submit" value="" />
                            </div>
                        </form>
                    </div>
                </div>
<?php
$videocontest = contestModel::where(function($query) {
                    $query->where(function($query) {
                        $currentdate = date('Y-m-d H:i:s');
                        $query->where('conteststartdate', '<=', $currentdate);
                        $query->where('contestenddate', '>=', $currentdate);
                    });
                    $query->orWhere(function($query) {
                        $currentdate = date('Y-m-d H:i:s');
                        $query->where('votingstartdate', '<=', $currentdate);
                        $query->where('votingenddate', '>=', $currentdate);
                    });
                })
                ->where('contesttype', 'v')->where('status', '1')->where('visibility', 'u')->get();

//$videocontest=contestModel::where('conteststartdate', '<=', $currentdate)->where('contestenddate', '>=', $currentdate)->where('contesttype','v')->where('status','1')->where('visibility','u')->orWhere('votingstartdate', '<=', $currentdate)->where('votingenddate', '>=', $currentdate)->get();	 
$videocontestcount = count($videocontest);
?>
                <div class="clrscr"></div>
                <div class="crsl-items_v" data-navigation="navbtns">
                    <div class="crsl-wrap">
<?php
for ($i = 0; $i < $videocontestcount; $i++) { 
}
if ($videocontestcount == 0) {
    ?>						
                            <div class="centertext">No contest available</div> 
<?php } ?>
                    </div><!-- post #5 -->
                </div><!-- @end .crsl-wrap -->
            </div><!-- @end .crsl-items -->
            <nav class="slidernav">
                <div id="navbtns" class="clearfix">
<?php
if ($videocontestcount > 10) {
    ?>
                        <a href="#">Load More...</a>
                                    <?php
                                }
                                ?>
                </div>
            </nav> 
        </div>
        <div id="tab-body-3" class="tab-body">
            <div id="t">
                <div class="con_hed_blk">
                    <div class="con_head">
                        <h1><span class="txt_contestlist">Contest List</span></h1>
                    </div>
                    <div class="con_search">
                        <form name="tab3-search" action="{{ URL::to('webpanel') }}" method="post" >
                            <div class="mb_con_search" style="vertical-align:top;margin:0; padding:0;">
                                <input type="hidden" name="tab" value="topic">
                                <input type="hidden" id="subtab" name="subtab" value="private">


                <?php $interestDetails = InterestCategoryModel::where('status', 1)->lists('Interest_name', 'Interest_id');
                ?>									
                                {{ Form::select('interest', array('Select category')+$interestDetails,isset($inputs['interest'])?$inputs['interest']:'',array('id'=> 'interestid_3','class'=>'radius sel_lang'))}} 

                                <input type="text" name="tsearch3" id="tsearch" value="{{ isset($inputs['tsearch3'])?$inputs['tsearch3']:'' }}" class="pch_searchcontest" placeholder="Search Contest" />
                                <input class="search_btn" type="submit" value="" />
                            </div>
                        </form>
                    </div>
                </div>
<?php
$topiccontest = contestModel::where(function($query) {
                    $query->where(function($query) {
                        $currentdate = date('Y-m-d H:i:s');
                        $query->where('conteststartdate', '<=', $currentdate);
                        $query->where('contestenddate', '>=', $currentdate);
                    });
                    $query->orWhere(function($query) {
                        $currentdate = date('Y-m-d H:i:s');
                        $query->where('votingstartdate', '<=', $currentdate);
                        $query->where('votingenddate', '>=', $currentdate);
                    });
                })
                ->where('contesttype', 't')->where('status', '1')->where('visibility', 'u')->get();
// $topiccontest=contestModel::where('conteststartdate', '<=', $currentdate)->where('contestenddate', '>=', $currentdate)->where('contesttype','t')->where('status','1')->where('visibility','u')->orWhere('votingstartdate', '<=', $currentdate)->where('votingenddate', '>=', $currentdate)->get();	 
$topiccontestcount = count($topiccontest);
?>
                <div class="clrscr"></div>
                <div class="crsl-items_t" data-navigation="navbtns">
                    <div class="crsl-wrap">
<?php
for ($i = 0; $i < $topiccontestcount; $i++) { 
}
if ($videocontestcount == 0) {
    ?>						
                            <div class="centertext">No contest available</div> 
<?php } ?>

                    </div><!-- @end .crsl-wrap -->
                </div><!-- @end .crsl-items -->
                <nav class="slidernav">
                    <div id="navbtns" class="clearfix">
<?php
if ($topiccontestcount > 10) {
    ?>
                            <a href="#">Load More...</a>
    <?php
}
?>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="clrscr"></div>

@stop