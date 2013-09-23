<?
/*************************************************************************************************************
** Title.........: PGP Class
** Version.......: 0.02
** Author........: Rodrigo Z. Armond <rodzadra@passagemdemariana.com.br>
** Filename......: gnugpg.class.php
** Last changed..: 2001-13-11
** Notes.........: Segunda versão.
** TODO..........: Documentation, more consistents error message, more options, etc, etc, etc....
**
**************************************************************************************************************/
include("config.php");							// This was not function by now.

define(GPG_BIN, "/usr/bin/gpg");					// This is the GNUpg Binary file
define(GPG_PARAMS, " --no-tty --no-secmem-warning --home ");		// The default parameters to use the GNUpg
define(GPG_USER_DIR, "/var/www/.gnupg/");					// This is where the users dir will be created
define(GPG_PASS_LENGTH, 8);						// This is the minimum lenght accepted of the passphrase
define(FAIL_NO_RECIPIENT, 1);						// If one recipient, from the recipient list, do not exist...
									// ...this will return an erro else, if one dont exist and all...
									// ...the other exist this continues (see. function mount_recipients).
define(GEN_HTTP_LOG, 1);						// This generate or not logs in the HTTP log

class gnugpg{
	var $userName;			// the user name (owner of the keyrings)
	var $userEmail;			// the user email (owner email)
	var $subject;			// the subject of message
	var $message;			// the clean txt message to encript
	var $passphrase;		// the passphrase to decrypt the message
	var $encrypted_message;		// the returned message encrypted
	var $decrypted_message;		// the returned message decrypted
	var $gpg_path;			// the gpg base path to the private sub-dir of the user
	var $recipientName;		// the name of the recipient
	var $recipientEmail;		// the recipient email
	var $keyArray;			// this will be filled with the keys on the keyrings
	var $public_key;		// this is the variable used to export the owner public key (export_key)
	var $encrypt_myself; 		// boolean to indicate if the message will be encrypted with the user owner key
	var $valid_keys;		// array with the list of recipients that are on the keyring
	var $not_valid_keys;		// array with a list of recipient that are not on the keyring

function gnugpg(){			// initialization of class variables

	$this->gpg_path = GPG_USER_DIR;
	$this->subject = $subject;
	$this->message = $message;
	$this->recipientEmail = $recipientEmail;
	$this->recipientName = $recipientName;
	$this->userName = $userName;
	$this->userEmail = $userEmail;
	$this->passphrase = $passphrase;
	$this->encrypt_myself = $encrypt_myself;

	
  	//verifies that the GNUpg binary exists
	if(!file_exists(GPG_BIN)){
  		$this->error = "GNUpg binary file ".GPG_BIN." does not exist.\n";
		return(false);
	}

	//check that the GNUpg binary is executable
	if(!is_executable(GPG_BIN)){
  		$this->error = "GNUpg binary file ".GPG_BIN." is not executable.\n";
		return(false);
 	}

 }

 /*
  function check_private_dir()

  This function check if the private gnupg dir exist for the user $userName
 */
 function check_private_dir(){
 	// clear the filesystem cache
	clearstatcache();

	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

	// check if the user dir exists
	if(!is_dir($priv_path)){
		$this->error = "Error: The user dir doesn't exist. (in function check_private_dir - 1)";
		return(false);
	}

	return(true);

 } // end function check_private_dir


 /*
  function check_pubring()

  This function check if the pubring.gpg exists
 */
 function check_pubring(){
 	// clear the filesystem cache
	clearstatcache();

	$file_ = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg/pubring.gpg";

	// check if the user dir exists
	if(!file_exists($file_)){
		$this->error = "Error: The user pubring does not exists. Maybe the key was not be generated. (in function check_pubring - 1)";

		return(false);
	}

	return(true);

 } // end function check_pubring

 
 /*
  function check_all()

  This function check the private dir and the pubring
 */
 function check_all(){

	if(!$this->check_private_dir() OR !$this->check_pubring()){
		return(false);
	}
	return(true);

 } // end function check_all

