<?php
require_once "dropbox-sdk/lib/Dropbox/autoload.php";

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
				'msgcode' =>"c101",
            );
			$final=array("response"=>$Response);
			return json_encode($final);

		}
        elseif (array('email' => $user_name, 'password' => $password)||array('username' => $user_name, 'password' => $password))
		{  
		
		$userid =  ProfileModel::select('ID','password')->where('username',$user_name)->Orwhere('email',$user_name)->get()->count();
		if($userid)
		{
		
		 $userid =  ProfileModel::select('ID','password','status','username','firstname','lastname','dateformat')->where('username',$user_name)->Orwhere('email',$user_name)->get();
	    if (Hash::check($password, $userid[0]['password']))
		{
			    $status = $userid[0]['status'];
				$dateformat =  $userid[0]['dateformat'];
				if($userid[0]['firstname']!=''){ $name = $userid[0]['firstname'].' '.$userid[0]['lastname']; }else{ $name = $userid[0]['username']; }
				$userid = $userid[0]['ID'];
				
				
				
				$update['gcm_id'] = Input::get('gcm_id');
				$update['device_id'] = Input::get('device_id');
				$update['device_type'] = Input::get('device_type');
				
				$affectedRows = ProfileModel::where('ID', $userid)->update($update); 
				if($dateformat=='mm/dd/yy') $dateformat="df1"; else $dateformat="df2";
			
             if($status==1){
				$Response = array(
					'success' => '1',
					'message' => 'successfully Login',
					'userid' =>$userid,
					'name' =>$name,
					'dateformat' =>$dateformat,
					'msgcode' =>"c102",
				);
				$final=array("response"=>$Response);
				return json_encode($final);
			}else{ 
			
				$admindetails=User::select('email')->where('ID',1)->first();
				
				$Response = array(
					'success' => '0',
					'message' => "Your account is inactive please contact admin (".$admindetails->email.")",
					'mailid' =>  $admindetails->email,
					'msgcode' =>"c196",
				);
				$final=array("response"=>$Response);
				return json_encode($final);
			
			}
		  
		}
		else{ 
			
			$Response = array(
                'success' => '0',
                'message' => 'Invalid Password',
				'msgcode' =>"c103",
            );
			$final=array("response"=>$Response);
			return json_encode($final);		
		}
           }
			else{ 
			$Response = array(
                'success' => '0',
                'message' => 'No user in this record',	
				'msgcode' =>"c104",
            );
			$final=array("response"=>$Response);
			return json_encode($final);	
			
			}
		}else
        {
		$Response = array(
                'success' => '0',
                'message' => "Invalid Username Or Password",
				'msgcode' =>"c105",
				
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
	$data['status'] = 1;
	$data['createddate'] = date('Y-m-d h:i:s');
	$data['dateformat'] = "mm/dd/yy";
	$validator = Validator::make($data,ProfileModel::$rules);
	if ($validator->fails())
		{			
			$Response = array(
			'success' => '0',
			'message' => $validator->messages()->first(),
			'msgcode' =>"c106",
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
	/* Mail::send([],
			array('pass' => $pass,'email' => $email,'username' => $username), function($message) use ($pass,$email,$username)
			{
			
			$mail_body = "Dear {username},<br><br>Your DingDatt Registration successfully completed.Your Login details are<br><br>Email: {email}<br>Username: {username}<br>Password: {password} <br><br> Thank You, <br><br>Regards,<br>DingDatt";
			$mail_body = str_replace("{password}", $pass, $mail_body);
			$mail_body = str_replace("{username}", $username, $mail_body);
			$mail_body = str_replace("{email}",$email,$mail_body);
			$message->setBody($mail_body, 'text/html');
			$message->to($email);
			$message->subject('DingDatt Registration');
			}); */
			
			Mail::send([],
			array('pass' => $pass,'email' => $email,'username' => $username), function($message) use ($pass,$email,$username)
			{
					
			 /* $mail_body = '<style>.thank{text-align:center; width:100%;}
					.but_color{color:#ffffff;}
					.cont_name{width:100px;}
					.cont_value{width:500px;}
					
					</style>
				<body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">
				<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
					&nbsp;&nbsp;<a href="'.URL().'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px; line-height:20px;" /></a>&nbsp;&nbsp;
				</div>
				<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
					<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear '.$username.'</div>
					
					<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your DingDatt Registration successfully completed.Your Login details are<br><br>Username: '.$username.'<br>Password: '.$pass.'
				</div>
				<div style="margin:10px;"><a href="'.URL().'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
				</div>
				</div>					
				<div style="font-size:12px; margin-top:10px;color: #5b5b5b; width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">				
				</body>'; */ 
				
				$mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="font-family:Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%; width:100%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear ' . $username . '</div>
								
								<table width="100%"><tr style="height:10px;"><td></td></tr><tr><td style="height:30px;">
								Your DingDatt Registration successfully completed.Your Login details are
								</td></tr>
								<tr><td style="height:30px;">Username: ' . $username . '</td></tr>
								<tr><td style="height:30px;">Password: ' . $pass . '</td></tr>
								<tr><td style="height:45px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a></td></tr>
								<tr><td style="border-top:#005377 1px solid; height:30px;"><span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span></td></tr>
								</table>
							</div>
														
							
							</body>';
			$message->setBody($mail_body, 'text/html');
			$message->to($email);
			$message->subject('DingDatt Registration');
			});
		$Response = array(
			'success' => '1',
			'message' => 'Record added successfully',
			'msgcode' =>"c107",
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
	
	
		if($profile->status==1){
		
			$dateofbirth = $profile->dateofbirth;
			
			if($profile->dateofbirth!='0000-00-00')
			$profile->dateofbirth = timezoneModel::convert($dateofbirth, 'UTC',$timezone, 'Y-m-d');
			
			
			$createddate = $profile->createddate;
			$profile->createddate = timezoneModel::convert($createddate, 'UTC',$timezone, 'Y-m-d');
			
			//return $profile->profilepicture;
			
			if($profile->profilepicture!=''){ 
			$profile->profileimagename = $profile->profilepicture;
			$profile->profilepicture  =  url().'/public/assets/upload/profile/'.$profile->profilepicture; 
			}
			
			$interestdetails = userinterestModel::select('interest_category.interest_id','interest_category.Interest_name')->where('user_id',$userid)->LeftJoin('interest_category','interest_category.interest_id','=','user_interest.interest_id')->where('interest_category.status',1)->get();
		
			$profile->interest = $interestdetails;
			
			$Response = array(
				'success' => '1',
				'message' => 'Profile Details',
				'msgcode' =>"c108",
			);
			$final=array("response"=>$Response, "profile"=>$profile);
			return json_encode($final);
		}else{

			$Response = array(
				'success' => '0',
				'message' => 'Inactive user',
				'msgcode' =>"c196",
			);
			$final=array("response"=>$Response, "profile"=>$profile);
			return json_encode($final);
		
		}
	
	//DB::select("SELECT user.*,DATE_FORMAT(CONVERT_TZ(`dateofbirth`,'+00:00','$timezone'),'%Y-%m-%d') as converteddateofbirth  FROM user WHERE ID =$userid");

}

public function editmyprofile()
{
    
	$GeneralData= array_filter(Input::except(array('_token','passwordhidden','profilepicture','interest_id','userid','timezone','dateofbirth')));
	 
	$timezone = Input::get('timezone');
	 
	 if(Input::get('dateofbirth')!='0000-00-00')
	$GeneralData['dateofbirth'] = timezoneModel::convert(Input::get('dateofbirth'), $timezone,'UTC', 'Y-m-d');
	
	 $newimg = Input::file('profilepicture');
	if($newimg!=''){ 
		$destinationPath = 'public/assets/upload/profile';
		$filename = Input::file('profilepicture')->getClientOriginalName();
		$Image = str_random(8).'_'.$filename;
		$GeneralData['profilepicture']= $Image;
		$uploadSuccess = Input::file('profilepicture')->move($destinationPath, $Image);
	}else{		
	//$GeneralData['profilepicture']=Input::get('profileimgedithidden');
	}
	$GeneralData['status']=1;	
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

		$affectedRows = ProfileModel::where('ID', $data)->update($GeneralData); 
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
		$profiledata = ProfileModel::where('ID', $data)->first(); 
		$dateformat =$profiledata->dateformat;
		
		if($GeneralData['dateformat']=='mm/dd/yy') $dateformat="df1"; else $dateformat="df2";
		
		$Response = array(
			'success' => '1',
			'message' => 'Record Updated successfully',
			'msgcode' =>"c109",
			'dateformat' =>$dateformat
		);
		
		$final=array("response"=>$Response);
		return json_encode($final);

        } else 
        {
		
		if($validation->messages()->first()=='The email has already been taken.') { $msgcode= "c110"; }else if($validation->messages()->first()=='The username has already been taken.'){ $msgcode= "c202"; }else{ $msgcode= "c203"; } 
		
		$Response = array(
			'success' => '0',
			'message' => $validation->messages()->first(),
			'msgcode' =>$msgcode,
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
		$inputdetails['status']=1;
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
						'msgcode' =>"c107",
					);
				   $final=array("response"=>$Response,"contest_id"=>$maxcontestid);
				   return json_encode($final);
		}
		else
		{
		
			$Response = array(
					'success' => '0',
					'message' => $validation->messages()->first(),
					'msgcode' =>"c112",
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);
		}
}
public function getcontestmobile()
{
	$timezone = Input::get('timezone');
	$contestid = Input::get('contestid');
    $contest = contestModel::where('status',1)->find($contestid);
	if($contest!='')
	{
		$contest['conteststartdate'];
		
		$interestdetails = contestinterestModel::select('interest_category.interest_id','interest_category.Interest_name')->where('contest_id',$contestid)->LeftJoin('interest_category','interest_category.interest_id','=','contest_interest_categories.category_id')->where('interest_category.status',1)->get();

		
		$contest->themephoto = url().'/public/assets/upload/contest_theme_photo/'.$contest->themephoto;
		
		$contest->sponsorphoto = url().'/public/assets/upload/sponsor_photo/'.$contest->sponsorphoto;
		
		$contest->conteststartdate = timezoneModel::convert($contest->conteststartdate, 'UTC', $timezone,'Y-m-d H:i:s');

		$contest->contestenddate = timezoneModel::convert($contest->contestenddate, 'UTC', $timezone, 'Y-m-d H:i:s');
		   
		$contest->votingstartdate = timezoneModel::convert($contest->votingstartdate, 'UTC', $timezone, 'Y-m-d H:i:s');
		   
		$contest->votingenddate = timezoneModel::convert($contest->votingenddate, 'UTC',$timezone, 'Y-m-d H:i:s');
		$contest->createddate = timezoneModel::convert($contest->createddate, 'UTC',$timezone, 'Y-m-d H:i:s');
		$contest->interest = $interestdetails;
		$Response = array(
							'success' => '1',
							'message' => 'Contest Details fetched Successfully',
							'msgcode' =>"c113",
						);
						
			$contestparticipantcount=contestparticipantModel::where('contest_id',$contestid)->get()->count();
			$contestparticipantcount = array('contestparticipantcount' => $contestparticipantcount);
		   $final=array("response"=>$Response,"contest Details"=>$contest,"contestparticipantcount"=>$contestparticipantcount);
		   return json_encode($final);
	}else{ 
		$Response = array(
							'success' => '0',
							'message' => 'Inactive contest',
							'msgcode' =>"c197",
						);
		 $final=array("response"=>$Response);
		   return json_encode($final);
	
	
	}
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
			
			$contestid=Input::get('contestid');	
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
					'msgcode' =>"c109",
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);			
		}
		else
		{
		
		     $Response = array(
                'success' => '0',
                'message' => $validation->messages()->first(),
				'msgcode' =>"c115",
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
						->where('contesttype',$contesttype)->where('contest.status','1')->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)->get()->count();
	
	
	$contestDetails=contestModel::select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')->where(function($query){ 
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
						->where('contesttype',$contesttype)->where('contest.status','1')->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)->orderby('contest.ID','DESC')->get();
     
}
else if($contestlisttype=='upcoming')
{
	 
	/*$contestDetailscount = contestModel::where('conteststartdate', '>', $currentdate)->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get()->count();
		
	$contestDetails = contestModel::where('conteststartdate', '>', $currentdate)->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get();  */
	
		$contestDetailscount=contestModel::where('conteststartdate', '>', $currentdate)
									->where('contesttype',$contesttype)
									->where('contest.status','1')
									->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)
									->get()->count();
	
	
	$contestDetails=contestModel::select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')->where('conteststartdate', '>', $currentdate)
									->where('contesttype',$contesttype)
									->where('contest.status','1')
									->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)
									->orderby('contest.ID','DESC')->get(); 
	
}
else if($contestlisttype=='archive')
{
	/* $contestDetailscount = contestModel::where('contestenddate', '<', $currentdate)
	->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby','contestparticipant.ID as contestparticipantid')->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('contest.status','1')
	->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)
	->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')->distinct()
	->get()->count();
		
	$contestDetails = contestModel::where('contestenddate', '<', $currentdate)
	->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby','contestparticipant.ID as contestparticipantid')
	->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('contest.status','1')
	->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)
	->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')->distinct()->orderby('contest.ID','DESC')
	->get(); */
	
	$contestDetailscount = contestModel::where('contestenddate', '<', $currentdate)
	->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('contest.status','1')
	->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)
	->get()->count();
		
	$contestDetails = contestModel::where('contestenddate', '<', $currentdate)
	->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')
	->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('contest.status','1')
	->where('visibility','u')->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)
	->orderby('contest.ID','DESC')
	->get();
	
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
										->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')->where('contest.status',1)									
										->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
										->where('private_contest_users.user_id',$loggeduserid)										
										->where('private_contest_users.status','1')->distinct()->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)->get()->count();

