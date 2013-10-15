<?php
/**
* Template for HTML mail
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
* @package	AIRS_Mailbox
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{SUBJECT}</title>
		<style type="text/css">
			#outlook a {padding: 0;}
			body {width: 100% !important;} .ReadMsgBody {width: 100%;} .ExternalClass {width: 100%;}
			body {-webkit-text-size-adjust: none;}
			
			body {margin: 0; padding: 0;}
			img {border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none;}
			table td {border-collapse: collapse;}
			#backgroundTable {height: 100% !important; margin: 0; padding: 0; width: 100% !important;}
			
			body { background-color: #fdfdfd; }
			#container { border-top: #eee 1px solid; border-right: 0; border-bottom: 0; border-left: 0; }
			
			.separator { width:  98%; height: 2px; }
			.separator_bg { width:  65px; padding-top: 2px; }
			.separator_bg > img { width:  65px; height: 500px; }
			
			h1, .h1 { color: #202020; display: block; font-family: Arial; font-size: 40px; font-weight: bold; line-height: 100%; margin-top: 2%; margin-right: 0; margin-bottom: 1%; margin-left: 0; text-align: left; }
			h2, .h2 { color: #404040; display: block; font-family: Arial; font-size: 18px; font-weight: bold; line-height: 100%; margin-top: 2%; margin-right: 0; margin-bottom: 1%; margin-left: 0; text-align: left; }
			h3, .h3 { color: #606060; display: block; font-family: Arial; font-size: 16px; font-weight: bold; line-height: 100%; margin-top: 2%; margin-right: 0; margin-bottom: 1%; margin-left: 0; text-align: left; }
			h4, .h4 { color: #808080; display: block; font-family: Arial; font-size: 14px; font-weight: bold; line-height: 100%; margin-top: 2%; margin-right: 0; margin-bottom: 1%; margin-left: 0; text-align: left; }
			
			#header { background-color: #FFFFFF; padding-top: 20px; padding-right: 10px; padding-bottom: 20px; padding-left: 10px; }
			#container, .bodyContent { background-color: #FDFDFD; }
			.bodyContent div { color: #505050; font-family: Arial; font-size: 14px; line-height: 150%; text-align: justify; padding-top: 10px; }
			.bodyContent div a: link, .bodyContent div a: visited, .bodyContent div a .yshortcuts { color: #336699; font-weight: normal; text-decoration: underline; }
			.bodyContent img { display: inline; height: auto; }
			code { margin-top: 5px; margin-bottom: 5px; padding: 5px; font-weight: bold; background-color: #fafafa; border: #eee 1px solid; }
			
			#copy { padding: 5px 10px; border-left: #a8c9cf 2px solid; border-right: #a8c9cf 2px solid; margin-right: 2.5px; text-align: justify; font-size: 10px; }
			#signature, .footerContent div { color: #707070; font-family: Arial; font-size: 11px; line-height: 125%; text-align: left; }
			
			.footerContent div a: link, .footerContent div a: visited, .footerContent div a .yshortcuts { color: #336699; font-weight: normal; text-decoration: underline; }
			
			.footerContent img { display: inline; }
			#signature { color: #999; }
		</style>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<center>
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
				<tr>
					<td align="center" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="90%">
							<tr>
								<td valign="top" align="right" class="separator_bg">
									<img src="{ABSOLUTE_URI}common/media/img/content_separator.png" />
								</td>
								<td valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="container">
										<tr>
											<td valign="center" id="header">
												<img src="{ABSOLUTE_URI}common/media/img/logo_airs_250.png" />
											</td>
										</tr>
										<tr>
											<td align="center" valign="top">
												<center><img class="separator" src="{ABSOLUTE_URI}common/media/img/hr.png" /></center>
												<table border="0" cellpadding="10" cellspacing="0" id="templateBody" width="100%">
													<tr>
														<td valign="top" class="bodyContent">
															<div>
																{MESSAGE}.<br />
																<br />
																Namast&egrave; :)
															</div>
														</td>
													</tr>
													<tr>
														<td align="center" valign="top">
															<center><img class="separator" src="{ABSOLUTE_URI}common/media/img/menu_hr.png" /></center>
															<table border="0" cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td valign="top" class="footerContent">
																		<table border="0" cellpadding="10" cellspacing="0" width="100%">
																			<tr>
																				<td colspan="2" valign="middle" id="signature">
																					{SIGNATURE}
																				</td>
																			</tr>
																			<tr><td>&nbsp;</td></tr>
																			<tr>
																				<td valign="top" width="350">
																					<div id="copy">
																						<em>{COPYRIGHT_DATA}</em>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<td valign="top" align="right" class="separator_bg">
									<img src="{ABSOLUTE_URI}common/media/img/content_separator_back.png" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>