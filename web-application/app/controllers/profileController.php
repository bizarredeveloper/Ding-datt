<?php

/* This controller search the user and also view the register member  */

class profileController extends BaseController {

    //home
    public function profile() {
        return View::make('user/register/userregister');
    }

    public function getprofile($data = NULL) {

        $editid = $data;
        $profileeditbyid = ProfileModel::where('ID', $editid)->get()->toArray();
        return View::make('user/register/userregister')->with('profileeditbyid', $profileeditbyid);
    }

    public function edit_profiles($data = null) {

        $user_id = $data;
        $profileData = ProfileModel::where('ID', $user_id)->first();
        $interestList = InterestCategoryModel::lists('Interest_name', 'Interest_id');
        $userInterest = userinterestModel::where('user_id', $user_id)->lists('interest_id');
        return View::make('user/register/edit_profile', array('profileData' => $profileData, 'interestList' => $interestList, 'userInterest' => (array) $userInterest))->with('user_id', $user_id);
    }

    public function profileupdate($data = Null) {
        //$data = Auth::user()->ID;
        $editid = $data;
        $GeneralData = Input::except(array('_token', 'status', 'pagename', 'profilepicture', 'profileimgedithidden', 'interest', 'update_profile'));

		
        $newimg = Input::file('profilepicture');
        if ($newimg != '') {
            $destinationPath = 'public/assets/upload/profile';
            $filename = Input::file('profilepicture')->getClientOriginalName();
            $Image = str_random(8) . '_' . $filename;
            $GeneralData['profilepicture'] = $Image;
            $uploadSuccess = Input::file('profilepicture')->move($destinationPath, $Image);
        }

		
		$authdate = Auth::user()->dateformat;
	   if($authdate=="dd/mm/yy"){ 
	   $cstart = explode("/",Input::get('dateofbirth'));
	   $GeneralData['dateofbirth'] = $cstart[2].'/'.$cstart[1].'/'.$cstart[0];
		}else{
		$cstart = explode("/",Input::get('dateofbirth'));
	   $GeneralData['dateofbirth'] = $cstart[2].'/'.$cstart[0].'/'.$cstart[1];
		}
		
        $interest = Input::get('interest');
        $interest_length = sizeof(Input::get('interest'));
        if ($interest_length > 0) {
            userinterestModel::whereNotIn('interest_id', $interest)->where('user_id', '=', $data)->delete();
            for ($i = 0; $i < $interest_length; $i++) {
                $interes['user_id'] = $data;
                $interes['interest_id'] = $interest[$i];
                $userInterest = userinterestModel::where('user_id', $data)->where('interest_id', $interest[$i])->lists('interest_id');
                if (count($userInterest) < 1)
                    userinterestModel::create($interes);
                unset($interes);
            }
        }
        else {
            userinterestModel::where('user_id', '=', $data)->delete();
        }

        $cur_date = date('Y-m-d');
        $updaterules = array(
            'username' => 'required|unique:user,username,' . $data,
            'password' => 'confirmed|min:5',
            'email' => 'required|email|unique:user,email,' . $data,
            'dateofbirth' => 'required',
            'timezone' => 'required|min:2',
                );
				
		
		
		//$updatedata['dateofbirth'] = Input::get('dateofbirth');
		
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
            $affectedRows = ProfileModel::where('ID', $data)->update($updatedata);
            $lantyp = Session::get('language');
            $labelname = ['txt_user_update_msg'];
            $languageDetails = languageModel::select($lantyp)->whereIn('ctrlCaptionId', $labelname)->get()->toArray();
            $user_id = $data;

            $profileData = ProfileModel::where('ID', $user_id)->first();
			
			$profileimg = $profileData->profilepicture;
			
			if($profileData->profilepicture!=''){ $profileimg = url().'/public/assets/upload/profile/'.$profileData->profilepicture;}else{ 
			$profileimg = url().'/assets/inner/images/avator.png'; 
			} 
			if($profileData->status==1) $stat ="Active"; else $stat ="In active";
			if($profileData->gender=="m") $gendr = "Male"; else if($profileData->gender=="f") $gendr="Female"; else $gendr="Other";
			$details='<table width="500" height="95" border="0" style="margin-bottom:10px;float:left;" border="1" cellspacing="0" cellpadding="0">
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">First Name:</span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$profileData->firstname.'</span></td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Last name:</span></td>
								<td  valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$profileData->lastname.'</span></td>
							  </tr>
							  
							   <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">User name:</span></td>
								<td  valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$profileData->username.'</span></td>
							  </tr>
							   
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Profile Image:</span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D"><img src="'.$profileimg.'" width="50" height="50" /></span></td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Email id: </span></td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">'.$profileData->email.'</span></td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Gender:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $gendr. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Date of birth:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->dateofbirth. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Face book page:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->facebookpage. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Twitter page:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->twitterpage. '</td>
							  </tr><tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Instagrampage page:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->instagrampage. '</td>
							  </tr><tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Hometown:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->hometown. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">School:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->school. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Maritalstatus:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->maritalstatus.'</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">No of kids:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->noofkids. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Status:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $stat. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">School:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->school. '</td>
							  </tr>
							  <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Timezone:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->timezone. '</td>
							  </tr>
							   <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Occupation:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->occupation. '</td>
							  </tr>
							   <tr>
								<td valign=top style="width:101.9pt;border:solid black 1.0pt;
  background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">Dateformat:</td>
								<td valign=top style="width:250pt;border:solid black 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt"><span style="font-size:11.0pt;font-family:"Calibri","sans-serif";
  color:#1F497D">' . $profileData->	dateformat. '</td>
							  </tr>
																
							</table>';
			$email = $profileData->email;
			
			if($profileData->firstname!=''){ $name =$profileData->firstname.''.$profileData->lastname; } else{ $name = $profileData->username; } 
			
			if(Auth::user()->ID==1 && $data!=1){ $this->admineditprofilemail($email,$name,$details,$user_id); }
			
            $interestList = InterestCategoryModel::lists('Interest_name', 'Interest_id');
            $userInterest = userinterestModel::where('user_id', $user_id)->lists('interest_id');
            $er_data['Message'] = "<span id='txt_user_update_msg'>" . $languageDetails[0][$lantyp] . "</span>";

            return Redirect::to('/edit_profile/' . $data)->with('er_data', $er_data)->with('user_id', $user_id);
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
            return Redirect::to('/edit_profile/' . $data)->with('er_data', $er_data)->with('old_data', $updatedata);
        }
    }

    public function other_profile($data = Null) {
        $memberlist = groupmemberModel::where('user_id', $data)->distinct()->lists('group_id');

		
       // return View::make('user/profile/otprofile')->with('profileid', $data)->with('memberlist', $memberlist);
		return View::make('user/profile/otherprofile')->with('profileid', $data)->with('memberlist', $memberlist);
    }

    public function otherprofileresponsive() {
        $tabname = $_GET['tabname'];
        $profile_id = $_GET['profile_id'];
        return Redirect::to("other_profile/" . $profile_id)->with('tab', $tabname);
    }
	    public function admineditprofilemail($email, $name, $details,$userid) {


        Mail::send([], array('email' => $email, 'name' => $name, 'details' => $details), function($message) use ($email,$name, $details,$userid) {
           
							
							$mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								</style>
							 <body style=" margin:0px auto; padding:0px;">

								<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
									&nbsp;&nbsp;<a href="'.URL::to('other_profile/'.$userid).'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
								</div>
								<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
									<div class="thank" style="font-size:13px; float:left;width:100%;margin-top:10px;">The following details are updated by admin</div>
									<div style="font-size:14px;	color: #D79600;	font-weight:bold;float:left;padding:10px 2px;width:100%;margin-bottom:5px;">Profile Details</div>
									
										'.$details.'
								</div>
															
								<div> <a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a></div>							
								<div style="font-size:12px; margin-top:10px;color: #5b5b5b; width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
								
								<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
								</body>';

            $message->setBody($mail_body, 'text/html');
            $message->to($email);
            $message->subject('Dingdatt-Admin edit your profile details');
        });
    }
	
}
?>