 /*
  function mount_recipients()

  This function return an array of valid recipients to receive the message encrypted
  (to be a valid recipient, the recipient must be on the keyring), and an array with
  invalid recipient (that isn't on the key ring). 

  NOTE!!! IF FAIL_NO_RECIPIENT IS 0 AND ONE (OR MORE) RECIPIENTS ARE NOT IN THE KEYRING,
  THIS FUNCTION WILL RETURN FALSE (THIS IS THE DEFAULT). OTHERWISE, IF FAIL_NO_RECIPIENT
  
  IS SET TO 1, THE FUNCTION WILL NOT RETURN AN ERROR MESSAGE AND WILL CONTINUES NORMALY.
  YOU CAN SET FAIL_NO_RECIPIENT TO 1 AND MAKE THE USE OF THE $this->not_valid_keys TO 
  FIND WHAT IS THE RECIPIENT THAT ARE NOT IN THE KEYRING.
 */
  function mount_recipients($recipients){

  	if(!$this->check_all()){
		return(false);
	}
  	
	// clear vars
	unset($this->valid_recipients, $this->unvalid_recipients);
	unset($keys, $valid_keys, $not_valid_keys);

	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

	// call the gpg to list the keys
	$tmp = explode(";",$recipients);	// create a temp array with all the recipients

	for($i=0; $i < count($tmp); $i++){
		// mount the command to list the keys
		$command = GPG_BIN.GPG_PARAMS.$priv_path." --with-colons --list-key ".trim($tmp[$i]);
		if(GEN_HTTP_LOG){
			$command .= " 2>/dev/null";
		}

		// execute the list-key command for all recipients separeted
		exec($command, $keyArray, $errorcode); 

		if($errorcode){
			if(FAIL_NO_RECIPIENT) {
				$this->error = "Error: One or more recipients are not in the keyring. (in function mount_recipients - 1)";
				return(false);
			}
			$not_valid_keys .= trim($tmp[$i]).";";
		} else {

			for($j=0; $j < count($keyArray); $j+=2){
				$keys = array(explode(":",$keyArray[$j]));
				$valid_keys .= $keys[0][9].";";
			}
			unset($keyArray);
		}
	}

	$this->valid_keys = explode(";",$valid_keys);
	$this->not_valid_keys = explode(";",$not_valid_keys);
		
 	return(true);
  } // end function mount_recipients


 /*
  function check_keyID()

  $keyID = the key(s) to check if exist.
  this can be a simple key or various keys separeded with ';'
  
  check if exist the user dir exist and if the keyID is on the keyring.
  Returns false when failed, or true.
 */
 function check_keyID($keyID){

  	if(!$this->check_all()){
		return(false);
	}

	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

	// call the gpg to list the keys
	$command = GPG_BIN.GPG_PARAMS.$priv_path;
	
	$tmp = explode(";",$keyID);
	
	for($i=0; $i < count($tmp); $i++){					// list-key for every recipient
		$command .= " --list-key ".trim($tmp[$i]);	
	}

	if(GEN_HTTP_LOG){
		$command .= " 2>/dev/null";
	}

	exec($command, $keyArray, $errocode);
	
	if($errorcode){
		$this->error = "Error: The keyID \"$keyID\" isn't on the keyring. (in function check_keyID - 3)";
		return(false);
	}
	if(count($keyArray > 0)) {
		return(true);
	} else {
		$this->error = "Error: The keyID \"$keyID\" isn't on the keyring. (in function check_keyID - 4)";
		return(false);
	}

 } // end function check_keyID


/*
  function list_keys()

  List all the publics keys on the keyrings
  Return an array ($this->keyArray) with the keys.
  Returns false when failed. If failed, look at $this->error for the reason.
 */
 function list_keys(){

	if(!$this->check_all()){
		return(false);
	}

 	if (!$this->check_keyID($this->userName)){
		return(false);
	}

	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

	$command = GPG_BIN.GPG_PARAMS.$priv_path." --list-key --fingerprint --with-colons";

	if(GEN_HTTP_LOG){
		$command .= " 2>/dev/null";
	}

	exec($command, $keyArray, $errorcode);

	if($errorcode){
		$this->error = "Error: Can't list the keys. (in function list_keys - 1)";
		return(false);
	}

	unset($this->keyArray);

	$this->keyArray = $keyArray;
	
	return(true);
	 
 } // end function list_keys



