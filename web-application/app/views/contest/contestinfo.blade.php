@extends('header.header')
<!-- Contest gallery and voting, invite and joining the contest -->
<?php
$assets_path = "assets/inner/";
?>
@section('includes')
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "d4ab7898-2b6c-4cc4-a753-12192e1bb354", shorten:false, doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>

<script type="text/javascript">
	function show_publish()
	{
		document.getElementById("join_up").style.display = "none";
		document.getElementById("join_pub").style.display = "block";
	}

    function show_otherupload()
    {
		document.getElementById("join_up").style.display = "block";
		document.getElementById("join_pub").style.display = "none";
    }
    function showhide_subtab(contest_id)
    {
		var main_tab = $("input:radio[name=tab]:checked").attr('id'); //alert(main_tab);
		if (main_tab == "tab3")
		{
			$("#subtab_div").show();
			var sub_tab = $("input:radio[name=subtab]:checked").attr('id');
			var dataString = "subtab=" + sub_tab + "&contest_id=" + contest_id;
			if (sub_tab == "tab8")
			{
				$("#tab_for_group").show();
				$("#tab_for_follower").hide();
			}
			else
			{
				$("#tab_for_group").hide();
				$("#tab_for_follower").show();
			}
				var imageheight = $('.cont_img_con img')[0].height;
				imageheight = imageheight + 250;
				$('.contest_subpage').css('height', imageheight);
		}
		else
		{
			if (main_tab == tab4){
				$('.contest_subpage').css('height', '713px');
				$('.tab-body-wrapper').css('height', '713px');
			}
			$("#subtab_div").hide();
		}
    }
    function showhide_subtab_mobile(contest_id)
    {
		var main_tab = $('#mobileselected').val();
		window.location = "<?php echo url(); ?>/contestinforesponsive?contest_id=" + contest_id + "&tabname=" + main_tab;
    }

    function subtabclick_mobile(contest_id){
		var subtab = $('#subtab_mobile').val();
		var main_tab = $('#mobileselected').val();
		window.location = "<?php echo url(); ?>/contestinfosubtabresponsive?contest_id=" + contest_id + "&tabname=" + main_tab + "&subtab=" + subtab;
    }
    function invite_groups(group_id, contest_id)
    {
		window.location = "<?php echo url(); ?>/invite_group?contest_id=" + contest_id + "&group_id=" + group_id;
    }

    function uninvite_groups(group_id, contest_id)
    {
		window.location = "<?php echo url(); ?>/uninvite_group?contest_id=" + contest_id + "&group_id=" + group_id;
    }
    function invite_followers(followerid, contest_id)
    {
		var dataString = 'followerid=' + followerid + "&contest_id=" + contest_id;
		$.ajax({
		type: "POST",
				url : '../invite_follower',
				data : dataString,
				success : function(data){
				if (data == 1)
				{
				$("#invite_follist_" + followerid).attr("style", "background-color:red");
						$("#invite_follist_" + followerid).attr("onClick", "");
						$("#invite_follist_" + followerid).attr("title", "Invited");
						$("#inv_success_folo").html("Invitation sent successfully");
				}
				}
		});
    }
    function uninvite_followers(followerid, contest_id){

		var dataString = 'followerid=' + followerid + "&contest_id=" + contest_id;
		$.ajax({
		type: "get",
				url : '../uninvite_follower',
				data : dataString,
				success : function(data){ console.log(data);
						if (data == 1)
				{
				$("#invite_follist_" + followerid).attr("style", "background-color:#6BCF29");
						$("#invite_follist_" + followerid).attr("onClick", "");
						$("#invite_follist_" + followerid).attr("title", "Invit");
						$("#inv_success_folo").html("Un invite successfully");
				}
				}
		});
    }

    ///// JOIN //////

    $(document).ready(function(e) {

		var imageheight = $('.cont_img_con img')[0].height; console.log(imageheight);
		imageheight = imageheight + 250;
		$('.contest_subpage').css('height', imageheight);
		var adminlogin = '<?php if (Auth::user()->ID == 1) echo 1; 	else echo 0; ?>'
		if (adminlogin == 1){
			$('.ib-nav').addClass('ib-nav1').removeClass('ib-nav');
		}
		$(document).on("change", ".joinimage", function () {
		
			var file = this.files[0]; //console.log(file);
			var imagefile = file.type;
			var filesize = file.size / (1024 * 1024);
			var contest_type = $("#contest_type").val();
			if (contest_type == "p")
			{
				if (filesize > 2)
				{
					$("#join_con_error").html("The maximum 2MB images can be upload.");
					$("#join_con_error1").val("The maximum 2MB images can be upload.");
					return false;
				}
				else
				{
					$("#join_con_error").html("");
					$("#join_con_error1").val("");
				}
					var match = ["image/jpeg", "image/png", "image/jpg"];
					if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
					{
						$("#join_con_error").html("Upload only image file.");
						$("#join_con_error1").val("Upload only image file.");
						$(".cont_spon").addClass("cont_spon_error");
						$(".cont_spon_error").removeClass("cont_spon");
						$('.imgblink!').attr('src', 'noimage.png');
						return false;
					}
					else
					{
						$("#join_con_error").html("");
						$("#join_con_error1").val("");
						$(".cont_spon_error").addClass("cont_spon");
						$(".cont_spon").removeClass("cont_spon_error");
						var reader = new FileReader();
						reader.onload = imageIsLoaded;
						reader.readAsDataURL(this.files[0]);
					}
			}
			else if (contest_type == "v")
			{
				if (filesize > 5)
				{
					$("#join_con_error").html("The maximum 5MB images can be upload.");
					$("#join_con_error1").val("The maximum 5MB images can be upload.");
					return false;
				}
				else
				{
					$("#join_con_error").html("");
					$("#join_con_error1").val("");
				}
				if (!((imagefile == "video/mp4")))
				{
					$("#join_con_error").html("Upload only video type mp4.");
					$("#join_con_error1").val("Upload only video type mp4.");
					$(".cont_spon").addClass("cont_spon_error");
					$(".cont_spon_error").removeClass("cont_spon");
					return false;
				}
				else
				{
					$("#join_con_error").html("");
					$("#join_con_error1").val("");
					$(".cont_spon_error").addClass("cont_spon");
					$(".cont_spon").removeClass("cont_spon_error");
				}
			}
			$("#txt_selectfromgallery").html(file.name);
		});
	//// Topic photo //////////////////

		$(document).on("change", ".jointopicimage", function () {
			var file = this.files[0]; //console.log(file);
			var imagefile = file.type;
			var filesize = file.size / (1024 * 1024);
			if (filesize > 2)
			{
				$("#join_con_error").html("The maximum 2MB images can be upload.");
				$("#join_con_error1").val("The maximum 2MB images can be upload.");
				return false;
			}
			else
			{
				$("#join_con_error").html("");
				$("#join_con_error1").val("");
			}
			var match = ["image/jpeg", "image/png", "image/jpg"];
			if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
			{
				$("#join_con_error").html("Upload only image file.");
				$("#join_con_error1").val("Upload only image file.");
				return false;
			}
			else
			{
				$("#join_con_error").html("");
				$("#join_con_error1").val("");
				var reader = new FileReader();
				reader.onload = imageIsLoadedtopicphoto;
				reader.readAsDataURL(this.files[0]);
			}
		});
		$(document).on("change", ".jointopicvideo", function () {
		var file = this.files[0]; //console.log(file);
		var imagefile = file.type;
		var filesize = file.size / (1024 * 1024);
		if (filesize > 5)
		{
			$("#join_con_error").html("The maximum 5MB images can be upload.");
			$("#join_con_error1").val("The maximum 5MB images can be upload.");
			return false;
		}
		else
		{
			$("#join_con_error").html("");
			$("#join_con_error1").val("");
		}
		if (!((imagefile == "video/mp4")))
		{
			$("#join_con_error").html("Upload only video type mp4.");
			$("#join_con_error1").val("Upload only video type mp4.");
			return false;
		}
		else
		{
			$("#join_con_error").html("");
			$("#join_con_error1").val("");
		}
		});
	///// reply comment //////////
		$(document).on('click', '.reply_div', function() {
			var replyid = $(this).attr('id');
			showid = replyid.split("_");
			document.getElementById("replydiv_" + showid[1]).style.display = "block";
		});
		$(document).on('click', '.reply_div_forreply', function(){
			var replyid = $(this).attr('id');
			showid = replyid.split("_");
			document.getElementById("replydiv_" + showid[1]).style.display = "block";
		});
		$(document).on('click', '.replycmtsub', function(){
			var comment_id = $(this).val();
			rpyid = $(this).attr('id').split("_");
			replycmt = document.getElementById("cmt_" + rpyid[1]).value;
			commentid = document.getElementById('replycmtid_' + rpyid[1]).value;
			participant_id = $('.contest_participant_id').val();
			contest_id = $('.contest_id').val();
			window.location = "<?php echo url(); ?>/putreplycomment?comment_id=" + commentid + "&replycmt=" + replycmt + "&participant_id=" + participant_id + "&contest_id=" + contest_id;
		});
    });
    function imageIsLoaded(e) {
		var image = new Image();
		image.src = e.target.result;
		$(".roundedimgjoin").attr('src', e.target.result);
    }
    function imageIsLoadedtopicphoto(e){
		var image = new Image();
		image.src = e.target.result;
		$(".roundedimgtopic").attr('src', e.target.result);
    }
    function imageIsLoadedtopicvideo(e){
		var image = new Image();
		image.src = e.target.result;
		$(".roundedtopicvideo").attr('src', e.target.result);
    }

//// Voting ////
    function voting(status, contesttype)
    {
		if (contesttype == 'p')
            var contestparticipant_id = $('#ib-img-preview').find('div.participant_id')[0].innerHTML;
            else if (contesttype == 'v')
            var contestparticipant_id = $('#ib-video-preview').find('div.participant_id')[0].innerHTML;
            else
            var contestparticipant_id = $('.ib-content-preview').find('div.ib-content-full').end().find('div.participant_id').text();
            //// Audio File ////
            if (status == "like"){

			var audioTag = document.createElement('audio');
			if (!(!!(audioTag.canPlayType) && ("no" != audioTag.canPlayType("audio/mpeg")) && ("" != audioTag.canPlayType("audio/mpeg")))) {
				AudioPlayer.embed("audioplayer", {soundFile: "audio.mp3"});
			}
			} else if (status == "dislike"){
				document.getElementById('audio2').play();
			}

		var dataString = "contestparticipant_id=" + contestparticipant_id + "&votingstatus=" + status;
		$.ajax({
		type: "get",
				url : '../voting',
				data : dataString,
				success : function(data){
				console.log(data);
				}
		});
    }

    function videocmt(contid, partid){
		window.location.href = '<?php echo url(); ?>/viewcomment?participant_id=' + partid + '&contest_id=' + contid;
    }
    $(document).on('click', '.cmt_btn_save', function(){
		var comment = $('#comment').val();
		var contest_id = $('.contest_id').val();
		var participantid = $('.contest_participant_id').val();
		window.location = "<?php echo url(); ?>/putcomment?participantid=" + participantid + "&comment=" + comment + "&contest_id=" + contest_id;
    });
            //// REply comments //////////	
	function show_reply(){
	document.getElementById("reply_div").style.display = "block";
	}

	//// For gallery /////

	function nl2br (str, is_xhtml) {
	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
			return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
	}

	function uninviteallgroup(){
		if (jQuery('.checkgrouplist:checked').length > 0)
		{
			var checkseparate = [];
			jQuery('.checkgrouplist').each(function(index, element){ if (jQuery(this).is(':checked')){ checkseparate.push(jQuery(this).val()); }  });
			var contest_id = '<?php echo $contest_id; ?>';
			window.location = "<?php echo url(); ?>/uninvite_allgroup?checkseparate=" + checkseparate + "&contest_id=" + contest_id;
		}
		else{
			alert("Choose member for invite");
		}
	}

</script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/scripts.js') }}"></script>

<link rel="stylesheet" type="text/css" media="all" href="{{ URL::to('assets/inner/css/style_gallery.css') }}">
<noscript>
<style>
    .ib-main a{
        cursor:pointer;
    }
    .ib-main-wrapper{
        position:absolute;
        top:0px;
        bottom:24px;
        overflow:scroll;
    }
    .centertext{ 
        font-size:20;
        font-weight:bold;
        color:red;
    }

</style>
</noscript>

@stop
@section('body')
{{ Form::hidden('pagename','contest_info', array('id'=> 'pagename')) }}
<?php
if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = "contest_info";
}
if (Session::has('subtab')) {
    $subtab = Session::get('subtab');
} else {
    $subtab = "group";
}
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
}
if (Session::has('Massage')) {
    $Massage = Session::get('Massage');
}
if (Session::has('contest_id')) {
    $contest_id = Session::get('contest_id');
}

if (Session::has('gallerytype')) {
    $gallerytype = Session::get('gallerytype');
} else {
    $gallerytype = 1;
}
if (Session::has('contest_partipant_id')) {
    $contest_partipant_id = Session::get('contest_partipant_id');
} else {
    $contest_partipant_id = '';
}

