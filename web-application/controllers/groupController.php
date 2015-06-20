<?php

class groupController extends BaseController {

    public function showgroup() {
        $searchedgroup = '';
        return View::make('group/group')->with('searchedgroup', $searchedgroup);
    }

    public function showgroupwithsearch() {
        $inputs = Input::get();
        $searchedgroup = Input::get('tsearch2');
        $tab = Input::get('tab');
        Session::put('tab', $tab);
        return Redirect::to("group")->with('inputs', $inputs)->with('searchedgroup', $searchedgroup)->with('tab', $tab);
    }

    public function creategroupinweb() {
        $userid = Auth::user()->ID;
        $curdate = date('Y-m-d h:i:s');
        $inputdetails['groupname'] = Input::get('groupname');
        $inputdetails['createdby'] = $userid;
        $inputdetails['createddate'] = $curdate;
        $inputdetails['grouptype'] = Input::get('grouptype');
        $inputdetails['status'] = 1;

        if (Input::file('groupimage')) {
            $inputdetails['groupimage'] = Input::file('groupimage');
            $destinationPath = 'public/assets/upload/group';
            $filename = Input::file('groupimage')->getClientOriginalName();
            $Image = str_random(8) . '_' . $filename;
            $inputdetails['groupimage'] = $Image;
        }

        $lantyp = Session::get('language');
        if ($lantyp == "")
            $lantyp = "value_en";


        $validation = Validator::make($inputdetails, groupModel::$rules);
        if ($validation->passes()) {
            if (Input::file('groupimage')) {
                $file = Input::file('groupimage');
                $uploadSuccess = $file->move($destinationPath, $Image);
            }
            $savegroup = groupModel::create($inputdetails);
            $group_id = groupModel::max('id');
            //// Group Owner add in the group member table////
            $inputgroupmember['group_id'] = $group_id;
            $inputgroupmember['user_id'] = $userid;
            $inputgroupmember['createddate'] = $curdate;
            $groupmember = groupmemberModel::create($inputgroupmember);
            ///if Followers or following checked And the group Private //////////
            ////////////
            if ($inputdetails['grouptype'] == 'private') {
                //return Input::get('follower');
                if (Input::get('follower'))
                    $follower = followModel::select('followerid as id')->where('userid', $userid)->get();
                if (Input::get('following'))
                    $following = followModel::select('userid as id')->where('followerid', $userid)->get();




                if (Input::get('follower') || Input::get('following')) {
                    if (Input::get('follower')) {

                        for ($i = 0; $i < count($follower); $i++) {
                            $id[$i] = $follower[$i]['id'];
                        }
                    }
                    if (Input::get('following')) {
                        for ($j = 0; $j < count($following); $j++) {
                            $id1[$j] = $following[$j]['id'];
                        }
                    }


                    if (Input::get('follower') != 0 && count($follower) >= 1 && Input::get('following') != 0 && count($following) >= 1) {
                        $id = array_values(array_unique(array_merge($id, $id1)));
                    } elseif (Input::get('following') != 0 && count($following) >= 1) {
                        $id = $id1;
                    } elseif (Input::get('follower') != 0 && count($follower) >= 1) {
                        $id = $id;
                    } else {
                        $id = 0;
                    }


                    $inviteinputdetails['inviteddate'] = $curdate;
                    $inviteinputdetails['group_id'] = $group_id;

                    if ($id != 0) {
                        for ($i = 0; $i < count($id); $i++) {

                            $inviteinputdetails['user_id'] = $id[$i];
                            $inviteinputdetails['invitetype'] = 'm';
                            $invite = invitememberforgroupModel::create($inviteinputdetails);
                            if ($invite) {
                                // HERE SET THE NOTIFIATION //
                            }
                        }
                    }
                }
            }

            ///////////

            if ($savegroup) {

                $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', ['Group Details Added successfully'])->get()->toArray();

                foreach ($languageDetails as $key => $val) {
                    $er_data['messagesave'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                }
                return Redirect::to("group")->withInput()->with('er_data', $er_data)->with('tab', 'creategroup');
            }
        } else {

            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', [$validation->messages()->first('groupname')])->get()->toArray();

            foreach ($languageDetails as $key => $val) {
                $er_data['groupname'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
            }
            return Redirect::to("group")->with('er_data', $er_data)->with('tab', 'creategroup');
        }
    }

    public function groupdelete() {
        $data = $_GET['groupid'];


        $groupmemberdelete = groupmemberModel::where('group_id', $data)->delete();
        $group = groupModel::where('ID', $data)->get()->count();
        if ($group > 0) {

            $invited = invitegroupforcontestModel::where('group_id', $data)->get()->count();
            if ($invited != 0)
                invitegroupforcontestModel::where('group_id', $data)->delete();

            $invitegroupmember = invitememberforgroupModel::where('group_id', $data)->get()->count();
            if ($invitegroupmember != 0) {
                $deletegroup = invitememberforgroupModel::where('group_id', $data)->delete();
            }


            $savegroup = groupModel::select('grouptype', 'groupname', 'createdby')->where('ID', $data)->get();

            $groupowneruserid = $savegroup[0]['createdby'];
            $getcreateduserdetails = ProfileModel::select('email', 'firstname', 'lastname', 'username')->where('ID', $groupowneruserid)->get();
            $email = $getcreateduserdetails[0]['email'];
            if ($getcreateduserdetails[0]['firstname'] != '') {
                $groupownername = $getcreateduserdetails[0]['firstname'] . ' ' . $getcreateduserdetails[0]['lastname'];
            } else {
                $groupownername = $getcreateduserdetails[0]['username'];
            }
            $groupname = $savegroup[0]['groupname'];
            ///// Group member details ///////////

            $groupdetails = '<div styel"float:left;">
						Your group "' . $groupname . '" is deleted by admin.
					</div>';

            if (Auth::user()->ID == 1) {
                ///Admin sent the notification mail to group Owner ////
                $this->adminmailtogroupownerdelete($email, $groupownername, $groupname, $groupownername, $data, $groupdetails);
            }

            groupModel::where('ID', $data)->delete();

            $lantyp = Session::get('language');
            if ($lantyp == "")
                $lantyp = "value_en";

            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', ['Group Deleted Successfully'])->get()->toArray();

            foreach ($languageDetails as $key => $val) {

                if (in_array($val['ctrlCaptionId'], ['alert_groupdelete']))
                    $er_data['message'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
            }

            return Redirect::to('group')->with('tab', 'grouplist')->with('er_data', $er_data);
        }
    }

    public function viewgroupmember($data = NULL) {

        return View::make('group/groupmember')->with('group_id', $data)->with('showjoinbtn', 'yes')->with('contest_id', 'no');
    }

    public function viewgroupmemberfrominvite() {
        $groupid = $_GET['groupid'];
        $contest_id = $_GET['contest_id'];
        return View::make('group/groupmember')->with('group_id', $groupid)->with('showjoinbtn', 'no')->with('contest_id', $contest_id);
    }

    public function groupmemberdelete() {
        $groupmemberid = $_GET['groupmemberid'];
        $data = $_GET['group_id'];

        $groupmemberdeletedetails = groupmemberModel::select('user_id')->where('id', $groupmemberid)->first();

        $invited = invitegroupforcontestModel::where('group_id', $data)->where('user_id', $groupmemberdeletedetails['user_id'])->count();
        if ($invited != 0)
            invitegroupforcontestModel::where('group_id', $data)->where('user_id', $groupmemberdeletedetails['user_id'])->delete();

        $groupmemberdelete = groupmemberModel::where('id', $groupmemberid)->delete();
        if ($groupmemberdelete) {
            /*             * ** Admin process********** */
            if (Auth::user()->ID == 1) {

                $savegroup = groupModel::select('grouptype', 'groupname', 'createdby')->where('ID', $data)->get();

                if ($savegroup[0]['grouptype'] == 'private') {
                    $groupowneruserid = $savegroup[0]['createdby'];
                    $getcreateduserdetails = ProfileModel::select('email', 'firstname', 'lastname', 'username')->where('ID', $groupowneruserid)->get();
                    $email = $getcreateduserdetails[0]['email'];
                    if ($getcreateduserdetails[0]['firstname'] != '') {
                        $groupownername = $getcreateduserdetails[0]['firstname'] . ' ' . $getcreateduserdetails[0]['lastname'];
                    } else {
                        $groupownername = $getcreateduserdetails[0]['username'];
                    }
                    $groupname = $savegroup[0]['groupname'];
                    ///// Group member details ///////////


                    $groupmemberdetails = ProfileModel::select('email', 'firstname', 'lastname', 'username')->where('ID', $groupmemberdeletedetails['user_id'])->get();
                    if ($groupmemberdetails[0]['firstname'] != '') {
                        $membername = $groupmemberdetails[0]['firstname'] . ' ' . $groupmemberdetails[0]['firstname'];
                    } else {
                        $membername = $groupmemberdetails[0]['username'];
                    }
                }
            }

            $lantyp = Session::get('language');
            if ($lantyp == "")
                $lantyp = "value_en";

            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', ['Group Member Deleted Successfully'])->get()->toArray();

            foreach ($languageDetails as $key => $val) {

                if (in_array($val['ctrlCaptionId'], ['alert_group_member_delete']))
                    $er_data['memberdelete'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
            }

            return Redirect::to('viewgroupmember/' . $data)->with('er_data', $er_data);
        }
    }

    public function editgroup($data = Null) {

        return View::make('group/editgroup')->with('groupid', $data);
    }

    public function updategroup($data = Null) {

        $inputdetails = Input::except(array('client_login', 'groupimage'));
        $updaterules = array(
            'groupname' => 'required',
                );

        $validation = Validator::make($inputdetails, $updaterules);
        if ($validation->passes()) {
            if (Input::file('groupimage')) {
                $inputdetails['groupimage'] = Input::file('groupimage');
                $destinationPath = 'public/assets/upload/group';
                $filename = Input::file('groupimage')->getClientOriginalName();
                $Image = str_random(8) . '_' . $filename;
                $inputdetails['groupimage'] = $Image;
                $file = Input::file('groupimage');
                $uploadSuccess = $file->move($destinationPath, $Image);
            }

            $affectedRows = groupModel::where('ID', $data)->update($inputdetails);
            if ($affectedRows) {

                /// Admin process ////
                if (Auth::user()->ID == 1) {
                    $savegroup = groupModel::select('grouptype', 'groupname', 'createdby', 'status', 'groupimage')->where('ID', $data)->get();
                    $groupowneruserid = $savegroup[0]['createdby'];
                    $getcreateduserdetails = ProfileModel::select('email', 'firstname', 'lastname', 'username')->where('ID', $groupowneruserid)->get();
                    $email = $getcreateduserdetails[0]['email'];
                    if ($getcreateduserdetails[0]['firstname'] != '') {
                        $groupownername = $getcreateduserdetails[0]['firstname'] . ' ' . $getcreateduserdetails[0]['lastname'];
                    } else {
                        $groupownername = $getcreateduserdetails[0]['username'];
                    }
                    $groupname = $savegroup[0]['groupname'];
                    ///// Group member details ///////////
                    if ($savegroup[0]['status'] == 1) {
                        $status = "Active";
                    } else {
                        $status = "Inactive";
                    }

                    $groupdetails = '<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your group <b>"' . $groupname . '"</b> is edited by admin  </div><div styel"float:left;">
						<table width="180" height="95" border="0" style="margin-bottom:10px;float:left;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group Name:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">' . $groupname . '</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group Type:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">' . $savegroup[0]['grouptype'] . '</td>
					  </tr>
					   <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group status </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">' . $status . '</td>
					  </tr>
					  
					  <tr style="border-radius:6px;-webkit-border-radius: 5px; -moz-border-radius: 5px;">
						<td colspan="2" style="vertical-align: text-middle;" >
							<span><a href="' . URL::to('viewgroupmember/' . $groupowneruserid) . '" style="text-decoration:none;><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '/assets/inner/images/vist_dingdatt.png" width="120" height="30" /></a></span>
						</td>
					  </tr>	
					</table>
					</div>';


                    $this->adminmailtogroupowner($email, $groupownername, $groupname, $groupownername, $data, $groupdetails);
                }
            }

            $lantyp = Session::get('language');
            if ($lantyp == "")
                $lantyp = "value_en";
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', ['Group Details Updated Successfully'])->get()->toArray();
            $er_data['message'] = $languageDetails[0][$lantyp];
            return Redirect::to('group')->with('tab', 'grouplist')->with('er_data', $er_data);
        }
        else {
            return Redirect::to('editgroup/' . $data);
        }
    }

    public function joinintogroup($data = Null) {
        $authuserid = Auth::user()->ID;
        //return $data;
        $alreadymember = groupmemberModel::where('group_id', $data)->where('user_id', $authuserid)->get()->count();
        if ($alreadymember) {
            $er_data['message'] = 'You are Already Member of this Group';
            return Redirect::to('viewgroupmember/' . $data)->with('er_data', $er_data);
        } else {
            $curdate = date('Y-m-d h:i:s');
            $grouptype = groupModel::select('grouptype', 'createdby')->where('ID', $data)->get();
            if ($grouptype[0]['grouptype'] == 'open') {
                $inputgroupmember['group_id'] = $data;
                $inputgroupmember['user_id'] = $authuserid;
                $inputgroupmember['createddate'] = $curdate;
                $groupmember = groupmemberModel::create($inputgroupmember);
                $er_data['message'] = 'You have joined the group';
                return Redirect::to('viewgroupmember/' . $data)->with('er_data', $er_data);
            } else {

                $checkalreadyinvited = invitememberforgroupModel::where('group_id', $data)->where('invitetype', 'u')->get()->count();
                if ($checkalreadyinvited == 0) {
                    $inviteinputdetails['inviteddate'] = $curdate;
                    $inviteinputdetails['group_id'] = $data;
                    $inviteinputdetails['user_id'] = $authuserid;
                    $inviteinputdetails['invitetype'] = 'u';
                    $invite = invitememberforgroupModel::create($inviteinputdetails);
                    if ($invite) {
                        // HERE SET THE NOTIFIATION- send to group admin //
                        $er_data['message'] = 'Your Request Sent to the Group Admin';
                        return Redirect::to('viewgroupmember/' . $data)->with('er_data', $er_data);
                    }
                } else {
                    $er_data['message'] = 'You are already sent request to the group admin';
                    return Redirect::to('viewgroupmember/' . $data)->with('er_data', $er_data);
                }
            }
        }
    }

    public function addthismembertogroup($data = null) {
        $group_id = $_GET['group_id'];
        $userid = $_GET['userid'];
        $savegroup = groupModel::select('grouptype', 'groupname', 'createdby')->where('ID', $group_id)->get();
        $curdate = date('Y-m-d h:i:s');

        $inputdetails['user_id'] = $userid;
        $inputdetails['group_id'] = $group_id;
        $inputdetails['createddate'] = $curdate;
        $inputdetails['invitetype'] = 'm';
        if ($savegroup[0]['grouptype'] == 'private') {
            $already = invitememberforgroupModel::where('user_id', $userid)->where('group_id', $group_id)->count();
            if ($already == 0) {
                $invite = invitememberforgroupModel::create($inputdetails);
                if ($invite) {
                    $groupowneruserid = $savegroup[0]['createdby'];
                    $getcreateduserdetails = ProfileModel::select('email', 'firstname', 'lastname', 'username')->where('ID', $groupowneruserid)->get();
                    $email = $getcreateduserdetails[0]['email'];
                    if ($getcreateduserdetails[0]['firstname'] != '') {
                        $groupownername = $getcreateduserdetails[0]['firstname'] . ' ' . $getcreateduserdetails[0]['lastname'];
                    } else {
                        $groupownername = $getcreateduserdetails[0]['username'];
                    }
                    $groupname = $savegroup[0]['groupname'];
                    ///// Group member details ///////////


                    $groupmemberdetails = ProfileModel::select('email', 'firstname', 'lastname', 'username')->where('ID', $userid)->get();
                    if ($groupmemberdetails[0]['firstname'] != '') {
                        $membername = $groupmemberdetails[0]['firstname'] . ' ' . $groupmemberdetails[0]['firstname'];
                    } else {
                        $membername = $groupmemberdetails[0]['username'];
                    }


                    if (Auth::user()->ID == 1) {
                        ///Admin sent the notification mail to group Owner ////
                        //$this->adminmailtogroupowner($email,$groupownername,$groupname,$membername,$group_id);
                    }

                    /* Here Set the Notification for send to Group member */
                    $er_data['message'] = 'Your request sent to the group member';
                    return Redirect::to('viewgroupmember/' . $group_id)->with('er_data', $er_data);
                }
            } else {

                $er_data['message'] = 'Request already sent to member';
                return Redirect::to('viewgroupmember/' . $group_id)->with('er_data', $er_data);
            }
        } else {
            $savegroupmembers = groupmemberModel::create($inputdetails);
            $er_data['message'] = 'You have added this member to group';
            return Redirect::to('viewgroupmember/' . $group_id)->with('er_data', $er_data);
        }
    }

    public function accepttherequest($data = null) {
        $accepttype = $_GET['accepttype'];
        return View::make('group/privategroupaccept')->with('accepttype', $accepttype);
    }

    public function groupresponsive() {
        $tabname = $_GET['tabname'];
        return Redirect::to("group")->with('tab', $tabname);
    }

    public function exitgroup() {

        $groupid = $_GET['groupid'];
        $groupmeberuserid = $_GET['groupmeberuserid'];
        $removemember = groupmemberModel::where('group_id', $groupid)->where('user_id', $groupmeberuserid)->delete();
        return Redirect::to('viewgroupmember/' . $groupid)->with('group_id', $groupid)->with('showjoinbtn', 'yes')->with('contest_id', 'no')->with('Message', 'You are exit from that group');
    }

    public function activegroup() {
        $groupid = $_GET['groupid'];
        $checkstatus = groupModel::where('ID', $groupid)->where('status', 1)->count();




        $savegroup = groupModel::select('grouptype', 'groupname', 'createdby', 'grouptype', 'status', 'groupimage')->where('ID', $groupid)->get();
        $groupowneruserid = $savegroup[0]['createdby'];
        $getcreateduserdetails = ProfileModel::select('email', 'firstname', 'lastname', 'username')->where('ID', $groupowneruserid)->get();
        $email = $getcreateduserdetails[0]['email'];
        if ($getcreateduserdetails[0]['firstname'] != '') {
            $groupownername = $getcreateduserdetails[0]['firstname'] . ' ' . $getcreateduserdetails[0]['lastname'];
        } else {
            $groupownername = $getcreateduserdetails[0]['username'];
        }
        $groupname = $savegroup[0]['groupname'];
        ///// Group member details ///////////

        if ($savegroup[0]['status'] == 1) {
            $status = "Active";
        } else {
            $status = "Inactive";
        }

        $groupdetailsactive = '<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your group <b>"' . $groupname . '"</b> is activated by admin  </div><div styel"float:left;">
						<table width="180" height="95" border="0" style="margin-bottom:10px;float:left;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group Name:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">' . $groupname . '</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group Type:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">' . $savegroup[0]['grouptype'] . '</td>
					  </tr>
					   <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group status </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">Active</td>
					  </tr>
					  
					  <tr style="border-radius:6px;-webkit-border-radius: 5px; -moz-border-radius: 5px;">
						<td colspan="2" style="vertical-align: text-middle;" >
							<span><a href="' . URL::to('viewgroupmember/' . $groupowneruserid) . '" style="text-decoration:none;><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '/assets/inner/images/vist_dingdatt.png" width="120" height="30" /></a></span>
						</td>
					  </tr>	
					</table>
					</div>';

        $groupdetailsdeactive = '<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your group <b>"' . $groupname . '"</b> is deactivated by admin  </div><div styel"float:left;">
						<table width="180" height="95" border="0" style="margin-bottom:10px;float:left;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group Name:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">' . $groupname . '</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group Type:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">' . $savegroup[0]['grouptype'] . '</td>
					  </tr>
					   <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Group status </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">Inactive</td>
					  </tr>
					  
					  <tr style="border-radius:6px;-webkit-border-radius: 5px; -moz-border-radius: 5px;">
						<td colspan="2" style="vertical-align: text-middle;" >
							<span><a href="' . URL::to('viewgroupmember/' . $groupowneruserid) . '" style="text-decoration:none;><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '/assets/inner/images/vist_dingdatt.png" width="120" height="30" /></a></span>
						</td>
					  </tr>	
					</table>
					</div>';



        if ($checkstatus == 1) {
            ///Inactive process /////
            $updatedetails['status'] = 0;
            $affectedRows = groupModel::where('ID', $groupid)->update($updatedetails);
            //$details = "Your contest ".$contestname." is deactivated by admin.";
            $this->adminmailtogroupownerdelete($email, $groupownername, $groupname, $groupownername, $groupid, $groupdetailsdeactive);
            if ($affectedRows)
                return 0;
        }
        else {
            ///Active process /////
            $updatedetails['status'] = 1;
            $affectedRows = groupModel::where('ID', $groupid)->update($updatedetails);
            //$details = "Your contest ".$contestname." is activated by admin.";
            $this->adminmailtogroupowner($email, $groupownername, $groupname, $groupownername, $groupid, $groupdetailsactive);
            if ($affectedRows)
                return 1;
        }
    }

    public function sharegroup($data = null) {

        return View::make('group/sharegroup')->with('group_id', $data);
    }

    public function adminmailtogroupowner($email, $groupownername, $groupname, $membername, $group_id, $groupdetails) {

        Mail::send([], array('email' => $email, 'groupownername' => $groupownername, 'groupname' => $groupname, 'group_id' => $group_id, 'membername' => $membername, 'groupdetails' => $groupdetails), function($message) use ($email, $groupownername, $groupname, $group_id, $membername, $groupdetails) {
            $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL::to('viewgroupmember/' . $group_id) . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear ' . $groupownername . '</div>
								
								
								
								' . $groupdetails . '
								
								<div style="margin:10px;"><a href="' . URL::to('viewgroupmember/' . $group_id) . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
								</div>

								
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							
							</body>';

            $message->setBody($mail_body, 'text/html');
            $message->to($email);
            $message->subject('Dingdatt- Group');
        });
    }

    public function adminmailtogroupownerdelete($email, $groupownername, $groupname, $membername, $group_id, $groupdetails) {

        Mail::send([], array('email' => $email, 'groupownername' => $groupownername, 'groupname' => $groupname, 'group_id' => $group_id, 'membername' => $membername, 'groupdetails' => $groupdetails), function($message) use ($email, $groupownername, $groupname, $group_id, $membername, $groupdetails) {
            $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear ' . $groupownername . '</div>
								
								' . $groupdetails . '
								
								
								<div style="margin:10px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
								
								</div>

								
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							
							</body>';

            $message->setBody($mail_body, 'text/html');
            $message->to($email);
            $message->subject('Dingdatt- Group');
        });
    }

}

?>