<?php
class HomeController extends BaseController
{
    
    public function home()
    {
	 $languageDetails = languagenameModel::lists('language_name','language_key');
	 return View::make('login/login')->with('languageDetails', $languageDetails);
    }
	 public function HomeLayout()
    {
      return View::make('home/home');
    }
}
?>