if (Session::has('viewcommentforparticipant')) {
    $viewcommentforparticipant = Session::get('viewcommentforparticipant');
}
if (isset($contest_id)) {
    $contestdetails = contestModel::where('ID', $contest_id)->first();
    $owner_contest = ProfileModel::where('ID', $contestdetails['createdby'])->first();
}
////// New User save the private contest for Share the Contest //////
if ($contestdetails->visibility == 'p') {
    $privatecontestmodel = privateusercontestModel::where('user_id', Auth::user()->ID)->where('contest_id', $contest_id)->get()->count();
    if ($privatecontestmodel == 0) {
        $private_cont['contest_id'] = $contest_id;
        $private_cont['user_id'] = Auth::user()->ID;
        $private_cont['requesteddate'] = date('Y-m-d H:i:s');
        $private_cont['status'] = 1;
        privateusercontestModel::create($private_cont);
    }
}
?>
<div class="tabs-wrapper"> <input type="hidden" class="contest_id" value="{{ $contest_id }}" />
    <input type="radio" name="tab" id="tab1" class="tab-head" onclick="showhide_subtab('{{$contest_id}}');" <?php
		if ($tab == "contest_info") { echo "checked"; }  ?> />
    <label for="tab1" id="txt_contestinfo">Contest Info</label>
    <?php
    $curdate = date('Y-m-d H:i:s');
    if ($contestdetails['conteststartdate'] <= $curdate && $contestdetails['contestenddate'] >= $curdate) {
        ?>
        <input type="radio" name="tab" id="tab2" class="tab-head" onclick="showhide_subtab('{{$contest_id}}');" <?php
    if ($tab == "join") {
        echo "checked";
    }
        ?> />
        <label for="tab2" id="txt_join">Join</label><!-- class="act_sel"-->
        <?php
    }
    ?>
    <?php
           if ($contestdetails['createdby'] == Auth::user()->ID && $curdate <= $contestdetails['contestenddate'] || Auth::user()->ID == 1 && $curdate <= $contestdetails['contestenddate']) {
               ?>
        <input type="radio" name="tab" id="tab3" class="tab-head" onclick="showhide_subtab('{{$contest_id}}');" <?php
        if ($tab == "invite") {
            echo "checked";
        }
        ?> />
        <label for="tab3" id="txt_invite">Invite</label>
        <?php
    }
    ?>
    <?php
    $curdate = date('Y-m-d H:i:s');
    if ($curdate >= $contestdetails['votingstartdate'] || Auth::user()->ID == 1)
        $count_participants = contestparticipantModel::where('contest_id', $contest_id)->get()->count();
    else
        $count_participants = contestparticipantModel::where('contest_id', $contest_id)->where('user_id', Auth::user()->ID)->get()->count();
    if ($count_participants > 0 || Auth::user()->ID == 1 && $count_participants > 0) {
        ?>
        <input type="radio" name="tab" id="tab4" class="tab-head" onclick="showhide_subtab('{{$contest_id}}');" <?php
        if ($tab == "gallery") {
            echo "checked";
        }
        ?> />
        <label for="tab4" id="txt_gallery">Gallery</label><!--<a href="contest_gallery.html"></a>-->
        <?php
    }
    ?>
    <?php
    if ($contestdetails['votingenddate'] < $curdate) {
        ?>
        <input type="radio" name="tab" id="tab5" onclick="showhide_subtab('{{$contest_id}}');" class="tab-head" />
        <label for="tab5" id="txt_leaderboard">Leader Board</label>
        <?php
    }
    ?>
    <?php
    if ($curdate < $contestdetails['contestenddate']) {
        ?>
        <input type="radio" name="tab" id="tab7" onclick="showhide_subtab('{{$contest_id}}');" class="tab-head" />
        <label for="tab7" id="txt_share">Share</label>

            <?php
        }
        ?>
    <div id="subtab_div" class="con_cat_right mbnone" <?php if ($tab != "invite") { ?> style="display:none" <?php } ?>>
        <input type="radio" name="subtab" id="tab8" onclick="showhide_subtab('{{$contest_id}}');" <?php
               if ($subtab == "group") {
                   echo "checked";
               }
        ?> class="tab-head" />
        <label for="tab8" id="mnu_group" >Group</label>
        <input type="radio" name="subtab" id="tab9" onclick="showhide_subtab('{{$contest_id}}');" <?php
               if ($subtab == "follower") {
                   echo "checked";
               }
        ?> class="tab-head" />
        <label for="tab9" id="mnu_follower">Follower</label>

    </div>



    <div class="mbblk">
        <select class="radius sel_lang" id="mobileselected" onchange="showhide_subtab_mobile('{{$contest_id}}');">
            <option value="contest_info"  <?php
                     if ($tab == "contest_info") {
                         echo "selected";
                     }
                     ?> class="txt_contestinfo">Contest Info</option>
            <?php if ($contestdetails['conteststartdate'] <= $curdate && $contestdetails['contestenddate'] >= $curdate) { ?>
                <option value="join" <?php
            if ($tab == "join") {
                echo "selected";
            }
            ?> class="txt_join" >Join</option>
        <?php } if ($contestdetails['createdby'] == Auth::user()->ID) {
            ?> <option  value="invite" <?php
                if ($tab == "invite") {
                    echo "selected";
                }
                ?> class="txt_invite" >Invite</option>
                    <?php } if ($count_participants > 0) { ?>	   <option  value="gallery" <?php
                    if ($tab == "gallery") {
                        echo "selected";
                    }
                    ?> class="txt_gallery">Gallery</option><?php } ?>
<?php if ($contestdetails['votingenddate'] < $curdate) { ?> <option value="leaderboard" class="txt_leaderboard" >Leader Board</option><?php } ?>

<?php
if ($contestdetails['createdby'] == Auth::user()->ID && $curdate < $contestdetails['votingenddate'] && $contestdetails['contestenddate']) {
    ?>
                <option value="share" class="txt_share">Share</option>
<?php } ?>
        </select>

