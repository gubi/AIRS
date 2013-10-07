<?php
/**
* Check database connection
* 
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
header("Content-type: text/plain");

try {
	$pdo = new PDO("mysql:host=" . $_POST["db_host"], $_POST["db_username"], $_POST["db_password"]);
	$stmt = $pdo->query("SHOW DATABASES");
	print "ok";
	$pdo = null;
} catch (PDOException $e) {
	switch ($e->getCode()) {
		case 1042:
			$message = "Hostname errato";
			break;
		case 1044:
			$message = "Accesso negato al database per questo utente";
			break;
		case 1045:
			$message = "Errore di connessione: username o password errati";
			break;
		case 1049:
			$message = "Database sconosciuto";
			break;
		case 1053:
			$message = "Il Server MySQL si sta arrestando...";
			break;
	}
	print "error::" . $message;
	die();
}
?>