 /*
  function encrypt_message()

  Encrypt a clean txt message.
  Returns false when failed, or the encrypted message in the $this->encrypted_message, when (if) succeed.
  If failed, look at the $this->error for the reason.       
 */
 function encrypt_message(){

 	if(!$this->check_all()){
		return(false);
	}
	
 	// first check if the key is on the keyring
	if (!$this->check_keyID($this->recipientEmail)){
		return(false);
	} 

	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

	
	// generate token for unique filenames
	$tmpToken = md5(uniqid(rand()));

	// create vars to hold paths and filenames
	$plainTxt = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/".$tmpToken.".data";
	$cryptedTxt =  $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/".$tmpToken.".pgp";

	// open .data file and dump the plaintext contents into this
	$fd = @fopen($plainTxt, "w+");
	if(!$fd){
		$this->error = "Error: Can't create the .data file. Verify if you have write access on the dir.(in function encrypt_message - 1)";
		return(false);
	}
	@fputs($fd, $this->message);
	@fclose($fd);

	$this->encrypt_myself = true;

	// invoque the GNUgpg to encrypt the plaintext file
	$command = GPG_BIN.GPG_PARAMS.$priv_path." --always-trust --armor";
	
	// mount the valid recipient array
	if(!$this->mount_recipients($this->recipientEmail)){
	 	return(false);
	}

	for($i=0; $i < count($this->valid_keys); $i++){
		if (trim($this->valid_keys[$i]) != ""){
		 	$command .= " --recipient '".$this->valid_keys[$i]."'";
		}
	}

	// Include the message to yourself
	if($this->encrypt_myself) {
		$command .= " --recipient '$this->userEmail'";
	}

	$command .= " --output '$cryptedTxt' -e $plainTxt";

	if(GEN_HTTP_LOG){
		$command .= " 2>/dev/null";
	}


	// execute the command
	system($command, $errorcode);

	if($errorcode){
		$this->error = "Error: Can't crypt the message. (in function encrypt_message - 2)";
		@unlink($plainTxt);
		return(false);
	} else {
		// open the crypted file and read contents into var
		$fd = @fopen($cryptedTxt, "r");
		$tmp = @fread($fd, filesize($cryptedTxt));
		@fclose($fd);
		
		// delete all the files
		@unlink($plainTxt);
		@unlink($cryptedTxt);

		// verifies the PGP signature
		if(ereg("-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----",$tmp)) {
			$this->encrypted_message = $tmp;
			unset($tmp);
        		return(true);
		} else {
			$this->error = "Error: The header/footer of the crypt message isn't valid. (in function encrypt_message - 3)";
			unset($tmp);
			return(false);
		}
	}
 } // end function encrypt_message()


 /*
  function decrypt_message()

  Decrypt the armored crypted message.
  Returns false when failed, or decrypted message in the $this->decrtypted_message, when (if) succeed.
  If failed, look at the $this->error for the reason.
 */
 function decrypt_message($message, $passphrase){

  	if(!$this->check_all()){
		return(false);
	}

 	// first check if the key is on the keyring
	if (!$this->check_keyID($this->recipientEmail)){
		return(false);
	}
	
	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

	// check the header/footer of message to see if this is a valid PGP message
 	if(!ereg("-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----",$message)) {
		$this->error = "Error: The header/footer of message not appear to be a valid PGP message. (in function decrypt_message - 1)";
		unset($passphrase);
		return(false);
	} else {

	 	// generate token for unique filenames
		$tmpToken = md5(uniqid(rand()));
		
		// create vars to hold paths and filenames
		$plainTxt = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/".$tmpToken.".data";
		$cryptedTxt =  $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/".$tmpToken.".gpg";
	
		// create/open .pgp file and dump the crypted contents
		$fd = @fopen($cryptedTxt, "w+");
		if(!$fd){
			$this->error = "Error: Can't create the .gpg file. Verify that you have write acces on the directory. (in function decrypt_message - 2)";
			unset($passphrase);
			return(false);
		}
		@fputs($fd, $message);
		@fclose($fd);
		
		// create the command to execute
		$command = "echo '$passphrase' | ".GPG_BIN.GPG_PARAMS.$priv_path." --batch --passphrase-fd 0 -r '$this->userName' -o $plainTxt --decrypt $cryptedTxt";

		if(GEN_HTTP_LOG){
			$command .= " 2>/dev/null";
		}

		// execute the command to decrypt the file
		system($command, $errcode);

		unset($passphrase);
		
		// open the decrypted file and read contents into var
		$fd = @fopen($plainTxt, "r");
		if(!$fd){
			$this->error = "Error: Can't read the .asc file. Verify if you have entered the correct user/password. (in function decrypt_message - 3)";
			@unlink($cryptedTxt);
			return(false);
		}
		$this->decrypted_message = @fread($fd, filesize($plainTxt));
		@fclose($fd);
		
		// delete all the files
		@unlink($plainTxt);
		@unlink($cryptedTxt);
	
		return(true);
		
	}
 } // end function decrypt_message


