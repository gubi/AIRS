<?
/*************************************************************************************************************
** Title.........: PGP Class
** Version.......: 0.01a
** Author........: Rodrigo Z. Armond <rodzadra@passagemdemariana.com.br>
** Filename......: config.php
** Last changed..: 2001-10-22
** Notes.........: This is the first alpha.
** TODO..........: Documentation, more consistents error message, more options, etc, etc, etc....
**
**************************************************************************************************************/
$GPG_BIN = "/usr/local/bin/gpg --no-tty --no-secmem-warning";	// This is the GNUpg Binary file
$GPG_USER_DIR = "/var/www/gpg/";					// This is where the users dir will be created
$GPG_PASS_LENGTH = 8;						// This is the minimum lenght accepted of the passphrase

// -- define the errors code

// -- system errors
define(GPG_ERR1, "Il file binario di GNUpg non esiste.\n");
define(GPG_ERR2, "Il file binario di GNUpg non è eseguibile.\n");
// -- check_key function errors code
define(GPG_ERR3, "La directory locale GNUpg dell'utente non esiste.\n");
define(GPG_ERR4, "La lista delle Chiavi non è stata trovata. Verifica che la directory locale GNUpg dell'utente sia esistente.\n");
define(GPG_ERR5, "Il keyID di destinazione non è nel tuo portachiavi.\n");
// -- encrypt_message function erros code
define(GPG_ERR6, "Non è possibile creare il file .data. Verifica che la directory locale GNUpg abbia i permessi in scrittura.\n");
define(GPG_ERR7, "Non è possibile cifrare il messaggio. Errore sconosciuto (GPG ERROR 7).\n");
define(GPG_ERR8, "Errore durante la cifratura del messaggio: le intestazioni del messaggio non sono valide.\n");
// -- decrypt_message function erros code
define(GPG_ERR9, "Errore durante la decifratura del messaggio: le intestazioni del messaggio cifrato non sembrano essere di un messaggio PGP valido.\n");
define(GPG_ERR10,"Non è possibile creare il file .gpg. Verifica che la directory locale GNUpg abbia i permessi in scrittura.\n");
define(GPG_ERR11,"Non è possibile leggere il file .asc. Verifica che username/password siano validi.\n");
// -- import_key function errors code
define(GPG_ERR12,"Nessun file di chiave pubblica specificato.\n");
define(GPG_ERR13,"Questa non sembra una valida chiave PGP pubblica. Errore in intestazione e/o piè.\n");
define(GPG_ERR14,"Non è possibile creare il file .tmp per l'aggiunta della chiave. Verifica che la directory locale GNUpg abbia i permessi in scrittura.\n");
define(GPG_ERR15,"Impossibile aggiungere la chiave pubblica al portachiavi utente. Errore sconosciuto.\n");
// -- export_key function erros code
define(GPG_ERR16,"Impossibile esportare la propria chiave pubblica. Può essere che l'utente non esista più.\n");
// -- remove_key function errors code
define(GPG_ERR17,"Nessuna chiave pubblica specificata da rimuovere.\n");
define(GPG_ERR18,"Non è possibile rimuovere la chiave.\n");
// -- list_key function errors code
define(GPG_ERR19,"Non è possibile elencare le chiavi.\n");
// -- gen_key function errors code
define(GPG_ERR20,"Lo username è vuoto.\n");
define(GPG_ERR21,"L'indirizzo e-mail è vuoto.\n");
define(GPG_ERR22,"La passphrase è vuota.\n");
define(GPG_ERR23,"La passphrase è troppo corta.\n");
define(GPG_ERR24,"Non è possibile creare una nuova directory utente. Verifica che la directory GPG abbia i permessi in scrittura.\n");
define(GPG_ERR25,"Non è possibile creare la directory utente locale GNUpg. Verifica che la directory GPG abbia i permessi in scrittura.\n");
define(GPG_ERR26,"La directory utente esiste, prova con un altro nome.\n");
define(GPG_ERR27,"Non è possibile creare il file temporaneo di configurazione. Verifica che la directory GPG abbia i permessi in scrittura.\n");
define(GPG_ERR28,"Non è possibile generare la chiave. Errore sconosciuto.\n");
?>
