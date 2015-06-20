<?php
class userdetailsController extends BaseController {
	//start of jquery register
	public function userdetails()
	{
	$userdetails = User::orderBy('firstname', 'desc')->get()->toArray();
	return View::make('user/view/userdetails')->with('userdetails', $userdetails);	
	}
	public function getuserdetails($data=Null)
	{
	$editid=$data;
	 $profileeditbyid= profileModel::where('ID', $editid)->get()->toArray();
	//$checked = $profileeditbyid[0]['status'];
	return View::make('user/register/userregister')->with('profileeditbyid', $profileeditbyid);
	}
	public function userdetailsdelete($data=Null)
	{	 
	$affectedRows = User::where('ID', $data)->delete();
	return Redirect::to('userdetails')->with('Message', 'User Details deleted Succesfully');
	}
}
?>