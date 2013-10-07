<?php
header("Content-type: text/plain;");

$ini = ";;; AIRS CONFIGURATION FILE ;;;\n";
$ini .= "\n";
$ini .= "[system]\n";
$ini .= "type = \"" . $_POST["system_type"] . "\" ; use 'beta|develop', empty for official release\n";
$ini .= "need_ssl = \"" . $_POST["system_need_ssl"] . "\"\n";
$ini .= "key = \"" . $_POST["system_key"] . "\" ; Public System key\n";
$ini .= "default_host_uri = \"" . $_POST["system_host"] . "\"\n";
$ini .= "\n";
$ini .= "[mail]\n";
$ini .= "pop_host = \"" . $_POST["mail_pop_host"] . "\"\n";
$ini .= "pop_port = " . $_POST["mail_pop_port"] . "\n";
$ini .= "pop_auth = " .(($_POST["mail_pop_auth"] == "on") ? "true" : "false") . "\n";
$ini .= "pop_username = \"" . $_POST["mail_pop_username"] . "\"\n";
$ini .= "pop_password = \"" . $_POST["mail_pop_password"] . "\"\n";
$ini .= "smtp_host = \"" . $_POST["mail_smtp_host"] . "\"\n";
$ini .= "smtp_port = " . $_POST["mail_smtp_port"] . "\n";
$ini .= "smtp_auth = " .(($_POST["mail_smtp_auth"] == "on") ? "true" : "false") . "\n";
$ini .= "smtp_username = \"" . $_POST["mail_smtp_username"] . "\"\n";
$ini .= "smtp_password = \"" . $_POST["mail_smtp_password"] . "\"\n";
$ini .= "debug = " .(($_POST["mail_debug"] == "on") ? "true" : "false") . "\n";
$ini .= "charset = \"" . $_POST["mail_charset"] . "\"\n";
$ini .= "\n";
$ini .= "From = \"" . $_POST["mail_from"] . "\"\n";
$ini .= "Reply-To = \"" . $_POST["mail_reply_to"] . "\"\n";
$ini .= "Errors-To = \"" . $_POST["mail_errors_to"] . "\"\n";
$ini .= "\n";
$ini .= "mail_text_signature = \"" . str_replace(array("\n", "\r"), array("\\n", ""), $_POST["mail_signature_txt"]) . "\"\n";
$ini .= "mail_html_signature = '" . addslashes($_POST["mail_signature_html"]) . "'\n";
$ini .= "\n";
$ini .= "[database]\n";
$ini .= "host = \"" . $_POST["db_host"] . "\"\n";
$ini .= "username = \"" . $_POST["db_username"] . "\"\n";
$ini .= "password = \"" . $_POST["db_password"] . "\"\n";
$ini .= "\n";
$ini .= "[language]\n";
$ini .= "default_language = \"" . $_POST["lang_code"] . "\"\n";
$ini .= "default_language_name = \"" . $_POST["lang_codename"] . "\"\n";
$ini .= "default_encoding = \"" . $_POST["lang_encoding"] . "\"\n";
$ini .= "\n";
$ini .= "[company]\n";
$ini .= "name = \"" . $_POST["company_name"] . "\"\n";
$ini .= "address = \"" . $_POST["company_address"] . "\"\n";
$ini .= "contacts = \"" . $_POST["company_contacts"] . "\"\n";
$ini .= "uri = \"" . $_POST["company_uri"] . "\"\n";
$ini .= "url = \"" . $_POST["company_url"] . "\"\n";
$ini .= "\n";
$ini .= "license_txt = \"" . $_POST["company_license_txt"] . "\"\n";
$ini .= "license_html = \"" . $_POST["company_license_html"] . "\"\n";

$airs_conf = "../../conf/airs.conf";
$fh = fopen($airs_conf, "w") or die("no");
fwrite($fh, $ini);
fclose($fh);
print "ok";

?>