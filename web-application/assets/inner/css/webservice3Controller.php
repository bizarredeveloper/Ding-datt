<?php
class webservice3Controller extends BaseController
{  
public function mobilelogin()
{
        $user_name=Input::get('username');
		$password=Input::get('password');
		$LoginData_email = ['email' =>$user_name, 'password' => $password];
		$LoginData_user = ['username' =>$user_name, 'password' => $password];
		
		$LoginData['username'] = $user_name;
		$LoginData['password']=$password;
		
		$LoginData = Input::get();
				
		$validator = Validator::make($LoginData,User::$loginrule); 
		if ($validator->fails())
        {	
			
			if($validator->messages()->first('username')=="The username field is required when email is .")
			$username="The usename or email field is required.";
			else
			$username=$validator->messages()->first('username');			
			
			
			//return $validator->messages()->first();
			
			$Response = array(
                'success' => '0',
                'message' => "Required field is missing",				
            );
			$final=array("response"=>$Response);
			return json_encode($final);

		}
        elseif (array('email' => $user_name, 'password' => $password)||array('username' => $user_name, 'password' => $password))
		{  
		
		$userid =  ProfileModel::select('ID','password')->where('username',$user_name)->Orwhere('email',$user_name)->get()->count();
		if($userid)
		{
		
		$userid =  ProfileModel::select('ID','password')->where('username',$user_name)->Orwhere('email',$user_name)->get();
	    if (Hash::check($password, $userid[0]['password']))
		{
			    $userid = $userid[0]['ID'];	
				$update['gcm_id'] = Input::get('gcm_id');
				$update['device_id'] = Input::get('device_id');
				$update['device_type'] = Input::get('device_type');
				
				$affectedRows = profileModel::where('ID', $userid)->update($update); 
		
             
			$Response = array(
                'success' => '1',
                'message' => 'successfully Login',
				'userid' =>$userid
            );
			$final=array("response"=>$Response);
			return json_encode($final);
		  
		}
		else{ 
			
			$Response = array(
                'success' => '0',
                'message' => 'Invalid Password',				
            );
			$final=array("response"=>$Response);
			return json_encode($final);		
		}
           }
			else{ 
			$Response = array(
                'success' => '0',
                'message' => 'No user in this record',				
            );
			$final=array("response"=>$Response);
			return json_encode($final);	
			
			}
		}else
        {
		$Response = array(
                'success' => '0',
                'message' => "Invalid Username Or Password",
				
            );
			$final=array("response"=>$Response);
			return json_encode($final);
	
		}
		
}
public function mobileregister()
{
	
	$data = Input::except(array('_token','password','timezone','dateofbirth'));
	if(Input::get('dateofbirth')!='')
	{	 
	 $timezone = Input::get('timezone');
	$date = Input::get('dateofbirth');
	$data['dateofbirth'] = timezoneModel::convert($date, $timezone,'UTC', 'Y-m-d');
	
	}
	if(Input::get('password')!='')
	{
	$password = Hash::make(Input::get('password'));
	$data['password']  =  $password;  
	}
	$data['status'] = 'A';
	$data['createddate'] = date('Y-m-d h:i:s');
	$validator = Validator::make($data,ProfileModel::$rules);
	if ($validator->fails())
		{			
			$Response = array(
			'success' => '0',
			'message' => $validator->messages()->first(),
		);
		$final=array("response"=>$Response);
		return json_encode($final);
		}
	else
		{	
		$data['timezone']=Input::get('timezone'); 
	  $userregister = ProfileModel::create($data);
		$pass = Input::get('password');
		$email = Input::get('email'); 
		$username = Input::get('username');
	Mail::send([],
			array('pass' => $pass,'email' => $email,'username' => $username), function($message) use ($pass,$email,$username)
			{
			//$user = MailTemplate::find(1);
			//$mail_body = $user->MailContent;
			//$mail_body = str_replace("{password}", Session::get('sess_string'), $mail_body);
			$mail_body = "Dear {username},<br><br>Your DingDatt Registration successfully completed.Your Login details are<br><br>Email: {email}<br>Username: {username}<br>Password: {password} <br><br> Thank You, <br><br>Regards,<br>DingDatt";
			$mail_body = str_replace("{password}", $pass, $mail_body);
			$mail_body = str_replace("{username}", $username, $mail_body);
			$mail_body = str_replace("{email}",$email,$mail_body);
			$message->setBody($mail_body, 'text/html');
			$message->to($email);
			$message->subject('DingDatt Registration');
			});
		$Response = array(
			'success' => '1',
			'message' => 'Record added successfully'
		);
		$final=array("response"=>$Response);
		return json_encode($final);
	 
	}  
}
/////// Get User Details /////////////////
public function getuserprofile()
{
	$timezone = Input::get('timezone');
	$userid = Input::get('userid');
	$date = '2015-03-26 16:00:00';
    $format = 'Y-m-d H:i';
	//echo Carbon::now()->timezoneName;                            // America/Toronto
	
	$profile = ProfileModel::find($userid);
	$dateofbirth = $profile->dateofbirth;
	$profile->dateofbirth = timezoneModel::convert($dateofbirth, 'UTC',$timezone, 'Y-m-d');
	$createddate = $profile->createddate;
	$profile->createddate = timezoneModel::convert($createddate, 'UTC',$timezone, 'Y-m-d');
	
		$Response = array(
			'success' => '1',
			'message' => 'Profile Details',
		);
		$final=array("response"=>$Response, "profile"=>$profile);
		return json_encode($final);	
	
	//DB::select("SELECT user.*,DATE_FORMAT(CONVERT_TZ(`dateofbirth`,'+00:00','$timezone'),'%Y-%m-%d') as converteddateofbirth  FROM user WHERE ID =$userid");

}

public function editmyprofile()
{
    
	 $GeneralData= array_filter(Input::except(array('_token','passwordhidden','profilepicture','maritalstatus','interest_id','userid','timezone','dateofbirth')));
	 
	$timezone = Input::get('timezone');
	 
	$GeneralData['dateofbirth'] = timezoneModel::convert(Input::get('dateofbirth'), $timezone,'UTC', 'Y-m-d');
	
	 $newimg = Input::file('profilepicture');
	if($newimg!=''){ 
		$destinationPath = 'public/assets/upload/profile';
		$filename = Input::file('profilepicture')->getClientOriginalName();
		$Image = str_random(8).'_'.$filename;
		$GeneralData['profilepicture']= $Image;
		$uploadSuccess = Input::file('profilepicture')->move($destinationPath, $Image);
	}else{		
	$GeneralData['profilepicture']=Input::get('profileimgedithidden');
	}
		
	$data=Input::get('userid'); 
	$newpassword = Input::get('password');
	
	$updaterules = array(
					'username' 	=> 'required|unique:user,username,'.$data,
					'password'	=> 'confirmed:min:5',
					'email'     => 'required|email|unique:user,email,'.$data,				
                	);
	
	$validation  = Validator::make($GeneralData, $updaterules);
	if ($validation->passes()) 
        { 
		
	
	if($newpassword!=""){   $GeneralData['password'] = Hash::make(Input::get('password')); 	}

	unset($GeneralData["password_confirmation"]);

		$affectedRows = profileModel::where('ID', $data)->update($GeneralData); 
		$interest_id=Input::get('interest_id'); 
		$interestid= explode(',',$interest_id);
		$interestidcount = count($interestid);		
		$interest['user_id']=Input::get('userid');  
		$affectedRows = userinterestModel::where('user_id', $data)->delete();		
		for($i=0;$i<$interestidcount; $i++)
		{		 
		 $interest['interest_id']=$interestid[$i]; 
		 $validationinterest  = Validator::make($interest, userinterestModel::$rules);	
		if ($validationinterest->passes()) 
        {
		$userregister = userinterestModel::create($interest);		 
		}		
		}
		$Response = array(
			'success' => '1',
			'message' => 'Record Updated successfully'
		);
		
		return json_encode($Response);

        } else 
        {
		
		$Response = array(
			'success' => '0',
			'message' => $validation->messages()->first()
		 );
		$final=array("response"=>$Response);		 
        return json_encode($final);
        }	


}
public function createcontestmobile()
{
    
	 $inputdetails = Input::except(array('_token','themephoto','sponsor','sponsorphoto','sponsorname','interest_id'));
	 
	  $conteststartdate = Input::get('conteststartdate');
	  $timezone = Input::get('timezone');
	   
	   
	 $inputdetails['conteststartdate'] = timezoneModel::convert(Input::get('conteststartdate'), $timezone,'UTC', 'Y-m-d H:i:s');	   
	 $inputdetails['contestenddate'] = timezoneModel::convert(Input::get('contestenddate'), $timezone,'UTC', 'Y-m-d H:i:s');	   
	 $inputdetails['votingstartdate'] = timezoneModel::convert(Input::get('votingstartdate'), $timezone,'UTC', 'Y-m-d H:i:s');	   
	 $inputdetails['votingenddate'] = timezoneModel::convert(Input::get('votingenddate'), $timezone,'UTC', 'Y-m-d H:i:s');
	
	if(Input::file('themephoto')!='')
		{
			$destinationPath = 'public/assets/upload/contest_theme_photo';
			$filename = Input::file('themephoto')->getClientOriginalName();
			$Image = str_random(8).'_'.$filename;
			$inputdetails['themephoto']= $Image;		
		}
       
        		
		if(Input::get('userid')) $inputdetails['createdby']=Input::get('userid'); 		
		
		$inputdetails['contestcode']=str_random(8);		
		$currentdate =  Carbon::now();
		$inputdetails['createddate'] = $currentdate;
		$inputdetails['visibility']='p'; 
		$validation  = Validator::make($inputdetails, contestModel::$rules);
		if ($validation->passes()) 
		{
				
			$file = Input::file('themephoto');
			$uploadSuccess = $file->move($destinationPath,$Image);
			$contestcreate = contestModel::create($inputdetails);
			
			if($contestcreate)
			{			
			$private_cont['contest_id']=contestModel::max('ID');
			$private_cont['user_id']=Input::get('userid');
			$private_cont['requesteddate']=date('Y-m-d H:i:s');
			$private_cont['status']=1;
			privateusercontestModel::create($private_cont);
			}
			
			$interest_id=Input::get('interest_id'); 
			$interestid= explode(',',$interest_id);
			$interestidcount = count($interestid);		
			
			$maxcontestid = contestModel::max('ID'); 
			$interest['contest_id'] = "$maxcontestid";
			for($i=0;$i<$interestidcount; $i++)
			{		 
				$interest['category_id']=$interestid[$i];  //return $interest;
				$validationinterest  = Validator::make($interest, contestinterestModel::$rules);	
				if ($validationinterest->passes()) 
				{
					$userregister = contestinterestModel::create($interest);		 
				}		
			}

				$Response = array(
						'success' => '1',
						'message' => 'Record Added Successfully',
					);
				   $final=array("response"=>$Response,"contest_id"=>$maxcontestid);
				   return json_encode($final);
		}
		else
		{
		
			$Response = array(
					'success' => '0',
					'message' => $validation->messages()->first(),
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);
		}
}
public function getcontestmobile()
{
	$timezone = Input::get('timezone');
	$contestid = Input::get('contestid');
    $contest = contestModel::find($contestid); //return $contest;
    $contest['conteststartdate'];
	
	$interestdetails = contestinterestModel::select('interest_category.interest_id','interest_category.Interest_name')->where('contest_id',$contestid)->LeftJoin('interest_category','interest_category.interest_id','=','contest_interest_categories.category_id')->get();

    $contest->conteststartdate = timezoneModel::convert($contest->conteststartdate, 'UTC', $timezone,'Y-m-d H:i:s');

    $contest->contestenddate = timezoneModel::convert($contest->contestenddate, 'UTC', $timezone, 'Y-m-d H:i:s');
	   
    $contest->votingstartdate = timezoneModel::convert($contest->votingstartdate, 'UTC', $timezone, 'Y-m-d H:i:s');
	   
    $contest->votingenddate = timezoneModel::convert($contest->votingenddate, 'UTC',$timezone, 'Y-m-d H:i:s');
	$contest->createddate = timezoneModel::convert($contest->createddate, 'UTC',$timezone, 'Y-m-d H:i:s');
	$contest->interest = $interestdetails;
	$Response = array(
						'success' => '1',
						'message' => 'Contest Details fetched Successfully',
					);
					
		$contestparticipantcount=contestparticipantModel::where('contest_id',$contestid)->get()->count();
		$contestparticipantcount = array('contestparticipantcount' => $contestparticipantcount);
            //$final=array("response"=>$Response,"contestdetails"=>$contestDetails);
			
				   $final=array("response"=>$Response,"contest Details"=>$contest,"contestparticipantcount"=>$contestparticipantcount);
				   return json_encode($final);
}

/////Update Contest Mobile
public function updatecontestmobile()
{
		$inputdetails = Input::except(array('_token','themephoto','contestid','interest_id','userid','timezone'));
		
		$timezone = Input::get('timezone');
	   
	   
	 $inputdetails['conteststartdate'] = timezoneModel::convert(Input::get('conteststartdate'), $timezone,'UTC', 'Y-m-d H:i:s');
	 
	  $inputdetails['contestenddate'] = timezoneModel::convert(Input::get('contestenddate'), $timezone,'UTC', 'Y-m-d H:i:s');
	   
	 $inputdetails['votingstartdate'] = timezoneModel::convert(Input::get('votingstartdate'), $timezone,'UTC', 'Y-m-d H:i:s');
	   
	 $inputdetails['votingenddate'] = timezoneModel::convert(Input::get('votingenddate'), $timezone,'UTC', 'Y-m-d H:i:s');
		
		if(Input::file('themephoto')!='')
		{
			$destinationPath = 'public/assets/upload/contest_theme_photo';
			$filename = Input::file('themephoto')->getClientOriginalName();
			$Image = str_random(8).'_'.$filename;
			$inputdetails['themephoto']= $Image;		
		}
		/*else
		{
			$inputdetails['themephoto']=Input::get('');
		}			
		
		//$inputdetails['createdby']
			
			if(Input::get('sponsor')=='on') { 
			$inputdetails['sponsor']='on';
			$inputdetails['sponsorname']=Input::get('sponsorname');		
			if(Input::file('sponsorphoto')!='')
			{
				$destinationPath = 'public/assets/upload/sponsor_photo';
				$filename = Input::file('sponsorphoto')->getClientOriginalName();
				$Image = str_random(8).'_'.$filename;
				$inputdetails['sponsorphoto']= $Image;		
			}
			else{ $inputdetails['sponsorphoto']= Input::get('sponsorphotohidden'); } */
			
					
			$contestid=Input::get('contestid');	//return $inputdetails;
			
			
			    $updaterules = array(
                    'contest_name'       => 'required|unique:contest,contest_name,'.$contestid,
					'conteststartdate'         => 'required',
					'contestenddate'         => 'required',
					'votingstartdate'         => 'required',
					'votingenddate'  => 'required',
					'noofparticipant' =>'required',
					'contesttype' =>'required',	
                	) ;

  			
			$validation  = Validator::make($inputdetails, $updaterules);
		if ($validation->passes()) 
		{
			
			if(Input::file('themephoto')!='') { $file = Input::file('themephoto'); $uploadSuccess = $file->move($destinationPath,$Image); }				
			$updatedata=$inputdetails; 
		    $affectedRows = contestModel::where('ID',$contestid)->update($updatedata);
			$interest_id=Input::get('interest_id'); 
		    $interestid= explode(',',$interest_id);
		    $interestidcount = count($interestid);		
		
		
		$interest['contest_id'] = $contestid;
		$deleteoldid = contestinterestModel::where('contest_id', $contestid)->delete();
		for($i=0;$i<$interestidcount; $i++)
		{		 
		 $interest['category_id']=$interestid[$i];  //return $interest;
		 $validationinterest  = Validator::make($interest, contestinterestModel::$rules);	
		if ($validationinterest->passes()) 
        {		
		$userregister = contestinterestModel::create($interest);		 
		}		
		} 
			
			$Response = array(
					'success' => '1',
					'message' => 'Record updated Successfully',
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);			
		}
		else
		{
		
		     $Response = array(
                'success' => '0',
                'message' => $validation->messages()->first(),
              );
             $final=array("response"=>$Response);
             return json_encode($final);
		}
}
/////Contest List ///////
public function getcontestlistmobile()
{

	$inputdetails = Input::except(array('_token'));
	$contestlisttype =Input::get('contestlisttype');
	$contesttype=Input::get('contesttype');
	$loggeduserid = Input::get('loggeduserid');
	$currentdate = Carbon::now();
	$timezone = Input::get('timezone');
	
if($contestlisttype=='current')
{
	$contestDetailscount=contestModel::where(function($query){ 
							$query->where(function($query){
								$currentdate = date('Y-m-d H:i:s');
								$query->where('conteststartdate', '<=', $currentdate);
								$query->where('contestenddate', '>=', $currentdate);
							});
							$query->orWhere(function($query){
								$currentdate = date('Y-m-d H:i:s');
								$query->where('votingstartdate', '<=', $currentdate);
								$query->where('votingenddate', '>=', $currentdate);
							});
						})
						->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get()->count();
	
	
	$contestDetails=contestModel::where(function($query){ 
							$query->where(function($query){
								$currentdate = date('Y-m-d H:i:s');
								$query->where('conteststartdate', '<=', $currentdate);
								$query->where('contestenddate', '>=', $currentdate);
							});
							$query->orWhere(function($query){
								$currentdate = date('Y-m-d H:i:s');
								$query->where('votingstartdate', '<=', $currentdate);
								$query->where('votingenddate', '>=', $currentdate);
							});
						})
						->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get();
     
}
else if($contestlisttype=='upcoming')
{
	 
	/*$contestDetailscount = contestModel::where('conteststartdate', '>', $currentdate)->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get()->count();
		
	$contestDetails = contestModel::where('conteststartdate', '>', $currentdate)->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get();  */
	
		$contestDetailscount=contestModel::where('conteststartdate', '>', $currentdate)
									->where('contesttype',$contesttype)
									->where('status','1')
									->where('visibility','u')
									->get()->count();
	
	
	$contestDetails=contestModel::where('conteststartdate', '>', $currentdate)
									->where('contesttype',$contesttype)
									->where('status','1')
									->where('visibility','u')
									->get(); 
	
}
else if($contestlisttype=='archive')
{
	$contestDetailscount = contestModel::where('contestenddate', '<', $currentdate)
	->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('status','1')
	->where('visibility','u')
	->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')
	->get()->count();
		
	$contestDetails = contestModel::where('contestenddate', '<', $currentdate)
	->select('contest.*','contestparticipant.ID as contestparticipantid')
	->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('status','1')
	->where('visibility','u')
	->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')
	->get(); 
	
	/*$contestDetailscount=contestModel::where('contestenddate', '<', $currentdate)
												->where('votingenddate', '<', $currentdate)
												->where('contesttype',$contesttype)
												->where('status','1')
												->where('visibility','u')
												->get()->count();
	
	$contestDetails=contestModel::where('contestenddate', '<', $currentdate)
												->where('votingenddate', '<', $currentdate)
												->where('contesttype',$contesttype)
												->where('status','1')
												->where('visibility','u')
												->get();
	*/
}
else if($contestlisttype=='private')  
{

   /* $contestDetailscount = contestModel::where('visibility','p')
			->select('contest.ID','contest_name','description','noofparticipant','contestparticipant.ID as contestparticipantid')
			->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
			->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')			
            ->where('private_contest_users.user_id',$loggeduserid)
            ->get()->count();
			
	 $contestDetails = contestModel::where('visibility','p')
			->select('contest.ID','contest_name','description','noofparticipant','contestparticipant.ID as contestparticipantid')
			->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
			->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')			
            ->where('private_contest_users.user_id',$loggeduserid)
            ->get();	
*/



 $contestDetailscount=contestModel::where('visibility','p')
										->where('contesttype',$contesttype)
										->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')									
										->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
										->where('private_contest_users.user_id',$loggeduserid)										
										->where('private_contest_users.status','1')->distinct()
										->get()->count();

$contestDetails=contestModel::where('visibility','p')
										->where('contesttype',$contesttype)
										->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')
										->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
										->where('private_contest_users.user_id',$loggeduserid)										
										->where('private_contest_users.status','1')->distinct()
										->get();	

										


  /*  $contestDetailscount = contestModel::where('visibility','p')
			->select('contest.ID','contest_name','description','noofparticipant')
			->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')
            ->where('contestparticipant.user_id',$loggeduserid)
            ->get()->count();
		
	$contestDetails = contestModel::where('visibility','p')
			->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')
            ->select('contest.ID','contest_name','description','noofparticipant')
			->where('contestparticipant.user_id',$loggeduserid)
            ->get();  */
			
//$languageDetailscount = contestparticipantModel::where('ID',$loggeduserid)->get();
}

 if($contestDetailscount) { 
			
	
	for($i=0;$i<count($contestDetails);$i++)
	{
	
	$participant = contestparticipantModel::select('ID')->where('contest_id',$contestDetails[$i]->ID)->where('user_id',$loggeduserid)->get()->count();
	if($participant!='') $contestDetails[$i]->contestparticipantid=1; else $contestDetails[$i]->contestparticipantid=0;
	
	$contestDetails[$i]->conteststartdate = timezoneModel::convert($contestDetails[$i]->conteststartdate, 'UTC',$timezone, 'd-M-Y h:i a');

	$contestDetails[$i]->contestenddate = timezoneModel::convert($contestDetails[$i]->contestenddate, 'UTC',$timezone, 'd-M-Y h:i a');
	   
	$contestDetails[$i]->votingstartdate = timezoneModel::convert($contestDetails[$i]->votingstartdate, 'UTC',$timezone, 'd-M-Y h:i a');
	   
	$contestDetails[$i]->votingenddate = timezoneModel::convert($contestDetails[$i]->votingenddate, 'UTC',$timezone, 'd-M-Y h:i a');
	
	$contestDetails[$i]->createddate = timezoneModel::convert($contestDetails[$i]->createddate, 'UTC', $timezone,'d-M-Y h:i a');
	

	//themephoto
	}
    
	//return $contestDetails;
			$Response = array(
                'success' => '1',
                'message' => 'Data Get Successfully',
            );
            $final=array("response"=>$Response,'contestdetails'=>$contestDetails);
            return json_encode($final);
 }
 else{
 			$Response = array(
                'success' => '0',
                'message' => 'No Data Available',
            );
            $final=array("response"=>$Response);
            return json_encode($final);
 
 }
}
public function getcontestinfo()
{
    $contestid=Input::get('contestid');
    $timezone = Input::get('timezone');
	$user_id  = Input::get('user_id');
	if($contestid){     
	$contestDetails =  contestModel::where('ID', $contestid)->get();
	 for($i=0;$i<count($contestDetails);$i++)
	{
	$contestparticipantid=contestparticipantModel::where('contest_id',$contestid)->where('user_id',$user_id)->get()->count();
	if($contestparticipantid) $contestDetails[$i]->contestparticipantid=1; else $contestDetails[$i]->contestparticipantid=0;
	
	$contestDetails[$i]->conteststartdate = timezoneModel::convert($contestDetails[$i]->conteststartdate, 'UTC',$timezone, 'd-M-Y h:i a');

	$contestDetails[$i]->contestenddate = timezoneModel::convert($contestDetails[$i]->contestenddate, 'UTC',$timezone, 'd-M-Y h:i a');
	   
	$contestDetails[$i]->votingstartdate = timezoneModel::convert($contestDetails[$i]->votingstartdate, 'UTC',$timezone, 'd-M-Y h:i a');
	   
	$contestDetails[$i]->votingenddate = timezoneModel::convert($contestDetails[$i]->votingenddate, 'UTC',$timezone, 'd-M-Y h:i a');
	
	$contestDetails[$i]->createddate = timezoneModel::convert($contestDetails[$i]->createddate, 'UTC', $timezone,'d-M-Y h:i a');
	}	
	$contestparticipantcount=contestparticipantModel::where('contest_id',$contestid)->get()->count();
	} 
	  
      $Response = array(
                'success' => '1',
                'message' => 'Data Get Successfully',				
            );
		$contestparticipantcount = array('contestparticipantcount' => $contestparticipantcount);
            $final=array("response"=>$Response,"contestdetails"=>$contestDetails,"contestparticipantcount"=>$contestparticipantcount);
            return json_encode($final);

}
public function joincontest()
{
       $curdate = Carbon::now();
	   $timezone = Input::get('timezone');
       $inputdetails = Input::except(array('_token','uploadfile','uploadtopic','timezone'));		
	   $inputdetails['uploaddate']=$curdate;
		if(Input::file('uploadfile')!='')
		{
			$destinationPath = 'public/assets/upload/contest_participant_photo';
			$filename = Input::file('uploadfile')->getClientOriginalName();
			$Image = str_random(8).'_'.$filename;
			$inputdetails['uploadfile']= $Image;
			$validation  = Validator::make($inputdetails, contestparticipantModel::$rules);		
		}
		if(Input::get('uploadtopic')!='')
		{
			$inputdetails['uploadtopic']=Input::get('uploadtopic'); //return $inputdetails;
			$validation  = Validator::make($inputdetails, contestparticipantModel::$topicrules);
		}
	  ///// Check Already Participated user in this Contest or not
		$user_id = Input::get('user_id');
		$contest_id = Input::get('contest_id');
		
		$verifyid = contestparticipantModel::where('contest_id',$contest_id)
				->where('user_id',$user_id)
			    ->get()->count();
				
	  ///// Check the Participant count of the contest ////////////	
		$contestcount = contestModel::select('noofparticipant')->where('ID',$contest_id)->get();	  
	    $participantcount = contestparticipantModel::where('contest_id',$contest_id)->get()->count();
		if($verifyid)
		{
			if ($validation->passes()) {
			if(Input::file('uploadfile')!='') { 
			$file = Input::file('uploadfile');
			$uploadSuccess = $file->move($destinationPath,$Image);
		    }
			
			$participant = contestparticipantModel::where('contest_id',$contest_id)
				->where('user_id',$user_id)
				->update($inputdetails);
			
			$Response = array(
                'success' => '1',
                'message' => 'Record updated Successfully',
            );
           $final=array("response"=>$Response);
           return json_encode($final);	
			}
			else
			{
			$Response = array(
                'success' => '0',
                'message' => 'Some Parameter missing',
            );
           $final=array("response"=>$Response);
           return json_encode($final);
			}
		}
		else{
			if($contestcount[0]['noofparticipant']!=0 && $contestcount[0]['noofparticipant']==$participantcount)
			{
			$Response = array(
					'success' => '0',
					'message' => 'Contest Participant Limit is crossed',
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);
			}
			else{
						
				if ($validation->passes()) 
				{			
					if(Input::file('uploadfile')!='') { 
						$file = Input::file('uploadfile');
						$uploadSuccess = $file->move($destinationPath,$Image);
					}
					$participant = contestparticipantModel::create($inputdetails);
					$Response = array(
							'success' => '1',
							'message' => 'Record Added Successfully',
						);
					   $final=array("response"=>$Response);
					   return json_encode($final);		
				}
				else
				{	
				$Response = array(
						'success' => '0',
						'message' => 'Some Parameter missing',
					);
				   $final=array("response"=>$Response);
				   return json_encode($final);
				}
			}
		}
	
}
public function contestgallery()
{
	$contest_id = Input::get('contest_id');
	$timezone = Input::get('timezone');
	$participantlist = contestparticipantModel::where('contest_id',$contest_id)->get();
	
	for($i=0;$i<count($participantlist);$i++)
	{
	$participantlist[$i]->uploaddate = timezoneModel::convert($participantlist[$i]->uploaddate, 'UTC',$timezone, 'd-M-Y h:i a');
	}

	
	$Response = array(
		'success' => '1',
		'message' => 'Record Fetched Successfully',
	);
	$final=array("response"=>$Response,"participantlist"=>$participantlist);
	return json_encode($final);

}

public function getcontestforvoting()
{
	$contest_id = Input::get('contest_id');
	$userid = Input::get('userid');
	$contest_participant_id = Input::get('contest_participant_id');
	$timezone = Input::get('timezone');
		
	$contestdetailscount = contestparticipantModel::where('ID', $contest_participant_id)
	    	->get()->count();
	
	$contestdetails = contestparticipantModel::where('ID', $contest_participant_id)
	    	->get();
	$contestparticipantid = $contestdetails[0]['user_id'];
	$followers = followModel::where('userid',$userid)->where('followerid',$contestparticipantid)->get()->count();
	$contestdetails[0]['follower']=$followers;

	
	for($i=0;$i<count($contestdetails);$i++)
	{	
	$contestdetails[$i]->uploaddate = timezoneModel::convert($contestdetails[$i]->uploaddate, 'UTC',$timezone, 'd-M-Y h:i a');
	}
	
	// User follow the contest created user means it returns 1;
		
	if($contestdetailscount){
		$Response = array(
                'success' => '1',
                'message' => 'Contest details for voting get Successfully',
            );
           $final=array("response"=>$Response, "contestdetails" => $contestdetails);
           return json_encode($final);
	
	}else{
		$Response = array(
                'success' => '0',
                'message' => 'Some details missing',
            );
           $final=array("response"=>$Response);
           return json_encode($final);
	}
}
public function voting()
{
	
	$curdate = Carbon::now();
	$votingdetailswithjson =   Input::get('votingdetails');
	
	 $decode = json_decode($votingdetailswithjson);
	 $decodecount = count($decode->vote); //$decode->vote[$i]->contest_participant_id;	 
	 $returnresult = array();	 
	 for($i=0; $i<$decodecount; $i++)
	 {

	 $inputdetails['user_id']=$decode->vote[$i]->user_id;
	 $inputdetails['contest_participant_id']=$decode->vote[$i]->contest_participant_id;
	 $inputdetails['vote']=$decode->vote[$i]->vote;
	 $inputdetails['votingdate'] = $curdate;
	 $validation  = Validator::make($inputdetails, votingModel::$rules);
	if ($validation->passes()){  
		//return $inputdetails;
		$ok = votingModel::create($inputdetails);
		if($ok) $returnresult[$i]=$decode->vote[$i]->contest_participant_id; 
	}
	else{ /// Not inserted Id
	 $returnresultnotadded[$i]=$decode->vote[$i]->contest_participant_id;
	}
	}	

	$Response = array(
                'success' => '1',
                'message' => 'voting updated successfully',
            );
           $final=array("response"=>$Response,"savedcontest_participant_id"=>$returnresult);
           return json_encode($final);
	
}
public function leaderboard()
{
		
	$contest_id = Input::get('contest_id');	
	$leaderboardresult = leaderboardModel::where('contest_id',$contest_id)
	->orderBy('position')
	->get();
     
	$Response = array(
                'success' => '1',
                'message' => 'Getting Result details',
            );
     $final=array("response"=>$Response,"Voting Result"=>$leaderboardresult);
     return json_encode($final); 
		
}
public function follower()
{
	$curdate = date('Y-m-d h:i:s');
	$inputdetails['followerid'] = Input::get('followerid');
	$inputdetails['userid'] = Input::get('userid');
	$inputdetails['createddate']=$curdate;
	$validation  = Validator::make($inputdetails, followModel::$rules);
		if ($validation->passes()){
			$followers = followModel::create($inputdetails);
			$Response = array(
                'success' => '1',
                'message' => 'Followers  Added Successfully',
            );
           $final=array("response"=>$Response,"follower"=>$followers);
           return json_encode($final);	
		}
		else{
			$Response = array(
                'success' => '0',
                'message' => 'Some details missing',
            );
           $final=array("response"=>$Response,"follower"=>$followers);
           return json_encode($final);
		}
}

public function getfollowers()
{
$userid = Input::get('userid');
$timezone = Input::get('timezone');
 $followers = followModel::where('userid',$userid)
->select('user.profilepicture','user.firstname','user.ID as followerid')
->leftJoin('user','user.ID','=','followers.followerid')
->get();

			$Response = array(
                'success' => '1',
                'message' => 'Followers  List',
            );
           $final=array("response"=>$Response,"followerlist"=>$followers);
           return json_encode($final);
}
public function getcommentsdetails()
{
    $userid=Input::get('userid');
    $contest_participant_id=Input::get('contest_participant_id');
/// Verifying the followers 
     	
	$contestdetailscount = contestparticipantModel::where('ID', $contest_participant_id)
	    	->get()->count();
	
	$contestdetails = contestparticipantModel::where('ID', $contest_participant_id)
	    	->get()->toArray();
	$contestparticipantid = $contestdetails[0]['user_id'];
	$followers = followModel::where('userid',$userid)->where('followerid',$contestparticipantid)->get()->count();
//// Get Comments 
	 $getcommentscount = commentModel::where('contest_participant_id',$contest_participant_id)->get()->count();
	if($getcommentscount)
	{
		
	$getcomments = commentModel::select('comments.id as comment_id','comments.contest_participant_id','comments.comment','user.ID as userid','user.firstname','user.profilepicture')
	->where('comments.contest_participant_id',$contest_participant_id)
	->LeftJoin('user','user.ID','=','comments.userid')->get();
	
	/*DB::select("SELECT user.*,DATE_FORMAT(CONVERT_TZ(`dateofbirth`,'+00:00','$timezone'),'%Y-%m-%d') as converteddateofbirth  FROM user WHERE ID =$userid");
	
	 $getcomments = commentModel::select('comments.id as comment_id','comments.contest_participant_id','comments.comment','user.ID as userid','user.firstname','user.profilepicture' )
	->where('comments.contest_participant_id',$contest_participant_id)
	->LeftJoin('user','user.ID','=','comments.userid')->get();
	*/
	
	$Response = array(
                'success' => '1',
                'message' => 'Getting Comments details',
            );
     $final=array("response"=>$Response,"Comments"=>$getcomments, "followers"=>$followers);
     return json_encode($final);
	
	}
	else{
	$Response = array(
                'success' => '0',
                'message' => 'No Comments Available in this Contest participant',
            );
     $final=array("response"=>$Response,"followers"=>$followers);
     return json_encode($final);
	
	
	}   
   
	
	/*return replycommentModel::select('replycomment.id', 'comments.id as comment_id','comments.contest_participant_id','replycomment.replycomment','comments.comment' )
	->RIGHTJOIN('comments','comments.id','=','replycomment.comment_id')
	->where('comments.contest_participant_id',$contest_participant_id)->get();
*/	
	
}
public function putcomments()
{

	$inputdetails=Input::get();
	$curdate = date('Y-m-d h:i:s');
	$inputdetails['createddate']=$curdate; 
    $validation  = Validator::make($inputdetails, commentModel::$rules);
		if ($validation->passes()){
		$savecomment = commentModel::create($inputdetails);
		if($savecomment)
		{
		$Response = array(
                'success' => '1',
                'message' => 'Comment saved successfully',
            );
     $final=array("response"=>$Response);
     return json_encode($final);
		}
		}
		else{ 
		$Response = array(
                'success' => '0',
                'message' => 'Some details are Missing',
            );
		 $final=array("response"=>$Response);
		 return json_encode($final);
		}
}
public function myhistory()
{
  $userid = Input::get('userid');
  $timezone = Input::get('timezone');
  $myhistory = contestparticipantModel::where('user_id',$userid)->get();
  for($i=0; $i<count($myhistory);$i++)
  {
  $myhistory[$i]->uploaddate = timezoneModel::convert($myhistory[$i]->uploaddate, 'UTC',$timezone, 'd-M-Y h:i a');
  }
  $Response = array(
                'success' => '1',
                'message' => 'My Participated history',
            );
     $final=array("response"=>$Response,'myhistory'=>$myhistory);
     return json_encode($final);

}
public function getfollowinglist()
{
$userid = Input::get('userid');
 $followers = followModel::where('followerid',$userid)
->select('user.profilepicture','user.firstname','user.ID as followinguserid')
->leftJoin('user','user.ID','=','followers.userid')
->get();

			$Response = array(
                'success' => '1',
                'message' => 'Following member  List',
            );
           $final=array("response"=>$Response,"followerlist"=>$followers);
           return json_encode($final);

}
public function viewprofile()
{
    $userid = Input::get('userid');
    $followingcount = followModel::where('userid',$userid)->get()->count();
    $followerscount = followModel::where('followerid',$userid)->get()->count();	
	$participatedcount = contestparticipantModel::where('user_id',$userid)->get()->count();
	$woncount = leaderboardModel::where('user_id',$userid)->get()->count();

	$return['following'] = $followingcount;
	$return['followers'] = $followerscount;
	$return['participated'] = $participatedcount;
	$return['won'] = $woncount;
  
   $Response = array(
                'success' => '1',
                'message' => 'My Profile Details',
            );
     $final=array("response"=>$Response,'viewmyprofile'=>$return);
     return json_encode($final);
   
}
function mobilemultilingual()
{
	$languagekey = Input::get('languagekey');
	$languagename = 'value_'.$languagekey;
	$languageDetails = languageModel::select('ctrlCaptionId',$languagename)->get()->toArray();
	$Response = array(
					'success' => '1',
					'message' => 'Language Details Fetched Successfully',
				);
		 $final=array("response"=>$Response,'languageDetails'=>$languageDetails);
		 return json_encode($final);
}
function mobilegetlanguages()
{
	$languageDetails = languagenameModel::select('language_key','language_name')->get();
	$Response = array(
					'success' => '1',
					'message' => 'Language Names are fetched Successfully',
				);
		 $final=array("response"=>$Response,'languageDetails'=>$languageDetails);
		 return json_encode($final);
}
public function creategroup()
{
	$curdate = date('Y-m-d h:i:s');
	$inputdetails['groupname'] = Input::get('groupname');
	$inputdetails['createdby'] = Input::get('userid'); 
	$inputdetails['createddate'] =$curdate; 
	$inputdetails['grouptype'] = Input::get('grouptype');
	$inputdetails['groupimage'] = Input::get('uploadimage');
	$userid = Input::get('userid');
	
	$validation  = Validator::make($inputdetails, groupModel::$rules);
	if ($validation->passes()){  
	$savegroup = groupModel::create($inputdetails);
	
	$group_id = groupModel::max('id');
	//// Group Owner add in the group member table////
	$inputgroupmember['group_id'] = $group_id;
	$inputgroupmember['user_id'] = $userid;
	$inputgroupmember['createddate'] = $curdate;
	$groupmember = groupmemberModel::create($inputgroupmember);
	///if Followers or following checked And the group Private //////////
					
			////////////
		if($inputdetails['grouptype']=='private')
		{
			//return Input::get('follower');
			if(Input::get('follower'))
			$follower = followModel::select('followerid as id')->where('userid',$userid)->get();
			if(Input::get('following'))
			$following = followModel::select('userid as id')->where('followerid',$userid)->get();

			
			
			if(Input::get('follower')||Input::get('following'))
			{	
				if(Input::get('follower'))
				{
				
					for($i=0; $i<count($follower);$i++) {			
						$id[$i] = $follower[$i]['id'];			
					}	
					
								
				}
				if(Input::get('following'))
				{
					for($i=0; $i<count($following);$i++) { 			
						$id1[$i] = $following[$i]['id'];
					}		
				}
				
										
				
				if(Input::get('follower')!=0 &&count($follower)>=1  && Input::get('following')!=0 && count($following)>=1)
				$id = array_unique(array_merge($id, $id1)); 
				elseif(Input::get('following')!=0 && count($following)>=1) $id=$id1;
				elseif(Input::get('follower')!=0 && count($follower)>=1) $id=$id;
				else $data ='no Data Available in this condition';
				
				//return $id;
				
				$inviteinputdetails['inviteddate']=$curdate;
				$inviteinputdetails['group_id']=$group_id; 
				for($i=0;$i<count($id);$i++){ 
					 $inviteinputdetails['user_id']=$id[$i];
					 $inviteinputdetails['invitetype']='m'; 
					 $invite = invitememberforgroupModel::create($inviteinputdetails);
						if($invite){ 
							// HERE SET THE NOTIFIATION //
						}	
				}				
			}
			
			
		} 
			///////////
		
		if($savegroup) {			
		 $Response = array(
                'success' => '1',
                'message' => 'Group Details saved successfully',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
		}
    }
	else
	{ 
	$Response = array(
                'success' => '0',
                'message' => $validation->messages()->first(),
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	} 
}
public function getgroupdetails()
{
	$group_id = Input::get('group_id');
	$timezone = Input::get('timezone');
	$groubcnt = groupModel::where('id',$group_id)->get()->count();
	if($groubcnt)
	{
	$groubdetails = groupModel::where('id',$group_id)->get();
	$groubdetails[0]->createddate = timezoneModel::convert($groubdetails[0]->createddate, 'UTC',$timezone, 'd-M-Y h:i a');
	$Response = array(
                'success' => '1',
                'message' => 'Group Details Fetched successfully',
            );
        $final=array("response"=>$Response,"Groupdetails"=>$groubdetails);
        return json_encode($final);
	
	}
	else
	{
	$Response = array(
                'success' => '0',
                'message' => 'No Details Available',
            );
        $final=array("response"=>$Response);
        return json_encode($final);

	}
}

public function updategroupdetails()
{	
	$inputdetails['groupname'] = Input::get('groupname');
	$inputdetails['grouptype'] = Input::get('grouptype');
	$group_id = Input::get('group_id');
	
	$updaterules = array(                    
					'groupname'  => 'required|unique:group,groupname,'.$group_id,
					'grouptype' =>'required',
                	) ;
					
	$validation  = Validator::make($inputdetails, $updaterules);
	if ($validation->passes()){	 
	 $participant = groupModel::where('ID',$group_id)->update($inputdetails);
	 $Response = array(
                'success' => '1',
                'message' => 'Group Details Updated successfully',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
		
	}
	else
	{ 
	$Response = array(
                'success' => '0',
                'message' => $validation->messages()->first(),
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	
	}
}
public function getgrouplist()
{
$user_id = Input::get('user_id');
$timezone = Input::get('timezone');

 //$grouplist = groupModel::select('groupname','grouptype','createdby','user.firstname as owner','groupimage')
	//	->LeftJoin('user','user.ID','=','group.createdby')->get();
		
		$grouplist = groupmemberModel::select('group_members.group_id','group.groupname','group.grouptype','group.createdby','user.firstname','user.lastname','user.username','group.groupimage','group.createdby as groupcreateuserid','group.ID as groupid')->leftJoin('group','group.ID','=','group_members.group_id')->LeftJoin('user','user.ID','=','group.createdby')->where('group_members.user_id',$user_id)->get();
		
		 $Response = array(
                'success' => '1',
                'message' => 'Group List fetched successfully',
            );
        $final=array("response"=>$Response,"grouplist"=>$grouplist);
        return json_encode($final);
		
}
public function getgrouplistforcontest()
{
	$user_id = Input::get('user_id');
	$timezone = Input::get('timezone');
	$contest_id = Input::get('contest_id');
	
	//$grouplist = groupModel::select('group.ID as groupid','groupname','grouptype','createdby','user.firstname as owner','groupimage')->LeftJoin('user','user.ID','=','group.createdby')->get();

	
	$grouplist = groupmemberModel::select('group_members.group_id','group.groupname','group.grouptype','group.createdby','user.firstname','user.lastname','user.username','group.groupimage','group.createdby as groupcreateuserid','group.ID as groupid')->leftJoin('group','group.ID','=','group_members.group_id')->LeftJoin('user','user.ID','=','group.createdby')->where('group_members.user_id',$user_id)->get();
	
	
	//return count($grouplist);
	
		for($k=0;$k<count($grouplist);$k++)
		{
		$invitecnt = invitegroupforcontestModel::where('group_id',$grouplist[$k]['groupid'])->where('contest_id',$contest_id)->get()->count();
		$grouplist[$k]['invite']= $invitecnt;	
		}	
		 $Response = array(
                'success' => '1',
                'message' => 'Group List fetched successfully',
            );
        $final=array("response"=>$Response,"grouplist"=>$grouplist);
        return json_encode($final);
		
}
public function getgroupmemberlist()
{
	$group_id = Input::get('group_id');
	
	$membercount = groupmemberModel::where('group_id',$group_id)->get()->count();
	if($membercount){
	
	return $savegroupmembers = groupmemberModel::select('group_members.id as groupmemberid','group_members.user_id','user.firstname','user.lastname','user.profilepicture','group.createdby as groupadmin_userid' )
	->LeftJoin('user','user.ID','=','group_members.user_id')
	->where('group_id',$group_id)
	->LeftJoin('group','group.ID','=','group_members.group_id')
	->get();
	
	}else{
		$Response = array(
                'success' => '0',
                'message' => 'No members available in this group',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	
	}

}
public function ungroup()
{
	$user_id = Input::get('user_id'); 
	$group_id = Input::get('group_id');

	$count = groupmemberModel::where('user_id',$user_id)->where('group_id',$group_id)->get()->count();
	if($count)
	{
	$count = groupmemberModel::where('user_id',$user_id)->where('group_id',$group_id)->delete();
	$Response = array(
                'success' => '1',
                'message' => 'You are removed from that group',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	}
	else
	{
	$Response = array(
                'success' => '0',
                'message' => 'Such User is not available in this group',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	}
}
public function unfollow()
{
$user_id=Input::get('user_id');
$following_id = Input::get('following_id');
$count = followModel::where('followerid',$user_id)->where('userid',$following_id)->get()->count();
if($count){
followModel::where('followerid',$user_id)->where('userid',$following_id)->delete();
$Response = array(
                'success' => '1',
                'message' => 'You are removed the following person',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
}else{
$Response = array(
                'success' => '0',
                'message' => 'Such following person is not available',
            );
        $final=array("response"=>$Response);
        return json_encode($final);

}

}

public function facebooklogin()
{
$inpudetails = Input::except(array('link'));
$inpudetails['facebook_id']=Input::get('id');
$emailid = Input::get('email');
$curdate = date('Y-m-d h:i:s');

$inpudetails['createddate']= $curdate;
$validator = Validator::make($inpudetails,ProfileModel::$socialrules);
		if ($validator->passes()) 
        {
		
		$verifyuser = ProfileModel::where('email',$emailid)->get()->count();
		$userid = ProfileModel::select('ID')->where('email',$emailid)->get();
		if($verifyuser)
		{
		$userid1 = $userid[0]['ID'];
		$updatedata['facebook_id'] = Input::get('id');
		$updatedata['facebookpage']=Input::get('link');	 //return $userid1;
		 
		$updatedata['gcm_id'] = Input::get('gcm_id');
		$updatedata['device_id'] = Input::get('device_id');
		$updatedata['device_type'] = Input::get('device_type');
		
		$userregister = ProfileModel::where('ID', $userid1)->update($updatedata);
				
		$Response = array(
                'success' => '1',
                'message' => 'successfully Login',
				'userid' =>$userid1
            );
			$final=array("response"=>$Response);
            return json_encode($final);		
		}
		else{
		$inpudetails['facebook_id'] = Input::get('id');
		$inpudetails['facebookpage']=Input::get('link');	//return $inpudetails;
		$inpudetails['status'] = 'A';		
		
		$inpudetails['gcm_id'] = Input::get('gcm_id');
		$inpudetails['device_id'] = Input::get('device_id');
		$inpudetails['device_type'] = Input::get('device_type'); 
		
		$inpudetails['timezone']=Input::get('timezone'); 
		
		$saved = ProfileModel::create($inpudetails);
		if($saved)
		{
		$userid = ProfileModel::max('ID'); 
		$Response = array(
                'success' => '1',
                'message' => 'successfully created Login',
				'userid' =>$userid
            );
			$final=array("response"=>$Response);
            return json_encode($final);	
		
		}		
		}
		
		}
		else
		{
		$Response = array(
                'success' => '0',
                'message' => 'Some Fields are Missing'
            );
			$final=array("response"=>$Response);			
            return json_encode($final);	
		}
}
public function mobilegooglelogin()
{
$inpudetails = Input::except(array('link'));
$inpudetails['google_id']=Input::get('id');
$emailid = Input::get('email');
$curdate = date('Y-m-d h:i:s');

$inpudetails['createddate']= $curdate; //return $inpudetails;
$validator = Validator::make($inpudetails,ProfileModel::$socialrules);
		if ($validator->passes()) 
        {
		
		$verifyuser = ProfileModel::where('email',$emailid)->get()->count();
		$userid = ProfileModel::select('ID')->where('email',$emailid)->get();
		if($verifyuser)
		{
		$userid1 = $userid[0]['ID'];
		$updatedata['google_id'] = Input::get('id');
		
		$updatedata['gcm_id'] = Input::get('gcm_id');
		$updatedata['device_id'] = Input::get('device_id');
		$updatedata['device_type'] = Input::get('device_type'); 
		
		$userregister = ProfileModel::where('ID', $userid1)->update($updatedata);
				
		$Response = array(
                'success' => '1',
                'message' => 'successfully Login',
				'userid' =>$userid1
            );
			$final=array("response"=>$Response);
            return json_encode($final);		
		}
		else{
		$inpudetails['google_id'] = Input::get('id');
		
		$inpudetails['status'] = 'A';
		$inpudetails['gcm_id'] = Input::get('gcm_id');
		$inpudetails['device_id'] = Input::get('device_id');
		$inpudetails['device_type'] = Input::get('device_type'); 
		
		$inpudetails['timezone']=Input::get('timezone');

		$saved = ProfileModel::create($inpudetails);
		if($saved)
		{
		$userid = ProfileModel::max('ID'); 
		$Response = array(
                'success' => '1',
                'message' => 'successfully created Login',
				'userid' =>$userid
            );
			$final=array("response"=>$Response);
            return json_encode($final);	
		
		}		
		}
		
		}
		else
		{
		$Response = array(
                'success' => '0',
                'message' => 'Some Fields are Missing'
            );
			$final=array("response"=>$Response);			
            return json_encode($final);	
		}

}
public function replycomments()
{
$inputdetails =  Input::get();
$curdate = date('Y-m-d h:i:s');
$inputdetails['createddate'] = $curdate;

$validation  = Validator::make($inputdetails, replycommentModel::$rules);
if ($validation->passes()){
	$save = replycommentModel::create($inputdetails);
	if($save)
	{
	$Response = array(
                'success' => '1',
                'message' => 'Reply Comments successfully',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	}
}
else
{
	$Response = array(
                'success' => '0',
                'message' => 'Some Details are missing',
            );
        $final=array("response"=>$Response);
        return json_encode($final);

}
}
public function getreplycomments()
{
$comment_id=Input::get('comment_id');
$user_id = Input::get('user_id');

$getreplycnt = replycommentModel::where('comment_id',$comment_id)->get()->count();
if($getreplycnt)
{
$getreply = replycommentModel::where('comment_id',$comment_id)->get();
$Response = array(
                'success' => '0',
                'message' => 'Reply Fetched Successfully',
            );
        $final=array("response"=>$Response,"replydata"=>$getreply);
        return json_encode($final);

}
else{
$Response = array(
                'success' => '0',
                'message' => 'No reply Comments Available in this record',
            );
        $final=array("response"=>$Response);
        return json_encode($final);

}
}

public function participantdetails()
{
	$contest_id = Input::get('contest_id');
	$timezone = Input::get('timezone');
	$particiapnt_user_id = Input::get('participantuserid');
	$participantlist = contestparticipantModel::where('contest_id',$contest_id)->where('user_id',$particiapnt_user_id)->get();
	
	for($i=0;$i<count($participantlist);$i++)
	{
	$participantlist[$i]->uploaddate = timezoneModel::convert($participantlist[$i]->uploaddate, 'UTC',$timezone, 'd-M-Y h:i a');
	}

	
	$Response = array(
		'success' => '1',
		'message' => 'Record Fetched Successfully',
	);
	$final=array("response"=>$Response,"participantlist"=>$participantlist);
	return json_encode($final);

}

public function invitegroupsforcontest()
{
//invitegroupsforcontest
     $inpudetails['invitedetail']=1;
	 $inpudetails['group_id'] = Input::get('group_id');
	 $inpudetails['contest_id'] = Input::get('contest_id');
	 $curdate = date('Y-m-d h:i:s');
     $inpudetails['inviteddate'] = $curdate;	 
	$invite_type = Input::get('invite_type');
	 
	
	/////// Contest info /////	
	//Input::get('contest_id');
	$contestinfo = contestModel::select('contest_name','contesttype','createdby','visibility')->where('ID',Input::get('contest_id'))->get();
	$contestname = $contestinfo[0]['contest_name'];
	$contesttype = $contestinfo[0]['contesttype'];
	
	//return $contestinfo[0]['visibility'];
	
	if($contesttype=='p') $contesttype="Photo"; else if($contesttype=='v') $contesttype="Video"; else if($contesttype=='t') $contesttype="Topic";
	
	//return $contestinfo[0]['createdby'];
	$contest_id=Input::get('contest_id');
	$contestcreatedby = User::find($contestinfo[0]['createdby']);
	
	if($contestcreatedby['firstname']!=''){ $contestcreatedby = $contestcreatedby['firstname'].''.$contestcreatedby['lastname']; }else{ $contestcreatedby = $contestcreatedby['username'];  } 
	
	
	
	if($invite_type=='All'){
		
		$invitedlis = invitegroupforcontestModel::where('contest_id',$inpudetails['contest_id'])->lists('group_id');		
		$invitedcnt = count($invitedlis);
		if($invitedcnt)
		{
		//$uninvitedgroup = groupModel::whereNotIn('id', $invitedlis)->lists('id');

		$uninvitedgroup = groupmemberModel::select('group.id')->leftJoin('group','group.ID','=','group_members.group_id')->LeftJoin('user','user.ID','=','group.createdby')->where('group_members.user_id',Input::get('user_id'))->whereNotIn('group.id', $invitedlis)->get()->toArray();
		
		}else{		
		 
		 //$uninvitedgroup =groupModel::lists('id');
		
		$uninvitedgroup = groupmemberModel::select('group.id')->leftJoin('group','group.ID','=','group_members.group_id')->LeftJoin('user','user.ID','=','group.createdby')->where('group_members.user_id',Input::get('user_id'))->get()->toArray();
		
		}

	 if(count($uninvitedgroup))
	 {
		
		for($i=0;$i<count($uninvitedgroup);$i++){		
	    
		$inpudetails['group_id']=$uninvitedgroup[$i]['id'];
		if($uninvitedgroup[$i]['id']!='')
	{
	$groupowner = groupModel::select('user.firstname','user.lastname','user.username','group.grouptype','group.groupname')->where('group.ID',$uninvitedgroup[$i]['id'])->LeftJoin('user','user.ID','=','group.createdby')->get();

	if($groupowner[0]['firstname']!='')
	$inviter=$groupowner[0]['firstname'] ." ".$groupowner[0]['lastname'];
	else
	$inviter=$groupowner[0]['username'];
	}
		
	  invitegroupforcontestModel::create($inpudetails);
		$groupname = $groupowner[0]['groupname'];
		
			/******** Here want to set the Notification for Group Members ***/
			
			$groupmemberlist = groupmemberModel::where('group_id',$uninvitedgroup[$i]['id'])->lists('user_id');			
			for($j=0;$j<count($groupmemberlist);$j++){ 
				//////Notification////////////////////
			if($contestinfo[0]['createdby']!=$groupmemberlist[$j])
			{
			$user_id = User::find($groupmemberlist[$j]);
			$gcmid = $user_id['gcm_id'];
			$email = $user_id['email'];
			
			if($contestinfo[0]['visibility']=='p')
			{
			                $privat_user['user_id']=$groupmemberlist[$j];
							$privat_user['contest_id']=$contest_id;
							$privat_user['requesteddate']=date('Y-m-d H:i:s');
							$privat_user['status']=1;
						$privatecontestcnt = privateusercontestModel::where('user_id',$groupmemberlist[$j])->where('contest_id',$contest_id)->get()->count();
						if($privatecontestcnt==0)
							privateusercontestModel::create($privat_user);
			
			}
			
			
			///////
				if($gcmid!=''){
				$Message['user_id']=$groupmemberlist[$j];
				$Message['title']='Ding Datt';
				$Message['message']='You are invited for the Contest :'.$contestname;
				$Message['contest_id']=$inpudetails['contest_id'];
				$Message = array("notification"=>$Message); 
				$DeviceId = array($gcmid);
                $Message = array("notification"=>$Message);
                $this->PushNotification($DeviceId, $Message);
				
				} else { 
				$email = $user_id['email'];
							
				
				Mail::send([],
					array('email' => $email,'contestcreatedby'=>$contestcreatedby,'contesttype' => $contesttype,'contestname'=>$contestname,'contest_id'=>$contest_id,'$groupname'=>$groupname), function($message) use ($email,$contestcreatedby,$contesttype,$contestname,$contest_id,$groupname)
					{
						 $mail_body = '<body style="padding:0px;margin:-20px 0 0 0px; font-family: Arial, Helvetica, sans-serif; color: #222222; font-size:12px;">
						<div style="width:550px;height:auto; border:1px solid #d5d5d5;padding:0px;margin:0px;overflow:hidden;">
						
						<div style="display:block; margin:25px; overflow:hidden;">
						<div style="display:block; padding: 10px; border: 1px solid #e5e5e5; margin:10px 0px;">
							<span style="padding:0px;margin:0px;font-weight:bold;">Invitation for join the contest.</span>
						</div>
						<div style="display: block; margin: 15px;">
							 <h4 style="padding:0px;margin:0; font-size:14px;color:#d33030;">Contest Details:</h4>
						</div>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;"> Contest Name:</span>'.$contestname.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Contest Type:</span>'.$contesttype.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Created by:</span>'.$contestcreatedby.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Group Name by:</span>'.$groupname.'</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;">This is auto generate email so please do not reply to this email.</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;"><a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">'.URL::to('contest_info/'.$contest_id).'</a> </p>
						<div style="padding:0; margin:15px;">
						<p style="padding:0px; font-weight: bold;">Thanks,</p>
						DingDatt</div>
						<a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">
						<img src="'.URL::to('assets/inner/img/DingDatt_logo_web1.png').'" width="110" height="86" style="width:110px; padding:0px; margin:0px;" alt="DingDatt"/>
						</a>
						</div>
						<div style="height:25px; width:100%;">&nbsp;</div>
						</div>
						</body>'; 
						
						$message->setBody($mail_body, 'text/html');
						$message->to($email);
						$message->subject('Dingdatt-Invitation for join the contest');
					});	
				}
				}
				//////Notification End //////////////
			}
	
		}
		$Response = array(
                'success' => '1',
                'message' => 'Invited Successfully',
            );
        $final=array("response"=>$Response);
        return json_encode($final);		
	 }
	 else{ 
		$Response = array(
                'success' => '0',
                'message' => 'Already Invited these Groups',
            );
        $final=array("response"=>$Response);
        return json_encode($final);	
	 
	 }
	
	}
	else{
		
	 $validator = Validator::make($inpudetails,invitegroupforcontestModel::$rules);
		if ($validator->passes()) 
        {
				
		$invite = invitegroupforcontestModel::create($inpudetails);
		 
		
		 	    
		$inpudetails['group_id']=Input::get('group_id');
		if(Input::get('group_id')!='')
	{
	$groupowner = groupModel::select('user.firstname','user.lastname','user.username','group.grouptype','group.groupname')->where('group.ID',Input::get('group_id'))->LeftJoin('user','user.ID','=','group.createdby')->get();

	if($groupowner[0]['firstname']!='')
	$inviter=$groupowner[0]['firstname'] ." ".$groupowner[0]['lastname'];
	else
	$inviter=$groupowner[0]['username'];
	}
		
		//invitegroupforcontestModel::create($inpudetails);
		
			/******** Here want to set the Notification for Group Members ***/
			$groupname = $groupowner[0]['groupname'];
			$groupmemberlist = groupmemberModel::where('group_id',Input::get('group_id'))->lists('user_id');	

				//return $contestinfo[0]['createdby'];
				//return $groupmemberlist[0];
				
				$temp=array();
				
			for($j=0;$j<count($groupmemberlist);$j++){ 
				//////Notification////////////////////	
		 //return (int)$groupmemberlist[$j].'s'.$contestinfo[0]['createdby'].'ad';
		$groupid1 = (int)$groupmemberlist[$j];
		 $contstcreated = (int)$contestinfo[0]['createdby'];
		 $temp[$j]="no";
			if($groupid1!=$contstcreated)
			{
			$temp[$j]="yes";
			//return "ABdgdfgdf";
			
			$user_id = User::find($groupmemberlist[$j]);
			$gcmid = $user_id['gcm_id'];;
			$email = $user_id['email'];
			
			if($contestinfo[0]['visibility']=='p')
			{
			                $privat_user['user_id']=$groupmemberlist[$j];
							$privat_user['contest_id']=$contest_id;
							$privat_user['requesteddate']=date('Y-m-d H:i:s');
							$privat_user['status']=1;
							$privatecontestcnt = privateusercontestModel::where('user_id',$groupmemberlist[$j])->where('contest_id',$contest_id)->get()->count();
							if($privatecontestcnt==0)
							privateusercontestModel::create($privat_user);
			
			}
			
				if($gcmid!=''){
				$Message['user_id']=$groupmemberlist[$j];
				$Message['title']='Ding Datt';
				$Message['message']='You are invited for the Contest :'.$contestname;
				$Message['contest_id']=$inpudetails['contest_id'];
				$Message = array("notification"=>$Message); 
				$DeviceId = array($gcmid);
                $Message = array("notification"=>$Message);
                $this->PushNotification($DeviceId, $Message);
				
				} else { 
				$email = $user_id['email'];
				
						
					Mail::send([],
					array('email' => $email,'contestcreatedby'=>$contestcreatedby,'contesttype' => $contesttype,'contestname'=>$contestname,'contest_id'=>$contest_id,'groupname'=>$groupname), function($message) use ($email,$contestcreatedby,$contesttype,$contestname,$contest_id,$groupname)
					{
						 $mail_body = '<body style="padding:0px;margin:-20px 0 0 0px; font-family: Arial, Helvetica, sans-serif; color: #222222; font-size:12px;">
						<div style="width:550px;height:auto; border:1px solid #d5d5d5;padding:0px;margin:0px;overflow:hidden;">
						
						<div style="display:block; margin:25px; overflow:hidden;">
						<div style="display:block; padding: 10px; border: 1px solid #e5e5e5; margin:10px 0px;">
							<span style="padding:0px;margin:0px;font-weight:bold;">Invitation for join the contest.</span>
						</div>
						<div style="display: block; margin: 15px;">
							 <h4 style="padding:0px;margin:0; font-size:14px;color:#d33030;">Contest Details:</h4>
						</div>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;"> Contest Name:</span>'.$contestname.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Contest Type:</span>'.$contesttype.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Created by:</span>'.$contestcreatedby.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Group Name by:</span>'.$groupname.'</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;">This is auto generate email so please do not reply to this email.</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;"><a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">'.URL::to('contest_info/'.$contest_id).'</a> </p>
						<div style="padding:0; margin:15px;">
						<p style="padding:0px; font-weight: bold;">Thanks,</p>
						DingDatt</div>
						<a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">
						<img src="'.URL::to('assets/inner/img/DingDatt_logo_web1.png').'" width="110" height="86" style="width:110px; padding:0px; margin:0px;" alt="DingDatt"/>
						</a>
						</div>
						<div style="height:25px; width:100%;">&nbsp;</div>
						</div>
						</body>'; 
						
						$message->setBody($mail_body, 'text/html');
						$message->to($email);
						$message->subject('Dingdatt-Invitation for join the contest');
					});
				}
				}
				//////Notification End //////////////
			}
		 
		 //return $temp;
		 //////
		 $Response = array(
                'success' => '1',
                'message' => 'You are Invited That group Successfully',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
		 
		}
		else
		{
		 $Response = array(
                'success' => '0',
                'message' => 'Required Details are Missing',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
		}
}		
}
public function invitefollowesforcontest()
{
//invitefollowerforcontestModel
$invite_type = Input::get('invite_type');
$contest_id = Input::get('contest_id');
 $curdate = date('Y-m-d h:i:s');
$inpudetails['contest_id']=$contest_id;
$inpudetails['invitedate']=$curdate;

 $contestdetails = contestModel::select('contest.createdby','contest.visibility','contest.contest_name','contest.contesttype','user.firstname','user.lastname','user.username')->LeftJoin('user','user.ID','=','contest.createdby')->where('contest.ID',$contest_id)->get();
$userid=$contestdetails[0]['createdby'];

if($contestdetails[0]['firstname']!=''){ $contestcreatedby = $contestdetails[0]['firstname'].' '.$contestdetails[0]['lastname']; } else { $contestcreatedby =$contestdetails[0]['username']; }
					
					$contesttype = $contestdetails[0]['contesttype'];
					if($contesttype=='p') $contesttype="Photo"; else if($contesttype=='v') $contesttype="Video"; else if($contesttype=='t') $contesttype="Topic";
					$contestname =  $contestdetails[0]['contest_name'];

//$userid = Input::get('user_id');
if($invite_type=='All')
{
		$invitedlis = invitefollowerforcontestModel::where('contest_id',$contest_id)->lists('follower_id');		
		$invitedcnt = count($invitedlis);
		if($invitedcnt)
		{
		$uninvitedfollower = followModel::where('userid',Input::get('user_id'))->whereNotIn('id', $invitedlis)->lists('id');		
		}else{		
		 $uninvitedfollower =followModel::where('userid',Input::get('user_id'))->lists('id');
		}
		

		
	 if(count($uninvitedfollower))
	 {
		for($i=0;$i<count($uninvitedfollower);$i++){		
		$inpudetails['follower_id']=$uninvitedfollower[$i];
		invitefollowerforcontestModel::create($inpudetails);
		
			/******** Here want to set the Notification for Group Members *********/
			
			//return $uninvitedfollower[$i];
			
		 $folloerdetails = followModel::select('user.firstname','user.lastname','user.username','user.ID as follower_user_id','user.gcm_id','user.email')->LeftJoin('user','user.ID','=','followers.followerid')->where('followers.id',$uninvitedfollower[$i])->get();
		
		 $gcmid = $folloerdetails[0]['gcm_id'];
		 $email = $folloerdetails[0]['email'];
		
 	
			if($contestdetails[0]['visibility']=='p')
			{
			                $privat_user['user_id']=$folloerdetails[0]['follower_user_id'];
							$privat_user['contest_id']=$contest_id;
							$privat_user['requesteddate']=date('Y-m-d H:i:s');
							$privat_user['status']=1;
							$privatecontestcnt = privateusercontestModel::where('user_id',$folloerdetails[0]['follower_user_id'])->where('contest_id',$contest_id)->get()->count();
							if($privatecontestcnt==0)
							privateusercontestModel::create($privat_user);
			
			}
			
				if($gcmid!=''){
				$Message['user_id']=$folloerdetails[0]['follower_user_id'];
				$Message['title']='Ding Datt';
				$Message['message']='You are invited for the Contest :'.$contestdetails[0]['contest_name'];
				$Message['contest_id']=$contest_id;
				$Message = array("notification"=>$Message); 
				$DeviceId = array($gcmid);
                $Message = array("notification"=>$Message);
                $this->PushNotification($DeviceId, $Message);
				
				} else { 
								
					//$contestcreatedby= User::find($contestdetails[0]['createdby']);
				
			
					
					Mail::send([],
					array('email' => $email,'contestcreatedby'=>$contestcreatedby,'contesttype' => $contesttype,'contestname'=>$contestname,'contest_id'=>$contest_id), function($message) use ($email,$contestcreatedby,$contesttype,$contestname,$contest_id)
					{
						 $mail_body = '<body style="padding:0px;margin:-20px 0 0 0px; font-family: Arial, Helvetica, sans-serif; color: #222222; font-size:12px;">
						<div style="width:550px;height:auto; border:1px solid #d5d5d5;padding:0px;margin:0px;overflow:hidden;">
						
						<div style="display:block; margin:25px; overflow:hidden;">
						<div style="display:block; padding: 10px; border: 1px solid #e5e5e5; margin:10px 0px;">
							<span style="padding:0px;margin:0px;font-weight:bold;">Invitation for join the contest.</span>
						</div>
						<div style="display: block; margin: 15px;">
							 <h4 style="padding:0px;margin:0; font-size:14px;color:#d33030;">Contest Details:</h4>
						</div>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;"> Contest Name:</span>'.$contestname.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Contest Type:</span>'.$contesttype.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Created by:</span>'.$contestcreatedby.'</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;">This is auto generate email so please do not reply to this email.</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;"><a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">'.URL::to('contest_info/'.$contest_id).'</a> </p>
						<div style="padding:0; margin:15px;">
						<p style="padding:0px; font-weight: bold;">Thanks,</p>
						DingDatt</div>
						<a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">
						<img src="'.URL::to('assets/inner/img/DingDatt_logo_web1.png').'" width="110" height="86" style="width:110px; padding:0px; margin:0px;" alt="DingDatt"/>
						</a>
						</div>
						<div style="height:25px; width:100%;">&nbsp;</div>
						</div>
						</body>'; 
						
						$message->setBody($mail_body, 'text/html');
						$message->to($email);
						$message->subject('Dingdatt-Invitation for join the contest');
					}); 
				}  
					
		}
		$Response = array(
                'success' => '1',
                'message' => 'Invited Successfully',
            );
        $final=array("response"=>$Response);
        return json_encode($final);		
	 }
	 else
	 {
	 $Response = array(
                'success' => '0',
                'message' => 'Already Invited All Followers',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	 }

 }else{
 
	$user_id = Input::get('user_id');
	 $follower_id = Input::get('follower_id');
	 $inpudetails['follower_id'] = $follower_id; 
	 invitefollowerforcontestModel::create($inpudetails);
		
			/******** Here want to set the Notification for Group Members *********/
			$groupmemberlist = followModel::select('id')->where('userid',$user_id)->where('followerid',$follower_id)->get();
			
			//return $groupmemberlist[0]['id'];
			///
		 $folloerdetails = followModel::select('user.firstname','user.lastname','user.username','user.ID as follower_user_id','user.gcm_id','user.email')->LeftJoin('user','user.ID','=','followers.followerid')->where('followers.id',$groupmemberlist[0]['id'])->get();
		
		  $gcmid = $folloerdetails[0]['gcm_id'];
		  $email = $folloerdetails[0]['email'];
		
 	
			if($contestdetails[0]['visibility']=='p')
			{
			                $privat_user['user_id']=$folloerdetails[0]['follower_user_id'];
							$privat_user['contest_id']=$contest_id;
							$privat_user['requesteddate']=date('Y-m-d H:i:s');
							$privat_user['status']=1;
							$privatecontestcnt = privateusercontestModel::where('user_id',$folloerdetails[0]['follower_user_id'])->where('contest_id',$contest_id)->get()->count();
							if($privatecontestcnt==0)
							privateusercontestModel::create($privat_user);
			
			}
			
			
				if($gcmid!=''){
				$Message['user_id']=$folloerdetails[0]['follower_user_id'];
				$Message['title']='Ding Datt';
				$Message['message']='You are invited for the Contest :'.$contestdetails[0]['contest_name'];
				$Message['contest_id']=$contest_id;
				$Message = array("notification"=>$Message); 
				$DeviceId = array($gcmid);
                $Message = array("notification"=>$Message);
                $this->PushNotification($DeviceId, $Message);
				
				} else { 
								
					//$contestcreatedby= User::find($contestdetails[0]['createdby']);
				
					Mail::send([],
					array('email' => $email,'contestcreatedby'=>$contestcreatedby,'contesttype' => $contesttype,'contestname'=>$contestname,'contest_id'=>$contest_id), function($message) use ($email,$contestcreatedby,$contesttype,$contestname,$contest_id)
					{
						 $mail_body = '<body style="padding:0px;margin:-20px 0 0 0px; font-family: Arial, Helvetica, sans-serif; color: #222222; font-size:12px;">
						<div style="width:550px;height:auto; border:1px solid #d5d5d5;padding:0px;margin:0px;overflow:hidden;">
						
						<div style="display:block; margin:25px; overflow:hidden;">
						<div style="display:block; padding: 10px; border: 1px solid #e5e5e5; margin:10px 0px;">
							<span style="padding:0px;margin:0px;font-weight:bold;">Invitation for join the contest.</span>
						</div>
						<div style="display: block; margin: 15px;">
							 <h4 style="padding:0px;margin:0; font-size:14px;color:#d33030;">Contest Details:</h4>
						</div>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;"> Contest Name:</span>'.$contestname.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Contest Type:</span>'.$contesttype.'</p>
						<p style="margin:15px;"><span style="font-weight:bold; width:150px; float:left; display:inline-block;">Created by:</span>'.$contestcreatedby.'</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;">This is auto generate email so please do not reply to this email.</p>
						<p style="margin-top:25px; font-size:11px; color: #999999; margin-left:15px;"><a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">'.URL::to('contest_info/'.$contest_id).'</a> </p>
						<div style="padding:0; margin:15px;">
						<p style="padding:0px; font-weight: bold;">Thanks,</p>
						DingDatt</div>
						<a href="'.URL::to('contest_info/'.$contest_id).'" style="dislay:block;outline: none; padding:25px;margin:25px; min-height:110px; width:100%; overflow:hidden;">
						<img src="'.URL::to('assets/inner/img/DingDatt_logo_web1.png').'" width="110" height="86" style="width:110px; padding:0px; margin:0px;" alt="DingDatt"/>
						</a>
						</div>
						<div style="height:25px; width:100%;">&nbsp;</div>
						</div>
						</body>'; 
						
						$message->setBody($mail_body, 'text/html');
						$message->to($email);
						$message->subject('Dingdatt-Invitation for join the contest');
					}); 
				}  
			
			////
	
		$Response = array(
                'success' => '1',
                'message' => 'Invited Successfully',
            );
        $final=array("response"=>$Response);
        return json_encode($final);	
}
}
public function getfollowerlistforinvitecontest()
{
	$contest_id = Input::get('contest_id');
	$userid = contestModel::select('createdby')->where('ID',$contest_id)->get();
	$userid=$userid[0]['createdby'];

	$followers = followModel::where('userid',$userid)
	->select('followers.id as followerprimaryid','user.profilepicture','user.firstname','user.ID as followerid')
	->leftJoin('user','user.ID','=','followers.followerid')
	->get();
	
	
for($k=0;$k<count($followers);$k++)
		{
		$invitecnt = invitefollowerforcontestModel::where('follower_id',$followers[$k]['followerprimaryid'])->where('contest_id',$contest_id)->get()->count();
		$followers[$k]['invite']= $invitecnt;	
		}	

				$Response = array(
					'success' => '1',
					'message' => 'Followers  List',
				);
			   $final=array("response"=>$Response,"followerlist"=>$followers);
			   return json_encode($final);
}

public function forgotpassword()
{


		$requestusername=Input::get('username');
		$lantyp = Session::get('language');
	
		if($requestusername=="")
		{

		}
        $UserDetails = User::where('username', $requestusername)->orWhere('email',$requestusername)->get()->toArray();
        if ($UserDetails) 
        {
        $string = str_random(5);     
        $passworddata = User::find($UserDetails[0]['ID']);
        $email=$UserDetails[0]['email'];
		$username=$UserDetails[0]['username'];       
        $passworddata->password = $string;
        $passworddata->save();

        Mail::send([],
		array('pass' => $string,'email' => $email,'username' => $username), function($message) use ($string,$email,$username)
		{

		$mail_body = "Dear {username},<br><br>Your Forgot password request Received.Your Password details is<br><br>Password: {password} <br><br> Thank You, <br><br>Regards,<br>DingDatt";
        $mail_body = str_replace("{password}", $string, $mail_body);
        $mail_body = str_replace("{username}", $username, $mail_body);
        $message->setBody($mail_body, 'text/html');
        $message->to($email);
        $message->subject('Password Details - DingDatt');
        });

        $Response = array(
					'success' => '1',
					'message' => 'Your Password send to your Mail',
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);
        }
        else
        {
		$Response = array(
					'success' => '0',
					'message' => 'Your Email Id and Password is Invalid',
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);
		
        }
        

}
public function getinterest()
{
	//$interestList=InterestCategoryModel::lists('Interest_name','Interest_id');
	 $interestList=InterestCategoryModel::select('Interest_name','Interest_id')->get()->toArray();
	$Response = array(
					'success' => '1',
					'message' => 'Interest Details are fetched Successfully',
				);
			   $final=array("response"=>$Response,"interestdetails"=>$interestList);
			   return json_encode($final);

}

public function participatedcontest()
{
	$userid = Input::get('userid');
	$timezone = Input::get('timezone');

	$participants=contestparticipantModel::where('user_id',$userid)->lists('contest_id'); 
	$participatedcontest=contestModel::select('ID','contest_name','themephoto','contestenddate','conteststartdate','votingstartdate','votingenddate','prize','createdby','description','noofparticipant')->whereIn('ID',$participants)->get();
	
	if(count($participants)!=0)
	{
	for($i=0; $i<count($participatedcontest);$i++){ 
	
	$participants=contestparticipantModel::where('user_id',$userid)->where('contest_id',$participatedcontest[$i]['ID'])->get()->count(); 
	
	if($participants) $participatedcontest[$i]['contestparticipantid']=1; else $participatedcontest[$i]['contestparticipantid']=0;
	
	$participatedcontest[$i]['contestenddate'] = timezoneModel::convert($participatedcontest[$i]['contestenddate'],'UTC',$timezone, 'd-M-Y h:i a'); 
	
	$participatedcontest[$i]['conteststartdate'] = timezoneModel::convert($participatedcontest[$i]['conteststartdate'],'UTC',$timezone, 'd-M-Y h:i a');
	
	$participatedcontest[$i]['votingstartdate'] = timezoneModel::convert($participatedcontest[$i]['votingstartdate'],'UTC',$timezone, 'd-M-Y h:i a');
	
	$participatedcontest[$i]['votingenddate'] = timezoneModel::convert($participatedcontest[$i]['votingenddate'],'UTC',$timezone, 'd-M-Y h:i a');
	
	
	}

		$Response = array(
						'success' => '1',
						'message' => 'Participated Contest details are fetched Successfully',
					);
				   $final=array("response"=>$Response,"participatedcontest"=>$participatedcontest);
				   return json_encode($final);
	}
	else{ 
		$Response = array(
						'success' => '0',
						'message' => 'No Data Available',
					);
				   $final=array("response"=>$Response);
				   return json_encode($final);

	}
}

public function createdcontest()
{
$userid = Input::get('userid');
$timezone = Input::get('timezone');
$createdcontest=contestModel::select('ID','contest_name','themephoto','contestenddate','conteststartdate','votingstartdate','votingenddate','prize','createdby','description','noofparticipant')->where('createdby',$userid)->get();
if(count($createdcontest)!=0)
{
for($i=0; $i<count($createdcontest);$i++){ 

$createdcontest[$i]['contestenddate'] = timezoneModel::convert($createdcontest[$i]['contestenddate'],'UTC',$timezone, 'd-M-Y h:i a'); 

$createdcontest[$i]['conteststartdate'] = timezoneModel::convert($createdcontest[$i]['conteststartdate'],'UTC',$timezone, 'd-M-Y h:i a');
	
	$createdcontest[$i]['votingstartdate'] = timezoneModel::convert($createdcontest[$i]['votingstartdate'],'UTC',$timezone, 'd-M-Y h:i a');
	
	$createdcontest[$i]['votingenddate'] = timezoneModel::convert($createdcontest[$i]['votingenddate'],'UTC',$timezone, 'd-M-Y h:i a');

$participants=contestparticipantModel::where('user_id',$userid)->where('contest_id',$createdcontest[$i]['ID'])->get()->count(); 
	
	if($participants) $createdcontest[$i]['contestparticipantid']=1; else $createdcontest[$i]['contestparticipantid']=0;
}

	$Response = array(
					'success' => '1',
					'message' => 'Created Contest details are fetched Successfully',
				);
			   $final=array("response"=>$Response,"createdcontest"=>$createdcontest);
			   return json_encode($final);
}
else{ 
	$Response = array(
					'success' => '0',
					'message' => 'No Data Available',
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);

}
}
//////////////need to modify///////////////////////


///Admin join the member to their group
public function joingroup()
	{
	$userid = Input::get('userid');
	$group_id = Input::get('group_id');
	$groupdetails = groupModel::select('grouptype','groupname')->where('id',$group_id)->get();
	$grouptype = $groupdetails[0]['grouptype'];
	$curdate = date('Y-m-d h:i:s');
	$useridexplode=explode(',',$userid); 
	$countuserexplode = count($useridexplode);
	$inputdetails['user_id']=$userid;
	$inputdetails['group_id']=$group_id;
	$inputdetails['createddate']=$curdate;

	if($grouptype=='open'){ 


		$validation  = Validator::make($inputdetails, groupmemberModel::$rules);
		if ($validation->passes()){ 
			
			for($i=0;$i<$countuserexplode;$i++){ 
				$inputdetails['user_id']=$useridexplode[$i];
				$savegroupmembers = groupmemberModel::create($inputdetails);
			}		
			if($savegroupmembers)
			{
			   $Response = array(
						'success' => '1',
						'message' => 'Group Members saved successfully',
					);
				$final=array("response"=>$Response);
				return json_encode($final);
			
			}
			}
			else{ 
				$Response = array(
						'success' => '0',
						'message' => 'Some Details Missing',
					);
				$final=array("response"=>$Response);
				return json_encode($final);
			}
	}
	else{
	////////// Private send invite to members  
	$inputdetails['inviteddate']=$curdate;
	
	 
		for($i=0;$i<$countuserexplode;$i++){ 
				 $inputdetails['user_id']=$useridexplode[$i];
				 $inputdetails['invitetype']='m';
				$invite = invitememberforgroupModel::create($inputdetails);
				if($invite){
				$userid = $useridexplode[$i];
				$getcreateduserdetails = ProfileModel::select('email','firstname','lastname')->where('ID',$userid)->get();
				$email = $getcreateduserdetails[0]['email'];
				$name = $getcreateduserdetails[0]['firstname'].' '.$getcreateduserdetails[0]['lastname'];
				$groupname = $groupdetails[0]['groupname'];
				$url = 'http://192.168.1.52/dingdatt';
				
				/* HERE SET THE NOTIFIATION */
				
				/*	Mail::send([],
			 array('email' => $email,'name' => $name,'groupname' => $groupname,'url' =>$url,'userid'=>$userid,'group_id' =>$group_id), function($message) use ($email,$name,$groupname,$url,$userid,$group_id)
			{
								
				$mail_body = "Dear {name}, we are Inviting you for joining the group {groupname} in Ding Datt. To activate the Group account Please <a href={URL}>click here</a>";
				
				$mail_body = str_replace("{name}", $name, $mail_body);
				$mail_body = str_replace("{groupname}", $groupname, $mail_body);
				 $mail_body = str_replace("{URL}", $url.'/acceptgroupadminrequest/userid='.$userid.'&groupid='.$group_id, $mail_body);
				$message->setBody($mail_body, 'text/html');
				$message->to($email);
				$message->subject('Invite Member for group Ding Datt');
				});  */
			
			
			
			}
			}
	  $Response = array(
						'success' => '1',
						'message' => 'we are Invite the Members successfully',
					);
				$final=array("response"=>$Response);
				return json_encode($final);
	}
}
///// This is accept both side requests ///////
public function groupmemberaccepttheadminrequest()
{
	$userid=Input::get('userid');	
	$userid = explode(',',$userid);
	$useridcount = count($userid);	
	$groupid = Input::get('group_id');
	$curdate = date('Y-m-d h:i:s');
	$inputdetails['group_id']=$groupid;	
	$inputdetails['createddate']=$curdate;
	$accepttype = Input::get('accepttype');
	
	for($i=0;$i<$useridcount;$i++)
	{
	$inputdetails['user_id']=$userid[$i];
	if($accepttype=='accept')
	{
	$saved = groupmemberModel::create($inputdetails);
	if($saved)
	{
		$invite = invitememberforgroupModel::where('group_id',$groupid)->where('user_id',$userid)->delete();
		if($invite)
		{
			$Response = array(
							'success' => '1',
							'message' => 'You are Accepted to that Group',
						);
					$final=array("response"=>$Response);
					return json_encode($final);
		}
	}
	}
	else{
	
	$invite = invitememberforgroupModel::where('group_id',$groupid)->where('user_id',$userid)->delete();
		if($invite)
		{
			$Response = array(
							'success' => '0',
							'message' => 'You are Rejected',
						);
					$final=array("response"=>$Response);
					return json_encode($final);
		}
	
	}
	}
	
}
//////////Member Send Request to group admin ///////
public function memberequeattogroup()
{
    $userid = Input::get('userid');
	$group_id = Input::get('group_id');
	$groupdetails = groupModel::select('grouptype','groupname')->where('id',$group_id)->get();
	$grouptype = $groupdetails[0]['grouptype'];
	$curdate = date('Y-m-d h:i:s');

	$inputdetails['user_id']=$userid;
	$inputdetails['group_id']=$group_id;
	$inputdetails['createddate']=$curdate;
	$inputdetails['invitetype']='u';
		
	if($grouptype=='open'){ 
    $validation  = Validator::make($inputdetails, groupmemberModel::$rules);
	if ($validation->passes()){ 
	      $savegroupmembers = groupmemberModel::create($inputdetails);
					
			if($savegroupmembers)
			{
			   $Response = array(
						'success' => '1',
						'message' => 'Group Members saved successfully',
					);
				$final=array("response"=>$Response);
				return json_encode($final);			
			}
	
	}
	}
	else{ 
	$invite = invitememberforgroupModel::create($inputdetails);
				if($invite){
				
				$getcreateduserdetails = ProfileModel::select('email','firstname','lastname')->where('ID',$userid)->get();
				$email = $getcreateduserdetails[0]['email'];
				$name = $getcreateduserdetails[0]['firstname'].' '.$getcreateduserdetails[0]['lastname'];
				$groupname = $groupdetails[0]['groupname'];
				/* Here Set the Notification for send to Group admin */
				$Response = array(
						'success' => '1',
						'message' => 'Your request sent to Group admin',
					);
				$final=array("response"=>$Response);
				return json_encode($final);
				
				}
		}
}



public function dropbox()
{

//return Dropbox::getSecret();

//return Path::checkArgNonRoot("fromPath", $fromPath);

//return Dropbox::getUserLocale();

//return Dropbox::getClientIdentifier( );

//return Dropbox::getAccessToken();
return View::make('dropbox/dropbox');
//return "SD";
}

 public function PushNotification($DeviceId,$Message) 
    {
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
}
?>