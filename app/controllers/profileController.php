<?php

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

        return View::make('user/profile/otherprofile')->with('profileid', $data)->with('memberlist', $memberlist);
    }

    public function otherprofileresponsive() {
        $tabname = $_GET['tabname'];
        $profile_id = $_GET['profile_id'];
        return Redirect::to("other_profile/" . $profile_id)->with('tab', $tabname);
    }

}

?>