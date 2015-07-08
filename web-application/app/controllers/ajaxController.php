<?php
/* This Controller calls whenever the ajax call required */
class ajaxController extends BaseController {

    public function getcontestList() {
        $maintab = $_POST['main_tab'];
        $subtab = $_POST['sub_tab']; //return $subtab;
        $tsearch = $_POST['tsearch'];
        $interest = $_POST['interest'];
        if ($interest == 0)
            $interest = '';
        $currentdate = date('Y-m-d H:i:s');
        if ($maintab == "tab1")
            $contest_type = "p";
        elseif ($maintab == "tab2")
            $contest_type = "v";
        elseif ($maintab == "tab3")
            $contest_type = "t";

        if ($subtab == "tab4") {
            if (isset($tsearch) && $tsearch != '' && $interest == '') { ///// current with search ////
                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where(function($query) {
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
                                ->where('contest.contesttype', $contest_type)->where('contest_name', 'like', '%' . $tsearch . '%')->where('contest.status', '1')->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)->get();
            } else if (isset($tsearch) && $tsearch != '' && $interest != '') {

                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where(function($query) {
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
                                ->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                                ->where('contest.contesttype', $contest_type)->where('contest_name', 'like', '%' . $tsearch . '%')->where('contest.status', '1')->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)->get();
            } else if (isset($interest) && $interest != '') {

                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where(function($query) {
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
                                ->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                                ->where('contest.contesttype', $contest_type)->where('contest.status', '1')->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)->get();
            } else { ///// current without search ////

                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where(function($query) {
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
                                ->where('contesttype', $contest_type)->where('contest.status', '1')->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)->get();
            }
        } elseif ($subtab == "tab5") {
            if (isset($tsearch) && $tsearch != '') { ///// upcoming with search ////
                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('conteststartdate', '>', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->where('contest_name', 'like', '%' . $tsearch . '%')
                        ->where('contest.status', '1')
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else if (isset($tsearch) && $tsearch != '' && $interest != '') {
                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('conteststartdate', '>', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->where('contest_name', 'like', '%' . $tsearch . '%')
                        ->where('contest.status', '1')->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else if (isset($interest) && $interest != '') {
                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('conteststartdate', '>', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->where('contest.status', '1')->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else { ///// upcoming without search ////

                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('conteststartdate', '>', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->where('contest.status', '1')
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            }
        } elseif ($subtab == "tab6") {
            if (isset($tsearch) && $tsearch != '' && $interest == '') { ///// Archieve with search ////
                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('contestenddate', '<', $currentdate)
                        ->where('votingenddate', '<', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->where('contest_name', 'like', '%' . $tsearch . '%')
                        ->where('contest.status', '1')
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else if (isset($tsearch) && $tsearch != '' && $interest != '') {

                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('contestenddate', '<', $currentdate)
                        ->where('votingenddate', '<', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->where('contest_name', 'like', '%' . $tsearch . '%')->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                        ->where('contest.status', '1')
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else if (isset($interest) && $interest != '') {

                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('contestenddate', '<', $currentdate)
                        ->where('votingenddate', '<', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                        ->where('contest.status', '1')
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else {  ///// Archieve without search ////
                $photocontest = contestModel::select('contest.themephoto','contest.conteststartdate', 'contest.contestenddate', 'contest.ID', 'contest.contest_name','contest.createdby','contest.prize')->where('contestenddate', '<', $currentdate)
                        ->where('votingenddate', '<', $currentdate)
                        ->where('contesttype', $contest_type)
                        ->where('contest.status', '1')
                        ->where('visibility', 'u')->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            }
        } elseif ($subtab == "tab7") {
            if (isset($tsearch) && $tsearch != '' && $interest == '') { ///// private with search ////
                $photocontest = contestModel::where('visibility', 'p')
                        ->where('contesttype', $contest_type)
                        ->where('contest_name', 'like', '%' . $tsearch . '%')
                        ->select('contest.ID', 'contest_name', 'themephoto','conteststartdate', 'contestenddate', 'description', 'noofparticipant','contest.createdby','contest.prize')
                        ->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
                        ->where('private_contest_users.user_id', Auth::User()->ID)->where('contest.status', 1)->where('private_contest_users.status', '1')->distinct()->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else if (isset($tsearch) && $tsearch != '' && $interest != '') {

                $photocontest = contestModel::where('visibility', 'p')
                        ->where('contesttype', $contest_type)
                        ->where('contest_name', 'like', '%' . $tsearch . '%')
                        ->select('contest.ID', 'contest_name', 'themephoto', 'contestenddate','conteststartdate', 'description', 'noofparticipant','contest.createdby','contest.prize')
                        ->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                        ->where('private_contest_users.user_id', Auth::User()->ID)->where('contest.status', 1)->where('private_contest_users.status', '1')->distinct()->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else if (isset($interest) && $interest != '') {
                $photocontest = contestModel::where('visibility', 'p')
                        ->where('contesttype', $contest_type)
                        ->select('contest.ID', 'contest_name', 'themephoto', 'contestenddate','conteststartdate', 'description', 'noofparticipant','contest.createdby','contest.prize')
                        ->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')->where('contest_interest_categories.category_id', $interest)
                        ->where('private_contest_users.user_id', Auth::User()->ID)->where('contest.status', 1)->where('private_contest_users.status', '1')->distinct()->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            } else {     ///// private without search ////

                 $photocontest = contestModel::where('visibility', 'p')
                        ->where('contesttype', $contest_type)
                        ->select('contest.ID', 'contest_name', 'themephoto', 'contestenddate','conteststartdate', 'description', 'noofparticipant','contest.createdby','contest.prize')
                        ->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')->LeftJoin('contest_interest_categories', 'contest_interest_categories.contest_id', '=', 'contest.ID')
                        ->where('private_contest_users.user_id', Auth::User()->ID)->where('contest.status', 1)->where('private_contest_users.status', '1')->distinct()->leftJoin('user', 'user.ID', '=', 'contest.createdby')->where('user.status', 1)
                        ->get();
            }
        }
        $contestcount = count($photocontest);
        $return_string = "";
		$loginuser = Auth::user()->ID;
        for ($i = 0; $i < $contestcount; $i++) {

            if (strlen($photocontest[$i]['contest_name']) < 10) {
                $contest_name = $photocontest[$i]['contest_name'];
            } else {
                $contest_name = substr(($photocontest[$i]['contest_name']), 0, 10) . "...";
            }
			
			$backgroundclr = (($photocontest[$i]['createdby']==$loginuser)? "style='border:5px solid rgb(82, 195, 16)'":"");
			$nonedisplay = (($photocontest[$i]['createdby']==$loginuser)? "style='display:none; border:5px solid rgb(82, 195, 16)'":"style='display:none;'");
			$authdate = Auth::user()->dateformat;
			if($authdate=='dd/mm/yy'){ $authdate='d/m/Y h:i a'; }else{ $authdate='m/d/Y h:i a'; }
			
			if($photocontest[$i]['prize']!=''){$prizedata = "Prize: ".$photocontest[$i]['prize']; }else{ $prizedata =""; }
			
			if($currentdate>$photocontest[$i]['conteststartdate']){$showdate = "<span class='postdate'>Ends on : " . timezoneModel::convert($photocontest[$i]['contestenddate'], 'UTC', Auth::user()->timezone, $authdate) . "</span>"; }else{ $showdate ="<span class='postdate'>Starts on : " . timezoneModel::convert($photocontest[$i]['conteststartdate'], 'UTC', Auth::user()->timezone, $authdate) . "</span>"; } 
			
			
			
            $return_string .= "<div class='crsl-item' " . (($i >= 14) ? $nonedisplay : "") .$backgroundclr. " >
			  <div class='thumbnail'>
				<a href='" . URL::to('contest_info/' . $photocontest[$i]['ID']) . "' >
					<img src='" . URL::to('public/assets/upload/contest_theme_photo/' . $photocontest[$i]['themephoto']) . "' alt='nyc subway'>
					</a>
				".$showdate."
			  </div>
			  <h3><a href='" . URL::to('contest_info/' . $photocontest[$i]['ID']) . "'>" . $contest_name . "</a></h3>
			  <h4>".$prizedata."</h4>
			</div>";
        }
        return $return_string . "||" . $contestcount;
    }

    public function invite_group() {
        $group_id = $_GET['group_id'];
        $contest_id = $_GET['contest_id'];
        $curdate = date('Y-m-d H:i:s');
        $contest_det = contestModel::where("ID", $contest_id)->first();
        $group_members = groupmemberModel::where("group_id", $group_id)->get();
        //$inv_suc_message='No member to Invite';
        $inv_suc_message = 0;
        for ($i = 0; $i < count($group_members); $i++) {
            $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $group_members[$i]['user_id'])->count();


            if ($invited == 0 && $group_members[$i]['user_id'] != 1) {
                if (Auth::user()->firstname != '')
                    $inviter = Auth::user()->firstname . " " . Auth::user()->lastname;
                else
                    $inviter = Auth::user()->username;
                if ($contest_det['contesttype'] == "p")
                    $contesttype = "Photo";
                else if ($contest_det['contesttype'] == "v")
                    $contesttype = "Video";
                else if ($contest_det['contesttype'] == "t")
                    $contesttype = "Topic";
                $contestname = $contest_det['contest_name'];

                $contestimage = $contest_det['themephoto'];
                $conteststartdate = $contest_det['conteststartdate'];
                $contestenddate = $contest_det['contestenddate'];

                //	for($j=0;$j<count($group_members);$j++)
                //{

                if ($contest_det['createdby'] != $group_members[$i]['user_id']) {

                    $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $group_members[$i]['user_id'])->count();
                    if ($contest_det['visibility'] == "p" && $invited_member == 0) {
                        $privat_user['user_id'] = $group_members[$i]['user_id'];
                        $privat_user['contest_id'] = $contest_id;
                        $privat_user['requesteddate'] = date('Y-m-d H:i:s');
                        $privat_user['status'] = 1;
                        privateusercontestModel::create($privat_user);
                        unset($privat_user);
                    }

                    $input_details['group_id'] = $group_id;
                    $input_details['contest_id'] = $contest_id;
                    $input_details['invitedetail'] = 1;
                    $input_details['inviteddate'] = $curdate;
                    $input_details['user_id'] = $group_members[$i]['user_id'];
                    $invvitedsata = invitegroupforcontestModel::create($input_details);

                    // Email Notification for invitation
                    if ($invited_member == 0) {
                        $user = ProfileModel::where('ID', $group_members[$i]['user_id'])->first();
                        if ($user['firstname'] != '')
                            $name = $user['firstname'] . ' ' . $user['lastname'];
                        else
                            $name = $user['username'];
                        $email = $user['email'];

                        $groupname = groupModel::where('ID', $group_id)->first();
                        $groupname = $groupname->groupname;


						$gcmid = $user['gcm_id'];
					$device_type = $user['device_type'];
					$usertimezone = $user['timezone'];
					$userdateformat = $user['dateformat'];
                   
					if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
					$conteststartdate = timezoneModel::convert($contest_det['conteststartdate'], 'UTC',$usertimezone, $userdateformat);
					$contestenddate = timezoneModel::convert($contest_det['contestenddate'], 'UTC',$usertimezone, $userdateformat);
                    ///
                    if ($gcmid != '' && $device_type=='A') {
                        $Message['user_id'] = $group_members[$i]['user_id'];
                        $Message['title'] = 'Ding Datt';
                        $Message['message'] = 'You are invited for the Contest :' . $contestname;
                        $Message['contest_id'] = $contest_id;
                        $Message = array("notification" => $Message);
                        $DeviceId = array($gcmid);
                        $Message = array("notification" => $Message);
                        $this->PushNotification($DeviceId, $Message);
                    }else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = 'You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					} else {
                        $this->invitegroupmemberforcontestmail($email, $inviter, $contesttype, $contestname, $contest_id, $groupname, $contestimage, $conteststartdate, $contestenddate);
					}
                        if ($invvitedsata)
                            $inv_suc_message = 1;
                    }
                    //--------------------------
                }
            }
        }
        return $inv_suc_message;
        //return Redirect::to("contest_info/".$contest_id)->with('tab','invite')->with('inv_suc_message',$inv_suc_message);
    }

    public function invitegroupmemberforcontest() {
        $groupmemberlist = $_GET['checkseparate'];
        $groupid = $_GET['groupid'];
        $contest_id = $_GET['contest_id'];
        $invitetype = $_GET['invitetype'];
        $groupmemberlistid = explode(',', $groupmemberlist);
        $curdate = date('Y-m-d H:i:s');
        /// Contest Details /////////////
        $contest_det = contestModel::where("ID", $contest_id)->first();
        if (Auth::user()->firstname != '')
            $inviter = Auth::user()->firstname . " " . Auth::user()->lastname;
        else
            $inviter = Auth::user()->username;
        if ($contest_det['contesttype'] == "p")
            $contesttype = "Photo";
        else if ($contest_det['contesttype'] == "v")
            $contesttype = "Video";
        else if ($contest_det['contesttype'] == "t")
            $contesttype = "Topic";
        $contestname = $contest_det['contest_name'];
        $contestimage = $contest_det['themephoto'];

        $contestcreatedby = User::find($contest_det['createdby']);

        if ($contestcreatedby['firstname'] != '') {
            $contestcreatedby = $contestcreatedby['firstname'] . '' . $contestcreatedby['lastname'];
        } else {
            $contestcreatedby = $contestcreatedby['username'];
        }

        $groupdetails = groupModel::where('ID', $groupid)->first();
        $groupname = $groupdetails['groupname'];

        $inv_suc_message = 0;

        for ($i = 0; $i < count($groupmemberlistid); $i++) {
            $groupmemberid = groupmemberModel::where('id', $groupmemberlistid[$i])->first();
            $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $groupmemberid['user_id'])->count();
            if ($invited == 0) {

                $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $groupmemberid['user_id'])->count();
                if ($contest_det['visibility'] == "p" && $invited_member == 0) {
                    $privat_user['user_id'] = $groupmemberid['user_id'];
                    $privat_user['contest_id'] = $contest_id;
                    $privat_user['requesteddate'] = date('Y-m-d H:i:s');
                    $privat_user['status'] = 1;
                    privateusercontestModel::create($privat_user);
                    unset($privat_user);
                }
                if ($invited_member == 0) {

                    $input_details['group_id'] = $groupid;
                    $input_details['contest_id'] = $contest_id;
                    $input_details['invitedetail'] = 1;
                    $input_details['inviteddate'] = $curdate;
                    $input_details['user_id'] = $groupmemberid['user_id'];
                    invitegroupforcontestModel::create($input_details);

                    $user = ProfileModel::where('ID', $groupmemberid['user_id'])->first();
                    if ($user['firstname'] != '')
                        $name = $user['firstname'] . ' ' . $user['lastname'];
                    else
                        $name = $user['username'];
                    $email = $user['email'];
                    $gcmid = $user['gcm_id'];
					$device_type = $user['device_type'];
					$usertimezone = $user['timezone'];
					$userdateformat = $user['dateformat'];
                   
					if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
					$conteststartdate = timezoneModel::convert($contest_det['conteststartdate'], 'UTC',$usertimezone, $userdateformat);
					$contestenddate = timezoneModel::convert($contest_det['contestenddate'], 'UTC',$usertimezone, $userdateformat);
		
		
                    if ($gcmid != '' && $device_type=='A') {
                        $Message['user_id'] = $groupmemberid['user_id'];
                        $Message['title'] = 'Ding Datt';
                        $Message['message'] = 'You are invited for the Contest :' . $contestname;
                        $Message['contest_id'] = $contest_id;
                        $Message = array("notification" => $Message);
                        $DeviceId = array($gcmid);
                        $Message = array("notification" => $Message);
                        $this->PushNotification($DeviceId, $Message);
                    }else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = 'You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					} else {
                        $this->invitegroupmemberforcontestmail($email, $contestcreatedby, $contesttype, $contestname, $contest_id, $groupname, $contestimage, $conteststartdate, $contestenddate);
                    }
                    $inv_suc_message = 1;
                }
            } else {
                
            }
        }
        if ($inv_suc_message == 1 || $invitetype == 'all')
            return 1;
        else
            return 0;
    }

    public function uninvite_group_member() {

        $group_id = $_GET['group_id'];
        $contest_id = $_GET['contest_id'];
        $groupmemberid = $_GET['groupmemberid'];
        $groupmemberid = groupmemberModel::where('id', $groupmemberid)->first();
        $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $groupmemberid['user_id'])->count();
        if ($invited) {
            $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $groupmemberid['user_id'])->delete();

            $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $groupmemberid['user_id'])->count();
            if ($invited_member) {

                $contestdetails = contestModel::where('ID', $contest_id)->get()->first();
                if ($contestdetails['createdby'] != $groupmemberid['user_id']) {
                    $invited_memberdelete = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $groupmemberid['user_id'])->delete();
                }
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function uninviteallgroupmemberforcontest() {

        $groupmemberlist = $_GET['checkseparate'];
        $groupid = $_GET['groupid'];
        $contest_id = $_GET['contest_id'];
        $uninvitetype = $_GET['uninvitetype'];
        $groupmemberlistid = explode(',', $groupmemberlist);
        $inv_suc_message = 0;
        for ($i = 0; $i < count($groupmemberlistid); $i++) {
            $groupmemberid = groupmemberModel::where('id', $groupmemberlistid[$i])->first();
            $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $groupmemberid['user_id'])->count();
            if ($invited != 0) {
                $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $groupmemberid['user_id'])->delete();

                $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $groupmemberid['user_id'])->count();
                if ($invited_member) {
                    $contestdetails = contestModel::where('ID', $contest_id)->get()->first();
                    if ($contestdetails['createdby'] != $groupmemberid['user_id']) {

                        $invited_memberdelete = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $groupmemberid['user_id'])->delete();
                        $inv_suc_message = 'Uninvited successfully.';
                        $inv_suc_message = 1;
                    }
                }
            }
        }
        if ($uninvitetype == 'all')
            return 1;
        else
            return 0;
    }

    public function uninvite_group() {
        $groupid = $_GET['group_id'];
        $contest_id = $_GET['contest_id'];
        $inv_suc_message = '';
        $groupmemberid = groupmemberModel::where('group_id', $groupid)->get();

        $k = 0;
        for ($i = 0; $i < count($groupmemberid); $i++) {
            $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $groupmemberid[$i]['user_id'])->count();
            if ($invited != 0) {
                $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $groupmemberid[$i]['user_id'])->delete();

                $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $groupmemberid[$i]['user_id'])->count();
                if ($invited_member) {

                    $contestdetails = contestModel::where('ID', $contest_id)->get()->first();
                    if ($contestdetails['createdby'] != $groupmemberid[$i]['user_id']) {

                        $invited_memberdelete = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $groupmemberid[$i]['user_id'])->delete();
                    }
                    $inv_suc_message = 'Uninvited successfully';
                }
                $k = 1;
            }
        }
        return $k;
    }

    public function uninvite_allgroup() {
        return "A";
        $contest_id = $_GET['contest_id'];
    }

    public function inviteall_group() {
        $group_ids = Input::get("group_list");
        $contest_id = Input::get("contest_id");
        $group_ids = explode(',', $group_ids);
        $groupcount = count($group_ids);
        $curdate = date('Y-m-d H:i:s');
        $contest_det = contestModel::where("ID", $contest_id)->first();

        if (Auth::user()->firstname != '')
            $inviter = Auth::user()->firstname . " " . Auth::user()->lastname;
        else
            $inviter = Auth::user()->username;
        if ($contest_det['contesttype'] == "p")
            $contesttype = "Photo";
        else if ($contest_det['contesttype'] == "v")
            $contesttype = "Video";
        else if ($contest_det['contesttype'] == "t")
            $contesttype = "Topic";
        $contestname = $contest_det['contest_name'];

        $inv_suc_message = '';



        if ($groupcount > 0) {

            for ($i = 0; $i < $groupcount; $i++) {
                $group_members = groupmemberModel::where("group_id", $group_ids[$i])->get();


                for ($j = 0; $j < count($group_members); $j++) {

                    $invited = invitegroupforcontestModel::where('contest_id', $contest_id)->where('user_id', $group_members[$j]['user_id'])->count();
                    if ($invited == 0) {

                        $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $group_members[$j]['user_id'])->count();
                        if ($contest_det['contesttype'] == "p" && $invited_member == 0) {
                            $privat_user['user_id'] = $group_members[$j]['user_id'];
                            $privat_user['contest_id'] = $contest_id;
                            $privat_user['requesteddate'] = date('Y-m-d H:i:s');
                            $privat_user['status'] = 1;
                            privateusercontestModel::create($privat_user);
                            unset($privat_user);
                        }
                        // Email Notification for invitation
                        if ($invited_member == 0) {
                            $user = ProfileModel::where('ID', $group_members[$j]['user_id'])->first();
                            if ($user['firstname'] != '')
                                $name = $user['firstname'] . ' ' . $user['lastname'];
                            else
                                $name = $user['username'];
                            $email = $user['email'];
                            Mail::send([], array('name' => $name, 'email' => $email, 'inviter' => $inviter, 'contesttype' => $contesttype, 'contestname' => $contestname), function($message) use ($name, $email, $inviter, $contesttype, $contestname) {
                                $mail_body = '<body style="padding:0px;margin:0px; font-family: Arial, Helvetica, sans-serif; color: #222222; font-size:12px;">
   <div style="width:550px;height:auto; border:1px solid #d5d5d5;padding:0px;margin:0px;overflow:hidden;">
    <div style="dislay:block;padding:25px;margin:0px; text-align:center; min-height:50px;">
        <div style="height:15px; width:100%; margin:0px;">&nbsp;</div>
        <a href="' . URL::to('assets/inner/img/DingDatt_logo_web1.png') . '" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">
        <img src="' . URL::to('assets/inner/img/DingDatt_logo_web1.png') . '" width="110" height="86" style="width:110px; padding:0px; margin:0px;" alt="DingDatt"/>
        </a>
        <div style="height:15px; width:100%; padding:0px;">&nbsp;</div>
     </div>
     
     <div style="display:block; margin:25px; overflow:hidden;">
         <div style="display:block; padding: 10px; border: 1px solid #e5e5e5; margin:10px 0px;">
            <span style="padding:0px;margin:0px;font-weight:bold;">Invitation for join the contest.</span>
         </div>
         <div style="display: block; margin: 15px;">
             <h4 style="padding:0px;margin:0; font-size:14px;color:#d33030;">Contest Details:</h4>
         </div>
         <p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;"> Contest Name:</span>' . $contestname . '</p>
         <p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Contest Type:</span>' . $contesttype . '</p>
         <p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Created by:</span>' . $inviter . '</p>
		 <p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;">This is auto generate email so please do not reply to this email.</p>
         <div style="padding:0; margin:15px;">
         <p style="padding:0px; font-weight: bold;">Thanks,</p>
         DingDatt</div>
     </div>
     <div style="height:25px; width:100%;">&nbsp;</div>
   </div>
</body>';
                                $mail_body = str_replace("{name}", $name, $mail_body);
                                $mail_body = str_replace("{inviter}", $inviter, $mail_body);
                                $mail_body = str_replace("{contesttype}", $contesttype, $mail_body);
                                $mail_body = str_replace("{contestname}", $contestname, $mail_body);
                                $message->setBody($mail_body, 'text/html');
                                $message->to($email);
                                $message->subject('Dingdatt-Invitation for join the contest');
                            });
                        }
                        //--------------------------

                        $input_details['group_id'] = $group_ids[$i];
                        $input_details['contest_id'] = $contest_id;
                        $input_details['invitedetail'] = 1;
                        $input_details['inviteddate'] = $curdate;
                        $input_details['user_id'] = $group_members[$j]['user_id'];
                        $added = invitegroupforcontestModel::create($input_details);
                        unset($input_details);
                        if ($added)
                            $inv_suc_message = 'Invitation sent successfully.';
                    }
                }
            }
            return 1;
        }
        else {
            return 0;
        }
    }

    public function getinviteList() {
        $subtab = Input::get('subtab');
        $contest_id = Input::get('contest_id');
        if ($subtab) {
            ?>
            <thead>
                <tr>
                    <th><input name="" type="checkbox" value=""></th>
                    <th>Image</th>
                    <th>Group Name</th>
                    <th class="tr_wid_button1" align="center">Invite</th>
                    <th class="tr_wid_button1" align="center">View</th>

                </tr>
            </thead>
            <tbody>
            <?php
            $grouplist = groupModel::select('group.ID as groupid', 'groupname', 'grouptype', 'createdby', 'user.firstname as owner', 'groupimage')->LeftJoin('user', 'user.ID', '=', 'group.createdby')->get();
            $groupcount = count($grouplist);
            for ($i = 0; $i < $groupcount; $i++) {
                $invited = invitegroupforcontestModel::where('group_id', $grouplist[$i]['groupid'])->where('contest_id', $contest_id)->count();
                ?>
                    <tr>
                        <td class="tr_wid_id"><input name="group_list[]"  type="checkbox" value="{{ $grouplist[$i]['groupid'] }}"></td>
                        <td align="center"><img src="{{ ($grouplist[$i]['groupimage']!='')?(URL::to('public/assets/upload/group/'.$grouplist[$i]['groupimage'])):(URL::to('assets/inner/img/default_groupimage.png')) }}" width="50" height="50"></td>
                        <td>{{ $grouplist[$i]['groupname'] }}</td>
                        <td class="tr_wid_button1" align="center"><a href="#" <?php if ($invited > 0) { ?> title="Invited" style="background-color:red;" <?php } else { ?> title="Invite" onClick="invite_groups('{{ $grouplist[$i]['groupid'] }}','{{ $contest_id }}');" <?php } ?> id="invite_list_{{ $grouplist[$i]['groupid'] }}" class="add-link"></a></td>
                        <td class="tr_wid_button1" align="center"><a href="{{ URL::to('viewgroupmember/'.$grouplist[$i]['groupid']) }}" class="view-link"></a></td>
                    </tr>
                <?php
            }
            ?>
            </tbody>
            <?php
        }
    }

    public function invite_follower() {
        $followerid = $_POST['followerid'];
        $contest_id = $_POST['contest_id'];
        $curdate = date('Y-m-d H:i:s');
        $contest_det = contestModel::where("ID", $contest_id)->first();

        if (Auth::user()->firstname != '')
            $inviter = Auth::user()->firstname . " " . Auth::user()->lastname;
        else
            $inviter = Auth::user()->username;
        if ($contest_det['contesttype'] == "p")
            $contesttype = "Photo";
        else if ($contest_det['contesttype'] == "v")
            $contesttype = "Video";
        else if ($contest_det['contesttype'] == "t")
            $contesttype = "Topic";
        $contestname = $contest_det['contest_name'];
        $conteststart = $contest_det['contest_name'];
        $conteststart = $contest_det['contest_name'];

        $invited = invitefollowerforcontestModel::where('follower_id', $followerid)->where('contest_id', $contest_id)->count();
        if ($invited == 0) {
            $input_details['follower_id'] = $followerid;
            $input_details['contest_id'] = $contest_id;
            $input_details['invitedate'] = $curdate;
            invitefollowerforcontestModel::create($input_details);

            $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $followerid)->count();
            if ($contest_det['visibility'] == "p" && $invited_member == 0) {
                $privat_user['user_id'] = $followerid;
                $privat_user['contest_id'] = $contest_id;
                $privat_user['requesteddate'] = date('Y-m-d H:i:s');
                $privat_user['status'] = 1;
                privateusercontestModel::create($privat_user);
                unset($privat_user);
            }
            // Email Notification for invitation
            if ($invited_member == 0) {
                $user = ProfileModel::where('ID', $followerid)->first();
                if ($user['firstname'] != '')
                    $name = $user['firstname'] . ' ' . $user['lastname'];
                else
                    $name = $user['username'];
                $email = $user['email'];
				
                $gcmid = $user['gcm_id'];
					$device_type = $user['device_type'];
                    ///
                    if ($gcmid != '' && $device_type=='A') {
                        $Message['user_id'] = $groupmemberid['user_id'];
                        $Message['title'] = 'Ding Datt';
                        $Message['message'] = 'You are invited for the Contest :' . $contestname;
                        $Message['contest_id'] = $contest_id;
                        $Message = array("notification" => $Message);
                        $DeviceId = array($gcmid);
                        $Message = array("notification" => $Message);
                        $this->PushNotification($DeviceId, $Message);
                    }else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = 'You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					} else {
					
					 $contestimage = $contest_det['themephoto'];

					$conteststartdate = $contest_det['conteststartdate'];
					$contestenddate = $contest_det['contestenddate'];
					   
					    $usertimezone = $user['timezone'];
						$userdateformat = $user['dateformat'];
					   
						if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
						$conteststartdate = timezoneModel::convert($contest_det['conteststartdate'], 'UTC',$usertimezone, $userdateformat);
						$contestenddate = timezoneModel::convert($contest_det['contestenddate'], 'UTC',$usertimezone, $userdateformat);
						
					//$name, $email, $contestcreatedby, $contesttype, $contestname, $contest_id,$contestimage,$conteststartdate,$contestenddate
				$sendmail = $this->invitefollowerforcontestmail($name, $email, $inviter, $contesttype, $contestname, $contest_id,$contestimage,$conteststartdate,$contestenddate);
				}
			}
            return 1;
        }
        else {
            return 0;
        }
    }

    public function inviteall_follower() {
        $follower_ids = Input::get("follower_list");
        $contest_id = Input::get("contest_id");
        $follower_ids = explode(',', $follower_ids);
        $followercount = count($follower_ids);
        $curdate = date('Y-m-d H:i:s');
        $contest_det = contestModel::where("ID", $contest_id)->first();

        if (Auth::user()->firstname != '')
            $inviter = Auth::user()->firstname . " " . Auth::user()->lastname;
        else
            $inviter = Auth::user()->username;
        if ($contest_det['contesttype'] == "p")
            $contesttype = "Photo";
        else if ($contest_det['contesttype'] == "v")
            $contesttype = "Video";
        else if ($contest_det['contesttype'] == "t")
            $contesttype = "Topic";
        $contestname = $contest_det['contest_name'];
		$contestimage = $contest_det['themephoto'];

        if ($followercount > 0) {
            for ($i = 0; $i < $followercount; $i++) {
                $invited = invitefollowerforcontestModel::where('follower_id', $follower_ids[$i])->where('contest_id', $contest_id)->count();
                if ($invited == 0) {
                    $input_details['follower_id'] = $follower_ids[$i];
                    $input_details['contest_id'] = $contest_id;
                    $input_details['invitedate'] = $curdate;
                    invitefollowerforcontestModel::create($input_details);

                    $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $follower_ids[$i])->count();
                    if ($contest_det['visibility'] == "p" && $invited_member == 0) {
                        $privat_user['user_id'] = $follower_ids[$i];
                        $privat_user['contest_id'] = $contest_id;
                        $privat_user['requesteddate'] = date('Y-m-d H:i:s');
                        $privat_user['status'] = 1;
                        privateusercontestModel::create($privat_user);
                    }

                    if ($invited_member == 0) {
                        $user = ProfileModel::where('ID', $follower_ids[$i])->first();
                        if ($user['firstname'] != '')
                            $name = $user['firstname'] . ' ' . $user['lastname'];
                        else
                            $name = $user['username'];

                        $email = $user['email'];
						$gcmid = $user['gcm_id'];
					$device_type = $user['device_type'];
                    ///
                    if ($gcmid != '' && $device_type=='A') {
                        $Message['user_id'] = $follower_ids[$i];
                        $Message['title'] = 'Ding Datt';
                        $Message['message'] = 'You are invited for the Contest :' . $contestname;
                        $Message['contest_id'] = $contest_id;
                        $Message = array("notification" => $Message);
                        $DeviceId = array($gcmid);
                        $Message = array("notification" => $Message);
                        $this->PushNotification($DeviceId, $Message);
                    }else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = 'You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					} else {
						$usertimezone = $user['timezone'];
						$userdateformat = $user['dateformat'];
					   
						if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
						$conteststartdate = timezoneModel::convert($contest_det['conteststartdate'], 'UTC',$usertimezone, $userdateformat);
						$contestenddate = timezoneModel::convert($contest_det['contestenddate'], 'UTC',$usertimezone, $userdateformat);

                        $this->invitefollowerforcontestmail($name, $email, $inviter, $contesttype, $contestname, $contest_id,$contestimage,$conteststartdate,$contestenddate);
						
						//$name, $email, $inviter, $contesttype, $contestname, $contest_id,$contestimage,$conteststartdate,$contestenddate
                    }
					}
                }
            }
            return 1;
        }
        else {
            return 0;
        }
    }

    public function uninvite_follower() {

        $contest_id = $_GET['contest_id'];
        $followerid = $_GET['followerid'];
        $invited = invitefollowerforcontestModel::where('follower_id', $followerid)->where('contest_id', $contest_id)->count();
        if ($invited == 1) {
            invitefollowerforcontestModel::where('follower_id', $followerid)->where('contest_id', $contest_id)->delete();
            $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $followerid)->count();
            if ($invited_member)
                privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $followerid)->delete();
            return 1;
        }
        else {
            return 0;
        }
    }

    public function uninvite_allfollower() {

        $contest_id = $_GET['contest_id'];


        $follower_ids = Input::get("follower_list");
        $follower_ids = explode(',', $follower_ids);
        $followercount = count($follower_ids);


        if ($followercount > 0) {
            for ($i = 0; $i < $followercount; $i++) {
                $invited = invitefollowerforcontestModel::where('follower_id', $follower_ids[$i])->where('contest_id', $contest_id)->count();
                if ($invited == 1) {
                    invitefollowerforcontestModel::where('follower_id', $follower_ids[$i])->where('contest_id', $contest_id)->delete();
                    $invited_member = privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $follower_ids[$i])->count();
                    if ($invited_member)
                        privateusercontestModel::where("contest_id", $contest_id)->where('user_id', $follower_ids[$i])->delete();
                }
            }
            return 1;
        }else {
            return 0;
        }
    }

    public function ajaxaccepgroup() {
        $invitetype = explode(',', $_GET['invite']);
        $groupid = explode(',', $_GET['group_id']);
        $user_id = explode(',', $_GET['user_id']);


        count($invitetype);

        for ($i = 0; $i < count($invitetype); $i++) {
            $curdate = date('Y-m-d h:i:s');
            $inputdetails['invitetype'] = $invitetype[$i];
            $inputdetails['group_id'] = $groupid[$i];
            $inputdetails['createddate'] = $curdate;
            $inputdetails['user_id'] = $user_id[$i];

            $verify = groupmemberModel::where('group_id', $groupid[$i])->where('user_id', $user_id[$i])->get()->count();
            if ($verify) {
                $datareturn = "You are Already Member of this group";
                $delete = invitememberforgroupModel::where('group_id', $groupid[$i])->where('user_id', $user_id[$i])->delete();
            } else {
                $savegroupmembers = groupmemberModel::create($inputdetails);
                $delete = invitememberforgroupModel::where('group_id', $groupid[$i])->where('user_id', $user_id[$i])->delete();
                if ($savegroupmembers && $delete)
                    $datareturn = "Member accepted successfully";
            }
        }
        return $datareturn;
    }

    public function ajaxrejectgroup() {
        $invitetype = explode(',', $_GET['invite']);
        $groupid = explode(',', $_GET['group_id']);
        $user_id = explode(',', $_GET['user_id']);

        for ($i = 0; $i < count($invitetype); $i++) {
            $curdate = date('Y-m-d h:i:s');
            $inputdetails['invitetype'] = $invitetype[$i];
            $inputdetails['group_id'] = $groupid[$i];
            $inputdetails['createddate'] = $curdate;
            $inputdetails['user_id'] = $user_id[$i];
            $cntinvittype = invitememberforgroupModel::where('group_id', $groupid[$i])->where('user_id', $user_id[$i])->get()->count();
            if ($cntinvittype) {
                $reject = invitememberforgroupModel::where('group_id', $groupid[$i])->where('user_id', $user_id[$i])->delete();
            }
            if (count($invitetype) > 0) {
                $datareturn = "You rejected that request";
            } else {
                $datareturn = "You are not invited";
            }
        }
        return $datareturn;
    }

    public function viewcomment() {
        return $_GET['contestparticipant_id'];
    }

    public function getmultilingualalert() {
        $ctrlCaptionId = $_GET['ctrlCaptionKey'];
        $lantyp = Session::get('language');
        if ($lantyp == "")
            $lantyp = "value_en";
        $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_delete_group_msg'])->get();
        return $languageDetails[0][$lantyp];
    }

    public function PushNotification($DeviceId, $Message) {
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $DeviceId,
            'data' => $Message
        );
        $headers = array(
            'Authorization: key = AIzaSyBLv4dCUVJ9_C8G26YiG9VfPoQc3mnUu-g',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    public function invitegroupmemberforcontestmail($email, $contestcreatedby, $contesttype, $contestname, $contest_id, $groupname, $contestimage, $conteststartdate, $contestenddate) {


        Mail::send([], array('email' => $email, 'contestcreatedby' => $contestcreatedby, 'contesttype' => $contesttype, 'contestname' => $contestname, 'contest_id' => $contest_id, 'groupname' => $groupname, 'contestimage' => $contestimage, 'conteststartdate' => $conteststartdate, 'contestenddate' => $contestenddate), function($message) use ($email, $contestcreatedby, $contesttype, $contestname, $contest_id, $groupname, $contestimage, $conteststartdate, $contestenddate) {
            /* $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								
								
								</style>
						 <body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL::to('contest_info/' . $contest_id) . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:center;">Thank you for joining us</div>
								<div style="font-size:14px;	color: #D79600;	font-weight:bold;float:left;padding:10px 2px;width:100%;margin-bottom:5px;">Contest Details</div>
								
								<table style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
									<tr>
										<td colspan="2" width="300" height="200" valign="top" style="top:0px;" >
											<table valign="middle">
												<tr>
													<td align="center" style="border:#cccccc 1px solid;">
													
														<img src="' . URL::to('public/assets/upload/contest_theme_photo/' . $contestimage) . '" width="280" height="auto" style="height:auto;" />
													</td>
												</tr>
											
											</table>
											
										</td>
										
										<td>
								<table width="600" height="95" border="0" style="margin-bottom:10px;float:left;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
							  <tr>
								<td class="cont_name" style="font-size:12px; color:#3BBA00; font-weight:bold; width:100px; padding-left:10px;">Contest Name:</td>
								<td class="cont_value" style="font-size:12px; color:#5d5d5d; font-weight:bold;width:100px; padding-left:10px;">' . $contestname . '</td>
							  </tr>
							  <tr>
								<td class="cont_name" style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Contest Type:</td>
								<td  class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">' . $contesttype . '</td>
							  </tr>
							   <tr>
								<td class="cont_name"  style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Created By:</td>
								<td  class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">' . $contestcreatedby . '</td>
							  </tr>
							   <tr>
								<td class="cont_name"  style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Group Name:</td>
								<td class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">' . $groupname . '</td>
							  </tr>
							  <tr>
								<td class="cont_name" style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Start Date:</td>
								<td class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">' . $conteststartdate . '</td>
							  </tr>
							  <tr>
								<td class="cont_name"  style="font-size:12px;color: #3BBA00;font-weight:bold; padding-left:10px;">End Date:</td>
								<td class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">' . $contestenddate . '</td>
							  </tr>
							  
							   <tr>
                              	<td colspan="2">
                                	<a href="' . URL::to('contest_info/' . $contest_id) . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
                            	</td>
                              </tr>					  
							  
							</table>
							</td>									
							</tr>
							</table>
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b;width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
							</body>'; */
							
							$mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								
								
								</style>
						 <body style=" margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:center;">Contest invitation</div>
								<div style="font-size:14px;	color: #D79600;	font-weight:bold;float:left;padding:10px 2px;width:100%;margin-bottom:5px;">Contest Details</div>
								
								<table  >
									<tr>
										<td colspan="2" width="250" height="220" valign="top" style="top:0px;" >
											<table valign="middle">
												<tr>
													<td align="center" style="border:#cccccc 1px solid;">
													
														<img src="'.URL::to('public/assets/upload/contest_theme_photo/'.$contestimage).'" width="230" height="200" />
													</td>
												</tr>
											
											</table>
										</td>
										<td>
								<table width="500" height="95" border="0" style="margin-bottom:10px;float:left;" border="1" cellspacing="0" cellpadding="0">
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Contest Name</span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contestname.'</span></td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Contest Type</span></td>
								<td  valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contesttype.'</span></td>
							  </tr>
							  
							   <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Created By</span></td>
								<td  valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contestcreatedby.'</span></td>
							  </tr>
							   
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Start Date</span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$conteststartdate.'</span></td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">End Date </span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contestenddate.'</span></td>
							  </tr>
							  
							  							   			  
							  
							</table>
							<p></p></br>
							<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
							</td>									
							</tr>
							</table>
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
							</body>';

            $message->setBody($mail_body, 'text/html');
            $message->to($email);
            $message->subject('Dingdatt-Invitation for join the contest');
        });
    }

    public function invitefollowerforcontestmail($name, $email, $contestcreatedby, $contesttype, $contestname, $contest_id,$contestimage,$conteststartdate,$contestenddate) {
        Mail::send([], array('name' => $name, 'email' => $email, 'contestcreatedby' => $contestcreatedby, 'contesttype' => $contesttype, 'contestname' => $contestname, 'contest_id' => $contest_id,'contestimage' =>$contestimage,'conteststartdate'=>$conteststartdate,'contestenddate'=>$contestenddate), function($message) use ($name, $email, $contestcreatedby, $contesttype, $contestname, $contest_id,$contestimage,$conteststartdate,$contestenddate) {

            $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								
								
								</style>
						 <body style=" margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:center;">Contest invitation</div>
								<div style="font-size:14px;	color: #D79600;	font-weight:bold;float:left;padding:10px 2px;width:100%;margin-bottom:5px;">Contest Details</div>
								
								<table  >
									<tr>
										<td colspan="2" width="250" height="220" valign="top" style="top:0px;" >
											<table valign="middle">
												<tr>
													<td align="center" style="border:#cccccc 1px solid;">
													
														<img src="'.URL::to('public/assets/upload/contest_theme_photo/'.$contestimage).'" width="230" height="200" />
													</td>
												</tr>
											
											</table>
										</td>
										<td>
								<table width="500" height="95" border="0" style="margin-bottom:10px;float:left;" border="1" cellspacing="0" cellpadding="0">
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Contest Name</span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contestname.'</span></td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Contest Type</span></td>
								<td  valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contesttype.'</span></td>
							  </tr>
							   <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Created By</span></td>
								<td  valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contestcreatedby.'</span></td>
							  </tr>
							   
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Start Date</span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$conteststartdate.'</span></td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">End Date </span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$contestenddate.'</span></td>
							  </tr>
							  							   			  
							  
							</table>
							<p></p></br>
							<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
							</td>									
							</tr>
							</table>
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
							</body>';
            $message->setBody($mail_body, 'text/html');
            $message->to($email);
            $message->subject('Dingdatt-Invitation for join the contest');
        });
    }
		public function PushNotificationIos($DeviceId,$Message) 
    {
      
    $ParentPemUrl = url()."/pushnotificationIOS.php?DeviceId=".$DeviceId.'&Message='.$Message;
    $TriggerParent = file_get_contents($ParentPemUrl);
    #exit;    
   
    }

}
?>