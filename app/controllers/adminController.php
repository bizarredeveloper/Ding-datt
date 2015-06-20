<?php

/* commented */

class adminController extends BaseController {

    public function user() {
        return View::make('admin/user');
    }

    public function adduser() {
        $data = Auth::user()->ID;
        $GeneralData = Input::except(array('_token', 'status', 'pagename', 'profilepicture', 'profileimgedithidden', 'interest', 'update_profile', 'timezone'));

        $newimg = Input::file('profilepicture');
        if ($newimg != '') {
            $destinationPath = 'public/assets/upload/profile';
            $filename = Input::file('profilepicture')->getClientOriginalName();
            $Image = str_random(8) . '_' . $filename;
            $GeneralData['profilepicture'] = $Image;
            $uploadSuccess = Input::file('profilepicture')->move($destinationPath, $Image);
        }


        $interest = Input::get('interest');
        $interest_length = sizeof(Input::get('interest'));
        $GeneralData['status'] = 1;
        $cur_date = date('Y-m-d');
        $GeneralData['timezone'] = Input::get('timezone');
        $updaterules = array(
            'username' => 'required|unique:user',
            'password' => 'required|confirmed|min:5',
            'email' => 'required|email|unique:user',
            'dateofbirth' => 'required',
            'timezone' => 'required|min:2',
                );
        $validation = Validator::make($GeneralData, $updaterules);
        $newpassword = Input::get('password');
        if ($newpassword != "") {
            $GeneralData['password'] = Hash::make(Input::get('password'));
        } else {
            unset($GeneralData["password"]);
        }
        unset($GeneralData["password_confirmation"]);
        $updatedata = $GeneralData;
        $lantyp = Session::get('language');
        if ($lantyp == "")
            $lantyp = "value_en";
        if (!isset($updatedata['maritalstatus']))
            $updatedata['maritalstatus'] = 0;
        if ($validation->passes()) {
            $affectedRows = ProfileModel::create($updatedata);
            $pass = $newpassword;
            $email = Input::get('email');
            $username = Input::get('username');
            Mail::send([], array('pass' => $pass, 'email' => $email, 'username' => $username), function($message) use ($pass, $email, $username) {
                $mail_body = '<style>.thank{text-align:center; width:100%;}
					.but_color{color:#ffffff;}
					.cont_name{width:100px;}
					.cont_value{width:500px;}
					
					</style>
				<body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

				<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
					&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px; line-height:20px;" /></a>&nbsp;&nbsp;
				</div>
				<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
					<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear ' . $username . '</div>
					
					<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your DingDatt Registration successfully completed.Your Login details are<br><br>Username: ' . $username . '<br>Password: ' . $pass . '
				</div>
					
					<div style="margin:10px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
					</div>
				</div>
											
				<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
				</body>';
                $message->setBody($mail_body, 'text/html');
                $message->to($email);
                $message->subject('DingDatt Registration');
            });
            $lantyp = Session::get('language');
            $labelname = ['txt_user_update_msg'];
            $languageDetails = languageModel::select($lantyp)->whereIn('ctrlCaptionId', $labelname)->get()->toArray();
            $user_id = Auth::user()->ID;
            $profileData = ProfileModel::where('ID', $user_id)->first();
            $interestList = InterestCategoryModel::lists('Interest_name', 'Interest_id');
            $userInterest = userinterestModel::where('user_id', $user_id)->lists('interest_id');

            $er_data['message'] = 'User details added successfully.';
            return Redirect::to('/user')->with('tab', 'userlist')->with('er_data', $er_data);
        } else {
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', [$validation->messages()->first('username'), $validation->messages()->first('password'), $validation->messages()->first('email'), $validation->messages()->first('dateofbirth'), $validation->messages()->first('timezone')])->get()->toArray();
            foreach ($languageDetails as $key => $val) {
                if (in_array($val['ctrlCaptionId'], ['alert_enterusername', 'alert_alreadyuser']))
                    $er_data['username'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enterpassword', 'alert_minpass5', 'alert_passconfnotmatch']))
                    $er_data['password'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enteremail', 'alert_validemail', 'alertr_emailalready']))
                    $er_data['email'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enterdob']))
                    $er_data['dateofbirth'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['txt_timezone_required']))
                    $er_data['timezone'] = "<span id='" . $val['ctrlCaptionId'] . "'>Timezone is required</span>";
            }

            return Redirect::to('/user')->with('tab', 'createuser')->with('er_data', $er_data)->with('old_data', $updatedata);
        }
    }

    public function searchuser() {
        $inputs = Input::get();
        $searcheduser = Input::get('tsearch2');
        $tab = Input::get('tab');
        return Redirect::to('/user')->with('inputs', $inputs)->with('searcheduser', $searcheduser)->with('tab', $tab);
    }

    public function activeuser() {
        $userid = Input::get('userid');
        $checkstatus = ProfileModel::where('ID', $userid)->where('status', 1)->count();
        if ($checkstatus == 1) {
            ///Inactive process /////
            $updatedetails['status'] = 0;
            $affectedRows = ProfileModel::where('ID', $userid)->update($updatedetails);
            if ($affectedRows)
                return 0;
        }
        else {
            ///Active process /////
            $updatedetails['status'] = 1;
            $affectedRows = ProfileModel::where('ID', $userid)->update($updatedetails);
            if ($affectedRows)
                return 1;
        }
    }

    public function userdelete() {
        $userid = $_GET['userid'];
        /// Comment delete //////////
        $cmtcnt = commentModel::select('id')->where('userid', $userid)->get();
        for ($i = 0; $i < count($cmtcnt); $i++) {

            $replycnt = replycommentModel::where('comment_id', $cmtcnt[$i]['id'])->get()->count();
            if ($replycnt)
                replycommentModel::where('comment_id', $cmtcnt[$i]['id'])->delete();
        }
        /// REply comt delete /////
        $replycmtcnt = replycommentModel::where('user_id', $userid)->get()->count();
        if ($replycmtcnt)
            replycommentModel::where('user_id', $userid)->delete();

        /// Contest delete/////
        $contestcnt = contestModel::select('ID')->where('createdby', $userid)->get();
        for ($k = 0; $k < count($contestcnt); $k++) {

            $conpartdet = contestparticipantModel::select('ID')->where('contest_id', $contestcnt[$k]['ID'])->get();
            for ($p = 0; $p < count($conpartdet); $p++) {
                $cmtcnt = commentModel::where('contest_participant_id', $conpartdet[$p]['ID'])->delete();
                votingModel::where('contest_participant_id', $conpartdet[$p]['ID'])->delete();
            }

            $contestintrdlet = contestinterestModel::where('contest_id', $contestcnt[$k]['ID'])->delete();
            invitefollowerforcontestModel::where('contest_id', $contestcnt[$k]['ID'])->delete();
            invitegroupforcontestModel::where('contest_id', $contestcnt[$k]['ID'])->delete();
            leaderboardModel::where('contest_id', $contestcnt[$k]['ID'])->delete();
            privateusercontestModel::where('contest_id', $contestcnt[$k]['ID'])->delete();
            contestModel::where('createdby', $userid)->delete();
            contestparticipantModel::where('contest_id', $contestcnt[$k]['ID'])->delete();
        }

        ///Contestparticipant delete ///////////
        $contestparticipant = contestparticipantModel::where('user_id', $userid)->get();
        for ($p = 0; $p < count($contestparticipant); $p++) {
            $cmtcnt = commentModel::where('contest_participant_id', $contestparticipant[$p]['ID'])->delete();
            votingModel::where('contest_participant_id', $contestparticipant[$p]['ID'])->delete();
        }
        ///////////////Group //////////////////
        $group = groupModel::select('ID')->where('createdby', $userid)->get();
        if (count($group) > 0) {
            groupmemberModel::where('group_id', $group[0]['ID'])->delete();
            invitegroupforcontestModel::where('group_id', $group[0]['ID'])->delete();
            invitememberforgroupModel::where('group_id', $group[0]['ID'])->delete();
        }
        contestparticipantModel::where('user_id', $userid)->delete();
        followModel::where('userid', $userid)->delete();
        followModel::where('followerid', $userid)->delete();
        userinterestModel::where('user_id', $userid)->delete();
        votingModel::where('user_id', $userid)->delete();
        invitememberforgroupModel::where('user_id', $userid)->delete();
        groupmemberModel::where('user_id', $userid)->delete();
        ProfileModel::where('ID', $userid)->delete();
        $er_data['message'] = 'User details deleted successfully';
        return Redirect::to('user')->with('er_data', $er_data);
    }

    public function viewusercontest($data = null) {

        return View::make('admin/contestlist')->with('usercontestlist', $data)->with('searchkey', '');
    }

    public function managecontest() {
        return View::make('admin/contestlist')->with('usercontestlist', '')->with('searchkey', '');
    }

    public function contestsearch() {
        $searchkey = Input::get('tsearch2');
        return View::make('admin/contestlist')->with('usercontestlist', '')->with('searchkey', $searchkey);
    }

    public function activecontest() {
        $contestid = $_GET['contestid'];
        $checkstatus = contestModel::where('ID', $contestid)->where('status', 1)->count();

        $contestdetailsformail = contestModel::select('user.firstname', 'user.lastname', 'user.username', 'contest.contest_name', 'contest.contesttype', 'user.email', 'contest.themephoto')->LeftJoin('user', 'user.ID', '=', 'contest.createdby')->where('contest.ID', $contestid)->first();

        $contestname = $contestdetailsformail->contest_name;
        $contesttype = $contestdetailsformail->contesttype;

        if ($contestdetailsformail->firstname != '')
            $name = $contestdetailsformail->firstname . ' ' . $contestdetailsformail->lastname;
        else
            $name = $contestdetailsformail->username;
        $contest_id = $contestid;
        $email = $contestdetailsformail->email;

        $contestimage = $contestdetailsformail->themephoto;


        if ($checkstatus == 1) {
            ///Inactive process /////
            $updatedetails['status'] = 0;
            $affectedRows = contestModel::where('ID', $contestid)->update($updatedetails);
            $details = "Your contest " . $contestname . " is deactivated by admin.";
            $this->contestchangesmaildelete($email, $name, $contestname, $contest_id, $contestimage, $details);
            if ($affectedRows)
                return 0;
        }
        else {
            ///Active process /////
            $updatedetails['status'] = 1;
            $affectedRows = contestModel::where('ID', $contestid)->update($updatedetails);
            $details = "Your contest " . $contestname . " is activated by admin.";
            $this->contestchangesmail($email, $name, $contestname, $contest_id, $contestimage, $details);
            if ($affectedRows)
                return 1;
        }
    }

    public function contestparticipantlist($data = null) {

        return View::make('admin/contestparticipantlist')->with('contest_id', $data);
    }

    public function removecontestparticipant() {
        $contestparticipantid = $_GET['contestparticipantid'];
        $contest_id = $_GET['contest_id'];
        $comment = commentModel::select('id')->where('contest_participant_id', $contestparticipantid)->get();
        for ($i = 0; $i < count($comment); $i++) {
            $replycmt = replycommentModel::where('comment_id', $comment[$i]['id'])->get()->count();
            if ($replycmt)
                replycommentModel::where('comment_id', $comment[$i]['id'])->delete();
        }
        if (count($comment))
            commentModel::select('id')->where('contest_participant_id', $contestparticipantid)->delete();

        $votingcnt = votingModel::where('contest_participant_id', $contestparticipantid)->get()->count();
        if ($votingcnt)
            votingModel::where('contest_participant_id', $contestparticipantid)->delete();
        $participantid = contestparticipantModel::where('ID', $contestparticipantid)->get()->count();
        if ($participantid) {

            //// REmove in leaderboard /////
            $participantidforleaderboard = contestparticipantModel::where('ID', $contestparticipantid)->get()->first();
            $participant_userid = $participantidforleaderboard->user_id;
            $participant_contest_id = $participantidforleaderboard->contest_id;

            $leaderboard = leaderboardModel::where('contest_id', $participant_contest_id)->where('user_id', $participant_userid)->get()->count();
            if ($leaderboard > 0)
                leaderboardModel::where('contest_id', $participant_contest_id)->where('user_id', $participant_userid)->delete();

            $delete = contestparticipantModel::where('ID', $contestparticipantid)->delete();
            if ($delete)
                $er_data['message'] = 'Participated contest removed successfully';
            else
                $er_data['message'] = 'NO data available';

            return Redirect::to('contestparticipantlist/' . $contest_id)->with('er_data', $er_data);
        }
    }

    public function adminviewcontest($data = null) {

        return Redirect::to('contest_info/' . $data)->with('tab', 'gallery');
    }

    public function contestdelete() {
        $data = Input::get('contestid');
        $searchkey = Input::get('searchkey');
        /// Contest details /////
        $contestdetailsformail = contestModel::select('user.firstname', 'user.lastname', 'user.username', 'contest.contest_name', 'contest.contesttype', 'user.email', 'contest.themephoto')->LeftJoin('user', 'user.ID', '=', 'contest.createdby')->where('contest.ID', $data)->first();
        $contestname = $contestdetailsformail->contest_name;
        $contesttype = $contestdetailsformail->contesttype;

        if ($contestdetailsformail->firstname != '')
            $name = $contestdetailsformail->firstname . ' ' . $contestdetailsformail->lastname;
        else
            $name = $contestdetailsformail->username;
        $contest_id = $data;
        $email = $contestdetailsformail->email;
        $details = "Your contest " . $contestname . " is deleted by admin.";
        $contestimage = $contestdetailsformail->themephoto;


        $contestdetails = contestparticipantModel::where('contest_id', $data)->get();
        for ($i = 0; $i < count($contestdetails); $i++) {

            $contestparticipantid = $contestdetails[$i]['ID'];

            $comment = commentModel::select('id')->where('contest_participant_id', $contestparticipantid)->get();
            for ($i = 0; $i < count($comment); $i++) {
                $replycmt = replycommentModel::where('comment_id', $comment[$i]['id'])->get()->count();
                if ($replycmt)
                    replycommentModel::where('comment_id', $comment[$i]['id'])->delete();
            }
            if (count($comment))
                commentModel::select('id')->where('contest_participant_id', $contestparticipantid)->delete();

            $votingcnt = votingModel::where('contest_participant_id', $contestparticipantid)->get()->count();
            if ($votingcnt)
                votingModel::where('contest_participant_id', $contestparticipantid)->delete();

            $participantid = contestparticipantModel::where('ID', $contestparticipantid)->get()->count();
            if ($participantid)
                $delete = contestparticipantModel::where('ID', $contestparticipantid)->delete();
        }

        $contestcatgory = contestinterestModel::where('contest_id', $data)->count();
        if ($contestcatgory)
            contestinterestModel::where('contest_id', $data)->delete();

        $deleteinvitecontest = invitegroupforcontestModel::where('contest_id', $data)->count();
        if ($deleteinvitecontest)
            invitegroupforcontestModel::where('contest_id', $data)->delete();

        $deletefollowercontest = invitefollowerforcontestModel::where('contest_id', $data)->count();
        if ($deletefollowercontest)
            invitefollowerforcontestModel::where('contest_id', $data)->delete();

        $privatecontestdelete = privateusercontestModel::where('contest_id', $data)->count();
        if ($privatecontestdelete)
            privateusercontestModel::where('contest_id', $data)->delete();



        $deletecontest = contestModel::where('ID', $data)->delete();
        if ($deletecontest) {

            $this->contestchangesmaildelete($email, $name, $contestname, $contest_id, $contestimage, $details);
        }
        $er_data['message'] = 'Contest removed successfully';

        return Redirect::to('managecontest')->with('er_data', $er_data)->with('usercontestlist', '')->with('searchkey', $searchkey);
    }

    public function reportlist() {
        return View::make('admin/reportlist');
    }

    public function takeactionforreport() {
        $contestparticipantid = Input::get('contestparticipantid');
        $contest_id = Input::get('contest_id');
        $contest_partipant_id = Input::get('contest_partipant_id');
        if ($contestparticipantid != '') {
            $comment = commentModel::select('id')->where('contest_participant_id', $contestparticipantid)->get();
            for ($i = 0; $i < count($comment); $i++) {
                $replycmt = replycommentModel::where('comment_id', $comment[$i]['id'])->get()->count();
                if ($replycmt)
                    replycommentModel::where('comment_id', $comment[$i]['id'])->delete();
            }
            if (count($comment))
                commentModel::select('id')->where('contest_participant_id', $contestparticipantid)->delete();

            $votingcnt = votingModel::where('contest_participant_id', $contestparticipantid)->get()->count();
            if ($votingcnt)
                votingModel::where('contest_participant_id', $contestparticipantid)->delete();

            $participantid = contestparticipantModel::where('ID', $contestparticipantid)->get()->count();
            if ($participantid) {
                $delete = contestparticipantModel::where('ID', $contestparticipantid)->delete();
                $data['message'] = 'That Contest participant details removed successfully';
            } else {
                $data['message'] = 'That Contest participant details already removed';
            }

            $reportflagcnt = reportflagModel::where('contest_participant_id', $contestparticipantid)->get()->count();
            $inputdetails['action_taken'] = 1;
            if ($reportflagcnt) {
                reportflagModel::where('contest_participant_id', $contestparticipantid)->update($inputdetails);
            }
            if ($contest_partipant_id == "") {
                return Redirect::to('contest_info/' . $contest_id)->with('data', $data)->with('tab', 'gallery');
            } else {
                $data['message'] = 'Action taken for this report';
                return Redirect::to('reportlist')->with('data', $data);
            }
        }
    }

    public function withoutdeletstakeaction() {

        $contestparticipantid = Input::get('contestparticipantid');
        $reportflagid = Input::get('reportflagid');
        $reportflagcnt = reportflagModel::where('ID', $reportflagid)->get()->count();
        $inputdetails['action_taken'] = 1;
        if ($reportflagcnt) {
            reportflagModel::where('ID', $reportflagid)->update($inputdetails);
            $data['message'] = 'Action taken for this report';
            return Redirect::to('reportlist')->with('data', $data);
        }
    }

    public function regenerateleaderboard() {
        $contestid = Input::get('contestid');
        if ($contestid) {
            $iputdetails['leaderboard'] = 0;
            $leaderboard = contestModel::where('ID', $contestid)->update($iputdetails);

            $leaderboardcnt = leaderboardModel::where('contest_id', $contestid)->get()->count();
            if ($leaderboardcnt > 0)
                leaderboardModel::where('contest_id', $contestid)->delete();

            return 1;
        }
    }

    ///// Admin send to contest owner ///////////

    public function contestchangesmail($email, $name, $contestname, $contest_id, $contestimage, $details) {
        Mail::send([], array('email' => $email, 'name' => $name, 'contestname' => $contestname, 'contest_id' => $contest_id, 'contestimage' => $contestimage, 'details' => $details), function($message) use ($email, $name, $contestname, $contest_id, $contestimage, $details) {
            $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
							<body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL::to('contest_info/' . $contest_id) . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px; line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear ' . $name . '</div>
								
								<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">' . $details . ' </div>
								
								<div style="margin:10px;"><a href="' . URL::to('contest_info/' . $contest_id) . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
								</div>

								
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
							</body>';

            $message->setBody($mail_body, 'text/html');
            $message->to($email);
            $message->subject('Dingdatt-contest information');
        });
    }

    public function contestchangesmaildelete($email, $name, $contestname, $contest_id, $contestimage, $details) {


        Mail::send([], array('email' => $email, 'name' => $name, 'contestname' => $contestname, 'contest_id' => $contest_id, 'contestimage' => $contestimage, 'details' => $details), function($message) use ($email, $name, $contestname, $contest_id, $contestimage, $details) {
            $mail_body = '<style>.thank{text-align:center; width:100%;}
					.but_color{color:#ffffff;}
					.cont_name{width:100px;}
					.cont_value{width:500px;}
					
					</style>
			    <body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

				<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
					&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px; line-height:20px;" /></a>&nbsp;&nbsp;
				</div>
				<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
					<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear ' . $name . '</div>
					
					<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">' . $details . ' </div>
					
					<div style="margin:10px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
					</div>

					
				</div>
											
				<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
				<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
				</body>';

            $message->setBody($mail_body, 'text/html');
            $message->to($email);
            $message->subject('Dingdatt-contest information');
        });
    }
}
?>