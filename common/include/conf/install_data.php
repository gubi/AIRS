<?php
$charset_arr = array(array("text" => "ARMSCII-8 Armeno", "value" => "armscii-8"),array("text" => "US ASCII", "value" => "ascii"),array("text" => "Big5 Cinese tradizionale", "value" => "big5"),array("text" => "Binary pseudo charset", "value" => "binary"),array("text" => "Windows Europa Centrale", "value" => "windows-1250"),array("text" => "Windows Cirillico", "value" => "windows-1251"),array("text" => "Windows Arabo", "value" => "windows-1256"),array("text" => "Windows Baltico", "value" => "windows-1257"),array("text" => "DOS Europa Occidentale", "value" => "windows-850"),array("text" => "DOS Europa Centrale", "value" => "windows-852"),array("text" => "DOS Russo", "value" => "windows-866"),array("text" => "SJIS for Windows Giapponese", "value" => "windows-932"),array("text" => "DEC Europa Occidentale", "value" => "dec8"),array("text" => "UJIS for Windows Giapponese", "value" => "eucjpms"),array("text" => "EUC-KR Koreano", "value" => "euckr"),array("text" => "GB2312 Cinese Semplificato", "value" => "gb2312"),array("text" => "GBK Cinese Semplificato", "value" => "gbk"),array("text" => "GEOSTD8 Georgiano", "value" => "geostd8"),array("text" => "ISO 8859-7 Greco", "value" => "greek"),array("text" => "ISO 8859-8 Ebreo", "value" => "hebrew"),array("text" => "HP Europa Occidentale", "value" => "hp8"),array("text" => "DOS Kamenicky Ceco-slovacco", "value" => "keybcs2"),array("text" => "KOI8-R Relcom Russo", "value" => "koi8r"),array("text" => "KOI8-U Ucraino", "value" => "koi8u"),array("text" => "cp1252 Europa Occidentale", "value" => "iso-8859-1"),array("text" => "ISO 8859-2 Europa Centrale", "value" => "iso-8859-2"),array("text" => "ISO 8859-9 Turco", "value" => "iso-8859-5"),array("text" => "ISO 8859-13 Baltico", "value" => "iso-8859-7"),array("text" => "Mac Europa Centrale", "value" => "macce"),array("text" => "Mac Europa Occidentale", "value" => "macroman"),array("text" => "Shift-JIS Giapponese", "value" => "sjis"),array("text" => "7bit Svedese", "value" => "swe7"),array("text" => "TIS620 Tailandese", "value" => "tis620"),array("text" => "UCS-2 Unicode", "value" => "ucs2"),array("text" => "EUC-JP Giapponese", "value" => "ujis"),array("text" => "UTF-8 Unicode", "value" => "utf-8", "selected" => "selected"));
$fieldset_arr[0] = array(
	"legend" => $i18n["system"]["legend"]["general_params"],
	"table" => array(
		"tr" => 4,
		"th" => array(
			$i18n["system"]["th"]["version_type"],
			$i18n["system"]["th"]["use_protected_version"],
			$i18n["system"]["th"]["global_encryption_key"],
			$i18n["system"]["th"]["system_address"]
		),
		"help" => array(
			$i18n["system"]["help"]["version_type"],
			$i18n["system"]["help"]["use_protected_version"],
			$i18n["system"]["help"]["global_encryption_key"],
			$i18n["system"]["help"]["system_address"]
		),
		"td" => array(
			array(
				"element" => "select",
				"id" => "system_version_type",
				"name" => "system_type",
				"style" => "width: 150px;",
				"tabindex" => 1,
				"nest" => array(
					"option" => array(
						array("text" => "- Nessuno - ", "value" => ""),
						array("text" => "Beta", "value" => "beta"),
						array("text" => "Sviluppo", "value" => "develop", "selected" => "selected")
					)
				)
			),
			array(
				"element" => "input",
				"id" => "system_need_ssl",
				"name" => "system_need_ssl",
				"type" => "checkbox",
				"tabindex" => 2,
				"checked" => "checked"
			),
			array(
				"element" => "input",
				"id" => "system_key",
				"name" => "system_key",
				"type" => "password",
				"class" => "key",
				"placeholder" => "••••••••••••••",
				"require" => "true",
				"size" => 36,
				"tabindex" => 3,
				"require" => "true",
				"error_txt" => $i18n["system"]["error"]["global_encryption_key"],
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "system_host",
				"name" => "system_host",
				"type" => "url",
				"value" => "https://" . $_SERVER["HTTP_HOST"],
				"require" => "true",
				"error_txt" => $i18n["system"]["error"]["system_address"],
				"tabindex" => 4,
				"nest" => array()
			)
		)
	)
);
$fieldset_arr[1] = array(
	"legend" => $i18n["mail"]["legend"]["mail"],
	"table" => array(
		"tr" => 22,
		"th" => array(
			$i18n["mail"]["th"]["system_mail_address"],
			"",
			$i18n["mail"]["th"]["pop_host"],
			$i18n["mail"]["th"]["pop_port"],
			$i18n["mail"]["th"]["pop_authentication"],
			$i18n["mail"]["th"]["pop_username"],
			$i18n["mail"]["th"]["pop_password"],
			"",
			$i18n["mail"]["th"]["smtp_host"],
			$i18n["mail"]["th"]["smtp_port"],
			$i18n["mail"]["th"]["smtp_authentication"],
			$i18n["mail"]["th"]["smtp_username"],
			$i18n["mail"]["th"]["smtp_password"],
			"",
			$i18n["mail"]["th"]["debug"],
			$i18n["mail"]["th"]["charset"],
			"",
			$i18n["mail"]["th"]["sender"],
			$i18n["mail"]["th"]["reply_to"],
			$i18n["mail"]["th"]["reply_errors_to"],
			$i18n["mail"]["th"]["signature_text"],
			$i18n["mail"]["th"]["signature_html"]
		),
		"help" => array(
			$i18n["mail"]["help"]["system_mail_address"],
			"",
			$i18n["mail"]["help"]["pop_host"],
			$i18n["mail"]["help"]["pop_port"],
			$i18n["mail"]["help"]["authentication"],
			$i18n["mail"]["help"]["username"],
			$i18n["mail"]["help"]["password"],
			"",
			$i18n["mail"]["help"]["smtp_host"],
			$i18n["mail"]["help"]["smtp_port"],
			$i18n["mail"]["help"]["authentication"],
			$i18n["mail"]["help"]["username"],
			$i18n["mail"]["help"]["password"],
			"",
			$i18n["mail"]["help"]["debug"],
			$i18n["mail"]["help"]["charset"],
			"",
			$i18n["mail"]["help"]["sender"],
			$i18n["mail"]["help"]["reply_to"],
			$i18n["mail"]["help"]["reply_errors_to"],
			$i18n["mail"]["help"]["signature_text"],
			$i18n["mail"]["help"]["signature_html"]
		),
		"td" => array(
			array(
				"element" => "input",
				"id" => "mail_system_address",
				"name" => "mail_system_address",
				"type" => "email",
				"value" => "airs@" . $_SERVER["HTTP_HOST"],
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_system_address"],
				"size" => 36,
				"tabindex" => 5,
				"nest" => array()
			),
			array(
				"element" => "span"
			),
			array(
				"element" => "input",
				"id" => "mail_pop_host",
				"name" => "mail_pop_host",
				"type" => "text",
				"value" => validateEmail("airs@" . $_SERVER["HTTP_HOST"], true, true, "airs.dev@inran.it", "mail.inran.it", false),
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_pop_host"],
				"tabindex" => 6,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_pop_port",
				"name" => "mail_pop_port",
				"type" => "number",
				"size" => 5,
				"value" => 143,
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_pop_port"],
				"tabindex" => 7,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_pop_auth",
				"name" => "mail_pop_auth",
				"type" => "checkbox",
				"checked" => "checked",
				"tabindex" => 8,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_pop_username",
				"name" => "mail_pop_username",
				"type" => "text",
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_pop_username"],
				"tabindex" => 9,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_pop_password",
				"name" => "mail_pop_password",
				"error_txt" => $i18n["mail"]["error"]["mail_pop_password"],
				"type" => "password",
				"require" => "true",
				"tabindex" => 10,
				"nest" => array()
			),
			array(
				"element" => "span"
			),
			array(
				"element" => "input",
				"id" => "mail_smtp_host",
				"name" => "mail_smtp_host",
				"type" => "text",
				"value" => validateEmail("airs@" . $_SERVER["HTTP_HOST"], true, true, "airs.dev@inran.it", "mail.inran.it", false),
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_smtp_host"],
				"tabindex" => 11,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_smtp_port",
				"name" => "mail_smtp_port",
				"type" => "number",
				"size" => 5,
				"value" => 25,
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_smtp_port"],
				"tabindex" => 12,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_smtp_auth",
				"name" => "mail_smtp_auth",
				"type" => "checkbox",
				"require" => "true",
				"checked" => "checked",
				"tabindex" => 13,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_smtp_username",
				"name" => "mail_smtp_username",
				"type" => "text",
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_smtp_username"],
				"tabindex" => 14,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_smtp_password",
				"name" => "mail_smtp_password",
				"type" => "password",
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_smtp_password"],
				"tabindex" => 15,
				"nest" => array()
			),
			array(
				"element" => "span"
			),
			array(
				"element" => "input",
				"name" => "mail_debug",
				"type" => "checkbox",
				"tabindex" => 16,
				"nest" => array()
			),
			array(
				"element" => "select",
				"id" => "mail_charset",
				"name" => "mail_charset",
				"type" => "text",
				"value" => "utf-8",
				"tabindex" => 17,
				"nest" => array(
					"option" => $charset_arr
				)
			),
			array(
				"element" => "span"
			),
			array(
				"element" => "input",
				"id" => "mail_from",
				"name" => "mail_from",
				"type" => "text",
				"value" => "AIRS System <airs@" . $_SERVER["HTTP_HOST"] . ">",
				"require" => "true",
				"error_txt" => $i18n["mail"]["error"]["mail_from"],
				"size" => 36,
				"tabindex" => 18,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_reply_to",
				"name" => "mail_reply_to",
				"type" => "text",
				"value" => "AIRS Developer <airs.dev@" . $_SERVER["HTTP_HOST"] . ">",
				"size" => 36,
				"tabindex" => 19,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "mail_errors_to",
				"name" => "mail_errors_to",
				"type" => "text",
				"value" => "AIRS Developer <airs.dev@" . $_SERVER["HTTP_HOST"] . ">",
				"size" => 36,
				"tabindex" => 20,
				"nest" => array()
			),
			array(
				"element" => "textarea",
				"id" => "mail_signature_txt",
				"name" => "mail_signature_txt",
				"class" => "text",
				"text" => $i18n["mail"]["txt"]["signature_text"],
				"tabindex" => 21,
				"nest" => array()
			),
			array(
				"element" => "textarea",
				"id" => "mail_signature_html",
				"name" => "mail_signature_html",
				"class" => "html",
				"text" => $i18n["mail"]["txt"]["signature_html"],
				"tabindex" => 22,
				"nest" => array()
			)
		)
	)
);
$fieldset_arr[2] = array(
	"legend" => $i18n["database"]["legend"]["database"],
	"table" => array(
		"tr" => 3,
		"th" => array(
			$i18n["database"]["th"]["host"],
			$i18n["database"]["th"]["username"],
			$i18n["database"]["th"]["password"]
		),
		"help" => array(
			$i18n["database"]["help"]["host"],
			$i18n["database"]["help"]["username"],
			$i18n["database"]["help"]["password"]
		),
		"td" => array(
			array(
				"element" => "input",
				"id" => "db_host",
				"name" => "db_host",
				"type" => "text",
				"value" => "localhost",
				"require" => "true",
				"error_txt" => $i18n["database"]["help"]["db_host"],
				"tabindex" => 23,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "db_username",
				"name" => "db_username",
				"type" => "text",
				"require" => "true",
				"error_txt" => $i18n["database"]["help"]["db_username"],
				"tabindex" => 24,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "db_password",
				"name" => "db_password",
				"type" => "password",
				"require" => "true",
				"error_txt" => $i18n["database"]["help"]["db_password"],
				"tabindex" => 25,
				"nest" => array()
			)
		)
	)
);
$fieldset_arr[3] = array(
	"legend" => $i18n["language"]["legend"]["language"],
	"table" => array(
		"tr" => 3,
		"th" => array(
			$i18n["language"]["th"]["default_language_iso"],
			$i18n["language"]["th"]["default_language_text"],
			$i18n["language"]["th"]["default_language_default_charset"]
		),
		"help" => array(
			$i18n["language"]["help"]["default_language_iso"],
			$i18n["language"]["help"]["default_language_text"],
			$i18n["language"]["help"]["default_language_default_charset"]
		),
		"td" => array(
			array(
				"element" => "input",
				"id" => "lang_code",
				"name" => "lang_code",
				"type" => "text",
				"size" => 1,
				"maxlength" => 2,
				"value" => substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2),
				"require" => "true",
				"error_txt" => $i18n["language"]["error"]["lang_code"],
				"tabindex" => 26,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "lang_codename",
				"name" => "lang_codename",
				"type" => "text",
				"value" => "italiano",
				"require" => "true",
				"error_txt" => $$i18n["language"]["error"]["lang_codename"],
				"tabindex" => 27,
				"nest" => array()
			),
			array(
				"element" => "select",
				"id" => "lang_encoding",
				"name" => "lang_encoding",
				"type" => "text",
				"tabindex" => 28,
				"nest" => array(
					"option" => $charset_arr
				)
			)
		)
	)
);
$fieldset_arr[4] = array(
	"legend" => $i18n["company"]["legend"]["organization"],
	"table" => array(
		"tr" => 7,
		"th" => array(
			$i18n["company"]["th"]["name"],
			$i18n["company"]["th"]["address"],
			$i18n["company"]["th"]["contacts"],
			$i18n["company"]["th"]["url"],
			$i18n["company"]["th"]["uri"],
			$i18n["company"]["th"]["license_txt"],
			$i18n["company"]["th"]["license_html"]
		),
		"help" => array(
			$i18n["company"]["help"]["name"],
			$i18n["company"]["help"]["address"],
			$i18n["company"]["help"]["contacts"],
			$i18n["company"]["help"]["url"],
			$i18n["company"]["help"]["uri"],
			$i18n["company"]["help"]["license_txt"],
			$i18n["company"]["help"]["license_html"]
		),
		"td" => array(
			array(
				"element" => "input",
				"id" => "company_name",
				"name" => "company_name",
				"type" => "text",
				"require" => "true",
				"error_txt" => $i18n["company"]["error"]["company_name"],
				"tabindex" => 29,
				"nest" => array()
			),
			array(
				"element" => "textarea",
				"id" => "company_address",
				"name" => "company_address",
				"tabindex" => 30,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "company_contacts",
				"name" => "company_contacts",
				"type" => "text",
				"tabindex" => 31,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "company_url",
				"name" => "company_url",
				"type" => "url",
				"size" => 36,
				"tabindex" => 32,
				"nest" => array()
			),
			array(
				"element" => "input",
				"id" => "company_uri",
				"name" => "company_uri",
				"type" => "url",
				"size" => 36,
				"tabindex" => 33,
				"nest" => array()
			),
			array(
				"element" => "textarea",
				"id" => "company_license_txt",
				"name" => "company_license_txt",
				"class" => "text",
				"text" => $i18n["company"]["txt"]["license_txt"],
				"tabindex" => 34,
				"nest" => array()
			),
			array(
				"element" => "textarea",
				"id" => "company_license_html",
				"name" => "company_license_html",
				"class" => "html",
				"text" => $i18n["company"]["txt"]["license_html"],
				"tabindex" => 35,
				"nest" => array()
			)
		)
	)
);
$fieldset_arr[5] = array(
	"legend" =>"",
	"table" => array(
		"tr" => 1,
		"th" => array(""),
		"help" => array(""),
		"td" => array(
			array(
				"element" => "button",
				"id" => "form_submit",
				"name" => "form_submit",
				"type" => "submit",
				"text" => "Prosegui &rsaquo;",
				"tabindex" => 36,
				"nest" => array()
			)
		)
	)
);
?>