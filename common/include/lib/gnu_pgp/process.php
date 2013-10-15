<?
include("gnupgp.class.php");

$action = $_POST["action"];
$userName = $_POST["userName"];
$userEmail = $_POST["userEmail"];
$recipientName = $_POST["recipientName"];
$recipientEmail = $_POST["recipientEmail"];
$message = $_POST["message"];
$passphrase = $_POST["passphrase"];
$key = $_POST["key"];
$nameID = $_POST["nameID"];
$emailID = $_POST["emailID"];
$keyID = $_POST["keyID"];

$gpg = new gnugpg;
$gpg->userName = $userName;
$gpg->userEmail = $userEmail;
$gpg->recipientName = $recipientName;
$gpg->recipientEmail = $recipientEmail;
$gpg->message = $message;

function Tab_List($key_Array){
	echo "<table border=1>";
	echo "<tr><th>Tipo</th><th>Fiducia</th><th>Lungezza</th><th>Algoritmo</th>";
	echo "<th>KeyID</th><th>Creazione</th><th>Scadenza</th><th>ID Locale</th>";
	echo "<th>Fiduciario</th><th>ID Utente</th><th>???</th><th>???</th></tr>";
	for($i=0; $i < count($key_Array); $i++){
		$tmp = explode(":",$key_Array[$i]);
		echo "<tr>";
		echo "<td>".$tmp[0]."</td>";			//type
		echo "<td>".$tmp[1]."</td>";			//trust
		echo "<td>".$tmp[2]."</td>";			//length
		echo "<td>".$tmp[3]."</td>";			//algorithm
		echo "<td>".$tmp[4]."</td>";			//KeyID
		echo "<td>".$tmp[5]."</td>";			//Creation date
		echo "<td>".$tmp[6]."</td>";			//Expiration date
		echo "<td>".$tmp[7]."</td>";			//Local ID
		echo "<td>".$tmp[8]."</td>";			//Ownertrust
		echo "<td>".htmlspecialchars($tmp[9])."</td>";	//User ID
		echo "<td>".$tmp[10]."</td>";			// ???
		echo "<td>".$tmp[11]."</td>";			// ???
		echo "</tr>";
		if($tmp[0] == "sub"){
			echo "<tr><td colspan=\"12\">&nbsp;</td></tr>";
		}
	}
	echo "</table>";
	echo "<br><br>";
	echo "<font size=-1>1. Tipo di protocollo<br>
		<UL>
	    		<LI>pub = chiave pubblica
	    		<LI>sub = sottochiave (chiave secondaria)
	    		<LI>sec = chiave segreta
	    		<LI>ssb = sottochiave segreta (chiave secondaria)
	    		<LI>uid = id utente (solo fingerprint di campo 10)
	    		<LI>fpr = fingerprint: (fingerprint di campo 10)
	    		<LI>pkd = dati di chiavepubblica (formato di campo speciale, vedi sotto)
		</UL>
		</font><br>";
	echo "<font size=-1>2. Fiducia calcolata. Questa è una singola lettera, ma generata in considerazione che nel prossimo futuro possano seguire informazioni addizionali. (non usato per chiavi segrete)<br>
	    	<UL>
			<LI>o = Sconosciuto (questa chiave è nuova al sistema)
			<LI>d = La chiave è stata disabilitata
			<LI>r = La chiave è stata revocata
			<LI>e = La chiave è scaduta
			<LI>q = Indefinito (nessun valore assegnato)
			<LI>n = Non fidarsi affatto di questa chiave
			<LI>m = C'è fiducia marginale in questa chiave
			<LI>f = La chiave è in piena fiducia.
			<LI>u = La chiave è di prossima fiducia. Campo utilizzato solo per le chiavi delle quali è disponibile anche la chiave segreta.
		</UL>
		   </font><br>";
	echo "<font size=-1>3. Lunghezza della chiave in bit.</font><br><br>";
	echo "<font size=-1>4. Algoritmo:<br>
		<UL>
			<LI>1 = RSA
			<LI>16 = ElGamal (solo cifratura)
		       	<LI>17 = DSA (a volte chiamato DH, solo firma)
		       	<LI>20 = ElGamal (firma e cifratura)
		</UL>
		</font><br>";
	echo "<font size=-1>5. KeyID.</font><br><br>";
	echo "<font size=-1>6. Data di creazione (in UTC).</font><br><br>";
	echo "<font size=-1>7. Data di scadenza della chiave (o vuoto se nessuna).</font><br><br>";
	echo "<font size=-1>8. ID Locale: Numero di record nella cartella dei record nel TrustDb. Questo valore è valido fintantoché il TrustDb non viene eliminato. Puoi usare \"#<local-id> come id utente quando specifichi la chiave. Questo è necessario perché gli id delle chiavi non sono unici - un programma può utilizzarlo per accedere alle chiavi più tardi.</font><br><br>";
	echo "<font size=-1> 9. Fiduciario (solo chiavi pubbliche principali). Questa è una singola lettera, ma generata in considerazione che nel prossimo futuro possano seguire informazioni addizionali.</font><br><br>";
	echo "<font size=-1>10. ID Utente. Il valore viene quotato come una stringa di C, per evitare caratteri di controllo (i duepunti sono quotati \"\x3a\").</font><br><br>";
	echo "<font size=-1>11. Field: ????.</font><br><br>";
	echo "<font size=-1>12. Field: ????.</font><br><br>";
}
function key_data($key_Array){
	for($i=0; $i < count($key_Array); $i++){
		$tmp = explode(":",$key_Array[$i]);
		$key_data[$i]["type"] = $tmp[0];
		$key_data[$i]["trust"] = $tmp[1];
		$key_data[$i]["length"] = $tmp[2];
		$key_data[$i]["algorithm"] = $tmp[3];
		$key_data[$i]["key_id"] = $tmp[4];
		$key_data[$i]["creation_date"] = $tmp[5];
		$key_data[$i]["expiration_date"] = $tmp[6];
		$key_data[$i]["local_id"] = $tmp[7];
		$key_data[$i]["ownertrust"] = $tmp[8];
		$key_data[$i]["user_id"] = htmlspecialchars($tmp[9]);
	}
	return $key_data;
}