 /*
  function import_key($key)

  Import public key to keyring. NOTE IT MUST BE IN ARMORED FORMAT (ASC).
  Returns false when failed.  If failed, look at the $this->error for the reason.
 */
 function import_key($key = ""){

  	if(!$this->check_all()){
		return(false);
	}
	
 	// first check if the key is on the keyring
	if (!$this->check_keyID($this->recipientEmail)){
		return(false);
	} 

	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

 	// check if the key to import isn't empty
	if($key == ""){
  		$this->error = "Error: No public key file specified. (in function import_key - 1)";
		return(false);
	}

	// Checks the header/footer to see if is a valid PGP PUBLIC KEY
	if(!ereg("-----BEGIN PGP PUBLIC KEY BLOCK-----.*-----END PGP PUBLIC KEY BLOCK-----",$key)) {
		$this->error = "Error: This not appear to be a valid PGP message. Error in header and/or footer. (in function import_key - 2)";
		return(false);
	} else {

	 	// generate token for unique filenames
		$tmpToken = md5(uniqid(rand()));
		
		// create vars to hold paths and filenames
		$tmpFile = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/".$tmpToken.".public.asc";

		// open file and dump in plaintext contents
		$fd = @fopen($tmpFile, "w+");
		if (!$fd){
			$this->error = "Error: Can't creates .tmp file to add the key. Verify that you have write access in the dir. (in function import_key - 3)";
			return(false);
		}
		@fputs($fd, $key);
		@fclose($fd);

		$command = GPG_BIN.GPG_PARAMS.$priv_path." --import '$tmpFile'";

		if(GEN_HTTP_LOG){
			$command .= " 2>/dev/null";
		}

		system($command,$errorcode);
	
		if($errorcode){
			$this->error = "Error: Can't add the public key. (in function import_key - 4)";
			@unlink($tmpFile);
			return(false);
		} else {
			@unlink($tmpFile);
			return(true);
		}
	}
	
 } // end function import_key


/*
  function export_key():
  
  Export the owner public key in asc armored format.
  Returns false when failed.  If failed, look at the $this->error for the reason.
 */
 function export_key(){			// TODO: option to make an file to attachment

   	if(!$this->check_all()){
		return(false);
	}

 	// first check if the key is on the keyring
	if (!$this->check_keyID($this->recipientEmail)){
		return(false);
	}
	
	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";


	$command = GPG_BIN.GPG_PARAMS.$priv_path." --batch --armor --export '".$this->userEmail."'";

	if(GEN_HTTP_LOG){
		$command .= " 2>/dev/null";
	}

	exec($command, $result, $errorcode);

	if($errorcode){
		$this->error = "Error: Can't export the public key. (in function export_key - 1)";
		return(false);
	}
	$this->public_key = implode("\n",$result);
	return(true);
	
 } // end function export_key



 /*
  function remove_key():

  Remove a public key from keyring
  Returns false when failed.  If failed, look at the $this->error for the reason.
 */
 function remove_key($key = ""){

   	if(!$this->check_all()){
		return(false);
	}
	
 	// first check if the key is on the keyring
	if (!$this->check_keyID($this->recipientEmail)){
		return(false);
	} 

	$priv_path = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail)."/.gnupg";

  	if($key == ""){
  		$this->error = "Error: no specified public key to remove. (in function remove_key - 1)";
		return(false);
	}

	$command = GPG_BIN.GPG_PARAMS.$priv_path." --batch --yes --delete-key '$key'";

	if(GEN_HTTP_LOG){
		$command .= " 2>/dev/null";
	}

	system($command,$errorcode);