<?php if ($tab == "invite") { ?><select class="radius sel_lang" id="subtab_mobile" onchange="subtabclick_mobile('{{$contest_id}}')">
                <option value="group" <?php
    if ($subtab == "group") {
        echo "selected";
    }
    ?> class="mnu_group" >Group</option>
                <option value="follower" <?php
    if ($subtab == "follower") {
        echo "selected";
    }
    ?> class="mnu_follower">Follower</option>
                <!--<option>Following</option>-->
            </select><?php } ?>
    </div>

    <input type="hidden" name="contest_id" id="contest_id" value="{{ $contest_id }}" />
    <input type="hidden" name="contest_type" id="contest_type" value="{{ $contestdetails['contesttype'] }}" />

    <div class="tab-body-wrapper contest_subpage">
        <div id="tab-body-1" class="tab-body">
            <div class="cont_img_con radius">
                <img src="{{ URL::to('public/assets/upload/contest_theme_photo/'.$contestdetails['themephoto']) }}" class="cont_img">
                <div class="cont_list">
                    <div class="name" ><strong><span class="pch_contestname" >Contest Name: </span></strong><div class="mb_brk"></div><span class="mleft">{{ $contestdetails['contest_name'] }}</span></div>
                    <div class="ctype"><strong><span class="txt_contesttype" >Contest Type: </span></strong><div class="mb_brk"></div><span class="mleft">{{ (($contestdetails['contesttype']=='p')?'Photo':(($contestdetails['contesttype']=='v')?'Video':(($contestdetails['contesttype']=='t')?'Topic':''))) }}</span></div>
                    <div class="cstart"><strong><span class="conteststart" >Contest Start Date: </span></strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails['conteststartdate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }}</span></div>
                    <div class="cend"><strong><span class="contestend" >Contest End Date: </span></strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails['contestenddate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }}</span></div>
                    <div class="vstart"><strong><span class="votingstart" >Voting start Date: </span></strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails['votingstartdate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }}</span></div>
                    <div class="vend"><strong><span class="votingend" >Voting End Date:</span> </strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails['votingenddate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }}</span></div>
                    <!--<div class="prize"><strong><span class="txt_prize">Prize:</span> </strong><div class="mb_brk"></div><span class="mleft">{{  $contestdetails['prize'] }}</span></div>-->
                    <div class="noparty"><strong><span class="txt_noofparticipant">No. of Participants:</span> </strong><div class="mb_brk"></div><span class="mleft">{{ ($contestdetails['noofparticipant']==0)?"Unlimited":$contestdetails['noofparticipant']; }}</span></div>
                </div>
            </div>

            <div class="cont_det_con">
                <h1>{{ $contestdetails['contest_name'] }}</h1>
                <div class="cont_des_con">
                    <strong><span class="txt_contestdescription">Contest Description</span></strong>
                    <div class="srl_h8 radius">
                        {{ nl2br($contestdetails['description']) }}
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="cont_des_btm">
                    <div class="cont_org fleft" <?php if ($contestdetails['sponsorname'] == '') { ?>style="margin-left:40%"<?php } ?> >
                        <div class="bgt_box blutxt radius"><span class="txt_organizer">Organizer</span></div>
                        @if($owner_contest['profilepicture']!='')
                        <div class="clrscr"></div>
                        <img src="{{ URL::to('public/assets/upload/profile/'.$owner_contest['profilepicture']) }}" class="roundedimg">
                        <div class="clrscr"></div>
                        @endif
                        @if($owner_contest['profilepicture']=='')
                        <div class="clrscr"></div>
                        <img src="{{ URL::to('assets/inner/images/avator.png') }}" class="roundedimg">
                        <div class="clrscr"></div>							
                        @endif
                        <strong><?php
						if ($owner_contest['firstname'] != '') {
							echo $owner_contest['firstname'] . " " . $owner_contest['lastname'];
						} else {
							echo $owner_contest['username'];
						}
						?></strong>
                    </div>

                    <div class="cont_spon fleft">
                        @if($contestdetails['sponsorname']!='')
                        <div class="bgt_box grntxt radius">Sponsor</div>
                        <div class="clrscr"></div>
                        @if($contestdetails['sponsorphoto']!='')

                        <img src="{{ URL::to('public/assets/upload/sponsor_photo/'.$contestdetails['sponsorphoto']) }}" class="roundedimg">
                        @endif
                        <div class="clrscr"></div>
                        <strong>{{ $contestdetails['sponsorname'] }}</strong>
                        @endif
                    </div>
                </div>

                <div class="clearfix"></div>

            </div>
        </div>

        <div id="tab-body-2" class="tab-body">
            <div class="join_con" id="join_up" style="display:block">
                <h1>{{ $contestdetails['contest_name'] }}</h1>
                <div class="cont_des_con">
                    <strong>
                            <?php
                            $user_id = Auth::user()->ID;
                            $old_cont_value = contestparticipantModel::where('contest_id', $contest_id)->where('user_id', $user_id)->first();
                            $old_cont_count = count($old_cont_value);
                            if ($contestdetails['contesttype'] == "p") {
                                echo ($old_cont_count > 0) ? "Change Participating Photo" : "Upload Participating Photo";
                            } elseif ($contestdetails['contesttype'] == "v") {
                                echo ($old_cont_count > 0) ? "Change Participating Video" : "Upload Participating Video";
                            } else {
                                echo ($old_cont_count > 0) ? "Update Participating Content" : "Add Participating Content";
                            }
                            ?>
                    </strong>
                </div>

                <div class="clearfix"></div>

                <div class="join_des_btm">           	
                    <div  <?php if (isset($Massage)) { ?> class="cont_spon_error" <?php } else { ?>class="cont_spon" <?php } ?> >
                        {{ isset($Massage)?"<p style='color:red;text-align:center'>".$Massage."</p>":""}}
                        <p id="join_con_error" style='color:red;text-align:center'></p>
                        <span id="topic_error" style='color:red;text-align:center' ></span>
                        <input type="hidden" id="join_con_error1">
                        <form name="join_contest" action="{{ URL::to('join_contest') }}" id="joinimage" method="post" enctype="multipart/form-data" >
				<?php
				if ($contestdetails['contesttype'] == "t") {
				?>
				<script>
					function checkbeforejoin(){

					if ($('textarea#uploadtopic').val() == ''){ alert("SS");
							$("#join_con_error").html("Please enter the topic values");
							return false;
					}
					}
				</script>

                                <textarea name="uploadtopic" id="uploadtopic" cols="100" rows="15" class="radius" style="margin-left:-50%">{{ isset($old_cont_value['uploadtopic'])?$old_cont_value['uploadtopic']:"" }}</textarea>

    <?php
    $topicphoto = url() . '/public/assets/upload/topicphotovideo/' . $old_cont_value['topicphoto'];

    $topicvideo = url() . '/public/assets/upload/topicphotovideo/' . $old_cont_value['topicvideo'];
    ?>
                                <div>
                                    <div class="topic_imgcls">
                                        <label class="myLabel topic_img">
                                            <img src="{{ (isset($old_cont_value['topicphoto'])&&$contestdetails['contesttype']=='t')?($topicphoto):(URL::to('assets/inner/img/join_gallery.png')) }}" class="roundedimg roundedimgtopic">
                                            <input type="file" name="topicphoto" class="jointopicimage"  onchange="updateuploadtopicimg(this.value)" >
                                        </label>
                                        <div class="clrsec"></div>
                                        <span class="chose_file">Select from Gallery</span>
                                    </div>

                                    <div>
                                        <?php  if($old_cont_value['topicvideo']!=''){?>
										 <video class="topic_vidcls" controls="">											
                                            <source src="{{ $topicvideo }}" type="video/mp4">                          								
                                        </video>
										
                                        <span class="chose_file chose_vid">Select from Gallery</span>
										<div class="clrscr"></div>										
                                    </div>
										
										<?php 
										}else{ ?>
										
										
                                    <div class="clrscr"></div>
                                    <img src="{{ URL::to('assets/inner/img/upload-vid.png') }}" class="roundedimg roundedimgtopic">		
										
									<?php } ?>									
									<!--<input type="file" name="topicvideo" class="jointopicimage"   >-->
										<!--video src="{{ (isset($old_cont_value['topicvideo'])&&$contestdetails['contesttype']=='t')?($topicvideo):(URL::to('assets/inner/img/join_gallery.png')) }}" style="width:80px; height:80px;"></video-->
                                     </div>
    <?php
} else {
    ?>
<script>
		function updateuploadcontestimg(vals)
		{
		//$("#txt_selectfromgallery").html(vals);
		}
	function checkbeforejoin()
	{

		if ($('.joinimage').val() == ''){
			$(".cont_spon").addClass("cont_spon_error");
			$(".cont_spon_error").removeClass("cont_spon");
			return false;
		}
		else{
			$(".cont_spon_error").addClass("cont_spon");
			$(".cont_spon").removeClass("cont_spon_error");
		}
		if ($("#join_con_error1").val() != "")
		{
			return false;
		}
		else{
		}
			return true;
	}
</script>
    <?php
    //	$joinedimage = fetchUrl('/participant/'.$old_cont_value['uploadfile']);
    $uploadfile = url() . '/public/assets/upload/contest_participant_photo/' . $old_cont_value['uploadfile'];
    ?>

                                <label class="myLabel" style="background:none;height:130px; margin-left:0;">
                                    <img src="{{ (isset($old_cont_value['uploadfile'])&&$contestdetails['contesttype']=='p')?($uploadfile):(URL::to('assets/inner/img/join_gallery.png')) }}" class="roundedimg roundedimgjoin">
                                    <input type="file" name="uploadfile" class="joinimage"  onchange="updateuploadcontestimg(this.value)"  >
                                    <input type="button" value="Browse" class="up_btn">
                                </label>
                                <div class="clrscr"></div>
                                <strong><span id="txt_selectfromgallery">Select from Gallery</span></strong>
                        <?php
                    }
                    ?>

                            <input type="hidden" name="user_id" value="{{ Auth::user()->ID }}" >
                            <input type="hidden" name="contest_id" value="{{ $contest_id }}" />
                            <div class="loginbox">
                                <p><center>
                                    <button class="radius martop_10" name="submitphoto" onclick="return checkbeforejoin();"><span class="txt_submit">Submit</span></button>&nbsp;&nbsp;&nbsp;                	
                                </center></p> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div id="tab-body-3" class="tab-body">
            <div id="tab_for_group" <?php
                                if ($subtab != "group") {
                                    echo "style='display:none'";
                                }
                                ?>>
                <form name="invite_group" action="{{ URL::to('inviteall_group') }}" method="post">
                    <input type="hidden" name="contest_id" value="{{ $contest_id }}">
                                <?php
                                if (Session::has('inv_suc_message'))
                                    $inv_suc_message = Session::get('inv_suc_message');
                                if (Session::has('inv_fail_message'))
                                    $inv_fail_message = Session::get('inv_fail_message');
                                ?>
                    <div id="inv_success" style="color:green;text-align:center;font-size:14px">{{ isset($inv_suc_message)?($inv_suc_message):"" }}</div>
                    <div id="inv_fail" style="color:red;text-align:center;font-size:14px">{{ isset($inv_fail_message)?($inv_fail_message):"" }}</div>
                    <div class="scrolltable">
                        <table class="display" cellspacing="0" width="100%" id="dd_group_list">
                            <thead>
                                <tr>
                                    <th  style="background-color:#0896D6"><input name="" class="checkallgroups" type="checkbox" value="1" style="visibility:hidden;"></th>
                                    <th><span class="txt_img">Image</span></th>
                                    <th><span class="txt_groupname">Group Name</span></th>
                                    <th class="tr_wid_button1" style="background-color:#0896D6" align="center"><span class="txt_invite">Invite</span>/Uninvite</th>
                                    <th class="tr_wid_button1" style="background-color:#0896D6" align="center"><span class="txt_view">View</span></th>

                                </tr>
                            </thead>
                            <tbody>
                                        <?php
                                        $grouplist = groupModel::select('group.ID as groupid', 'groupname', 'grouptype', 'createdby', 'user.firstname as owner', 'groupimage')->LeftJoin('group_members', 'group_members.group_id', '=', 'group.ID')->Leftjoin('user', 'user.ID', '=', 'group_members.group_id')->where('group_members.user_id', Auth::user()->ID)->where('group.status', 1)->get();
                                        $groupcount = count($grouplist);
                                        for ($i = 0; $i < $groupcount; $i++) {

                                            $invited = invitegroupforcontestModel::where('group_id', $grouplist[$i]['groupid'])->where('contest_id', $contest_id)->count();

                                            $groupmemberlist = groupmemberModel::where('group_id', $grouplist[$i]['groupid'])->get()->count();

                                            $groupmemberlist_admin = groupmemberModel::where('group_id', $grouplist[$i]['groupid'])->where('user_id', 1)->get()->count();
                                            ?>
                                    <tr class="clickforrowselect" <?php /* onClick="aa(<?php echo $i; ?>)" */ ?> id="clickforrowselect_<?php echo $i; ?>">
                                        <td class="tr_wid_id"><input name="group_list" id="checkgrouplist_<?php echo $i; ?>" class="checkgrouplist" type="checkbox" value="{{ $grouplist[$i]['groupid'] }}" <?php if ($invited + 1 == $groupmemberlist - $groupmemberlist_admin && $groupmemberlist - $groupmemberlist_admin != 1) echo "checked"; ?> ></td>
                                        <td align="center"><img src="{{ ($grouplist[$i]['groupimage']!='')?(URL::to('public/assets/upload/group/'.$grouplist[$i]['groupimage'])):(URL::to('assets/inner/img/default_groupimage.png')) }}" width="50" height="50"></td>
                                        <td>{{ $grouplist[$i]['groupname'] }}</td>
                                        <td class="tr_wid_button1 inviteall inviteall_<?php echo $grouplist[$i]['groupid']; ?>" align="center">
    <?php
    if ($groupmemberlist_admin > 0) {
        if ($invited + 2 == $groupmemberlist && $groupmemberlist != 2) {
            echo "<font style='color:red;' >Invited</font>";
        } else if ($invited != 0) {
            echo "<font style='color:#0896D6;' >Partially Invited</font>";
        } else {
            echo "<font style='color:green;' >Invite</font>";
        }
    } else {
        if ($invited + 1 == $groupmemberlist && $groupmemberlist != 1) {
            echo "<font style='color:red;' >Invited</font>";
        } else if ($invited != 0) {
            echo "<font style='color:#0896D6;' >Partially Invited</font>";
        } else {
            echo "<font style='color:green;' >Invite</font>";
        }
    }
    ?>

                                        </td>
                                        <td class="tr_wid_button1" align="center"><a href="{{ URL::to('viewgroupmemberfrominvite?groupid='.$grouplist[$i]['groupid'].'&contest_id='.$contest_id) }}" class="view-link"></a></td>
                                    </tr>
    <?php
}
?>
                            </tbody>
                        </table>
                    </div>
                    <div class="clrscr"></div>

                    <div class="loginbox">
                        <p><center>
                        </center></p> 
                    </div>
                </form>

            </div>
            <div id="tab_for_follower" <?php
                                if ($subtab == "group") {
                                    echo "style='display:none'";
                                }
                                ?>>
                <form name="invite_follower" action="{{ URL::to('inviteall_follower') }}" method="post">
                    <input type="hidden" name="contest_id" value="{{ $contest_id }}">
                                <?php
                                if (Session::has('inv_suc_message'))
                                    $inv_suc_message = Session::get('inv_suc_message');
                                if (Session::has('inv_fail_message'))
                                    $inv_fail_message = Session::get('inv_fail_message');
                                ?>
                    <div id="inv_success_folo" style="color:green;text-align:center;font-size:14px">{{ isset($inv_suc_message)?($inv_suc_message):"" }}</div>
                    <div id="inv_fail" style="color:red;text-align:center;font-size:14px">{{ isset($inv_fail_message)?($inv_fail_message):"" }}</div>
                    <div class="scrolltable">
                        <table class="display" cellspacing="0" width="100%" id="dd_follower_list">
                            <thead>
                                <tr>
                                    <th style="background-color:#0896D6"><input name="" class="checkallfollowers" style="visibility:hidden;" type="checkbox" value="1"></th>
                                    <th><span class="txt_img">Image</span></th>
                                    <th><span class="txt_followername">Follower Name</span></th>
                                    <th class="tr_wid_button1"  style="background-color:#0896D6" align="center"><span class="txt_invite">Invite</span>/Uninvite</th>
                                    <th class="tr_wid_button1"  style="background-color:#0896D6" align="center"><span class="txt_view">View</span></th>

                                </tr>
                            </thead>
                            <tbody>
<?php
$cur_user = Auth::user()->ID;
$followerlist = followModel::where('followerid', $cur_user)->select('userid', 'followerid', 'user.profilepicture as profilepicture', 'user.firstname as firstname', 'user.lastname as lastname', 'user.username')->leftJoin('user', 'followers.userid', '=', 'user.ID')->where('user.status', 1)->get();


$followercount = count($followerlist);

for ($i = 0; $i < $followercount; $i++) {
    $invited = invitefollowerforcontestModel::where('follower_id', $followerlist[$i]['userid'])->where('contest_id', $contest_id)->count();
    ?>
                                    <tr>
                                        <td class="tr_wid_id"><input name="follower_list[]" class="checkfollowerlist"  type="checkbox" value="{{ $followerlist[$i]['userid'] }}" <?php if ($invited > 0) echo "checked"; ?>></td>
                                        <td align="center"><img src="{{ ($followerlist[$i]['profilepicture']!='')?(URL::to('public/assets/upload/profile/'.$followerlist[$i]['profilepicture'])):(URL::to('assets/inner/img/default_groupimage.png')) }}" width="50" height="50"></td>
                                        <td><?php
            if ($followerlist[$i]['firstname'] != '') {
                echo$followerlist[$i]['firstname'] . " " . $followerlist[$i]['lastname'];
            } else {
                echo $followerlist[$i]['username'];
            }
            ?></td>
                                        <td class="tr_wid_button1 inviteall inviteall_<?php echo $followerlist[$i]['userid']; ?>" align="center">
            <?php
            if ($invited > 0) {
                echo "<font style='color:red;' >Invited</font>";
            } else {
                echo "<font style='color:green;' >Invite</font>";
            }
            ?>
                                        </td>
                                        <td class="tr_wid_button1" align="center"><a href="{{ URL::to('other_profile/'.$followerlist[$i]['userid']) }}" class="view-link"></a></td>
                                    </tr>
            <?php
        }
        ?>
                            </tbody>
                        </table></div>
                    <div class="clrscr"></div>

                    <div class="loginbox">
                        <p><center>

                        </center></p> 
                    </div>
                </form>
            </div>
        </div>
<?php
$curdate = date('Y-m-d H:i:s');
if (Auth::user()->ID == 1) {

    if ($contest_partipant_id != '') {
        //// For report flag ///////////
        $participants = contestparticipantModel::where('contest_id', $contest_id)->where('ID', $contest_partipant_id)->get();
    } else {
        $participants = contestparticipantModel::where('contest_id', $contest_id)->get();
    }
} else {

    if ($curdate > $contestdetails['votingstartdate'])
        $participants = contestparticipantModel::where('contest_id', $contest_id)->get();
    else
        $participants = contestparticipantModel::where('contest_id', $contest_id)->where('user_id', Auth::user()->ID)->get();
}


$noofparticipants = count($participants);
?>
        <div id="tab-body-4" class="tab-body">
<?php
if ($gallerytype == 1) {
    ?>
                <div id="gallery">
                    <div class="con_hed_blk">
                        <div class="con_head">
                            <h1>{{ $contestdetails['contest_name'] }}</h1>
                        </div>
                        <div class="con_search">
                            <form name="" action="">
                                <div class="mb_con_search" style="vertical-align:top;margin:0; padding:0;">

                                </div>
                            </form>
                        </div>
                    </div>  
                    <div class="clrscr"></div>
                    <div class="container">
                                        <?php if ($curdate > $contestdetails['votingenddate']) { ?>
                            <div id="ib-main-wrapper1" class="ib-main-wrapper1">
                                        <?php } else { ?>
                                <div id="ib-main-wrapper" class="ib-main-wrapper">
                                        <?php } ?>

                                <div class="ib-main">
                                    <div class="crsl-wrap">


    <?php
    if ($contestdetails['contesttype'] == 't') {
        if ($curdate > $contestdetails['votingenddate']) {
            for ($i = 0; $i < $noofparticipants; $i++) {
                if (Auth::user()->ID == 1) {
                    ?><div class="crsl-item"><?php } ?><div class="thumbnail">
                                                            <a href="<?php echo url(); ?>/viewcomment?participant_id=<?php echo $participants[$i]['ID']; ?>&contest_id=<?php echo $contest_id; ?>" class="ib-content" >

                                                                <div class="ib-teaser">
                                                                    <h2><!--{{ $contestdetails['contest_name'] }}--><span class="hideinfullscreen"><?php echo substr(($participants[$i]['uploadtopic']), 0, 20) . "....."; ?></span></h2>
                                                                </div>
                                                                <div class="topiccontent">											
                                                                </div>
                                                                <div class="ib-content-full">
                                                                    <div class="fullscreen" >
                <?php echo nl2br($participants[$i]['uploadtopic']); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="participant_id"><?php echo $participants[$i]['ID']; ?></div>
                                                            </a>
                                                        </div><?php if (Auth::user()->ID == 1) { ?><span style="cursor:pointer;" onclick="return removethis('<?php echo $participants[$i]['ID']; ?>', '<?php echo $contest_id; ?>', '<?php echo $contest_partipant_id; ?>')">Remove</span></div><?php } ?>
                                                    <?php
                                                }
                                            } else {
                                                for ($i = 0; $i < $noofparticipants; $i++) {
                                                    $voted = votingModel::where('contest_participant_id', $participants[$i]['ID'])->where('user_id', Auth::user()->ID)->get()->count();
                                                    if (!$voted) {

                                                        $participant_owner = User::where('ID', $participants[$i]['user_id'])->first();
                                                        ?>
                    <?php if (Auth::user()->ID == 1) { ?><div class="crsl-item"><?php } ?><div class="thumbnail">
                                                                <a href="#" class="ib-content" >

                                                                    <div class="ib-teaser">
                                                                        <h2><p class="defaulthide" style="display:none;" >{{ $contestdetails['contest_name'] }}</p><span class="hideinfullscreen"><?php echo substr(($participants[$i]['uploadtopic']), 0, 20) . "...."; ?></span></h2>
                                                                    </div>
                                                                    <div class="topiccontent">

                                                                    </div>
                                                                    <div class="ib-content-full">
                                                                        <div class="fullscreen" >
                                                        <?php echo nl2br($participants[$i]['uploadtopic']); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="participant_id"><?php echo $participants[$i]['ID']; ?></div>
                                                                    <span class="participant_detail"><?php
                                            if ($participant_owner['firstname'] != '') {
                                                echo $participant_owner['firstname'] . " " . $participant_owner['lastname'];
                                            } else {
                                                echo $participant_owner['username'];
                                            }
                                            ?> </span>
                                                                </a>
                                                            </div><?php if (Auth::user()->ID == 1) { ?><span style="cursor:pointer;" onclick="return removethis('<?php echo $participants[$i]['ID']; ?>', '<?php echo $contest_id; ?>', '<?php echo $contest_partipant_id; ?>')">Remove</span></div><?php } ?>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        } else if ($contestdetails['contesttype'] == 'v') {

                                                            if ($curdate > $contestdetails['votingenddate']) {
                                                                for ($i = 0; $i < $noofparticipants; $i++) {
                                                                    if (Auth::user()->ID == 1) {
                                                                        ?><div class="crsl-item"><?php } ?><div class="thumbnail">
                                                            <a  href="#" class="ib-video" style="width:125px; height:125px;" onclick="videocmt('<?php echo $contest_id; ?>', '<?php echo $participants[$i]['ID']; ?>')" >
                                                                <video width="125" height="125"  controls>											
                                                                    <source src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participants[$i]['uploadfile']; ?>" type="video/mp4">								
                                                                </video>
                                                                <div class="participant_id"><?php echo $participants[$i]['ID']; ?></div>


                                                            </a></div><?php if (Auth::user()->ID == 1) { ?><span style="cursor:pointer;" onclick="return removethis('<?php echo $participants[$i]['ID']; ?>', '<?php echo $contest_id; ?>', '<?php echo $contest_partipant_id; ?>')">Remove</span></div><?php } ?>
                <?php
            }
        } else {
            for ($i = 0; $i < $noofparticipants; $i++) {
                $voted = votingModel::where('contest_participant_id', $participants[$i]['ID'])->where('user_id', Auth::user()->ID)->get()->count();

                $participant_owner = User::where('ID', $participants[$i]['user_id'])->first();

                if (!$voted) {
                    ?>
                                                        <?php if (Auth::user()->ID == 1) { ?><div class="crsl-item"><?php } ?><div class="thumbnail">
                                                                <a class="ib-video" style="width:125px; height:125px;" >
                                                                    <video width="125" height="125"  controls>											
                                                                        <source src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participants[$i]['uploadfile']; ?>" type="video/mp4">								
                                                                    </video>
                                                                    <div class="participant_id" ><?php echo $participants[$i]['ID']; ?></div>
                                                                    <span class="participant_detail"><?php
                                                                        if ($participant_owner['firstname'] != '') {
                                                                            echo $participant_owner['firstname'] . " " . $participant_owner['lastname'];
                                                                        } else {
                                                                            echo $participant_owner['username'];
                                                                        }
                                                                        ?></span>
                                                                </a></div><?php if (Auth::user()->ID == 1) { ?><span style="cursor:pointer;" onclick="return removethis('<?php echo $participants[$i]['ID']; ?>', '<?php echo $contest_id; ?>', '<?php echo $contest_partipant_id; ?>')">Remove</span></div><?php } ?>
                                                        <?php
                                                    }
                                                }
                                            }
                                        } else {
                                            if ($curdate > $contestdetails['votingenddate']) {

                                                for ($i = 0; $i < $noofparticipants; $i++) {
                                                    $participant_owner = User::where('ID', $participants[$i]['user_id'])->first();
                                                    ?>
                <?php if (Auth::user()->ID == 1) { ?><div class="crsl-item"><?php } ?><div class="thumbnail">
                                                            <a href="<?php echo url(); ?>/viewcomment?participant_id=<?php echo $participants[$i]['ID']; ?>&contest_id=<?php echo $contest_id; ?>"  ><img src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participants[$i]['uploadfile']; ?>"  alt="image01"   /><span><?php
                                if ($participant_owner['firstname'] != '') {
                                    echo $participant_owner['firstname'] . " " . $participant_owner['lastname'];
                                } else {
                                    echo $participant_owner['username'];
                                }
                                ?></span>
                                                                <div><?php echo $participants[$i]['ID']; ?></div>
                                                            </a>
                                                        </div><?php if (Auth::user()->ID == 1) { ?><span style="cursor:pointer;" onclick="return removethis('<?php echo $participants[$i]['ID']; ?>', '<?php echo $contest_id; ?>')">Remove</span></div><?php } ?>
                                <?php
                            }
                        } else {

                            for ($i = 0; $i < $noofparticipants; $i++) {
                                $participant_owner = User::where('ID', $participants[$i]['user_id'])->first();
                                $voted = votingModel::where('contest_participant_id', $participants[$i]['ID'])->where('user_id', Auth::user()->ID)->get()->count();
                                if (!$voted) {
                                    ?>
                                            <?php if (Auth::user()->ID == 1) { ?><div class="crsl-item"><?php } ?><div class="thumbnail">
                                                                <a href="#"><img src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participants[$i]['uploadfile']; ?>" data-largesrc="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participants[$i]['uploadfile']; ?>" alt="image01"/><span><?php
                        if ($participant_owner['firstname'] != '') {
                            echo $participant_owner['firstname'] . " " . $participant_owner['lastname'];
                        } else {
                            echo $participant_owner['username'];
                        }
                        ?></span>
                                                                    <div><?php echo $participants[$i]['ID']; ?></div>
                                                                </a>
                                                            </div><?php if (Auth::user()->ID == 1) { ?><span style="cursor:pointer;" onclick="return removethis('<?php echo $participants[$i]['ID']; ?>', '<?php echo $contest_id; ?>', '<?php echo $contest_partipant_id; ?>')">Remove</span></div><?php } ?>										
                    <?php
                }
            }
        }
    }
    ?>

                                    </div>
                                    <div class="clr"></div>
                                </div>
                            </div><!-- ib-main -->
                        </div><!-- ib-main-wrapper -->
                        <!-- </div>-->
                    </div>

                                <?php
                            } else { ////// Same tab for comments also /////////////// 
                                $viewcommentforparticipant;
                                $participantdetails = contestparticipantModel::select('user.username', 'user.firstname', 'user.lastname', 'user.ID as usrid', 'contestparticipant.uploadfile', 'contestparticipant.uploadtopic', 'contestparticipant.dropbox_path')->LeftJoin('user', 'user.ID', '=', 'contestparticipant.user_id')->where('contestparticipant.ID', $viewcommentforparticipant)->get();


                                $contestdetails = contestModel::select('contest_name', 'themephoto', 'contesttype')->where('ID', $contest_id)->get();

                                $authusrid = Auth::user()->ID;
                                $participantusrid = $participantdetails[0]['usrid'];
                                $followercnt = followModel::where('userid', $authusrid)->where('followerid', $participantusrid)->get()->count();
                                ?>
                    <div id="comments" >

                                <?php /* if(Session::has('Massage')) { ?>
                                  <p class="alert" style="color:red;"><?php echo Session::get('Massage'); ?> </p>
                                  <?php} */ ?>

                        <div class="cont_img_con radius">
    <?php if ($contestdetails[0]['contesttype'] == 'p') { ?>
                                <img src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participantdetails[0]['uploadfile']; ?>" class="cont_img">
    <?php } else if ($contestdetails[0]['contesttype'] == 'v') { ?>			
                                <video class="cont_img"   controls>											
                                    <source src="<?php echo url(); ?>/public/assets/upload/contest_participant_photo/<?php echo $participantdetails[0]['contestparticipant']; ?>" type="video/mp4">								
                                </video>
    <?php } else { ?>
                                <div class="topiccomment"  >{{ nl2br($participantdetails[0]['uploadtopic']) }}</div>	
    <?php } ?>

                            <div class="cont_list">  
                                <div class="name"><strong><span class="pch_contestname" >Contest Name:</span> </strong><span class="mleft">{{ $contestdetails[0]['contest_name']}}</span></div>
                                <div style="float:left;"><strong style="float:left"><!--<span class="txt_img_postedby">--><?php
    if ($contestdetails[0]['contesttype'] == 'p') {
        echo "Image";
    } elseif ($contestdetails[0]['contesttype'] == 'v') {
        echo "Video";
    } else {
        echo "Topic";
    }
    ?> posted by:<!--</span>--> </strong>

                                    <div style="float:left;"><?php if ($participantdetails[0]['firstname'] == '')
        echo $participantdetails[0]['username'];
    else
        echo $participantdetails[0]['firstname'] . ' ' . $participantdetails[0]['lastname'];
    ?></div>
                                <?php if (Auth::user()->ID != $participantusrid) { ?>
                                        <div style="float:left;"><?php if ($followercnt) { ?><img src="{{ URL::to('/assets/inner/img/bell_symbol.png') }}" title="Following" ><?php } else { ?><a href="<?php echo url(); ?>/follow?followerid=<?php echo $participantusrid; ?>&contest_id=<?php echo $contest_id ?>&participant_id=<?php echo $viewcommentforparticipant; ?>" ><input type="button" id="follow" name="" value="Follow" style="cursor:pointer;" class="follow_btn" /></a><?php } ?></div>
    <?php } ?>
                                </div>
    <?php
    if ($contestdetails[0]['contesttype'] == 't') {
        $uploadedfile = $participantdetails[0]['uploadtopic'];
    } else {
        $uploadedfile = url() . '/public/assets/upload/contest_participant_photo/' . $participantdetails[0]['uploadfile'];
    }
    ?>

                                <meta property="og:title" content="DingDatt - {{ $contestdetails[0]['contest_name'] }} contest"/>
                                <meta property="og:type" content="website"/>
                                <meta property="og:url" content="{{ Request::url() }}"/>
                                <meta property="og:image" content="{{ URL::to('public/assets/upload/contest_participant_photo/'.$uploadedfile)}}"/>
                                <meta property="og:description" content="<?php
                                                echo "Image posted by:";
                                                if ($participantdetails[0]['firstname'] == '')
                                                    echo $participantdetails[0]['username'];
                                                else
                                                    echo $participantdetails[0]['firstname'] . ' ' . $participantdetails[0]['lastname'];
                                                ?>"/>

                                <div class="clr"></div>
                                <div style="float:left;"><strong style="float:left">Share with:</strong></div>	
                                <span >		
                                    <span class='st_facebook' ></span>
                                    <span class='st_twitter' ></span>
                                    <span class='st_email' ></span>
                                    <span class='st_googleplus' ></span>
                                    <span class='st_tumblr' ></span>
                                    <span class='st_instagram'></span>
                                    <span class='st_pinterest'></span>

                                </span>
                            </div>
                        </div>
                        <a href="{{ URL::to('contesttab?contest_id='.$contest_id.'&contest_partipant_id='.$viewcommentforparticipant) }}" class="ib-close" >Close</a>
                        <h1>{{ $contestdetails[0]['contest_name']}}</h1>
                        <div class="cont_det_con">

                            <div class="cont_des_con">

                                {{ isset($Massage)?"<p style='color:red;text-align:center'>".$Massage."</p>":""}}

                                <strong>Contest Comment</strong>
                                <input type="hidden" class="contest_participant_id" value="<?php echo $viewcommentforparticipant; ?>" >

    <?php
    $commentcnt = commentModel::select('comments.id as comment_id', 'comments.userid as commentuserid', 'comments.comment', 'user.profilepicture', 'user.firstname', 'user.lastname', 'user.username')->LeftJoin('user', 'user.ID', '=', 'comments.userid')->where('contest_participant_id', $viewcommentforparticipant)->get();


    for ($i = 0; $i < count($commentcnt); $i++) {
        ?>

                                    <table width="98%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="left">
                                                <div>
                                                    <textarea  id="cmt" name="cmt" value="" class="full_cmt rud_gry" disabled>{{ $commentcnt[$i]['comment'] }}</textarea>
                                                    <input type="button" value="Reply" class="cmt_btn reply_div" id="showdiv_<?php echo $i; ?>"   >
                                                </div>
                                            </td>
                                            <td width="1%"></td>
                                            <td align="center" width="70" style="background:url(img/reply_left.png) no-repeat top left; min-width:70px;"><img src="{{ URL::to('/public/assets/upload/profile/'.$commentcnt[$i]['profilepicture']) }}" width="50" height="50" class="roundedimg brd_grn"><br>
                                                <div class="cmt_uname radius"><?php
        if ($commentcnt[$i]['firstname'] == '') {
            echo $commentcnt[$i]['username'];
        } else {
            echo $commentcnt[$i]['firstname'] . ' ' . $commentcnt[$i]['lastname'];
        }
        ?></div></td>
                                        </tr>
                                    </table>
                                    <!--- Reply Comments ---->
                                    <div id="replydiv_<?php echo $i; ?>" style="display:none;"  >
                                        <input type="hidden" id="replycmtid_<?php echo $i; ?>" value=<?php echo $commentcnt[$i]['comment_id']; ?> >
                                        <textarea  id="cmt_<?php echo $i; ?>" name="cmt" value="" class="full_cmt" placeholder="Reply for the comment"></textarea>

                                        <input type="button" value="Submit" id="replycmt_<?php echo $i; ?>" class="cmt_btn submit replycmtsub"  >
                                    </div>
        <?php
        $replycmtcnt = replycommentModel::select('user.profilepicture', 'user.firstname', 'user.lastname', 'user.username', 'replycomment.replycomment')->where('comment_id', $commentcnt[$i]['comment_id'])->LeftJoin('user', 'user.ID', '=', 'replycomment.user_id')->get()->count();

        $replycmt = replycommentModel::select('user.profilepicture', 'user.firstname', 'user.lastname', 'user.username', 'replycomment.replycomment')->where('comment_id', $commentcnt[$i]['comment_id'])->LeftJoin('user', 'user.ID', '=', 'replycomment.user_id')->get();

        if ($replycmtcnt) {
            for ($j = 0; $j < count($replycmt); $j++) {
                ?>


                                            <table width="98%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="70" align="center" style="background:url(img/reply_right.png) no-repeat top right; min-width:70px;"><img src="{{ URL::to('/public/assets/upload/profile/'.$replycmt[$j]['profilepicture']) }}" width="50" height="50" class="roundedimg brd_grn"><br>
                                                        <div class="cmt_uname radius"><?php
                                    if ($replycmt[$j]['firstname'] == '') {
                                        echo $replycmt[$j]['username'];
                                    } else {
                                        echo $replycmt[$j]['firstname'] . ' ' . $replycmt[$j]['lastname'];
                                    }
                                    ?></div></td>
                                                    <td width="1%"></td>
                                                    <td valign="middle">
                                                        <div>
                                                            <textarea  id="cmt" name="cmt" value="" disabled class="full_cmt reply_gry">{{ $replycmt[$j]['replycomment'] }}</textarea>
                                                            <input type="button" value="Reply" class="cmt_btn reply_div_forreply" id="replyreply_<?php echo $i; ?>" onClick="show_reply()">
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table> 

                                                <?php
                                                }
                                            }
                                        }
                                        ?>
                            </div>              
                            <div class="clearfix"></div>
                            <div>
                                <textarea  id="comment" name="cmt" value="" class="full_cmt" placeholder="Enter your comment"></textarea>
                                <input type="button" value="Submit" class="cmt_btn cmt_btn_save submit"  onclick="putcomment(<?php echo $viewcommentforparticipant; ?>)" >
                            </div>

                            <div class="clearfix"></div>

                        </div></div>
                            <?php } ?>
            </div>

            <div id="tab-body-5" class="tab-body">
<?php
$contestdetails = contestModel::select('contest_name', 'conteststartdate', 'contestenddate', 'votingstartdate', 'votingenddate', 'prize', 'noofparticipant', 'themephoto', 'contesttype')->where('ID', $contest_id)->get();
?>

                <div class="cont_img_con radius">
                    <img src="<?php echo url(); ?>/public/assets/upload/contest_theme_photo/<?php echo $contestdetails[0]['themephoto']; ?>" class="cont_img">
                    <div class="cont_list">
                        <div class="name"><strong><span class="pch_contestname"> Contest Name: </span> </strong><div class="mb_brk"></div><span class="mleft">{{ $contestdetails[0]['contest_name']}}</span></div>
                        <div class="cstart"><strong><span class="conteststart">Contest Start Date:</span> </strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails[0]['conteststartdate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }}</span></div>
                        <div class="cend"><strong><span class="contestend"> Contest End Date: </span></strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails[0]['contestenddate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }} </span></div>
                        <div class="vstart"><strong><span class="votingstart"> Voting start Date:</span> </strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails[0]['votingstartdate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }} </span></div>
                        <div class="vend"><strong><span class="votingend"> Voting End Date:</span> </strong><div class="mb_brk"></div><span class="mleft">{{ (timezoneModel::convert($contestdetails[0]['votingenddate'],'UTC',Auth::user()->timezone, 'd-M-Y h:i a')) }} </span></div>
                        <!--<div class="prize"><strong><span class="txt_prize"> Prize: </span></strong><div class="mb_brk"></div><span class="mleft">{{ $contestdetails[0]['prize']}}</span></div>-->
                        <div class="noparty"><strong><span class="txt_noofparticipant">No. of Participants: </span> </strong><div class="mb_brk"></div><span class="mleft">{{ ($contestdetails[0]['noofparticipant']==0)?"Unlimited":$contestdetails[0]['noofparticipant']; }} </span></div>
                    </div>
                </div>
                <h1>{{ $contestdetails[0]['contest_name']}} - Leader Board</h1>
                <div class="cont_det_con">

                    <div class="cont_des_con">
                    </div>

                    <div class="clearfix"></div>

                            <?php
                            $leaderboarddata = leaderboardModel::select('user.ID as userid', 'user.username', 'user.firstname', 'user.lastname', 'user.profilepicture', 'leaderboard.votes', 'leaderboard.user_id as leaderusrid', 'leaderboard.position')->LeftJoin('user', 'user.ID', '=', 'leaderboard.user_id')->where('contest_id', $contest_id)->orderby('position')->take(20)->get();

                            if (count($leaderboarddata) == 0) {
                                echo "<div class='centertext'>Leader Board will be generated Soon.....</div>";
                            } else {
                                ?>


                        <table class="display" cellspacing="0" width="100%" id="dd_leader_board">
                            <thead>
                                <tr>
                                    <th><span class="txt_rank">Rank</span></th>
                                    <th><?php if ($contestdetails[0]['contesttype'] == 'p') { ?><span class="txt_img">Image</span><?php
                                } else if ($contestdetails[0]['contesttype'] == 'v') {
                                    echo "Video";
                                } else {
                                    echo "Topic";
                                }
                                ?>  </th>
                                    <th><span class="txt_contestantname">Contestant Name</span></th>
                                    <th><span class="txt_noofvotes">No of Dings</span></th>
                                    <th><span class="txt_view">View</span></th>
                                </tr>
                            </thead>
                            <tbody>
    <?php
    for ($i = 0; $i < count($leaderboarddata); $i++) {

        // $leaderboarddata[$i]['leaderusrid'];

        $participantcnt = contestparticipantModel::where('user_id', $leaderboarddata[$i]['leaderusrid'])->get()->count();
        if ($participantcnt != 0) {

            $contestparticipatedimg = contestparticipantModel::select('uploadfile', 'uploadtopic', 'dropbox_path')->where('contest_id', $contest_id)->where('user_id', $leaderboarddata[$i]['leaderusrid'])->first();
            ?>
                                        <tr>
                                            <td>{{ $leaderboarddata[$i]['position'] }}</td>
                                            <td align="center">
            <?php if ($contestdetails[0]['contesttype'] == 'p') { ?>
                                                    <img src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $contestparticipatedimg->uploadfile; ?>" width="50" height="50">
            <?php } else if ($contestdetails[0]['contesttype'] == 'v') {
                ?>


                                                    <video style="width:80px; height:60px;"  >											
                                                        <source src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $contestparticipatedimg->uploadfile; ?>" type="video/mp4">								
                                                    </video>

            <?php } else { ?>

                                                    <div class="blacktxtbox"><?php echo substr(($contestparticipatedimg->uploadtopic), 0, 50) . "...."; ?></div>

            <?php }
            ?>
                                            </td>
                                            <td class="tr_wid_id"><?php if ($leaderboarddata[$i]['firstname'] != "")
                echo $leaderboarddata[$i]['firstname'] . ' ' . $leaderboarddata[$i]['lastname'];
            else
                echo $leaderboarddata[$i]['username'];
            ?></td>
                                            <td>{{ $leaderboarddata[$i]['votes'] }}</td>
                                            <td><a href="{{ URL::to('other_profile/'.$leaderboarddata[$i]['userid']) }}" class="view-link"></a></td>
                                        </tr>
        <?php }
    }
    ?>

                            </tbody>
                        </table> <?php } ?>
                </div>
            </div>

            <div id="tab-body-6" class="tab-body">

            </div>
            <div id="tab-body-7" class="tab-body">
                <div class="sharebox fullwidth">
                    <h1>Share the Contest with</h1>
                    <br>
                    <center>

                        <style>
                            .stButton .stLarge {
                                display: inline-block;
                                width: 300px;
                                height:35px;
                                position: relative;
                                color:#fff;
                                font-size:16px;
                                padding:10px;
                                text-align :left;
                                -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;
                            }
                            .st_facebook_large .stButton .stLarge {
                                background: #314984 url(../assets/inner/img/share_facebook.png) no-repeat 5px -2px !important;
                            }

                            .st_facebook_large .stButton .stLarge:before {
                                content:'Facebook';
                                margin-left:30px;
                            }

                            .st_email_large .stButton .stLarge {
                                background: #9BA2AA url(../assets/inner/img/share_email.png) no-repeat 5px -2px !important;
                            }
                            .st_email_large .stButton .stLarge:before {
                                content:'Email';
                                margin-left:30px;
                            }

                            .st_twitter_large .stButton .stLarge {
                                background: #00ACED url(../assets/inner/img/share_twitter.png) no-repeat 5px -2px !important;
                            }
                            .st_twitter_large .stButton .stLarge:before {
                                content:'Twitter';
                                margin-left:30px;
                            }

                            .st_linkedin_large .stButton .stLarge {
                                background: #007BB6 url(../assets/inner/img/share_linkedin.png) no-repeat 5px -2px !important;
                            }
                            .st_linkedin_large .stButton .stLarge:before {
                                content:'LinkedIn';
                                margin-left:30px;
                            }
                            .st_tumblr_large .stButton .stLarge {
                                background: #3E5976 url(../assets/inner/img/share_tumblr.png) no-repeat 5px -2px !important;
                            }
                            .st_tumblr_large .stButton .stLarge:before {
                                content:'Tumblr';
                                margin-left:30px;
                            }
                            .st_googleplus_large .stButton .stLarge {
                                background: #DE4939 url(../assets/inner/img/share_googleplus.png) no-repeat 5px -2px !important;
                            }
                            .st_googleplus_large .stButton .stLarge:before {
                                content:'Google+';
                                margin-left:30px;
                            }
                            .st_instagram_large .stButton .stLarge {
                                background: #517fa4 url(../assets/inner/img/instagram_icons.png) no-repeat 5px -2px !important;
                            }
                            .st_instagram_large .stButton .stLarge:before {
                                content:'Instagram';
                                margin-left:30px;
                            }
                            .st_pinterest_large .stButton .stLarge {
                                background: #cb2027 url(../assets/inner/img/pinterest.jpeg) no-repeat 5px -2px !important;
                            }
                            .st_pinterest_large .stButton .stLarge:before {
                                content:'pinterest';
                                margin-left:30px;
                            }



                        </style>
<?php
$contestdetails = contestModel::where('ID', $contest_id)->first();
$desc_msg = "Contest Name : " . $contestdetails['contest_name'] . "
Contest Type : " . (($contestdetails['contesttype'] == 'p') ? 'Photo' : (($contestdetails['contesttype'] == 'v') ? 'Video' : (($contestdetails['contesttype'] == 't') ? 'Topic' : ''))) . "
Organised by: " . (($owner_contest['firstname'] != '') ? ($owner_contest['firstname'] . ' ' . $owner_contest['lastname']) : $owner_contest['username']) . "
";
if ($curdate < $contestdetails['votingenddate']) {
    ?>
                            <meta property="og:title" content="DingDatt - Invitation for join the contest"/>
                            <meta property="og:type" content="website"/>
                            <meta property="og:url" content="{{ Request::url() }}"/>
                            <meta property="og:image" content="{{ URL::to('public/assets/upload/contest_theme_photo/'.$contestdetails['themephoto'])}}"/>
                            <meta property="og:description" content="{{ $desc_msg }}"/>
<?php }
?>
                        <p><span class='st_facebook_large' displayText='Facebook' st_msg="{{ $desc_msg }}" st_summary="{{ $desc_msg }}"></span></p><br>
                        <p><span class='st_twitter_large' st_via="" st_msg="{{ $desc_msg }}" displayText='Tweet'></span></p><br>
                        <p><span class='st_tumblr_large' displayText='Tumblr' st_msg="{{ $desc_msg }}"></span></p><br>
                        <p><span class='st_googleplus_large' displayText=''  st_msg="{{ $desc_msg }}" st_summary="{{ $desc_msg }}"></span></p><br>
                        <p><span class='st_email_large' st_via="" displayText='Email'></span></p><br>
                        <p><span class='st_instagram_large' displayText='Instagram'></span></p><br>
                        <p><span class='st_pinterest_large' displayText='pinterest'></span></p>

                    </center>
                </div>
            </div>

            <div id="tab-body-9" class="tab-body">

            </div>
        </div>
    </div>

    <div class="clrscr"></div>


    <script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery.tmpl.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery.kinetic.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery.easing.1.3.js') }}"></script>
    <script type="text/javascript">
$(function() {
	var $ibWrapper = $('#ib-main-wrapper'),
	Template = (function() {

	// true if dragging the container
	var kinetic_moving = false,
	// current index of the opened item
	current = - 1,
	// true if the item is being opened / closed
	isAnimating = false,
	// items on the grid
	//$ibItems					= $ibWrapper.find('div.ib-main > a'),

	$ibItems = $ibWrapper.find('div.thumbnail > a'),
	// image items on the grid
	//$ibImgItems					= $ibItems.not('.ib-content'),
	$ibImgItems = $ibItems.not('.ib-content'),
	// total image items on the grid
	imgItemsCount = $ibImgItems.length,
	//// Contentcount 
	contentItemsCount = $ibWrapper.find('div.ib-main').find('a.ib-content').length,
	//// Videocount //////
	videoItemsCount = $ibWrapper.find('div.ib-main').find('a.ib-video').length,
	currentselecteditem = 0,
	admin = '<?php 	if (Auth::user()->ID == 1) {  echo 1; } else { 	echo 0; } ?>',
	init = function() {
		// add a class ib-image to the image items
		$ibImgItems.addClass('ib-image');
		// apply the kinetic plugin to the wrapper
		loadKinetic();
		// load some events
		initEvents();
		$('.ib-nav').addClass('ib-nav1').removeClass('ib-nav');
	},
	loadKinetic = function() {
		setWrapperSize();
		$ibWrapper.kinetic({
			moved	: function() {
				kinetic_moving = true;
			},
			stopped	: function() {
				kinetic_moving = false;
			}
		});
	},
	setWrapperSize = function() {
		var containerMargins = $('#ib-top').outerHeight(true) + $('#header').outerHeight(true) + parseFloat($ibItems.css('margin-top'));
		$ibWrapper.css('height', $(window).height() - containerMargins)
	},
	initEvents = function() {
		// open the item only if not dragging the container
		$ibItems.bind('click.ibTemplate', function(event) {
			if (!kinetic_moving)
			openItem($(this));
			return false;
		});
		// on window resize, set the wrapper and preview size accordingly
		$(window).bind('resize.ibTemplate', function(event) {
			setWrapperSize();
			$('#ib-img-preview, #ib-content-preview').css({
			width	: $(window).width(),
			height	: $(window).height()
			})
		});
	},
	openItem = function($item) {
		
		if (isAnimating) return false;
		// if content item
			if ($item.hasClass('ib-content')) {

				isAnimating = true;
				current = $item.index('.ib-content');
				loadContentItem($item, function() { isAnimating = false; });
			}
			else if ($item.hasClass('ib-video'))
			{
				isAnimating = true;
				current = $item.index('.ib-video');
				loadvideopreview($item, function() { isAnimating = false; });
			}
			// if image item
			else {
				isAnimating = true;
				current = $item.index('.ib-image');
				loadImgPreview($item, function() { isAnimating = false; });
			}

	},
	// opens one image item (fullscreen)
	loadImgPreview = function($item, callback) {

		$('.contest_subpage').css('height', '713px');
		var largeSrc = $item.children('img').data('largesrc'),
		description = $item.children('span').text(),
		contestid = $item.children('div').text(),
		largeImageData = {
			src			: largeSrc,
			description	: description,
			contestid : contestid,
		};
		//$('.ib-nav span').css('background','../images/nav_gallery.png');

		// preload large image
		$item.addClass('ib-loading');
		preloadImage(largeSrc, function() {

			$item.removeClass('ib-loading');
			var hasImgPreview = ($('#ib-img-preview').length > 0);
			if (!hasImgPreview)
			$('#previewTmpl').tmpl(largeImageData).insertAfter($ibWrapper);
			else
			$('#ib-img-preview').children('img.ib-preview-img')
			.attr('src', largeSrc)
			.end().find('div.ib-preview-flag').end()
			.find('span.ib-preview-descr')
			.html("<span style='font-size:14px;'>Posted by</span></br>" + description).end()
			.find('div.participant_id').text(contestid);
			//get dimentions for the image, based on the windows size
			var dim = getImageDim(largeSrc);
			$item.removeClass('ib-img-loading');
			//set the returned values and show/animate preview
			$('#ib-img-preview').css({
			width	: $item.width(),
			height	: $item.height(),
			left	: $item.offset().left,
			top		: $item.offset().top
			}).children('img.ib-preview-img').hide().css({
			width	: dim.width,
			height	: dim.height,
			left	: dim.left,
			top		: dim.top
			}).fadeIn(400).end().show().animate({
			width	: $(window).width(),
			left	: 0
			}, 500, 'easeOutExpo', function() {

				$(this).animate({
				height	: $(window).height(),
				top		: 0
				}, 400, function() {

					var $this = $(this);
					$this.find('span.ib-preview-descr, span.ib-close,div.ib-preview-flag').show();
					$this.find('div.participant_id').css('visibility', 'hidden');
					//if( imgItemsCount > 1 )
					var arrowshow = '<?php
					if ($curdate <= $contestdetails['votingstartdate']) {
					echo 0;
					} else
					echo 1;
					?>';
					if (arrowshow == 1 || admin == 1)
					$this.find('div.ib-nav').show();
					if (callback) callback.call();
				});
			});
			if (!hasImgPreview)
			initImgPreviewEvents();
		});
	},
loadvideopreview = function($item, callback){

	$('.contest_subpage').css('height', '713px');
	var largeSrc = $item.children('video')[0]['currentSrc'],
	contestid = $item.children('div.participant_id').text(),
	participant_detail = $item.children('span.participant_detail').text(),
	largevideoData = {
		src			: largeSrc,
		contestid : contestid,
		participant_detail : participant_detail,
	};
	preloadvideo(largeSrc, function() {  console.log(largeSrc);
	var hasvideoPreview = ($('#ib-video-preview').length > 0);
	if (!hasvideoPreview)
	$('#previewTmplvideo').tmpl(largevideoData).insertAfter($ibWrapper);
	else
	$('#ib-video-preview').children('video.ib-preview-video').prop("f", true).children('source')
	.attr('src', largeSrc)
	.end().find('div.ib-preview-flag').end()
	.find('div.participant_id').text(participant_detail).find('span.participant_detail').text(contestid);
	//get dimentions for the image, based on the windows size
	var dim = getImageDim(largeSrc);
	$item.removeClass('ib-img-loading');
	//set the returned values and show/animate preview
	$('#ib-video-preview').css({
	width	: $item.width(),
	height	: $item.height(),
	left	: $item.offset().left,
	top		: $item.offset().top
	}).children('video.ib-preview-video').hide().css({
	//	width	: 700,
	//	height	: 500,
	//	left	: dim.left,
	//	top		: dim.top
	}).fadeIn(400).end().show().animate({
	//	width	: 700,
	//	height :430,
	left	: 0
	}, 500, 'easeOutExpo', function() {

	$(this).animate({
	height	: $(window).height(),
	top		: 0
	}, 400, function() {

	var $this = $(this);
	$this.find('span.ib-close,div.participant_id,span.participant_detail,div.ib-preview-flag,span.ib-preview-descr').show();
	//	$this.find('span.participant_detail').css('visibility', 'hidden');
	if (videoItemsCount > 1){
	var arrowshow = '<?php
	if ($curdate <= $contestdetails['votingstartdate']) {
	echo 0;
	} else
	echo 1;
	?>';
	if (arrowshow == 1 || admin == 1)
	$this.find('div.ib-nav').show();
	}
	if (callback) callback.call();
	});
	});
	if (!hasvideoPreview)
	initvideoPreviewEvents();
	});
},
// opens one content item (fullscreen)
loadContentItem = function($item, callback) {

	$('.contest_subpage').css('height', '713px');
	$('.hideinfullscreen').hide();
	var hasContentPreview = ($('#ib-content-preview').length > 0),
	teaser = $item.children('div.ib-teaser').html(),
	content = $item.children('div.ib-content-full').html(),
	contestid = $item.children('div.participant_id').text(),
	participant_name = $item.children('span.ib-preview-descr').text(),
	participant_detail = $item.children('span.participant_detail').text(),
	contentData = {
	teaser		: teaser,
	content		: content,
	contestid   : contestid,
	participant_name : participant_name,
	participant_detail : participant_detail
	};

	if (!hasContentPreview)
	$('#contentTmpl').tmpl(contentData).insertAfter($ibWrapper);
	//set the returned values and show/animate preview
	$('#ib-content-preview').css({
	width	: $item.width(),
	height	: $item.height(),
	left	: $item.offset().left,
	top		: $item.offset().top
	}).show().animate({
	width	: $(window).width(),
	left	: 0
	}, 500, 'easeOutExpo', function() {

	$(this).animate({
	height	: $(window).height(),
	top		: 0
	}, 400, function() {


	var $this = $(this),
	$teaser = $this.find('div.ib-teaser'),
	$content = $this.find('div.ib-content-full'),
	$close = $this.find('span.ib-close'),
	$contestid = $this.find('div.participant_id'), //alert($('#ib-content-preview').length);
	$participant_name = $this.find('span.ib-preview-descr');
	$participant_detail = $this.find('span.participant_detail');
	//console.log($participant_detail);

	if ($('#ib-content-preview').length > 1)
	$this.find('div.participant_id').css('visibility', 'hidden');
	////	
	//if( hasContentPreview ) {
	$teaser.html(teaser)
	$content.html(content)
	$contestid.html(contestid)
	$participant_name.html(participant_name)
	$participant_detail.html("<span style='font-size:14px;'>Posted by</span></br>" + participant_detail);

	//}

	$('.defaulthide').show();
	$teaser.show();
	$content.show();
	$close.show();
	$contestid.show();
	$participant_name.show();
	$participant_detail.show();
	$this.find('div.participant_id').css('visibility', 'hidden');
	var arrowshow = '<?php
	if ($curdate <= $contestdetails['votingstartdate']) {
	echo 0;
	} else
	echo 1;
	?>';
	if (arrowshow == 1 || admin == 1)
	$this.find('div.ib-nav').show();
	if (callback) callback.call();
	});
	});
	if (!hasContentPreview)
	initContentPreviewEvents();
},
// preloads as video 
	preloadvideo = function(src, callback){

		console.log(callback.call());
		console.log(videoItemsCount);
		$('<video>').load(function(){

		if (callback) callback.call();
		}).attr('src', src);
	},
	// preloads an image
	preloadImage = function(src, callback) { 
		$('<img/>').load(function(){
			if (callback) callback.call();
		}).attr('src', src);
	},
// load the events for the image preview : navigation ,close button, and window resize
	initImgPreviewEvents = function() {

		var $preview = $('#ib-img-preview');
		$preview.find('span.ib-nav-prev').bind('click.ibTemplate', function(event) {

		navigate('next'); //prev

		}).end().find('span.ib-nav-next').bind('click.ibTemplate', function(event) {

		navigate('next');
		}).end().find('span.ib-nav-pass').bind('click.ibTemplate', function(event) {

		navigate('next');
		}).end().find('span.ib-close').bind('click.ibTemplate', function(event) {

		$('.hideinfullscreen').show();
		closeImgPreview();
		});
		//resizing the window resizes the preview image
		$(window).bind('resize.ibTemplate', function(event) {

		var $largeImg = $preview.children('img.ib-preview-img'),
		dim = getImageDim($largeImg.attr('src'));
		$largeImg.css({
		width	: dim.width,
		height	: dim.height,
		left	: dim.left,
		top		: dim.top
		})

		});
	},
// load the events for the content preview : close button
	initContentPreviewEvents = function() {

		var $preview = $('#ib-content-preview');
		$preview.find('span.ib-nav-prev').bind('click.ibTemplate', function(event) {

		navigatecontent('next'); //prev

		}).end().find('span.ib-nav-next').bind('click.ibTemplate', function(event) {

		navigatecontent('next');
		}).end().find('span.ib-nav-pass').bind('click.ibTemplate', function(event) {

		navigatecontent('next');
		}).end().find('span.ib-close').bind('click.ibTemplate', function(event) {

		$('.hideinfullscreen').show();
		closeContentPreview();
		});
	},
	initvideoPreviewEvents = function() {

	var $preview = $('#ib-video-preview');
	$preview.find('span.ib-nav-prev').bind('click.ibTemplate', function(event) {

	navigatevideo('next'); //prev

	}).end().find('span.ib-nav-next').bind('click.ibTemplate', function(event) {

	navigatevideo('next');
	}).end().find('span.ib-nav-pass').bind('click.ibTemplate', function(event) {

	navigatevideo('next');
	}).end().find('span.ib-close').bind('click.ibTemplate', function(event) {

	//$('.hideinfullscreen').show();

	closevideoPreview();
	});
	//resizing the window resizes the preview image
	/*$(window).bind('resize.ibTemplate', function( event ) {

	var $largeImg	= $preview.children('img.ib-preview-img'),
	dim			= getImageDim( $largeImg.attr('src') );

	$largeImg.css({
	width	: dim.width,
	height	: dim.height,
	left	: dim.left,
	top		: dim.top
	})

	}); */

	},
// navigate the image items in fullscreen mode
navigate = function(dir) {

//console.log(currentselecteditem); console.log(imgItemsCount);

if (imgItemsCount - 1 == currentselecteditem) { if (admin == 0) location.reload(); }
if (isAnimating) return false;
isAnimating = true;
var $preview = $('#ib-img-preview'),
$loading = $preview.find('div.ib-loading-large');
$loading.show();
if (dir === 'next') {

currentselecteditem++;
(current === imgItemsCount - 1) ? current = 0 : ++current;
}
else if (dir === 'prev') {

(current === 0) ? current = imgItemsCount - 1 : --current;
}



var $item = $ibImgItems.eq(current),
largeSrc = $item.children('img').data('largesrc'),
description = $item.children('span').text();
contestid = $item.children('div').text();
//if(imgItemsCount-1==current) { 

preloadImage(largeSrc, function() {

$loading.hide();
//get dimentions for the image, based on the windows size
var dim = getImageDim(largeSrc);
$preview.children('img.ib-preview-img')
.attr('src', largeSrc)
.css({
width	: dim.width,
height	: dim.height,
left	: dim.left,
top		: dim.top
})
.end()
.find('span.ib-preview-descr')
.html('<span style="font-size:14px;">Posted by</span></br>' + description).end()
.find('div.participant_id').text(contestid);
$ibWrapper.scrollTop($item.offset().top)
.scrollLeft($item.offset().left);
isAnimating = false;
});
//}

},
/// Content navigate /////
navigatecontent = function(dir) {

if (isAnimating) return false;
isAnimating = true;
var $preview = $('#ib-content-preview'),
$loading = $preview.find('div.ib-loading-large');
//$description	= $preview.find('div.participant_detail');

$loading.show();
if (dir === 'next') {

if (contentItemsCount - 1 == currentselecteditem) { if (admin == 0) location.reload(); }
currentselecteditem++;
(current === contentItemsCount - 1) ? current = 0 : ++current;
}
else if (dir === 'prev') {

(current === 0) ? current = contentItemsCount - 1 : --current;
}

var $item = $ibItems.eq(current),
//largeSrc	= $item.children('img').data('largesrc'),
description = $item.children('div.ib-content-full').text(); //console.log(description);
contestid = $item.children('div.participant_id').text();
participant_name = $item.children('span.participant_detail').text();
$preview.find('div.ib-content-full')
.html(nl2br(description)).end().find('span.ib-preview-descr').html('<span style="font-size:14px;">Posted by</span></br>' + participant_name).end()
.find('div.participant_id').text(contestid);
$ibWrapper.scrollTop($item.offset().top)
.scrollLeft($item.offset().left);
isAnimating = false;
$loading.hide();
},
//navigatevideo
navigatevideo = function(dir) {


//if( isAnimating ) return false;

isAnimating = true;
var $preview = $('#ib-video-preview'),
$loading = $preview.find('div.ib-loading-large');
console.log(videoItemsCount);
//$loading.show(); 

if (dir === 'next') {

console.log(currentselecteditem);
if (videoItemsCount - 1 == currentselecteditem) { if (admin == 0) location.reload(); }
//if(videoItemsCount-1==current) { location.reload(); }
(current === videoItemsCount - 1) ? current = 0 : ++current;
currentselecteditem++;
}
else if (dir === 'prev') {

(current === 0) ? current = videoItemsCount - 1 : --current;
}

var $item = $ibImgItems.eq(current),
largeSrc = $item.children('video')[0]['currentSrc'],
contestid = $item.children('div.participant_id').text();
description = $item.children('span.participant_detail').text();
/*if(videoItemsCount-1!=currentselecteditem)
{ */

preloadvideo(largeSrc, function() {

//$loading.hide();

//get dimentions for the image, based on the windows size
//var	dim	= getImageDim( largeSrc );

$preview.children('video.ib-preview-video')
.attr('src', largeSrc).prop("controls", true)
.css({
//width	: 700,
//height	: ,
//left	: dim.left,
//top		: dim.top
})
.end().find('span.ib-preview-descr').html('<span style="font-size:14px;">Posted by</br></span>' + description).end()
.find('div.participant_id').text(contestid);
$ibWrapper.scrollTop($item.offset().top)
.scrollLeft($item.offset().left);
isAnimating = false;
});
//}
},
//////

// closes the fullscreen image item
closeImgPreview = function() {

if (isAnimating) return false;
isAnimating = true;
var $item = $ibImgItems.eq(current);
$('#ib-img-preview').find('span.ib-preview-descr, div.ib-nav, span.ib-close,div.participant_id')
.hide()
.end()
.animate({
height	: $item.height(),
top		: $item.offset().top
}, 500, 'easeOutExpo', function() {

$(this).animate({
width	: $item.width(),
left	: $item.offset().left
}, 400, function() {

$(this).fadeOut(function() {isAnimating = false; });
});
});
var imageheight = $('.cont_img_con img')[0].height; console.log(imageheight);
imageheight = imageheight + 250;
$('.contest_subpage').css('height', imageheight);
},
// closes the fullscreen content item
closeContentPreview = function() {

$('.defaulthide').hide();
if (isAnimating) return false;
isAnimating = true;
var $item = $ibItems.not('.ib-image').eq(current);
$('#ib-content-preview').find('div.ib-teaser, div.ib-content-full, span.ib-close')
.hide()
.end()
.animate({
height	: $item.height(),
top		: $item.offset().top
}, 500, 'easeOutExpo', function() {

$(this).animate({
width	: $item.width(),
left	: $item.offset().left
}, 400, function() {

$(this).fadeOut(function() {isAnimating = false; });
});
});
var imageheight = $('.cont_img_con img')[0].height; console.log(imageheight);
imageheight = imageheight + 250;
$('.contest_subpage').css('height', imageheight);
},
closevideoPreview = function() {

if (isAnimating) return false;
isAnimating = true;
var $item = $ibImgItems.eq(current);
$('#ib-video-preview').find('div.ib-nav, span.ib-close,div.participant_id')
.hide()
.end()
.animate({
height	: $item.height(),
top		: $item.offset().top
}, 500, 'easeOutExpo', function() {

$(this).animate({
width	: $item.width(),
left	: $item.offset().left
}, 400, function() {

$(this).fadeOut(function() {isAnimating = false; });
});
});
var imageheight = $('.cont_img_con img')[0].height; console.log(imageheight);
imageheight = imageheight + 250;
$('.contest_subpage').css('height', imageheight);
},
// get the size of one image to make it full size and centered
getImageDim = function(src) {

var img = new Image();
img.src = src;
var w_w = $(window).width(),
w_h = $(window).height(),
r_w = w_h / w_w,
i_w = img.width,
i_h = img.height,
r_i = i_h / i_w,
new_w, new_h,
new_left, new_top;
if (r_w > r_i) {

new_h = w_h;
new_w = w_h / r_i;
}
else {

new_h = w_w * r_i;
new_w = w_w;
}

return {
width	: new_w,
height	: new_h,
left	: (w_w - new_w) / 2,
top		: (w_h - new_h) / 2
};
};
return { init : init };
})();
Template.init();
});</script>

<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css')}}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js')}}"></script>
<!--<script type="text/javascript" src="js/datatbl/tbl_script.js"></script>-->
<script type="text/javascript">

function aa(row){
document.getElementById('checkgrouplist_' + row).checked = true;
var contest_id = '<?php echo $contest_id; ?>';
var group_id = document.getElementById('checkgrouplist_' + row).value; //alert(group_id);


jQuery('.inviteall_' + group_id).html('Proccessing');
jQuery('.inviteall_' + group_id).css('color', 'blue');
var dataString = 'group_id=' + group_id + "&contest_id=" + contest_id;
$.ajax({
type: "GET",
url : '../invite_group',
data : dataString,
success : function(data){  //console.log(data);
if (data == 1)
{

$(".inviteall_" + group_id).css('color', 'red');
$(".inviteall_" + group_id).html("Invited");
$("#inv_success").html("Invitation sent successfully");
}
if (data == 0){
$("#inv_success").html("No member to Invite");
$(".inviteall_" + group_id).html("No member");
}
}
});
}

$(document).ready(function() {

jQuery('#email_content-tmce').click();
//$('#dd_group_list').dataTable({"bPaginate": false,"bSort":false});

jQuery('#dd_group_list').dataTable({
"bPaginate":true,
"sPaginationType":"full_numbers",
"iDisplayLength": 10,
"sPageButton": "paginate_button",
'aoColumnDefs': [{
'bSortable': false,
	'aTargets': [0, - 1, - 2] /* 1st one, start by the right */
}]
});
jQuery('#dd_follower_list').dataTable({
"bPaginate":true,
"sPaginationType":"full_numbers",
"iDisplayLength": 10,
"sPageButton": "paginate_button",
'aoColumnDefs': [{
'bSortable': false,
	'aTargets': [0, - 1, - 2] /* 1st one, start by the right */
}]
});
jQuery('#dd_leaderboard_list,#dd_leader_board').dataTable({
"bPaginate":true,
"sPaginationType":"full_numbers",
"iDisplayLength": 10,
"sPageButton": "paginate_button"
});
jQuery('.checkallgroups').click(function(event){
var group_list = [];
var contest_id = '<?php echo $contest_id; ?>';
if (jQuery(this).is(':checked')) {

jQuery('.checkgrouplist').prop('checked', true);
group_list.push(jQuery(this).val());
jQuery('.checkgrouplist').each(function(index, element){ if (jQuery(this).is(':checked')){
group_list.push(jQuery(this).val());
//jQuery('.invitetype_'+jQuery(this).val()).html('Proccessing');
//$(".invitetype_"+jQuery(this).val()).css('color', 'blue');

}
});
var dataString = 'group_list=' + group_list + "&contest_id=" + contest_id;
$.ajax({
type: "post",
	url : '../inviteall_group',
	data : dataString,
	success : function(data){
	console.log(data);
			/*	if(data==1)
			 {
			 $(".invitetypeall").css('color', 'red');								
			 $(".invitetypeall").html("Invited");
			 $("#inv_success").html("Invited successfully");
			 } */
	}
});
} else{

jQuery('.checkgrouplist').prop('checked', false);
}
});
//// Invite group ///

$(".paginate_button").click(function(){

jQuery('.checkgrouplist').click(function(event){

var contest_id = '<?php echo $contest_id; ?>'; //alert(contest_id);

var group_id = jQuery(this).val();
jQuery('.inviteall_' + group_id).html('Proccessing');
jQuery('.inviteall_' + group_id).css('color', 'blue');
//groupid,contest_id,checkseparate

if (jQuery(this).is(':checked')){

var dataString = 'group_id=' + group_id + "&contest_id=" + contest_id;
$.ajax({
type: "GET",
	url : '../invite_group',
	data : dataString,
	success : function(data){  //console.log(data);
	if (data == 1)
	{

	$(".inviteall_" + group_id).css('color', 'red');
			$(".inviteall_" + group_id).html("Invited");
			$("#inv_success").html("Invitation sent successfully");
	}
	if (data == 0){
	$("#inv_success").html("No member to Invite");
			$(".inviteall_" + group_id).html("No members to invite");
	}

	}
});
} else{
/// Uninvite process ////

var dataString = 'group_id=' + group_id + "&contest_id=" + contest_id;
$.ajax({
type: "GET",
	url : '../uninvite_group',
	data : dataString,
	success : function(data){  //console.log(data);
	if (data == 1)
	{

	$(".inviteall_" + group_id).css('color', 'green');
			$(".inviteall_" + group_id).html("Invite");
			$("#inv_success").html("Uninvited successfully");
	}
	if (data == 0){
	$("#inv_success").html("No member to uninvite");
			$(".inviteall_" + group_id).html("No members to uninvite");
	}
	}
});
}
});
});
jQuery('.checkgrouplist').click(function(event){

var contest_id = '<?php echo $contest_id; ?>';
var group_id = jQuery(this).val();
jQuery('.inviteall_' + group_id).html('Proccessing');
jQuery('.inviteall_' + group_id).css('color', 'blue');
//groupid,contest_id,checkseparate

if (jQuery(this).is(':checked')){

var dataString = 'group_id=' + group_id + "&contest_id=" + contest_id;
$.ajax({
type: "GET",
	url : '../invite_group',
	data : dataString,
	success : function(data){  //console.log(data);
	if (data == 1)
	{

	$(".inviteall_" + group_id).css('color', 'red');
			$(".inviteall_" + group_id).html("Invited");
			$("#inv_success").html("Invitation sent successfully");
	}
	if (data == 0){
	$("#inv_success").html("No member to Invite");
			$(".inviteall_" + group_id).html("No members to invite");
	}

	}
});
} else{
/// Uninvite process ////

var dataString = 'group_id=' + group_id + "&contest_id=" + contest_id;
$.ajax({
type: "GET",
	url : '../uninvite_group',
	data : dataString,
	success : function(data){  //console.log(data);
	if (data == 1)
	{

	$(".inviteall_" + group_id).css('color', 'green');
			$(".inviteall_" + group_id).html("Invite");
			$("#inv_success").html("Uninvited successfully");
	}
	if (data == 0){
	$("#inv_success").html("No member to uninvite");
			$(".inviteall_" + group_id).html("No members to uninvite");
	}
	}
});
}
});
//////////////////////Check all///////////////////////////////////	
jQuery('.checkallfollowers').click(function(event){

var contest_id = '<?php echo $contest_id; ?>';
var follower_list = [];
if (jQuery(this).is(':checked')) {

jQuery('.checkfollowerlist').prop('checked', true);
//	follower_list.push(jQuery(this).val()); 
jQuery('.checkfollowerlist').each(function(index, element){ if (jQuery(this).is(':checked')){
follower_list.push(jQuery(this).val());
jQuery('.inviteall').html('Proccessing');
$(".inviteall").css('color', 'blue');
}
});
var dataString = 'contest_id=' + contest_id + "&follower_list=" + follower_list;
$.ajax({
type: "GET",
	url : '../inviteall_follower',
	data : dataString,
	success : function(data){ console.log(data);
			/*	if(data==1)
			 { */

			$(".inviteall").css('color', 'red');
			$(".inviteall").html("Invited");
			$("#inv_success_folo").html("Invitation sent successfully");
			//}
	}
});
} else{

jQuery('.checkfollowerlist').prop('checked', false);
jQuery('.checkfollowerlist').each(function(index, element){ follower_list.push(jQuery(this).val()); });
var dataString = 'contest_id=' + contest_id + "&follower_list=" + follower_list;
$.ajax({
type: "GET",
	url : '../uninvite_allfollower',
	data : dataString,
	success : function(data){ console.log(data);
			if (data == 1)
	{

	$(".inviteall").css('color', 'green');
			$(".inviteall").html("Invite");
			$("#inv_success_folo").html("Uninvited successfully");
	}
	}
});
}
});
jQuery('.checkfollowerlist').click(function(event){

var contest_id = '<?php echo $contest_id; ?>';
var followerid = jQuery(this).val();
jQuery('.inviteall_' + followerid).html('Proccessing');
jQuery('.inviteall_' + followerid).css('color', 'blue');
if (jQuery(this).is(':checked')){

var dataString = 'followerid=' + followerid + "&contest_id=" + contest_id;
$.ajax({
type: "POST",
	url : '../invite_follower',
	data : dataString,
	success : function(data){
	if (data == 1)
	{
	$(".inviteall_" + followerid).css('color', 'red');
			$(".inviteall_" + followerid).html("Invited");
			$("#inv_success_folo").html("Invitation sent successfully");
	}
	}
});
} else{
var dataString = 'followerid=' + followerid + "&contest_id=" + contest_id;
$.ajax({
type: "get",
	url : '../uninvite_follower',
	data : dataString,
	success : function(data){ console.log(data);
			if (data == 1)
	{
	$(".inviteall_" + followerid).css('color', 'green');
			$(".inviteall_" + followerid).html("Invite");
			$("#inv_success_folo").html("Uninvited successfully");
	}
	}
});
}
});
/// Pagination for follower ///
$(".paginate_button").click(function(){

jQuery('.checkfollowerlist').click(function(event){

var contest_id = '<?php echo $contest_id; ?>';
var followerid = jQuery(this).val();
jQuery('.inviteall_' + followerid).html('Proccessing');
jQuery('.inviteall_' + followerid).css('color', 'blue');
if (jQuery(this).is(':checked')){

var dataString = 'followerid=' + followerid + "&contest_id=" + contest_id;
$.ajax({
type: "POST",
	url : '../invite_follower',
	data : dataString,
	success : function(data){
	if (data == 1)
	{
	$(".inviteall_" + followerid).css('color', 'red');
			$(".inviteall_" + followerid).html("Invited");
			$("#inv_success_folo").html("Invitation sent successfully");
	}
	}
});
} else{
var dataString = 'followerid=' + followerid + "&contest_id=" + contest_id;
$.ajax({
type: "get",
	url : '../uninvite_follower',
	data : dataString,
	success : function(data){ console.log(data);
			if (data == 1)
	{
	$(".inviteall_" + followerid).css('color', 'green');
			$(".inviteall_" + followerid).html("Invite");
			$("#inv_success_folo").html("Uninvited successfully");
	}
	}
});
}
});
});
jQuery('.lboxh_close').click(function(event){

jQuery('.lightbox').hide();
});
jQuery('.reportshow').click(function(event){  console.log("A");
jQuery('.lightbox').show();
});
});
function close(){

alert("A");
}

function removethis(contestparticipantid, contest_id, contest_partipant_id){
var answer = confirm("Are you sure want to remove?");
if (answer){
window.location = "<?php echo url(); ?>/takeactionforreport?contestparticipantid=" + contestparticipantid + "&contest_id=" + contest_id + "&contest_partipant_id=" + contest_partipant_id;
}
}
function showreport(type){

jQuery('.lightbox').show();
var posteduser = jQuery('.ib-preview-descr').find('span').remove();
posteduser = jQuery('.ib-preview-descr').find('br').remove();
jQuery('.lbox_postby').html('Posted by:' + jQuery('.ib-preview-descr').html());

}
function submitreport(){
var reporteddata = jQuery('#report_tarea').val();
var participantid = jQuery('.participant_id').html();
var dataString = 'reporteddata=' + reporteddata + "&participantid=" + participantid;
$.ajax({
type: "get",
	url : '../report',
	data : dataString,
	success : function(data){ console.log(data);
			if (data == 1)
	{

jQuery('.lightbox').hide();
		jQuery('#report_tarea').val('');
}
}
});
}

    </script>

    <div class="lightbox" id="img1" onClick="close()">
        <div class="lightbox_con">
            <div class="lbox_head">
                <div class="lboxh_title">Report <?php
if ($contestdetails['contesttype'] == 'p') {
    echo "photo";
} elseif ($contestdetails['contesttype'] == 'v') {
    echo "video";
} else {
    echo "topic";
}
?></div>
                <div class="lboxh_close close" style="cursor:pointer;" ><span  >X</span></div>
            </div>
            <div class="lbox_main">
                <div class="lboxm_lft">
                    <div class="lbox_postby">Posted by: Madhu</div>
                </div>
                <div class="lboxm_rgt">
                    <h1>Why do you want to report this <?php
if ($contestdetails['contesttype'] == 'p') {
    echo "photo";
} elseif ($contestdetails['contesttype'] == 'v') {
    echo "video";
} else {
    echo "topic";
}
?>?</h1>
                    <textarea id="report_tarea"></textarea>
                    <div style="clear:both"></div><button class="submitreport" onclick="submitreport()">submit</button>
                </div>
            </div>
        </div>
    </div>		

    <!--- For Image Preview -----> 
    <script id="previewTmpl" type="text/x-jquery-tmpl">
        <div id="ib-img-preview" class="ib-preview">
        <img src="${src}" alt="" class="ib-preview-img"/>


        <?php /* <a href="#"  class="ib-preview-descr-bottom" data-tooltip="Report Photo"><?php if(Auth::user()->ID!=1){ ?><span class="ib-preview-descr reportshow" data-tooltip="Report Photo" onclick="showreport('p')" style="display:none;">${description}</span><?php } ?>
          </a> */ ?>

<?php if (Auth::user()->ID != 1) { ?><div class="ib-preview-flag" data-tooltip="Report Photo"  onclick="showreport('p')"  >&nbsp;</div><?php } ?>

        <span class="ib-preview-descr reportshow"  style="display:none;"><span style="font-size:14px;">Posted by</span> </br>${description}</span>				
        <div class="participant_id" style="display:none;">${contestid}</div>


        <div class="ib-nav" style="display:none;">				

        <!-- For Gallery -->
<?php if (Auth::user()->ID == 1) { ?>
            <span class="ib-nav-prev"  >Previous</span>
            <span class="ib-nav-next" >Next</span>
<?php } else { ?>
            <!-- It is for Voting Icon -->					
            <span class="ib-nav-prev" onclick="voting('dislike','p')" >Previous</span>
            <span class="ib-nav-next" onclick="voting('like','p')">Next</span>
            <span class="ib-nav-pass" onclick="voting('pass','p')">Pass</span>
<?php } ?>
        </div>
        <span class="ib-close" style="display:none;">Close Preview</span>
        <div class="ib-loading-large" style="display:none;">Loading...</div>
        </div>		
    </script>
    <!--- For Topic Preview ---->
    <script id="contentTmpl" type="text/x-jquery-tmpl">
        <div id="ib-content-preview" class="ib-content-preview">

<?php /* <a href="#"  class="ib-preview-descr-bottom" data-tooltip="Report Topic"><?php if(Auth::user()->ID!=1){ ?><span class="ib-preview-descr reportshow participant_detail" data-tooltip="Report Topic" onclick="showreport('t')" style="display:none;">${participant_detail}</span><?php } ?>
  </a> */ ?>

        <div class="ib-teaser" style="display:none;">${teaser}</div>

<?php if (Auth::user()->ID != 1) { ?><div class="ib-preview-flag" data-tooltip="Report Topic"  onclick="showreport('t')"  >&nbsp;</div><?php } ?>

        <span class="ib-preview-descr reportshow participant_detail"  style="display:none;">${participant_detail}</span>				


        <div class="ib-content-full" style="display:none; " >${content}</div>
        <div class="participant_id" style="display:none;">${contestid}</div>

        <div class="ib-nav" style="display:none;">				
        <!-- It is for Voting Icon -->	
<?php if (Auth::user()->ID == 1) { ?>

            <span class="ib-nav-prev"  >Previous</span>
            <span class="ib-nav-next"  >Next</span>


<?php } else { ?>	
            <span class="ib-nav-prev" onclick="voting('dislike','t')"  >Previous</span>
            <span class="ib-nav-next" onclick="voting('like','t')" >Next</span>
            <span class="ib-nav-pass" onclick="voting('pass','t')" >Pass</span>	
<?php } ?>
        </div>
        <span class="ib-close" style="display:none;">Close Preview</span>
        <div class="ib-loading-large" style="display:none;">Loading...</div>

        </div>
    </script>
    <!-- For Video Preview -->

    <script id="previewTmplvideo" type="text/x-jquery-tmpl">
        <div id="ib-video-preview" class="ib-preview">
<?php if (Auth::user()->ID != 1) { ?><div class="ib-preview-flag" data-tooltip="Report Video"  onclick="showreport('v')"  >&nbsp;</div><?php } ?>

        <span class="ib-preview-descr reportshow"  style="display:none;"><span style="font-size:14px;">Posted by</span> </br>${participant_detail}</span>	

        <video alt="" class="ib-preview-video" controls><source src="${src}" type="video/mp4"></video>
        <div class="participant_id" style="display:none;">${contestid}</div>
        <div class="ib-nav" style="display:none;">				
        <!-- It is for Voting Icon -->	
<?php if (Auth::user()->ID == 1) { ?>

            <span class="ib-nav-prev"  >Previous</span>
            <span class="ib-nav-next"  >Next</span>


<?php } else { ?>				
            <span class="ib-nav-prev" onclick="voting('dislike','v')" >Previous</span>
            <span class="ib-nav-next" onclick="voting('like','v')" >Next</span>
            <span class="ib-nav-pass" onclick="voting('pass','v')" >Pass</span>
<?php } ?>
        </div>
        <span class="ib-close" style="display:none;">Close Preview</span>
        <div class="ib-loading-large" style="display:none;">Loading...</div>
        </div>		
    </script>

    <audio id="audio1" src="<?php echo url(); ?>/assets/tones/timer_bell_or_desk_bell_ringing.mp3"></audio>
    <audio id="audio2" src="<?php echo url(); ?>/assets/tones/alarm_car4.mp3"></audio>
    <!--<div >
    <audio id="audio1" controls >
<source src="<?php echo url(); ?>/assets/tones/timer_bell_or_desk_bell_ringing.mp3">
</audio>

<audio id="audio2" controls >
<source src="<?php echo url(); ?>/assets/tones/alarm_car4.mp3">
</audio>
</div>-->

    @stop