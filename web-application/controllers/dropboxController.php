<?php
require_once "dropbox-sdk/lib/Dropbox/autoload.php";

class dropboxController extends BaseController
{   
    public function dropbox()
    {
	//return Input::file('uploadfile'); 
	//return $uploadfile = Input::file('uploadfile');
	 //use \Dropbox as dbx;
    $appInfo = \Dropbox\AppInfo::loadFromJsonFile("app_info.json");
	
	$webAuth = new \Dropbox\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
 $authorizeUrl = $webAuth->start();

	$accessToken = "LUu8h-uvauAAAAAAAAAAHbDw-bulVL7u8BoRAJtedc0-eCDY-Xj4Qxf1iGucUN7j";
$dbxClient = new \Dropbox\Client($accessToken, "PHP-Example/1.0");

$accountInfo = $dbxClient->getAccountInfo();

//  $dbxClient->getAccessToken();
//return  $dbxClient->_getMetadata("/participant/Fl0dsItC_Airtel.png");
$host = $dbxClient->getHost();

return $this->fetchUrl("/participant/Fl0dsItC_Airtel.png");

//$appendFilePath =  $dbxClient->appendFilePath();
//print_r($appendFilePath);

/*echo "1. Go to: " . $authorizeUrl . "\n";
echo "2. Click \"Allow\" (you might have to log in first).\n";
echo "3. Copy the authorization code.\n";
$authCode = \trim(\readline("Enter the authorization code here: "));
list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
print "Access Token: " . $accessToken . "\n";  */


/*$f = fopen(url()."/public/assets/upload/contest_theme_photo/Fl0dsItC_Airtel.png", "rb");
$result = $dbxClient->uploadFile("/participant/Fl0dsItC_Airtel.png", \Dropbox\WriteMode::add(), $f);
fclose($f);*/

$f = fopen("Fl0dsItC_Airtel.png", "w+b");
$fileMetadata = $dbxClient->getFile("/participant/Fl0dsItC_Airtel.png", $f);




//return $dbxClient->getMetadata("/participant/Fl0dsItC_Airtel.png"); 
fclose($f); 


	//return View::make('dropbox/dropbox');
	}
		
	public function dropboxfetch(){
	 return View::make();
	}
	
	  private function fetchUrl($path)
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
}
?>