	if($errorcode) {
		$this->error = "Error: Can't remove the key. (in function remove_key - 2) ";
		return(false);
	}
	return(true);
	
 } // end function remove_key


 /* 
  function gen_key()

  Make the generation of keys controlled by a parameter file.
  This feature is not very well tested and is not very well documented.
  Just use this if you do not have how to generate the key in a secure machine.
 */
 function gen_key($userName, $comment="", $userEmail, $passphrase){
	
	// the utf8.php includes is necessary, because to generate the key is needed to
	// enter the characters in the UTF-8 form :-/
 	include("utf8.php");
 
	// verify the variables
 	if(empty($userName)){
		$this->error = "Error: The username is empty. (in function gen_key - 1)";
		return(false);
	}
 	if(empty($userEmail)){
		$this->error = "Error: The email is empty. (in function gen_key - 2)";
		return(false);
	}
 	if(empty($passphrase)){
		$this->error = "Error: The passphrase is empty. (in function gen_key - 3)";
		return(false);
	}
	if(strlen(trim($passphrase)) < GPG_PASS_LENGTH){
		$this->error = "Error: The passphrase is too short. (in function gen_key - 4)".count(trim($passphrase));
		return(false);
	}
	
	if($this->check_private_dir()){
		$this->error = "Error: The user dir already exist. (in function gen_key - 5)";
		return(false);
	}

 	$path_ = $this->gpg_path.ereg_replace("[@]","_",$this->userEmail);

 	// create the user dir (if this not exists)
 	if(!file_exists($path_)){
		if(!mkdir($path_, 0777, true)){
			$this->error = "Error: Can't create a new user dir. (in function gen_key - 6) " . $this->gpg_path;
			return(false);
		}
		if(!mkdir($path_."/.gnupg", 0777, true)){
			$this->error = "Error: Can't create the gnupg dir. (in function gen_key - 7)";
			return(false);
		}
	} else {
		$this->error = "Error: The user dir exist, please try another name. (in function gen_key - 8)";
		return(false);
	}

	$utf = new utf;
	$utf->loadmap("./8859-1.TXT","iso");

	// prepares the temporary config file
 	//$tmpConfig  = "Key-Type: DSA\r\nKey-Length: 1024\r\nSubkey-Type: ELG-E\r\nSubkey-Length: 2048\r\n";
 	$tmpConfig  = "Key-Type: RSA\r\nKey-Length: 2048\r\nSubkey-Type: ELG-E\r\nSubkey-Length: 2048\r\n";
	$tmpConfig .= "Name-Real: ".$utf->cp2utf($userName,"iso")."\r\n";
	if (!empty($comment)){
		$tmpConfig .= "Name-Comment: ".$utf->cp2utf($comment)."\r\n";
	}
	$tmpConfig .= "Name-Email: ".$userEmail."\r\nExpire-Date: 0\r\nPassphrase: ".$passphrase."\r\n";
	$tmpConfig .= "%commit\r\n";

	// generate token for unique filenames
	$tmpToken = md5(uniqid(rand()));

	// create vars to hold paths and filenames
	$tmpConfigFile = $path_."/".$tmpToken.".conf";

	// open .data file and dump the plaintext contents into this
	$fd = @fopen($tmpConfigFile, "w+");
	if(!$fd){
		$this->error = "Error: Can't create the temporary config file. Verify if you have write permission on the dir.(in function gen_key - 9)";
		return(false);
	}
	@fputs($fd, $tmpConfig);
	@fclose($fd);

	unset($tmpConfig);

	// invoque the GNUgpg to generate the key
	$home = $path_."/.gnupg";
	$command = GPG_BIN.GPG_PARAMS."$home --batch --gen-key -a $tmpConfigFile";

	if(GEN_HTTP_LOG){
		$command .= " 2>/dev/null";
	}

	system($command, $errorcode);

	@unlink($tmpConfigFile);

	if($errorcode){
		$this->error = "Error: Can't generate the key. (in function encrypt - 10) ~ " . $command . "\n<br />" . $path_;
		$this->RmdirR($path_);
		return(false);
	} else {
		return(true);
	}

 } // end function gen_key

// remove dirs recursivelly -- from commum_function (UebiMiau)
function RmdirR($userPath) {
	
	// just for a minimum of security
	if($this->gpg_path != GPG_USER_DIR or $this->gpg_path = "/"){
		return(false);
	}
	$location = $userPath;
	$all=opendir($location); 
        if (substr($location,-1) <> "/") $location = $location."/";
        $all=opendir($location);
        while ($file=readdir($all)) {
                if (is_dir($location.$file) && $file <> ".." && $file <> ".") {
                        $this->RmdirR($location.$file);
                        unset($file);
                } elseif (!is_dir($location.$file)) {
                        unlink($location.$file);
                        unset($file);
                }
        }
        closedir($all);
        unset($all);
        rmdir($location);

} // end function RmdirR

} // end class
?>