$contestDetails=contestModel::where('visibility','p')
										->where('contesttype',$contesttype)
										->select('contest.ID','contest_name','themephoto','description','noofparticipant','contest.prize','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.createdby')->where('contest.status',1)
										->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
										->where('private_contest_users.user_id',$loggeduserid)										
										->where('private_contest_users.status','1')->distinct()->leftJoin('user','user.ID','=','contest.createdby')->where('user.status',1)->orderby('contest.ID','DESC')->get();	

										


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
	
	$contestDetails[$i]->conteststartdate = timezoneModel::convert($contestDetails[$i]->conteststartdate, 'UTC',$timezone, 'd-m-Y h:i a');

	$contestDetails[$i]->contestenddate = timezoneModel::convert($contestDetails[$i]->contestenddate, 'UTC',$timezone, 'd-m-Y h:i a');
	   
	$contestDetails[$i]->votingstartdate = timezoneModel::convert($contestDetails[$i]->votingstartdate, 'UTC',$timezone, 'd-m-Y h:i a');
	   
	$contestDetails[$i]->votingenddate = timezoneModel::convert($contestDetails[$i]->votingenddate, 'UTC',$timezone, 'd-m-Y h:i a');
	
	$contestDetails[$i]->createddate = timezoneModel::convert($contestDetails[$i]->createddate, 'UTC', $timezone,'d-m-Y h:i a');
	
	$contestDetails[$i]->themephoto = url().'/public/assets/upload/contest_theme_photo/'.$contestDetails[$i]->themephoto;
	//themephoto
	}
    
	//return $contestDetails;
			$Response = array(
                'success' => '1',
                'message' => 'Data Get Successfully',
				'msgcode' =>"c116",
            );
            $final=array("response"=>$Response,'contestdetails'=>$contestDetails);
            return json_encode($final);
 }
 else{
 			$Response = array(
                'success' => '0',
                'message' => 'No Data Available',
				'msgcode' =>"c117",
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
	$contestDetails =  contestModel::where('ID', $contestid)->where('status',1)->get();
	 for($i=0;$i<count($contestDetails);$i++)
	{
	$contestparticipantid=contestparticipantModel::where('contest_id',$contestid)->where('user_id',$user_id)->get()->count();
	if($contestparticipantid) $contestDetails[$i]->contestparticipantid=1; else $contestDetails[$i]->contestparticipantid=0;
	
	$contestDetails[$i]->themephoto = url().'/public/assets/upload/contest_theme_photo/'.$contestDetails[$i]->themephoto;
	
	$contestDetails[$i]->sponsorphoto = url().'/public/assets/upload/sponsor_photo/'.$contestDetails[$i]->sponsorphoto;
	
	$contestDetails[$i]->conteststartdate = timezoneModel::convert($contestDetails[$i]->conteststartdate, 'UTC',$timezone, 'd-m-Y h:i a');

	$contestDetails[$i]->contestenddate = timezoneModel::convert($contestDetails[$i]->contestenddate, 'UTC',$timezone, 'd-m-Y h:i a');
	   
	$contestDetails[$i]->votingstartdate = timezoneModel::convert($contestDetails[$i]->votingstartdate, 'UTC',$timezone, 'd-m-Y h:i a');
	   
	$contestDetails[$i]->votingenddate = timezoneModel::convert($contestDetails[$i]->votingenddate, 'UTC',$timezone, 'd-m-Y h:i a');
	
	$contestDetails[$i]->createddate = timezoneModel::convert($contestDetails[$i]->createddate, 'UTC', $timezone,'d-m-Y h:i a');
	}
$contestparticipantcount = contestparticipantModel::where('contest_id',$contestid)->get();	
	$contestparticipantcount = count($contestparticipantcount);
	} 
	  
      $Response = array(
                'success' => '1',
                'message' => 'Data Get Successfully',
				'msgcode' =>"c116",
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
		////////New requirements/////////////////				
			if(Input::file('topicphoto')!=''){
				$destinationtopicphoto='public/assets/upload/topicphotovideo';
				$topicphoto = Input::file('topicphoto')->getClientOriginalName();
				$topicImage = str_random(8).'_'.$topicphoto;
				$inputdetails['topicphoto']= $topicImage;			
			}
			if(Input::file('topicvideo')!=''){
				$destinationtopicvideo='public/assets/upload/topicphotovideo';
				$topicvideo = Input::file('topicvideo')->getClientOriginalName();
				$topicvideoname = str_random(8).'_'.$topicvideo;
				$inputdetails['topicvideo']= $topicvideoname;		
			
			}		
			
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
			if(Input::get('uploadtopic')!='')
		    {				
				if(Input::file('topicphoto')!=''){ 
					$topicphotofile = Input::file('topicphoto');
					$uploadSuccess = $topicphotofile->move($destinationtopicphoto,$topicImage);				
				}
				if(Input::file('topicvideo')!=''){ 
					$topicphotofile = Input::file('topicvideo');
					$uploadSuccess = $topicphotofile->move($destinationtopicvideo,$topicvideoname);				
				}
			}
			
			$participant = contestparticipantModel::where('contest_id',$contest_id)
				->where('user_id',$user_id)
				->update($inputdetails);
			
			$Response = array(
                'success' => '1',
                'message' => 'Record updated Successfully',
				'msgcode' =>"c109",
            );
           $final=array("response"=>$Response);
           return json_encode($final);	
			}
			else
			{
			$Response = array(
                'success' => '0',
                'message' => 'Some Parameter missing',
				'msgcode' =>"c101",
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
					'msgcode' =>"c121",
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
					
					if(Input::get('uploadtopic')!='')
					{				
						if(Input::file('topicphoto')!=''){ 
							$topicphotofile = Input::file('topicphoto');
							$uploadSuccess = $topicphotofile->move($destinationtopicphoto,$topicImage);				
						}
						if(Input::file('topicvideo')!=''){ 
							$topicphotofile = Input::file('topicvideo');
							$uploadSuccess = $topicphotofile->move($destinationtopicvideo,$topicvideoname);				
						}
					}
							$participant = contestparticipantModel::create($inputdetails);
					$Response = array(
							'success' => '1',
							'message' => 'Record Added Successfully',
							'msgcode' =>"c107",
						);
					   $final=array("response"=>$Response);
					   return json_encode($final);		
				}
				else
				{	
				$Response = array(
						'success' => '0',
						'message' => 'Some Parameter missing',
						'msgcode' =>"c120",
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
	$votinguser = Input::get('user_id');
	
	$participantlist = contestparticipantModel::select('user.username','user.firstname','user.lastname','user.ID as user_id','user.profilepicture','contestparticipant.ID as contestparticipantid','contestparticipant.contest_id','contestparticipant.uploadfile','contestparticipant.uploaddate','contestparticipant.uploadtopic','contestparticipant.dropbox_path','contestparticipant.topicphoto','contestparticipant.topicvideo')->where('contest_id',$contest_id)->LeftJoin('user','user.ID','=','contestparticipant.user_id')->get();
	
		
	for($i=0;$i<count($participantlist);$i++)
	{
	$voted =votingModel::where('contest_participant_id',$participantlist[$i]->contestparticipantid)->where('user_id',$votinguser)->get()->count();										
		
    $followers = followModel::where('userid',$participantlist[$i]->user_id)->where('followerid',$votinguser)->get()->count();		
										
										
	
	$participantlist[$i]->uploaddate = timezoneModel::convert($participantlist[$i]->uploaddate, 'UTC',$timezone, 'd-m-Y h:i a');
	$participantlist[$i]->uploadfile  = url().'/public/assets/upload/contest_participant_photo/'.$participantlist[$i]->uploadfile;

  //$participantlist[$i]->uploadfile  = $participantlist[$i]->dropbox_path;
	
	if($participantlist[$i]->profilepicture!=''){ $participantlist[$i]->profilepicture  =  url().'/public/assets/upload/profile/'.$participantlist[$i]->profilepicture; }
	
	if($participantlist[$i]->firstname!=''){ $participantlist[$i]->name = $participantlist[$i]->firstname.' '.$participantlist[$i]->lastname; }else{
	$participantlist[$i]->name = $participantlist[$i]->username; }
	
	$participantlist[$i]->vote = $voted;
	$participantlist[$i]->following =$followers;
	
	if($participantlist[$i]->topicvideo!='') $participantlist[$i]->topicvideo = url().'/public/assets/upload/topicphotovideo/'.$participantlist[$i]->topicvideo;
	
	if($participantlist[$i]->topicphoto!='') $participantlist[$i]->topicphoto = url().'/public/assets/upload/topicphotovideo/'.$participantlist[$i]->topicphoto; 
	
	}

	
	$Response = array(
		'success' => '1',
		'message' => 'Record Fetched Successfully',
		'msgcode' =>"c124",
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
	$contestdetails[$i]->uploaddate = timezoneModel::convert($contestdetails[$i]->uploaddate, 'UTC',$timezone, 'd-m-Y h:i a');
	}
	
	// User follow the contest created user means it returns 1;
		
	if($contestdetailscount){
		$Response = array(
                'success' => '1',
                'message' => 'Contest details for voting get Successfully',
				'msgcode' =>"c125",
            );
           $final=array("response"=>$Response, "contestdetails" => $contestdetails);
           return json_encode($final);
	
	}else{
		$Response = array(
                'success' => '0',
                'message' => 'Some details missing',
				'msgcode' =>"c101",
            );
           $final=array("response"=>$Response);
           return json_encode($final);
	}
}
public function voting()
{
	
	$curdate = Carbon::now();
	$user_id = Input::get('user_id');
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
				'msgcode' =>"c127",
				'user_id' =>$user_id
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
	
	 $leaderboarddata = leaderboardModel::select('user.ID as userid', 'user.username', 'user.firstname', 'user.lastname', 'user.profilepicture', 'leaderboard.votes', 'leaderboard.user_id as leaderusrid', 'leaderboard.position')->LeftJoin('user', 'user.ID', '=', 'leaderboard.user_id')->where('contest_id', $contest_id)->orderby('position')->get();
	
	for($i=0; $i<count($leaderboarddata);$i++)
  {
  
   $participantcnt = contestparticipantModel::where('user_id', $leaderboarddata[$i]['leaderusrid'])->get()->count();
   if ($participantcnt != 0) {
   
	            $contestdetails = contestparticipantModel::select('contestparticipant.uploadfile','contestparticipant.uploadtopic','user.firstname','user.lastname','user.username','contestparticipant.dropbox_path','contestparticipant.topicvideo','contestparticipant.topicphoto')->where('contest_id', $contest_id)->where('user_id', $leaderboarddata[$i]['userid'])->LeftJoin('user','user.ID','=','contestparticipant.user_id')->first();
   
  //$contestdetails = contestparticipantModel::select('contestparticipant.uploadfile','contestparticipant.uploadtopic','user.firstname','user.lastname','user.username','contestparticipant.dropbox_path','contestparticipant.topicvideo','contestparticipant.topicphoto')->LeftJoin('user','user.ID','=','contestparticipant.user_id')->where('contest_id', $leaderboardresult[$i]['contest_id'])->where('user_id',$leaderboardresult[$i]['user_id'])->get()->first();
  
  
  
  if($contestdetails->firstname!=''){ $leaderboardresult[$i]['name'] = $contestdetails->firstname.' '.$contestdetails->lastname; }else{ $leaderboardresult[$i]['name'] = $contestdetails->username; }
  
 $leaderboardresult[$i]['uploadfile'] = url().'/public/assets/upload/contest_participant_photo/'.$contestdetails->uploadfile;
  
 // $leaderboardresult[$i]->uploadfile  = $leaderboardresult[$i]->dropbox_path;
  
  $leaderboardresult[$i]['uploadtopic'] = $contestdetails->uploadtopic;
  
 // if($getcomments[$i]['firstname']!=''){  $getcomments[$i]['name']=$getcomments[$i]['firstname'].' '.$getcomments[$i]['lastname']; } else { $getcomments[$i]['name']=$getcomments[$i]['username'];} 
 
   	if($leaderboardresult[$i]->topicvideo!='') $leaderboardresult[$i]->topicvideo = url().'/public/assets/upload/topicphotovideo/'.$leaderboardresult[$i]->topicvideo;
	
	if($leaderboardresult[$i]->topicphoto!='') $leaderboardresult[$i]->topicphoto = url().'/public/assets/upload/topicphotovideo/'.$leaderboardresult[$i]->topicphoto;
  }
  else{
  $leaderboardresult[$i]->uploadfile='';
  $leaderboardresult[$i]->uploadtopic='';
  
  $name1 = User::select('firstname','lastname','username')->where("ID",$leaderboarddata[$i]['leaderusrid'])->first();
  if(count($name1)>0)
    if($name1->firstname!=''){ $leaderboardresult[$i]['name'] = $name1->firstname.' '.$name1->lastname; }else{ $leaderboardresult[$i]['name'] = $name1->username; }
	else
	$leaderboardresult[$i]['name'] ='';

  }
  
   }
     
	$Response = array(
                'success' => '1',
                'message' => 'Getting Result details',
				'msgcode' =>"c128",
            );
     $final=array("response"=>$Response,"Voting Result"=>$leaderboardresult);
     return json_encode($final); 
		
}
public function follower()
{
	$curdate = date('Y-m-d h:i:s');
	$inputdetails['followerid'] = Input::get('userid');
	$inputdetails['userid'] = Input::get('followerid');
	$inputdetails['createddate']=$curdate;
	$validation  = Validator::make($inputdetails, followModel::$rules);
		if ($validation->passes()){
			$followers = followModel::create($inputdetails);
					
		$followedetails = User::find(Input::get('userid'));		
		$email = $followedetails['email'];
		$userdetails = User::find(Input::get('followerid'));	
		
		if($userdetails['firstname']!='') $username = $userdetails['firstname'].' '.$userdetails['lastname']; else $username = $userdetails['username'];
		
		
		if($followedetails['firstname']!='') $followingusername = $followedetails['firstname'].' '.$followedetails['lastname']; else $followingusername = $followedetails['username'];
		
		Mail::send([],
		array('followingusername' => $followingusername,'email' => $email,'username' => $username), function($message) use ($followingusername,$email,$username)
		{
       			
		/* $mail_body = '<style>.thank{text-align:center; width:100%;}
					.but_color{color:#ffffff;}
					.cont_name{width:100px;}
					.cont_value{width:500px;}					
					</style>
			 <body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

				<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
					&nbsp;&nbsp;<a href="'.URL().'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px; line-height:20px;" /></a>&nbsp;&nbsp;
				</div>
				<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
					<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear '.$followingusername.'</div>
					
					<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">The Member'.$username.' is following you.
			 </div>					
				<div style="margin:10px;"><a href="'.URL().'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
					</div>
				</div>											
				<div style="font-size:12px; margin-top:10px;color: #5b5b5b; width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">				
				</body>'; */ 
				
				$mail_body = '<style>.thank{text-align:center; width:100%;}
					.but_color{color:#ffffff;}
					.cont_name{width:100px;}
					.cont_value{width:500px;}
					
					</style>
				<body style="font-family:Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%; width:100%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear '.$followingusername.'</div>
								
								<table width="100%"><tr style="height:10px;"><td></td></tr><tr><td style="height:30px;">
								The Member '.$username.' is following you.
								</td></tr>
								<tr><td style="height:45px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a></td></tr>
								<tr><td style="border-top:#005377 1px solid; height:30px;"><span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span></td></tr>
								</table>
							</div>													
							
							</body>';

        $message->setBody($mail_body, 'text/html');
        $message->to($email);
        $message->subject('Follower details - DingDatt');
        }); 			
			
			$Response = array(
                'success' => '1',
                'message' => 'Followers  Added Successfully',
				'msgcode' =>"c129",
            );
           $final=array("response"=>$Response,"follower"=>$followers);
           return json_encode($final);	
		}
		else{
			$Response = array(
                'success' => '0',
                'message' => 'Some details missing',
				'msgcode' =>"c101",
            );
           $final=array("response"=>$Response,"follower"=>$followers);
           return json_encode($final);
		}
}

public function getfollowers()
{
$userid = Input::get('userid');
$timezone = Input::get('timezone');
 $followers = followModel::where('followerid',$userid)
->select('user.profilepicture','user.firstname','user.lastname','user.username','user.ID as followerid')
->leftJoin('user','user.ID','=','followers.userid')
->get();

for($i=0; $i<count($followers);$i++){ 
if($followers[$i]['firstname']!=''){ $followers[$i]['name']=$followers[$i]['firstname'].' '.$followers[$i]['lastname'];}else{ $followers[$i]['name']=$followers[$i]['username']; }
 if($followers[$i]['profilepicture']!='') { $followers[$i]['profilepicture']=url().'/public/assets/upload/profile/'.$followers[$i]['profilepicture']; }
  }

			$Response = array(
                'success' => '1',
                'message' => 'Followers  List',
				'msgcode' =>"c131",
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
		
	$getcomments = commentModel::select('comments.id as comment_id','comments.contest_participant_id','comments.comment','user.ID as userid','user.firstname','user.lastname','user.username','user.profilepicture')
	->where('comments.contest_participant_id',$contest_participant_id)
	->LeftJoin('user','user.ID','=','comments.userid')->get();
	
	
	for($i=0; $i<count($getcomments);$i++)
  {
  if($getcomments[$i]['firstname']!=''){  $getcomments[$i]['name']=$getcomments[$i]['firstname'].' '.$getcomments[$i]['lastname']; } else { $getcomments[$i]['name']=$getcomments[$i]['username'];} 
  
  	if($getcomments[$i]['profilepicture']!=''){ $getcomments[$i]['profilepicture']  =  url().'/public/assets/upload/profile/'.$getcomments[$i]['profilepicture']; }
  }
  
	/*DB::select("SELECT user.*,DATE_FORMAT(CONVERT_TZ(`dateofbirth`,'+00:00','$timezone'),'%Y-%m-%d') as converteddateofbirth  FROM user WHERE ID =$userid");
	
	 $getcomments = commentModel::select('comments.id as comment_id','comments.contest_participant_id','comments.comment','user.ID as userid','user.firstname','user.profilepicture' )
	->where('comments.contest_participant_id',$contest_participant_id)
	->LeftJoin('user','user.ID','=','comments.userid')->get();
	*/
	
	$Response = array(
                'success' => '1',
                'message' => 'Getting Comments details',
				'msgcode' =>"c132",
            );
     $final=array("response"=>$Response,"Comments"=>$getcomments, "followers"=>$followers);
     return json_encode($final);
	
	}
	else{
	$Response = array(
                'success' => '0',
                'message' => 'No Comments Available in this Contest participant',
				'msgcode' =>"c133",
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
				'msgcode' =>"c134",
            );
     $final=array("response"=>$Response);
     return json_encode($final);
		}
		}
		else{ 
		$Response = array(
                'success' => '0',
                'message' => 'Some details are Missing',
				'msgcode' =>"c101",
            );
		 $final=array("response"=>$Response);
		 return json_encode($final);
		}
}
public function myhistory()
{
		
  $userid = Input::get('userid');
  $timezone = Input::get('timezone');
 // $myhistory = contestparticipantModel::where('user_id',$userid)->get();
 
 	$curdate=date('Y-m-d H:i:s');
		$myhistory = contestparticipantModel::select('contest.contest_name','contestparticipant.contest_id','contestparticipant.uploadfile','contestparticipant.uploadtopic','contest.contesttype','contestparticipant.dropbox_path','contestparticipant.topicvideo','contestparticipant.topicphoto')->where('contestparticipant.user_id',$userid)->LeftJoin('contest','contest.ID','=','contestparticipant.contest_id')->where('contest.votingenddate','<',$curdate)->LeftJoin('user','user.ID','=','contest.createdby')->get();


 for($i=0; $i<count($myhistory);$i++)
  {
  $myhistory[$i]->uploaddate = timezoneModel::convert($myhistory[$i]->uploaddate, 'UTC',$timezone, 'd-m-Y h:i a');
  $myhistory[$i]->uploadfile  = url().'/public/assets/upload/contest_participant_photo/'.$myhistory[$i]->uploadfile;
 
 //$myhistory[$i]->uploadfile  = $myhistory[$i]->dropbox_path;
  
 $contestdetails = contestModel::select('contesttype','contest_name')->where('ID',$myhistory[$i]->contest_id)->get()->first();
 $myhistory[$i]->contesttype = $contestdetails->contesttype;
 $myhistory[$i]->contest_name = $contestdetails->contest_name;
 
 
	if($myhistory[$i]->topicvideo!='') $myhistory[$i]->topicvideo = url().'/public/assets/upload/topicphotovideo/'.$myhistory[$i]->topicvideo;
	
	if($myhistory[$i]->topicphoto!='') $myhistory[$i]->topicphoto = url().'/public/assets/upload/topicphotovideo/'.$myhistory[$i]->topicphoto;
	
  }
  $Response = array(
                'success' => '1',
                'message' => 'My Participated history',
				'msgcode' =>"c136",
            );
     $final=array("response"=>$Response,'myhistory'=>$myhistory);
     return json_encode($final);

}
public function getfollowinglist()
{
$userid = Input::get('userid');
 $followers = followModel::where('userid',$userid)
->select('user.profilepicture','user.firstname','user.lastname','user.username','user.ID as followinguserid')
->leftJoin('user','user.ID','=','followers.followerid')
->get();

for($i=0; $i<count($followers);$i++){ 
if($followers[$i]['firstname']!=''){ $followers[$i]['name']=$followers[$i]['firstname'].' '.$followers[$i]['lastname'];}else{ $followers[$i]['name']=$followers[$i]['username']; }
 if($followers[$i]['profilepicture']!='') { $followers[$i]['profilepicture']=url().'/public/assets/upload/profile/'.$followers[$i]['profilepicture']; }
  }

			
			$Response = array(
                'success' => '1',
                'message' => 'Following member  List',
				'msgcode' =>"c137",
            );
           $final=array("response"=>$Response,"followerlist"=>$followers);
           return json_encode($final);

}
public function viewprofile()
{
    $userid = Input::get('userid');
	$myuserid = Input::get('myuserid');
    $followingcount = followModel::where('userid',$userid)->get()->count();
    $followerscount = followModel::where('followerid',$userid)->get()->count();	
	$participatedcount = contestparticipantModel::where('user_id',$userid)->get()->count();
	$woncount = leaderboardModel::where('user_id',$userid)->get()->count();
	$userdetails = User::select('firstname','lastname','username','profilepicture')->where('ID',$userid)->first();

	$return['following'] = $followingcount;
	$return['followers'] = $followerscount;
	$return['participated'] = $participatedcount;
	$return['won'] = $woncount;
	$return['userid'] = $userid;
	
	if($userdetails->firstname!=''){ $return['name']=$userdetails->firstname.' '.$userdetails->lastname; }else{ $return['name']=$userdetails->username; }
	$return['profilepicture'] =   url().'/public/assets/upload/profile/'.$userdetails->profilepicture;
	
  
   $Response = array(
                'success' => '1',
                'message' => 'My Profile Details',
				'msgcode' =>"c138",
				
            );
	if($myuserid!='')
	{
	$follow = followModel::where('userid',$myuserid)->where('followerid',$userid)->get()->count();
	if($follow) { $follow1 = 1; }else{ $follow1 = 0;}
	//return $follow1;
	$final=array("response"=>$Response,'viewmyprofile'=>$return,'follow'=>$follow1);
	}
	else{ 
	$final=array("response"=>$Response,'viewmyprofile'=>$return);
	
	}	
     
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
					'msgcode' =>"c139",
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
					'msgcode' =>"c140",
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
	//$inputdetails['groupimage'] = Input::file('uploadimage');
	$inputdetails['status'] =1;
	$userid = Input::get('userid');
	
	$validation  = Validator::make($inputdetails, groupModel::$rules);
	if ($validation->passes()){  
	
	
	if(Input::file('uploadimage')!='')
		{
			$destinationPath = 'public/assets/upload/group';
			$filename = Input::file('uploadimage')->getClientOriginalName();
			$Image = str_random(8).'_'.$filename;
			$inputdetails['groupimage']= $Image;
			$file = Input::file('uploadimage');
			$uploadSuccess = $file->move($destinationPath,$Image);
			
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
				$id = array_values(array_unique(array_merge($id, $id1))); 
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
				'msgcode' =>"c141",
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
				'msgcode' =>"c142",
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
	$groubdetails[0]->createddate = timezoneModel::convert($groubdetails[0]->createddate, 'UTC',$timezone, 'd-m-Y h:i a');
	if($groubdetails[0]->groupimage!=''){ 
	$groubdetails[0]->groupimagename =$groubdetails[0]->groupimage; 
	$groubdetails[0]->groupimage =url().'/public/assets/upload/group/'.$groubdetails[0]->groupimage; 
	}
	$Response = array(
                'success' => '1',
                'message' => 'Group Details Fetched successfully',
				'msgcode' =>"c143",
            );
        $final=array("response"=>$Response,"Groupdetails"=>$groubdetails);
        return json_encode($final);
	
	}
	else
	{
	$Response = array(
                'success' => '0',
                'message' => 'No Details Available',
				'msgcode' =>"c117",
            );
        $final=array("response"=>$Response);
        return json_encode($final);

	}
}

public function updategroupdetails()
{	
	$inputdetails['groupname'] = Input::get('groupname');
	//if(Input::get('grouptype')!='')  $inputdetails['grouptype'] = Input::get('grouptype');
	
	$group_id = Input::get('group_id');
	
	if(Input::file('uploadimage')!='')
		{
			$destinationPath = 'public/assets/upload/group';
			$filename = Input::file('uploadimage')->getClientOriginalName();
			$Image = str_random(8).'_'.$filename;
			$inputdetails['groupimage']= $Image;
			$file = Input::file('uploadimage');
			$uploadSuccess = $file->move($destinationPath,$Image);
			
		}
	
	$updaterules = array(                    
					'groupname'  => 'required|unique:group,groupname,'.$group_id,
					
                	) ;
					
	$validation  = Validator::make($inputdetails, $updaterules);
	if ($validation->passes()){	 
	 $participant = groupModel::where('ID',$group_id)->update($inputdetails);
	 $Response = array(
                'success' => '1',
                'message' => 'Group Details Updated successfully',
				'msgcode' =>"c145",
            );
        $final=array("response"=>$Response);
        return json_encode($final);
		
	}
	else
	{ 
	$Response = array(
                'success' => '0',
                'message' => $validation->messages()->first(),
				'msgcode' =>"c146",
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
		
		$grouplist = groupmemberModel::select('group_members.group_id','group.groupname','group.grouptype','group.createdby','user.firstname','user.lastname','user.username','group.groupimage')->leftJoin('group','group.ID','=','group_members.group_id')->where('group.status',1)->LeftJoin('user','user.ID','=','group.createdby')->where('group_members.user_id',$user_id)->where('user.status',1)->get();
		
		 for($i=0; $i<count($grouplist);$i++){
		 $grouplist[$i]['groupimage']=url().'/public/assets/upload/group/'.$grouplist[$i]['groupimage'];
		 if($grouplist[$i]['firstname']!=''){ $grouplist[$i]['name']=$grouplist[$i]['firstname'].''.$grouplist[$i]['lastname']; }else{ $grouplist[$i]['name']=$grouplist[$i]['username']; }
		 
		 }
		 
		 $Response = array(
                'success' => '1',
                'message' => 'Group List fetched successfully',
				'msgcode' =>"c147",
            );
        $final=array("response"=>$Response,"grouplist"=>$grouplist);
        return json_encode($final);
		
}
public function getgrouplistsearch(){
$user_id = Input::get('user_id');
$searchkey = Input::get('searchkey');

$grouplist = groupModel::select('group.ID','group.groupname','group.grouptype','group.createdby','user.firstname','user.lastname','user.username','group.groupimage')->LeftJoin('user','user.ID','=','group.createdby')->where('user.status',1)->where('group.groupname','like','%'.$searchkey.'%')->where('group.status',1)->get();

for($i=0; $i<count($grouplist);$i++){

	if($grouplist[$i]['groupimage']!='') { $grouplist[$i]['groupimage']=url().'/public/assets/upload/group/'.$grouplist[$i]['groupimage']; }
	if($grouplist[$i]['firstname']!=''){ $grouplist[$i]['name']=$grouplist[$i]['firstname'].' '.$grouplist[$i]['lastname']; }else{ $grouplist[$i]['name']=$grouplist[$i]['username']; }
	
	$member = groupmemberModel::where('group_id',$grouplist[$i]['ID'])->where('user_id',$user_id)->count();
	if($member!=0) $grouplist[$i]['member']=1;else $grouplist[$i]['member']=0;
}

$Response = array(
                'success' => '1',
                'message' => 'Group List fetched successfully',
				'msgcode' =>"c147",
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

	
	$grouplist = groupmemberModel::select('group_members.group_id','group.groupname','group.grouptype','group.createdby','user.firstname','user.lastname','user.username','group.groupimage','group.createdby as groupcreateuserid','group.ID as groupid')->leftJoin('group','group.ID','=','group_members.group_id')->where('group.status',1)->LeftJoin('user','user.ID','=','group.createdby')->where('user.status',1)->where('group_members.user_id',$user_id)->get();

	
	//return count($grouplist);
	
		for($k=0;$k<count($grouplist);$k++)
		{
		
		$groupmemberlistnew = groupmemberModel::where('group_id', $grouplist[$k]['groupid'])->lists('user_id');
		$invitednew = invitegroupforcontestModel::whereIn('user_id', $groupmemberlistnew)->where('contest_id', $contest_id)->count();
						
		$groupmemberlist_admin = groupmemberModel::where('group_id',$grouplist[$k]['groupid'])->where('user_id',1)->get()->count();
		
		if($groupmemberlist_admin>0)
		{
		if($invitednew+2==count($groupmemberlistnew) && count($groupmemberlistnew)!=2 ){ $invitecnt = 1; }else if($invitednew!=0){ $invitecnt = -1;  }else{ $invitecnt = 0; }
		}else{
		if($invitednew+1==count($groupmemberlistnew) && count($groupmemberlistnew)!=1 ){ $invitecnt = 1;  }else if($invitednew!=0){ $invitecnt = -1;  }else{ $invitecnt = 0; }
		
		}
										
		
		
		$grouplist[$k]['invite']= $invitecnt;

		$grouplist[$k]['groupimage'] = url().'/public/assets/upload/group/'.$grouplist[$k]['groupimage'];
		
		}	
		 $Response = array(
                'success' => '1',
                'message' => 'Group List fetched successfully',
				'msgcode' =>"c147",
            );
        $final=array("response"=>$Response,"grouplist"=>$grouplist);
        return json_encode($final);
		
}
public function getgroupmemberlist()
{
	$group_id = Input::get('group_id');
	$contest_id = Input::get('contest_id');
	
	$membercount = groupmemberModel::where('group_id',$group_id)->get()->count();
	if($membercount){
	
	$savegroupmembers = groupmemberModel::select('group_members.id as groupmemberid','group_members.user_id','user.firstname','user.lastname','user.profilepicture','group.createdby as groupadmin_userid','user.username' )
	->LeftJoin('user','user.ID','=','group_members.user_id')
	->where('group_id',$group_id)
	->LeftJoin('group','group.ID','=','group_members.group_id')
	->get();
	
	for($i=0; $i<count($savegroupmembers);$i++){ 
		
		if($savegroupmembers[$i]['firstname']!=''){ $savegroupmembers[$i]['name']=$savegroupmembers[$i]['firstname'].' '.$savegroupmembers[$i]['lastname'];  }else{  $savegroupmembers[$i]['name']=$savegroupmembers[$i]['username']; }
		
		$savegroupmembers[$i]['profilepicture'] = url().'/public/assets/upload/profile/'.$savegroupmembers[$i]['profilepicture'];
	
	if($contest_id!=''){
	$invited=invitegroupforcontestModel::where('contest_id',$contest_id)->where('user_id',$savegroupmembers[$i]['user_id'])->count();
	
	if($invited) $invited=1; else $invited=0;
	
	$savegroupmembers[$i]['invited']=$invited;	
	}	
	}	
	}	
		$Response = array(
                'success' => '1',
                'message' => 'Success',
				'msgcode' =>"c150",
            );
        $final=array("response"=>$Response,'groupmemberlist'=>$savegroupmembers);
        return json_encode($final);
	
	

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
				'msgcode' =>"c151",
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	}
	else
	{
	$Response = array(
                'success' => '0',
                'message' => 'Such User is not available in this group',
				'msgcode' =>"c152",
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	}
}
public function unfollow()
{
$user_id=Input::get('user_id');
$following_id = Input::get('following_id');
//$count = followModel::where('followerid',$user_id)->where('userid',$following_id)->get()->count();
$count = followModel::where('followerid',$following_id)->where('userid',$user_id)->get()->count();
if($count){
followModel::where('followerid',$following_id)->where('userid',$user_id)->delete();
$Response = array(
                'success' => '1',
                'message' => 'You are removed the following person',
				'msgcode' =>"c153",
            );
        $final=array("response"=>$Response);
        return json_encode($final);
}else{
$Response = array(
                'success' => '0',
                'message' => 'Such following person is not available',
				'msgcode' =>"c154",
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

//$inpudetails['status']= 1;

$validator = Validator::make($inpudetails,ProfileModel::$socialrules);
		if ($validator->passes()) 
        {
		
		$verifyuser = ProfileModel::where('email',$emailid)->get()->count();
		$userid = ProfileModel::select('ID','status','firstname','lastname','username','dateformat')->where('email',$emailid)->get();
		if($verifyuser)
		{
			if($userid[0]['status']==1)
			{
				$dateformat =  $userid[0]['dateformat'];
				$userid1 = $userid[0]['ID'];
				$updatedata['facebook_id'] = Input::get('id');
				$updatedata['facebookpage']=Input::get('link');	 //return $userid1;
				 
				$updatedata['gcm_id'] = Input::get('gcm_id');
				$updatedata['device_id'] = Input::get('device_id');
				$updatedata['device_type'] = Input::get('device_type');
				
				$userregister = ProfileModel::where('ID', $userid1)->update($updatedata);
				
				if($userid[0]['firstname']!=''){ $name = $userid[0]['firstname'].' '.$userid[0]['lastname']; }else{ $name = $userid[0]['username']; }
				if($dateformat=='mm/dd/yy') $dateformat="df1"; else $dateformat="df2";	
				$Response = array(
						'success' => '1',
						'message' => 'successfully Login',
						'userid' =>$userid1,
						'name'  =>$name,
						'dateformat' =>$dateformat,
						'msgcode' =>"c102",
					);
					$final=array("response"=>$Response);
					return json_encode($final);		
			}else{
			
				$admindetails=User::select('email')->where('ID',1)->first();
				
				$Response = array(
					'success' => '0',
					'message' => "Your account is inactive please contact admin (".$admindetails->email.")",
					'mailid' =>  $admindetails->email,
					'msgcode' =>"c196",
				);
				$final=array("response"=>$Response);
				return json_encode($final);			
			
			}
		}
		else{
		$inpudetails['facebook_id'] = Input::get('id');
		$inpudetails['facebookpage']=Input::get('link');	//return $inpudetails;
		$inpudetails['status'] = '1';		
		
		$inpudetails['gcm_id'] = Input::get('gcm_id');
		$inpudetails['device_id'] = Input::get('device_id');
		$inpudetails['device_type'] = Input::get('device_type'); 
		
		$inpudetails['timezone']=Input::get('timezone');
		$inpudetails['dateformat'] = "mm/dd/yy";
		
		$saved = ProfileModel::create($inpudetails);
		if($saved)
		{
		$userid = ProfileModel::max('ID');
		if($dateformat=='mm/dd/yy') $dateformat="df1"; else $dateformat="df2";
		$Response = array(
                'success' => '1',
                'message' => 'successfully created Login',
				'userid' =>$userid,
				'dateformat' =>$dateformat,
				'msgcode' =>"c156",
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
                'message' => 'Some Fields are Missing',
				'msgcode' =>"c101",
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
//$inpudetails['status']= 1;

$validator = Validator::make($inpudetails,ProfileModel::$socialrules);
		if ($validator->passes()) 
        {
		
		$verifyuser = ProfileModel::where('email',$emailid)->get()->count();
		$userid = ProfileModel::select('ID','status','firstname','lastname','username','dateformat')->where('email',$emailid)->get();
		if($verifyuser)
		{
			if($userid[0]['status']==1)
			{
				$dateformat =  $userid[0]['dateformat'];
				$userid1 = $userid[0]['ID'];
				$updatedata['google_id'] = Input::get('id');
				
				$updatedata['gcm_id'] = Input::get('gcm_id');
				$updatedata['device_id'] = Input::get('device_id');
				$updatedata['device_type'] = Input::get('device_type'); 
				
				$userregister = ProfileModel::where('ID', $userid1)->update($updatedata);
				if($userid[0]['firstname']!=''){ $name = $userid[0]['firstname'].' '.$userid[0]['lastname']; }else{ $name = $userid[0]['username']; }	
				if($dateformat=='mm/dd/yy') $dateformat="df1"; else $dateformat="df2";
				$Response = array(
						'success' => '1',
						'message' => 'successfully Login',
						'userid' =>$userid1,
						'name'  => $name,
						'dateformat' =>$dateformat,
						'msgcode' =>"c102",
					);
					$final=array("response"=>$Response);
					return json_encode($final);	
			}else{
			
				$admindetails=User::select('email')->where('ID',1)->first();
				
				$Response = array(
					'success' => '0',
					'message' => "Your account is inactive please contact admin (".$admindetails->email.")",
					'mailid' =>  $admindetails->email,
					'msgcode' =>"c196",
				);
				$final=array("response"=>$Response);
				return json_encode($final);				
			
			}
		}
		else{
		$inpudetails['google_id'] = Input::get('id');
		
		$inpudetails['status'] = '1';
		$inpudetails['gcm_id'] = Input::get('gcm_id');
		$inpudetails['device_id'] = Input::get('device_id');
		$inpudetails['device_type'] = Input::get('device_type'); 
		
		$inpudetails['timezone']=Input::get('timezone');
		$inpudetails['dateformat'] = "mm/dd/yy";
		
		$saved = ProfileModel::create($inpudetails);
		if($saved)
		{
		if($dateformat=='mm/dd/yy') $dateformat="df1"; else $dateformat="df2";
		$userid = ProfileModel::max('ID'); 
		$Response = array(
                'success' => '1',
                'message' => 'successfully created Login',
				'userid' =>$userid,
				'dateformat' =>$dateformat,
				'msgcode' =>"c156",
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
                'message' => 'Some Fields are Missing',
				'msgcode' =>"c101",
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
				'msgcode' =>"c161",
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
				'msgcode' =>"c101",
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


$getreply = replycommentModel::select('user.username','user.firstname','user.lastname','user.profilepicture','replycomment.comment_id','replycomment.replycomment','replycomment.user_id')->where('comment_id',$comment_id)->LeftJoin('user','user.ID','=','replycomment.user_id')->get();

for($i=0; $i<count($getreply); $i++){

if($getreply[$i]['firstname']!=''){ $getreply[$i]['name']=$getreply[$i]['firstname'].' '.$getreply[$i]['lastname'];  }else{ $getreply[$i]['name']=$getreply[$i]['username'];  }

if($getreply[$i]['profilepicture']!=''){ $getreply[$i]['profilepicture']  =  url().'/public/assets/upload/profile/'.$getreply[$i]['profilepicture']; }	

} 

$Response = array(
                'success' => '1',
                'message' => 'Reply Fetched Successfully',
				'msgcode' =>"c163",
            );
        $final=array("response"=>$Response,"replydata"=>$getreply);
        return json_encode($final);

}
else{
$Response = array(
                'success' => '0',
                'message' => 'No reply Comments Available in this record',
				'msgcode' =>"c164",
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
	$participantlist = contestparticipantModel::select('user.username','user.firstname','user.lastname','user.ID as user_id','user.profilepicture','contestparticipant.ID as contestparticipantid','contestparticipant.contest_id','contestparticipant.uploadfile','contestparticipant.uploaddate','contestparticipant.uploadtopic','contestparticipant.dropbox_path','contestparticipant.topicphoto','contestparticipant.topicvideo')->where('contest_id',$contest_id)->where('user_id',$particiapnt_user_id)->LeftJoin('user','user.ID','=','contestparticipant.user_id')->get();
	
	for($i=0;$i<count($participantlist);$i++)
	{
	$participantlist[$i]->uploaddate = timezoneModel::convert($participantlist[$i]->uploaddate, 'UTC',$timezone, 'd-m-Y h:i a');
	if($participantlist[$i]->firstname!=''){ $participantlist[$i]->name = $participantlist[$i]->firstname.' '.$participantlist[$i]->lastname; }else{
	$participantlist[$i]->name = $participantlist[$i]->username;
	}

if($participantlist[$i]->profilepicture!=''){ $participantlist[$i]->profilepicture  =  url().'/public/assets/upload/profile/'.$participantlist[$i]->profilepicture; }

	//$participantlist[$i]->uploadfile = $participantlist[$i]->dropbox_path;
	$participantlist[$i]->uploadfile = url().'/public/assets/upload/contest_participant_photo/'.$participantlist[$i]->uploadfile;
	
	if($participantlist[$i]->topicphoto!='') $participantlist[$i]->topicphoto = url().'/public/assets/upload/topicphotovideo/'.$participantlist[$i]->topicphoto;
	if($participantlist[$i]->topicvideo!='') $participantlist[$i]->topicvideo = url().'/public/assets/upload/topicphotovideo/'.$participantlist[$i]->topicvideo;
	}
	
	$Response = array(
		'success' => '1',
		'message' => 'Record Fetched Successfully',
		'msgcode' =>"c165",
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
	
	if($invite_type=='single'){
		$group_id=Input::get('group_id');
		$contest_id=Input::get('contest_id');
		$curdate = date('Y-m-d H:i:s');
		$contest_det=contestModel::where("ID",$contest_id)->first();
		$group_members=groupmemberModel::where("group_id",$group_id)->get();
		//$inv_suc_message='No member to Invite';
		$inv_suc_message=0;
		for($i=0;$i<count($group_members);$i++)
		{
		$invited=invitegroupforcontestModel::where('contest_id',$contest_id)->where('user_id',$group_members[$i]['user_id'])->count();
				
		
		if($invited==0 && $group_members[$i]['user_id']!=1)
		{
			if($contest_det->firstname!='')
			$inviter=$contest_det->firstname ." ".$contest_det->lastname;
			else
			$inviter=$contest_det->username;
			if($contest_det->contesttype=="p")
			$contesttype="Photo";
			else if($contest_det->contesttype=="v")
			$contesttype="Video";
			else if($contest_det->contesttype=="t")
			$contesttype="Topic";
			$contestname=$contest_det->contest_name;
			
			$contestimage = $contest_det->themephoto;
			$conteststartdate = $contest_det->conteststartdate;
			$contestenddate = $contest_det->contestenddate;
			
			$contestcreatedby = User::find($contest_det->createdby);
	
			if($contestcreatedby->firstname!=''){ $contestcreatedby = $contestcreatedby->firstname.''.$contestcreatedby->lastname; }else{ $contestcreatedby = $contestcreatedby->username;  } 
			
		//	for($j=0;$j<count($group_members);$j++)
			//{
				
				if($contest_det->createdby!=$group_members[$i]['user_id'])
				{
				
				$invited_member=privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$group_members[$i]['user_id'])->count();
				if($contest_det->visibility=="p" && $invited_member==0)
				{
					$privat_user['user_id']=$group_members[$i]['user_id'];
					$privat_user['contest_id']=$contest_id;
					$privat_user['requesteddate']=date('Y-m-d H:i:s');
					$privat_user['status']=1;
					privateusercontestModel::create($privat_user);
					unset($privat_user);
				}
				
			$input_details['group_id']=$group_id;
			$input_details['contest_id']=$contest_id;
			$input_details['invitedetail']=1;
			$input_details['inviteddate']=$curdate;
			$input_details['user_id']=$group_members[$i]['user_id'];
			$invvitedsata = invitegroupforcontestModel::create($input_details);
			
				// Email Notification for invitation
				if($invited_member==0)
				{
					
						$user_id = User::find($group_members[$i]['user_id']);
						$gcmid = $user_id['gcm_id'];
						$email = $user_id['email'];
						$device_type = $user_id['device_type'];
						
						///////
							if($gcmid!='' && $device_type=='A'){
							$Message['user_id']=$group_members[$i]['user_id'];
							$Message['title']='Ding Datt';
							$Message['message']='You are invited for the Contest :'.$contestname;
							$Message['contest_id']=$inpudetails['contest_id'];
							$Message = array("notification"=>$Message); 
							$DeviceId = array($gcmid);
							$Message = array("notification"=>$Message);
							$this->PushNotification($DeviceId, $Message);
							
							}else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = $group_members[$i]['user_id'].'*You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					}else{
						
					
								$user=ProfileModel::where('ID',$group_members[$i]['user_id'])->first();
								if($user['firstname'] !='')
								$name=$user['firstname'].' '.$user['lastname'];
								else
								$name=$user['username'];
								$email=$user['email'];
								$groupname  = groupModel::where('ID',$group_id)->first();
								$groupname  = $groupname->groupname;
								
								$usertimezone = $user['timezone'];
						$userdateformat = $user['dateformat'];
					   
						if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
						$conteststartdate = timezoneModel::convert($contest_det->conteststartdate, 'UTC',$usertimezone, $userdateformat);
						$contestenddate = timezoneModel::convert($contest_det->contestenddate, 'UTC',$usertimezone, $userdateformat);
								
								$this->invitegroupmemberforcontestmail($email,$contestcreatedby,$contesttype,$contestname,$contest_id,$groupname,$contestimage,$conteststartdate,$contestenddate);
							}
					if($invvitedsata) $inv_suc_message=1;
					}
			}
		}else{  $inv_suc_message=2; }
		}
		if($inv_suc_message==1)
		{	
			$Response = array(
					'success' => '1',
					'message' => 'Invited Successfully',
					'msgcode' =>"c166",
				);
			$final=array("response"=>$Response);
			return json_encode($final);	
		}else if($inv_suc_message==2){
		
			$Response = array(
					'success' => '1',
					'message' => 'Invited Successfully',
					'msgcode' =>"c166",
				);
			$final=array("response"=>$Response);
			return json_encode($final);	
		
		}else{ 		
			$Response = array(
					'success' => '0',
					'message' => 'No members to invite',
					'msgcode' =>"c167",
				);
			$final=array("response"=>$Response);
			return json_encode($final);	
		}
		}
		///// End of this code ////////////
			
}
public function invitegroupmemberforcontest(){
	
		$groupmemberlist  = Input::get('groupmemberid');
		$groupid = Input::get('group_id');
		$contest_id = Input::get('contest_id');
		$groupmemberlistid = explode(',',$groupmemberlist);
		$curdate = date('Y-m-d H:i:s');
		/// Contest Details /////////////
		$contest_det=contestModel::where("ID",$contest_id)->first();
		if($contest_det->firstname!='')
			$inviter=$contest_det->firstname ." ".$contest_det->lastname;
			else
			$inviter=$contest_det->username;
			if($contest_det->contesttype=="p")
			$contesttype="Photo";
			else if($contest_det->contesttype=="v")
			$contesttype="Video";
			else if($contest_det->contesttype=="t")
			$contesttype="Topic";
			$contestname=$contest_det->contest_name;
			$contestimage = $contest_det->themephoto;
			
			$conteststartdate = $contest_det->conteststartdate;
			$contestenddate = $contest_det->contestenddate;
			
			$contestcreatedby = User::find($contest_det->createdby);
	
			if($contestcreatedby['firstname']!=''){ $contestcreatedby = $contestcreatedby['firstname'].''.$contestcreatedby['lastname']; }else{ $contestcreatedby = $contestcreatedby['username'];  } 
		
		$groupdetails  = groupModel::where('ID',$groupid)->first();
		$groupname = $groupdetails['groupname'];

		$inv_suc_message =0; 
		
		for($i=0;$i<count($groupmemberlistid);$i++){
		$groupmemberid = groupmemberModel::where('id',$groupmemberlistid[$i])->first();	
		$invited=invitegroupforcontestModel::where('contest_id',$contest_id)->where('user_id',$groupmemberid->user_id)->count();
		if($invited==0)
			{
			
				$invited_member=privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$groupmemberid['user_id'])->count();
					if($contest_det->visibility=="p" && $invited_member==0)
					{
						$privat_user['user_id']=$groupmemberid['user_id'];
						$privat_user['contest_id']=$contest_id;
						$privat_user['requesteddate']=date('Y-m-d H:i:s');
						$privat_user['status']=1;
						privateusercontestModel::create($privat_user);
						unset($privat_user);
					}
					if($invited_member==0)
					{
						
						$input_details['group_id']=$groupid;
				$input_details['contest_id']=$contest_id;
				$input_details['invitedetail']=1;
				$input_details['inviteddate']=$curdate;
				$input_details['user_id']=$groupmemberid['user_id'];
				invitegroupforcontestModel::create($input_details);
						
						$user=ProfileModel::where('ID',$groupmemberid['user_id'])->first();
						if($user['firstname'] !='')
						$name=$user['firstname'].' '.$user['lastname'];
						else
						$name=$user['username'];
						$email=$user['email'];
						$gcmid = $user['gcm_id'];
						$device_type = $user['device_type'];
						///
						if($gcmid!='' && $device_type=='A'){
					$Message['user_id']=$groupmemberid['user_id'];
					$Message['title']='Ding Datt';
					$Message['message']='You are invited for the Contest :'.$contestname;
					$Message['contest_id']=$contest_id;
					$Message = array("notification"=>$Message); 
					$DeviceId = array($gcmid);
					$Message = array("notification"=>$Message);
					$this->PushNotification($DeviceId, $Message);
					
					}else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = $groupmemberid['user_id'].'*You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					} else { 
					
						$usertimezone = $user['timezone'];
						$userdateformat = $user['dateformat'];
					   
						if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
						$conteststartdate = timezoneModel::convert($contest_det->conteststartdate, 'UTC',$usertimezone, $userdateformat);
						$contestenddate = timezoneModel::convert($contest_det->contestenddate, 'UTC',$usertimezone, $userdateformat);
					
					$this->invitegroupmemberforcontestmail($email,$contestcreatedby,$contesttype,$contestname,$contest_id,$groupname,$contestimage,$conteststartdate,$contestenddate);
					}
					////
					//$inv_suc_message = 	'Invitation sent successfully.';
					$inv_suc_message =1;
					}
			}			
		}
		if($inv_suc_message == 1 ){ 
			$Response = array(
						'success' => '1',
						'message' => 'Invited Successfully',
						'msgcode' =>"c166",
					);
				$final=array("response"=>$Response);
				return json_encode($final);	
		}else{ 		
			$Response = array(
					'success' => '0',
					'message' => 'No members to invite',
					'msgcode' =>"c167",
				);
			$final=array("response"=>$Response);
			return json_encode($final);	
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

 $contestdetails = contestModel::select('contest.createdby','contest.visibility','contest.contest_name','contest.contesttype','user.firstname','user.lastname','user.username','contest.conteststartdate','contest.contestenddate','contest.themephoto')->LeftJoin('user','user.ID','=','contest.createdby')->where('contest.ID',$contest_id)->get();
$userid=$contestdetails[0]['createdby'];

if($contestdetails[0]['firstname']!=''){ $contestcreatedby = $contestdetails[0]['firstname'].' '.$contestdetails[0]['lastname']; } else { $contestcreatedby =$contestdetails[0]['username']; }

$conteststartdate = $contestdetails[0]['conteststartdate'];
			$contestenddate = $contestdetails[0]['contestenddate'];
					
					$contesttype = $contestdetails[0]['contesttype'];
					if($contesttype=='p') $contesttype="Photo"; else if($contesttype=='v') $contesttype="Video"; else if($contesttype=='t') $contesttype="Topic";
					$contestname =  $contestdetails[0]['contest_name'];
					$contestimage = $contestdetails[0]['themephoto'];

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
			
		 //$folloerdetails = followModel::select('user.firstname','user.lastname','user.username','user.ID as follower_user_id','user.gcm_id','user.email','user.device_type')->LeftJoin('user','user.ID','=','followers.followerid')->where('followers.id',$uninvitedfollower[$i])->get();
		 
		 $folloerdetails = ProfileModel::where('ID',$followerid)->get();
		
		 $gcmid = $folloerdetails[0]['gcm_id'];
		 $device_type = $folloerdetails[0]['device_type'];
		 $email = $folloerdetails[0]['email'];
		 if($folloerdetails[0]['firstname']!=''){ $name =$folloerdetails[0]['firstname'].' '.$folloerdetails[0]['lastname']; }else{ $name =$folloerdetails[0]['username'];} 
		
 	
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
			
				if($gcmid!='' && $device_type=='A'){
				$Message['user_id']=$folloerdetails[0]['follower_user_id'];
				$Message['title']='Ding Datt';
				$Message['message']='You are invited for the Contest :'.$contestdetails[0]['contest_name'];
				$Message['contest_id']=$contest_id;
				$Message = array("notification"=>$Message); 
				$DeviceId = array($gcmid);
                $Message = array("notification"=>$Message);
                $this->PushNotification($DeviceId, $Message);
				
				}else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = $folloerdetails[0]['follower_user_id'].'*You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					} else { 
								
					//$contestcreatedby= User::find($contestdetails[0]['createdby']);
					
					$usertimezone = $folloerdetails[0]['timezone'];
					$userdateformat = $folloerdetails[0]['dateformat'];
                   
					if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
					$conteststartdate = timezoneModel::convert($contestdetails[0]['conteststartdate'], 'UTC',$usertimezone, $userdateformat);
					$contestenddate = timezoneModel::convert($contestdetails[0]['contestenddate'], 'UTC',$usertimezone, $userdateformat);
					
				
				$this->invitefollowerforcontestmail($name,$email,$contestcreatedby,$contesttype,$contestname,$contest_id,$conteststartdate,$contestenddate,$contestimage);
				
				}  
					
		}
		$Response = array(
                'success' => '1',
                'message' => 'Invited Successfully',
				'msgcode' =>"c166",
            );
        $final=array("response"=>$Response);
        return json_encode($final);		
	 }
	 else
	 {
	 $Response = array(
                'success' => '0',
                'message' => 'Already Invited All Followers',
				'msgcode' =>"171",
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	 }

 }else{
 $ok=0;
	$user_id = Input::get('user_id');
	 $follower_id = Input::get('follower_id');
	 $inpudetails['follower_id'] = $follower_id; 
	 /*$invitefollowcnt = invitefollowerforcontestModel::where('follower_id',$follower_id)->where('contest_id',$contest_id)->get()->count();
	 if($invitefollowcnt==0)
	 {*/
	 invitefollowerforcontestModel::create($inpudetails);
		$ok=1;
			/******** Here want to set the Notification for Group Members *********/
			$groupmemberlist = followModel::select('id')->where('userid',$follower_id)->where('followerid',$user_id)->get();
			
			//return $groupmemberlist[0]['id'];
			///
		 /*$folloerdetails = followModel::select('user.firstname','user.lastname','user.username','user.ID as follower_user_id','user.gcm_id','user.email')->LeftJoin('user','user.ID','=','followers.followerid')->where('followers.id',$groupmemberlist[0]['id'])->get();
		
		if($folloerdetails[0]['firstname']!=''){ $name =$folloerdetails[0]['firstname'].' '.$folloerdetails[0]['lastname']; }else{ $name =$folloerdetails[0]['username'];} 
		
		  $gcmid = $folloerdetails[0]['gcm_id'];
		  $email = $folloerdetails[0]['email']; */
		  
		  $user = ProfileModel::where('ID', $follower_id)->first();
                if ($user['firstname'] != '')
                    $name = $user['firstname'] . ' ' . $user['lastname'];
                else
                    $name = $user['username'];
                $email = $user['email'];
				$gcmid = $user['gcm_id'];
				$device_type = $user['device_type'];
 	
			if($contestdetails[0]['visibility']=='p')
			{
			                $privat_user['user_id']=$follower_id;
							$privat_user['contest_id']=$contest_id;
							$privat_user['requesteddate']=date('Y-m-d H:i:s');
							$privat_user['status']=1;
							$privatecontestcnt = privateusercontestModel::where('user_id',$follower_id)->where('contest_id',$contest_id)->get()->count();
							if($privatecontestcnt==0)
							privateusercontestModel::create($privat_user);
			
			}
			
			
				if($gcmid!='' && $device_type=='A'){
				$Message['user_id']=$follower_id;
				$Message['title']='Ding Datt';
				$Message['message']='You are invited for the Contest :'.$contestdetails[0]['contest_name'];
				$Message['contest_id']=$contest_id;
				$Message = array("notification"=>$Message); 
				$DeviceId = array($gcmid);
                $Message = array("notification"=>$Message);
                $this->PushNotification($DeviceId, $Message);
				
				}else if($gcmid!='' && $device_type=='I'){
						$DeviceId = $gcmid;
						$Message = $follower_id.'*You are invited for the Contest :'.$contestname;
						$Message = str_replace(" ", "_", $Message);
						$this->PushNotificationIos($DeviceId,$Message);
					
					} else { 
								
					//$contestcreatedby= User::find($contestdetails[0]['createdby']);
					$usertimezone = $user['timezone'];
					$userdateformat = $user['dateformat'];
                   
					if($userdateformat=='dd/mm/yy'){ $userdateformat='d/m/Y h:i a'; }else{ $userdateformat='m/d/Y h:i a'; }
					$conteststartdate = timezoneModel::convert($contestdetails[0]['conteststartdate'], 'UTC',$usertimezone, $userdateformat);
					$contestenddate = timezoneModel::convert($contestdetails[0]['contestenddate'], 'UTC',$usertimezone, $userdateformat);
					
				$this->invitefollowerforcontestmail($name,$email,$contestcreatedby,$contesttype,$contestname,$contest_id,$conteststartdate,$contestenddate,$contestimage);
										
				}  
		//}else{$ok=2;}
			
			////
		if($ok==1){
		$Response = array(
                'success' => '1',
                'message' => 'Invited Successfully',
				'msgcode' =>"c166",
            );
        $final=array("response"=>$Response);
        return json_encode($final);
		}else if($ok==2){
		$Response = array(
                'success' => '0',
                'message' => 'Already Invited this Followers',
				'msgcode' =>"171",
            );
        $final=array("response"=>$Response);
        return json_encode($final);
		}
}
}
public function getfollowerlistforinvitecontest()
{
	$contest_id = Input::get('contest_id');
	$userid = contestModel::select('createdby')->where('ID',$contest_id)->get();
	$userid=$userid[0]['createdby'];

	$followers = followModel::where('followerid',$userid)
	->select('followers.id as followerprimaryid','user.profilepicture','user.firstname','user.lastname','user.username','user.ID as followerid','followers.followerid as followid')
	->leftJoin('user','user.ID','=','followers.userid')
	->get();
	
	
for($k=0;$k<count($followers);$k++)
		{
		$invitecnt = invitefollowerforcontestModel::where('follower_id',$followers[$k]['followerid'])->where('contest_id',$contest_id)->get()->count();
		if($invitecnt==1){ $followers[$k]['invite']= $invitecnt; }else{ $followers[$k]['invite']= 0; } 
			
		if($followers[$k]['firstname']!=''){ $followers[$k]['name'] = $followers[$k]['firstname'].' '.$followers[$k]['lastname']; }else{ $followers[$k]['name'] = $followers[$k]['username']; }
		if($followers[$k]['profilepicture']!='') { $followers[$k]['profilepicture']=  url().'/public/assets/upload/profile/'.$followers[$k]['profilepicture']; }
		
		}	

				$Response = array(
					'success' => '1',
					'message' => 'Followers  List',
					'msgcode' =>"c173",
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

       /* Mail::send([],
		array('pass' => $string,'email' => $email,'username' => $username), function($message) use ($string,$email,$username)
		{

		$mail_body = "Dear {username},<br><br>Your Forgot password request Received.Your Password details is<br><br>Password: {password} <br><br> Thank You, <br><br>Regards,<br>DingDatt";
        $mail_body = str_replace("{password}", $string, $mail_body);
        $mail_body = str_replace("{username}", $username, $mail_body);
        $message->setBody($mail_body, 'text/html');
        $message->to($email);
        $message->subject('Password Details - DingDatt');
        });*/
		
				Mail::send([],
					array('pass' => $string,'email' => $email,'username' => $username), function($message) use ($string,$email,$username)
					{
						/* $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="font-family:Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="'.URL().'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear {username}</div>
								
								<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your Forgot password request Received.Your Password details is<br><br>Username: {username} <br><br>Password: {password}  </div>
								
								<div style="margin:10px;"><a href="'.URL().'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
								</div>
								
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b; width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
							</body>'; */
							
							$mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="font-family:Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%; width:100%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear {username}</div>
								
								<table width="100%"><tr style="height:10px;"><td></td></tr><tr><td style="height:30px;">
								Your Forgot password request Received.Your Password details is
								</td></tr>
								<tr><td style="height:30px;">Username: {username}</td></tr>
								<tr><td style="height:30px;">Password: {password}</td></tr>
								<tr><td style="height:45px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a></td></tr>
								<tr><td style="border-top:#005377 1px solid; height:30px;"><span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span></td></tr>
								</table>
							</div>
														
							
							</body>';
							
						$mail_body = str_replace("{password}", $string, $mail_body);
						$mail_body = str_replace("{username}", $username, $mail_body);
							
						$message->setBody($mail_body, 'text/html');
						$message->to($email);
						$message->subject('DingDatt - Forgot password');
					}); 

        $Response = array(
					'success' => '1',
					'message' => 'Your Password send to your Mail',
					'msgcode' =>"c174",
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);
        }
        else
        {
		$Response = array(
					'success' => '0',
					'message' => 'Your Email Id and Password is Invalid',
					'msgcode' =>"c175",
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);
		
        }
        

}
public function getinterest()
{
	//$interestList=InterestCategoryModel::lists('Interest_name','Interest_id');
	 $interestList=InterestCategoryModel::select('Interest_name','Interest_id')->where('status',1)->get()->toArray();
	$Response = array(
					'success' => '1',
					'message' => 'Interest Details are fetched Successfully',
					'msgcode' =>"c176",
				);
			   $final=array("response"=>$Response,"interestdetails"=>$interestList);
			   return json_encode($final);

}

public function participatedcontest()
{
	$userid = Input::get('userid');
	$timezone = Input::get('timezone');

	$participants=contestparticipantModel::where('user_id',$userid)->lists('contest_id'); 
	$participatedcontest=contestModel::select('ID','contest_name','themephoto','contestenddate','conteststartdate','votingstartdate','votingenddate','prize','createdby','description','noofparticipant')->whereIn('ID',$participants)->orderby('ID','DESC')->get();
	

	  
	if(count($participants)!=0)
	{
	for($i=0; $i<count($participatedcontest);$i++){ 
	
	$participants=contestparticipantModel::where('user_id',$userid)->where('contest_id',$participatedcontest[$i]['ID'])->get()->count(); 
	
	if($participants) $participatedcontest[$i]['contestparticipantid']=1; else $participatedcontest[$i]['contestparticipantid']=0;
	
	$participatedcontest[$i]['contestenddate'] = timezoneModel::convert($participatedcontest[$i]['contestenddate'],'UTC',$timezone, 'd-m-Y h:i a'); 
	
	$participatedcontest[$i]['conteststartdate'] = timezoneModel::convert($participatedcontest[$i]['conteststartdate'],'UTC',$timezone, 'd-m-Y h:i a');
	
	$participatedcontest[$i]['votingstartdate'] = timezoneModel::convert($participatedcontest[$i]['votingstartdate'],'UTC',$timezone, 'd-m-Y h:i a');
	
	$participatedcontest[$i]['votingenddate'] = timezoneModel::convert($participatedcontest[$i]['votingenddate'],'UTC',$timezone, 'd-m-Y h:i a');
	$participatedcontest[$i]['themephoto'] = url().'/public/assets/upload/contest_theme_photo/'.$participatedcontest[$i]['themephoto'];
	
	}

		$Response = array(
						'success' => '1',
						'message' => 'Participated Contest details are fetched Successfully',
						'msgcode' =>"c177",
					);
				   $final=array("response"=>$Response,"participatedcontest"=>$participatedcontest);
				   return json_encode($final);
	}
	else{ 
		$Response = array(
						'success' => '0',
						'message' => 'No Data Available',
						'msgcode' =>"c117",
					);
				   $final=array("response"=>$Response);
				   return json_encode($final);

	}
}

public function createdcontest()
{
$userid = Input::get('userid');
$timezone = Input::get('timezone');
$createdcontest=contestModel::select('ID','contest_name','themephoto','contestenddate','conteststartdate','votingstartdate','votingenddate','prize','createdby','description','noofparticipant')->where('createdby',$userid)->where('status',1)->orderby('ID','DESC')->get();
if(count($createdcontest)!=0)
{
for($i=0; $i<count($createdcontest);$i++){ 

$createdcontest[$i]['contestenddate'] = timezoneModel::convert($createdcontest[$i]['contestenddate'],'UTC',$timezone, 'd-m-Y h:i a'); 

$createdcontest[$i]['conteststartdate'] = timezoneModel::convert($createdcontest[$i]['conteststartdate'],'UTC',$timezone, 'd-m-Y h:i a');
	
	$createdcontest[$i]['votingstartdate'] = timezoneModel::convert($createdcontest[$i]['votingstartdate'],'UTC',$timezone, 'd-m-Y h:i a');
	
	$createdcontest[$i]['votingenddate'] = timezoneModel::convert($createdcontest[$i]['votingenddate'],'UTC',$timezone, 'd-m-Y h:i a');

$participants=contestparticipantModel::where('user_id',$userid)->where('contest_id',$createdcontest[$i]['ID'])->get()->count(); 
	
	if($participants) $createdcontest[$i]['contestparticipantid']=1; else $createdcontest[$i]['contestparticipantid']=0;
	
	$createdcontest[$i]['themephoto'] = url().'/public/assets/upload/contest_theme_photo/'.$createdcontest[$i]['themephoto'];
}

	$Response = array(
					'success' => '1',
					'message' => 'Created Contest details are fetched Successfully',
					'msgcode' =>"c179",
				);
			   $final=array("response"=>$Response,"createdcontest"=>$createdcontest);
			   return json_encode($final);
}
else{ 
	$Response = array(
					'success' => '0',
					'message' => 'No Data Available',
					'msgcode' =>"c117",
				);
			   $final=array("response"=>$Response);
			   return json_encode($final);

}
}
//////////////need to modify///////////////////////


///Admin join the member to their group
public function addmemberintogroup()
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
						'msgcode' =>"c181",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
			
			}
			}
			else{ 
				$Response = array(
						'success' => '0',
						'message' => 'Some Details Missing',
						'msgcode' =>"c182",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
			}
	}
	else{
	////////// Private send invite to members  
	$inputdetails['inviteddate']=$curdate;
	$ok=0;
	 
		for($i=0;$i<$countuserexplode;$i++){ 
				 $inputdetails['user_id']=$useridexplode[$i];
				 $inputdetails['invitetype']='m';
				$invitedcnt = invitememberforgroupModel::where('user_id',$useridexplode[$i])->where('invitetype','m')->where('group_id',$group_id)->count();
				if($invitedcnt==0)
				{
				$invite = invitememberforgroupModel::create($inputdetails);
				if($invite){
				$ok=1;
				$userid = $useridexplode[$i];
				$getcreateduserdetails = ProfileModel::select('email','firstname','lastname')->where('ID',$userid)->get();
				$email = $getcreateduserdetails[0]['email'];
				$name = $getcreateduserdetails[0]['firstname'].' '.$getcreateduserdetails[0]['lastname'];
				$groupname = $groupdetails[0]['groupname'];
				$url = 'http://192.168.1.52/dingdatt';
				}			
			}else{ $ok =-1;}
			}
			if($ok==1){
			  $Response = array(
								'success' => '1',
								'message' => 'we are Invite the Members successfully',
								'msgcode' =>"c183",
							);
						$final=array("response"=>$Response);
						return json_encode($final);
				}else if($ok==-1){
						  $Response = array(
							'success' => '0',
							'message' => 'Already invited',
							'msgcode' =>"c200",
						);
					$final=array("response"=>$Response);
					return json_encode($final);				
				}else{
				$Response = array(
						'success' => '0',
						'message' => 'Some Details Missing',
						'msgcode' =>"c182",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
				
				}
	}
}
///// This is accept both side requests ///////
public function groupmemberaccepttheadminrequest()
{

	$userid=Input::get('userid');
	$groupid = Input::get('group_id');
	$k=2;
	
	if($userid!='' && $groupid!='')
	{
	$userid = explode(',',$userid);
	$useridcount = count($userid);	
	$groupid = explode(',',$groupid);
	$curdate = date('Y-m-d h:i:s');
	
	$inputdetails['createddate']=$curdate;
	$accepttype = Input::get('accepttype');
	
	for($i=0;$i<$useridcount;$i++)
	{
	$inputdetails['user_id']=$userid[$i];
	$inputdetails['group_id']=$groupid[$i];	
	
	if($accepttype=='accept')
	{
	$saved = groupmemberModel::create($inputdetails);
	
	if(!empty($saved))
	{

		 $invite = invitememberforgroupModel::where('group_id',$groupid[$i])->where('user_id',$userid[$i])->delete();
		if($invite)
		{
				$k=1;
		}
	}
	}
	else{
	
	$invite = invitememberforgroupModel::where('group_id',$groupid[$i])->where('user_id',$userid[$i])->delete();
		if($invite)
		{
			$k=0;
		}
	
	}
	}
	
	
	//return $k;


	if($k==1){
		$Response = array(
							'success' => '1',
							'message' => 'You are Accepted to that Group',
							'msgcode' =>"c184",
						);
					$final=array("response"=>$Response);
					return json_encode($final);

	}else if($k==0){ 
	
		$Response = array(
							'success' => '1',
							'message' => 'You are Rejected',
							'msgcode' =>"c185",
						);
					$final=array("response"=>$Response);
					return json_encode($final);
	
	}else{
		$Response = array(
							'success' => '0',
							'message' => 'Required field is missing',
							'msgcode' =>"c182",
						);
					$final=array("response"=>$Response);
					return json_encode($final);
	
	}

}
}
//////////Member Send Request to group admin ///////
public function memberequesttogroup()
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
						'msgcode' =>"c181",
					);
				$final=array("response"=>$Response);
				return json_encode($final);			
			}
	
	}
	}
	else{ 
		
		$alreadyinvited =invitememberforgroupModel::where('user_id',$userid)->where('invitetype','u')->where('group_id',$group_id)->count();
		
		if($alreadyinvited==0)
		{
			$invite = invitememberforgroupModel::create($inputdetails);
				if($invite){
				
				$getcreateduserdetails = ProfileModel::select('email','firstname','lastname')->where('ID',$userid)->get();
				$email = $getcreateduserdetails[0]['email'];
				$name = $getcreateduserdetails[0]['firstname'].' '.$getcreateduserdetails[0]['lastname'];
				$groupname = $groupdetails[0]['groupname'];
				
				$Response = array(
						'success' => '1',
						'message' => 'Your request sent to Group admin',
						'msgcode' =>"c187",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
				
				}
		}else{		
			$Response = array(
						'success' => '0',
						'message' => 'Already invited',
						'msgcode' =>"c200",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
		
		}
		}
}
public function searchmember(){
	$searcheduser = Input::get('searchkey');
	$data = Input::get('group_id');
	//$userdetails = ProfileModel::where('')->get();

	$groupmemberid = groupmemberModel::where('group_id',$data)->get()->lists('user_id');
    $groupmemberuserid = ProfileModel::whereNotIn('user.ID', $groupmemberid)->lists('ID');

	$userdetails=ProfileModel::select('profilepicture','firstname','lastname','username','ID')->where('username','like','%'.$searcheduser.'%')->where('status',1)->whereIn('user.ID', $groupmemberuserid)->Orwhere('firstname','like','%'.$searcheduser.'%')->whereIn('user.ID', $groupmemberuserid)->get();	
	
	//$userdetails=ProfileModel::select('profilepicture','firstname','lastname','username','ID')->where('username','like','%'.$searchkey.'%')->where('status',1)->Orwhere('firstname','like','%'.$searchkey.'%')->get();

	for($i=0; $i<count($userdetails); $i++){

	if($userdetails[$i]['profilepicture']!='') { $userdetails[$i]['profilepicture']=url().'/public/assets/upload/profile/'.$userdetails[$i]['profilepicture']; }
	
	if($userdetails[$i]['firstname']!=''){ $userdetails[$i]['name']=$userdetails[$i]['firstname'].' '.$userdetails[$i]['lastname']; }else{
	$userdetails[$i]['name']=$userdetails[$i]['username'];
	}
	}
	
	if(count($userdetails)>0)
	{
	$Response = array(
						'success' => '1',
						'message' => 'User details fetched successfully',
						'msgcode' =>"c188",
					);
				$final=array("response"=>$Response, "userdetails"=>$userdetails);
				return json_encode($final);
	}else{
	
	$Response = array(
						'success' => '0',
						'message' => 'No data avialable',
						'msgcode' =>"c181",
					);
				$final=array("response"=>$Response, "userdetails"=>$userdetails);
				return json_encode($final);
	
	}
}

public function getadminrequest(){
	
  $user_id = Input::get('user_id');
	
	$invitedlist = invitememberforgroupModel::select('invitememberforgroup.user_id as userid','user.profilepicture','user.firstname','user.lastname','user.username','group.groupname','group.ID as group_id','invitememberforgroup.invitetype','group.groupimage')->where('group.createdby',$user_id)->where('invitememberforgroup.invitetype','u')->LeftJoin('group','group.ID','=','invitememberforgroup.group_id')->Join('user','user.ID','=','invitememberforgroup.user_id')->get();
	
	for($i=0;$i<count($invitedlist); $i++){
	
	if($invitedlist[$i]['firstname']!='') {  $invitedlist[$i]['name'] = $invitedlist[$i]['firstname'].' '.$invitedlist[$i]['lastname']; }  else { $invitedlist[$i]['name'] = $invitedlist[$i]['username']; }
	
	if($invitedlist[$i]['profilepicture']!=''){ $invitedlist[$i]['profilepicture'] =  url().'/public/assets/upload/profile/'.$invitedlist[$i]['profilepicture']; }	
	
	if($invitedlist[$i]['groupimage']!=''){ $invitedlist[$i]['groupimage']= url().'/public/assets/upload/group/'.$invitedlist[$i]['groupimage']; }
	}
	if(count($invitedlist)>0)
	{
	$Response = array(
						'success' => '1',
						'message' => 'Your admin request are fetched successfully',
						'msgcode' =>"c190",
					);
				$final=array("response"=>$Response, "getadminrequest"=>$invitedlist);
				return json_encode($final);
	}else{
	
	$Response = array(
						'success' => '0',
						'message' => 'No data avialable',
						'msgcode' =>"c117",
					);
				$final=array("response"=>$Response, "getadminrequest"=>$invitedlist);
				return json_encode($final);
	
	}	
	}
	public function getmemberrequest(){
	
	$user_id = Input::get('user_id');
	
	$invitedlist = invitememberforgroupModel::select('invitememberforgroup.user_id as userid','user.firstname','user.lastname','user.username','group.groupname','invitememberforgroup.invitetype','group.ID as group_id','group.groupimage','group.createdby')->LeftJoin('user','user.ID','=','invitememberforgroup.user_id')->where('user_id',$user_id)->where('invitetype','m')->LeftJoin('group','group.ID','=','invitememberforgroup.group_id')->get();

	for($i=0;$i<count($invitedlist); $i++){	
		
	
	$memberdetails = User::find($invitedlist[$i]['createdby']);  
		if($memberdetails['firstname']!=''){   $invitedlist[$i]['name'] = $memberdetails['firstname'].' '.$memberdetails['lastname'];  } else {  $invitedlist[$i]['name']=$memberdetails['username'];  } 
		
	if($memberdetails['profilepicture']!=''){ $invitedlist[$i]['profilepicture'] =  url().'/public/assets/upload/profile/'.$memberdetails['profilepicture']; }	
	
	if($invitedlist[$i]['groupimage']!=''){ $invitedlist[$i]['groupimage']= url().'/public/assets/upload/group/'.$invitedlist[$i]['groupimage']; }
	
	}
	
	if(count($invitedlist)>0)
	{
	
	$Response = array(
						'success' => '1',
						'message' => 'Your member request are fetched successfully',
						'msgcode' =>"c192",
					);
				$final=array("response"=>$Response, "getmemberrequest"=>$invitedlist);
				return json_encode($final);
	}else{ 
	
	$Response = array(
						'success' => '0',
						'message' => 'No data avialable',
						'msgcode' =>"c117",
					);
				$final=array("response"=>$Response, "getmemberrequest"=>$invitedlist);
				return json_encode($final);
	
	}	
	}
	public function requestcount(){
		$user_id = Input::get('user_id');
		
		$admincount = invitememberforgroupModel::select('invitememberforgroup.user_id as userid','user.profilepicture','user.firstname','user.lastname','user.username','group.groupname','group.ID as group_id','invitememberforgroup.invitetype','group.groupimage')->where('group.createdby',$user_id)->where('invitememberforgroup.invitetype','u')->LeftJoin('group','group.ID','=','invitememberforgroup.group_id')->Join('user','user.ID','=','invitememberforgroup.user_id')->get()->count();
	
		$membercount = invitememberforgroupModel::select('invitememberforgroup.user_id as userid','user.firstname','user.lastname','user.username','group.groupname','invitememberforgroup.invitetype','group.ID as group_id','group.groupimage','group.createdby')->LeftJoin('user','user.ID','=','invitememberforgroup.user_id')->where('user_id',$user_id)->where('invitetype','m')->LeftJoin('group','group.ID','=','invitememberforgroup.group_id')->get()->count();
		

	
				$final=array("admincount"=>$admincount,"membercount"=>$membercount);
				return json_encode($final);

	
	}
	function  reportflag(){
		$reporteddata = Input::get('reporteddata');
		$participantid = Input::get('participantid');
		$authuserid = Input::get('user_id');
				
		$inputdetails['contest_participant_id'] = $participantid;
		$inputdetails['report_description'] = $reporteddata;
		$inputdetails['report_userid']= $authuserid; 
		
		$participant_details = contestparticipantModel::select('user_id','contest_id')->where('ID',$participantid)->first();
		
		$inputdetails['postedby_userid']= $participant_details['user_id'];
		$inputdetails['contest_id'] = $participant_details['contest_id'];
		
		$inputdetails['createddate'] = date('Y-m-d h:i:s');

		//reportflagModel::where()
		
		//return $inputdetails;
		
		$validation  = Validator::make($inputdetails, reportflagModel::$rules); 
		if ($validation->passes()) 
			{			
			$created = reportflagModel::create($inputdetails);
			//if($created) return 1;
			
			$Response = array(
						'success' => '1',
						'message' => 'You are reported successfully',
						'msgcode' =>"c194",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
			}
			else{
			
				$Response = array(
						'success' => '0',
						//'message' => 'Required field is missing',
						'message'=>$validation->messages(),
						'msgcode' =>"c195",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
			
			}
	}
	
	function uninvitegroupsforcontest(){
			

		$groupid = Input::get('group_id');
		$contest_id = Input::get('contest_id');
		$inv_suc_message =0;
		$groupmemberid = groupmemberModel::where('group_id',$groupid)->get();	
		
		$k=0;
		for($i=0;$i<count($groupmemberid);$i++){

		$invited=invitegroupforcontestModel::where('contest_id',$contest_id)->where('user_id',$groupmemberid[$i]['user_id'])->count();
		if($invited!=0)
			{
				$invited=invitegroupforcontestModel::where('contest_id',$contest_id)->where('user_id',$groupmemberid[$i]['user_id'])->delete();
				
				$invited_member=privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$groupmemberid[$i]['user_id'])->count();
				if($invited_member) {

				$contestdetails = contestModel::where('ID',$contest_id)->get()->first();	
				if($contestdetails['createdby']!=$groupmemberid[$i]['user_id']){
		
				$invited_memberdelete=privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$groupmemberid[$i]['user_id'])->delete();
				}
				$inv_suc_message =1;
				}
				$k=1;
			}
		}
		if($k==1){
		
			$Response = array(
						'success' => '1',
						'message' => 'Uninvited successfully',
						'msgcode' =>"c198",
					);
				$final=array("response"=>$Response);
				return json_encode($final);		
		}else{
		
			$Response = array(
						'success' => '0',
						'message' => 'No data',
						'msgcode' =>"c199",
					);
				$final=array("response"=>$Response);
				return json_encode($final);
		
		} 
	
	}
	
	function uninvitegroupmemberforcontest(){ 
	
		$groupmemberlist  = Input::get('groupmemberid');
		$group_id = Input::get('group_id');
		$contest_id = Input::get('contest_id');
		$groupmemberlistid = explode(',',$groupmemberlist);
		$inv_suc_message=0;
		for($i=0;$i<count($groupmemberlistid);$i++){
		$groupmemberid = groupmemberModel::where('id',$groupmemberlistid[$i])->get()->first();	
		$invited=invitegroupforcontestModel::where('contest_id',$contest_id)->where('user_id',$groupmemberid->user_id)->count();
		if($invited!=0)
			{
				$invited=invitegroupforcontestModel::where('contest_id',$contest_id)->where('user_id',$groupmemberid->user_id)->delete();
				
				$invited_member=privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$groupmemberid->user_id)->count();
				if($invited_member) 
				{
					$contestdetails = contestModel::where('ID',$contest_id)->get()->first();	
					if($contestdetails['createdby']!=$groupmemberid->user_id){
					$invited_memberdelete=privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$groupmemberid->user_id)->delete();
					}
				}
				$inv_suc_message=1;
			}
			else{
				$inv_suc_message=2;
			}
		} 
		if($inv_suc_message==1){ 
			$Response = array(
						'success' => '1',
						'message' => 'Uninvited successfully',
						'msgcode' =>"c198",
					);
				$final=array("response"=>$Response);
				return json_encode($final);				
		}else if($inv_suc_message==2){
			$Response = array(
						'success' => '0',
						'message' => 'You are not invited.So cant able to uninvite',
						'msgcode' =>"c201",
					);
				$final=array("response"=>$Response);
				return json_encode($final);

		}else{ 
			$Response = array(
						'success' => '0',
						'message' => 'No data',
						'msgcode' =>"c199",
					);
				$final=array("response"=>$Response);
				return json_encode($final);		
		} 	
	}
	public function uninvitefollowerforcontest(){
	
		$contest_id = Input::get('contest_id');
		 $followerid = Input::get('follower_id');
		 $invited=invitefollowerforcontestModel::where('follower_id',$followerid)->where('contest_id',$contest_id)->count();
		if($invited==1)
		{
			invitefollowerforcontestModel::where('follower_id',$followerid)->where('contest_id',$contest_id)->delete();
			 $invited_member=privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$followerid)->count();
			if($invited_member) privateusercontestModel::where("contest_id",$contest_id)->where('user_id',$followerid)->delete();
			$Response = array(
						'success' => '1',
						'message' => 'Uninvited successfully',
						'msgcode' =>"c198",
					);
				$final=array("response"=>$Response);
				return json_encode($final);				
		}
		else { 
			$Response = array(
						'success' => '0',
						'message' => 'No data',
						'msgcode' =>"c199",
					);
				$final=array("response"=>$Response);
				return json_encode($final);		
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
	
	public function PushNotificationIos($DeviceId,$Message) 
    {
      
    $ParentPemUrl = url()."/pushnotificationIOS.php?DeviceId=".$DeviceId."&Message=".$Message;
    $TriggerParent = file_get_contents($ParentPemUrl);
    #exit;    
   
    }
	
	function fetchUrl($path)
    {
        //sadly, https doesn't work out of the box on windows for functions
        //like file_get_contents, so let's make this easy for devs

	$dropbox_root = 'dropbox';

	$dropbox_oauth_token = 'AAD8XwoPtQc0zcXn2Pv3adcMbVVe_pgroi-0NniA6dUF7w';

	$dropbox_oauth_consumer_key = 'z6tj74qaywh91i9';

	$dropbox_access_token = 'LUu8h-uvauAAAAAAAAAAHbDw-bulVL7u8BoRAJtedc0-eCDY-Xj4Qxf1iGucUN7j';
		
		
    $url = "https://api.dropbox.com/1/media/<root>/<path>";
	$fields = array('root'=>$dropbox_root,'path'=>$path,'oauth_token'=>$dropbox_oauth_token,'oauth_consumer_key'=>$dropbox_oauth_consumer_key,'access_token'=>$dropbox_access_token);
	$fields_string = '';
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result,true);
	return $result['url'];
       
    }
	public function invitegroupmemberforcontestmail($email,$contestcreatedby,$contesttype,$contestname,$contest_id,$groupname,$contestimage,$conteststartdate,$contestenddate){
		

			Mail::send([],
					array('email' => $email,'contestcreatedby'=>$contestcreatedby,'contesttype' => $contesttype,'contestname'=>$contestname,'contest_id'=>$contest_id,'groupname'=>$groupname,'contestimage'=>$contestimage,'conteststartdate'=>$conteststartdate,'contestenddate'=>$contestenddate), function($message) use ($email,$contestcreatedby,$contesttype,$contestname,$contest_id,$groupname,$contestimage,$conteststartdate,$contestenddate)
					{
						 
						 /* $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								
								
								</style>
						 <body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
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
													
														<img src="'.URL::to('public/assets/upload/contest_theme_photo/'.$contestimage).'" width="280" height="auto" style="height:auto;" />
													</td>
												</tr>
											
											</table>
											
										</td>
										
										<td>
								<table width="600" height="95" border="0" style="margin-bottom:10px;float:left;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
							  <tr>
								<td class="cont_name" style="font-size:12px; color:#3BBA00; font-weight:bold; width:100px; padding-left:10px;">Contest Name:</td>
								<td class="cont_value" style="font-size:12px; color:#5d5d5d; font-weight:bold;width:100px; padding-left:10px;">'.$contestname.'</td>
							  </tr>
							  <tr>
								<td class="cont_name" style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Contest Type:</td>
								<td  class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$contesttype.'</td>
							  </tr>
							   <tr>
								<td class="cont_name"  style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Created By:</td>
								<td  class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$contestcreatedby.'</td>
							  </tr>
							   
							  <tr>
								<td class="cont_name" style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Start Date:</td>
								<td class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$conteststartdate.'</td>
							  </tr>
							  <tr>
								<td class="cont_name"  style="font-size:12px;color: #3BBA00;font-weight:bold; padding-left:10px;">End Date:</td>
								<td class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$contestenddate.'</td>
							  </tr>
							  
							   <tr>
                              	<td colspan="2">
                                	<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
                            	</td>
                              </tr>					  
							  
							</table>
							</td>									
							</tr>
							</table>
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b; width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
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
	public function invitefollowerforcontestmail($name,$email,$contestcreatedby,$contesttype,$contestname,$contest_id,$conteststartdate,$contestenddate,$contestimage){

					
					Mail::send([],
					array('name'=>$name,'email' => $email,'contestcreatedby'=>$contestcreatedby,'contesttype' => $contesttype,'contestname'=>$contestname,'contest_id'=>$contest_id,'conteststartdate'=>$conteststartdate,'contestenddate'=>$contestenddate,'contestimage'=>$contestimage), function($message) use ($name,$email,$contestcreatedby,$contesttype,$contestname,$contest_id,$conteststartdate,$contestenddate,$contestimage)
					{
					
					
					/* $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								</style>
						 <body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
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
													
														<img src="'.URL::to('public/assets/upload/contest_theme_photo/'.$contestimage).'" width="280" height="auto" style="height:auto;" />
													</td>
												</tr>
											
											</table>
											
										</td>
										
										<td>
								<table width="600" height="95" border="0" style="margin-bottom:10px;float:left;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
							  <tr>
								<td class="cont_name" style="font-size:12px; color:#3BBA00; font-weight:bold; width:100px; padding-left:10px;">Contest Name:</td>
								<td class="cont_value" style="font-size:12px; color:#5d5d5d; font-weight:bold;width:100px; padding-left:10px;">'.$contestname.'</td>
							  </tr>
							  <tr>
								<td class="cont_name" style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Contest Type:</td>
								<td  class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$contesttype.'</td>
							  </tr>
							   <tr>
								<td class="cont_name"  style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Created By:</td>
								<td  class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$contestcreatedby.'</td>
							  </tr>
							   
							  <tr>
								<td class="cont_name" style="font-size:12px;color: #3BBA00;font-weight:bold;padding-left:10px;">Start Date:</td>
								<td class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$conteststartdate.'</td>
							  </tr>
							  <tr>
								<td class="cont_name"  style="font-size:12px;color: #3BBA00;font-weight:bold; padding-left:10px;">End Date:</td>
								<td class="cont_value" style="font-size:12px;color: #5d5d5d;font-weight:bold;padding-left:10px;">'.$contestenddate.'</td>
							  </tr>
							  
							   <tr>
                              	<td colspan="2">
                                	<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
                            	</td>
                              </tr>					  
							  
							</table>
							</td>									
							</tr>
							</table>
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b; width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
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
}
?>