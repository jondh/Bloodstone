<?php 
 
App::uses('Component', 'Controller');
App::uses('Model', 'User');
 
class AccessTokenComponent extends Component {
 
    public function checkAccessTokens($publicToken = "0", $privateToken = "0", $timeStamp = "0") {
    	if($publicToken != "0" && $privateToken != "0" && $timeStamp != "0"){
    		$UserModel = ClassRegistry::init('User');

			$token = $UserModel->getPrivateTokenFromPublicToken($publicToken);

			if($token){
				if($token['User']['private_access_token'] != "" && $token['User']['private_access_token'] != null){

					$tokenHash = Security::hash($token['User']['private_access_token'].$timeStamp, 'sha256');

					if($tokenHash == $privateToken){
						return "they good";
					}
					else{
						return "private";
					}
				}
				else{
					return "error";
				}
			}
			else{
				return "public";
			}
		}
		else{
			return "bad data";
		}
    }
}    
 
?>
