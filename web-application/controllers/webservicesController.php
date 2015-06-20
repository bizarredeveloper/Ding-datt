<?php

class webservicesController extends BaseController {

public function mobilelogin()
{
      
		if((Input::get('username'))!=''){ $LoginData_user = Input::except(array('email')); }else{
		$LoginData_user = Input::except(array('username')); }

		$LoginData = Input::get();
		$validator = Validator::make($LoginData,ProfileModel::$loginrule);
		
		if (Auth::attempt($LoginData_user))
		{  
           $userid = Auth::user()->ID;	   
		   $Response = array(
                'success' => '1',
                'message' => 'successfully Login',
				'userid' =>$userid
            );
			$final=array("response"=>$Response);
            return json_encode($final);				 
			
		} 
		else
        {
			
			$Response = array(
                'success' => '0',
                'message' => $validator->messages(),
            );
			$final=array("response"=>$Response);
			
            return json_encode($final);	
        }
		
}


public function addgroupmembers()
{
	$curdate = date('Y-m-d h:i:s');
	$inputdetails = Input::get();
	$inputdetails['createddate'] = $curdate; //return $inputdetails;
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
	else{ 
	    $Response = array(
                'success' => '0',
                'message' => 'Some Details Missing',
            );
        $final=array("response"=>$Response);
        return json_encode($final);
	}
}
public function getgrouplist()
{
	
	$grouplist = groupModel::get()->toArray();
	$Response = array(
                'success' => '1',
                'message' => 'Group Members saved successfully',
            );
        $final=array("response"=>$Response,"grouplist" =>$grouplist);
        return json_encode($final);
	

}

public function mobileregister1()
{
return "AS";
}

}
?>