switch ($action){
	case "Genera":
		$result = $gpg->gen_key($userName, $comment, $userEmail, $passphrase);
		if(!$result){
			echo $gpg->error;
			exit();
		} else {
			echo "<h3>La chiave è stata generata con successo.</h3>";
		}
		break;
	case "Elenca":
		$result = $gpg->list_keys();
		if(!$result){
			echo $gpg->error;
			exit();
		} else {
			echo "<h3>Questa è la chiave nel portachiavi di <font color=red>".$gpg->userEmail."</font></h3><br>";
			Tab_List($gpg->keyArray);
		}
		break;
	case "Esporta":
		$result = $gpg->export_key();
		if(!$result){
			echo $gpg->error;
			exit();
		} else {
			echo "<h3>Questa è la Chiave Pubblica di <font color=red>".$gpg->userEmail."</font></h3><br>";
			echo "<form><TEXTAREA rows=\"30\" cols=\"80\">".$gpg->public_key."</TEXTAREA>";
		}
		break;
	case "Importa":
		$result = $gpg->import_key($key);
		if(!$result){
			echo $gpg->error;
			exit();
		} else {
			echo "<h3>La chiave è stata importata con successo.</h3><br>";
					$result = $gpg->list_keys();
			if(!$result){
				echo $gpg->error;
				exit();
			} else {
				echo "<h3>Questa è la chiave nel portachiavi di <font color=red>".$gpg->userEmail."</font></h3><br>";
				Tab_List($gpg->keyArray);
			}
		}
		break;
	case "Rimuovi":
		if(!empty($keyID)){
			$key = $keyID;
		} elseif (!empty($emailID)){
				$key = $emailID;
			} else {
				$key = $nameID;
				}

		$result = $gpg->remove_key($key);
		if(!$result){
			echo $gpg->error;
			exit();
		} else {
			echo "<h3>La chiave è stata rimossa con successo.</h3><br>";
			$result = $gpg->list_keys();
			if(!$result){
				echo $gpg->error;
				exit();
			} else {
				echo "<h3>Questa sono le chiavi nel portachiavi di <font color=red>".$gpg->userEmail."</font></h3><br>";
				Tab_List($gpg->keyArray);
			}
		}
	case "Cifra":
		if(empty($userEmail)){
			echo "Il campo \"Dall'indirizzo e-mail:\" non può essere vuoto!";
			exit();
		}
		if(empty($recipientEmail)){
			echo "Il campo \"All'indirizzo e-mail:\" non può essere vuoto!";
			exit();
		}
		$result = $gpg->encrypt_message();
		if (!$result) {
			echo $gpg->error;
			exit();
		} else {
			echo "<h3>Il messaggio è stato cifrato!</h3><br>";
			echo "<form><TEXTAREA rows=\"20\" cols=\"80\">".$gpg->encrypted_message."</TEXTAREA></form>";
		}
		break;
	case "Decifra":
		if(empty($userEmail)){
			echo "Il campo \"Indirizzo e-mail\" non può essere vuoto!";
			exit();
		}
		if(empty($passphrase)){
			echo "Il campo \"Passphrase\" non può essere vuoto!";
			exit();
		}
		if(empty($message)){
			echo "Il \"Message\" non può essere vuoto!";
			exit();
		}
		$result = $gpg->decrypt_message($message, $passphrase);
		if (!$result) {
			echo $gpg->error;
			exit();
		} else {
			echo "<h3>Il messaggio è stato decifrato!</h3><br>";
			echo "<form><TEXTAREA rows=\"20\" cols=\"80\">".$gpg->decrypted_message."</TEXTAREA></form>";
		}
		break;
} // end switch $action

?>
