<?php
/**
* Generates home page
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

$use_sts = true;
if($use_sts && !isset($_SERVER["HTTPS"]) && $config["system"]["need_ssl"] == "true"){
	if(isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== ""){
		header("Location: https://" . $_SERVER["SERVER_NAME"]);
	} else {
		require_once("common/tpl/__505.tpl");
	}
} else {
	/* Home page data */
	function factor($f1, $f2){
		return rand($f1, $f2);
	}
	$A = array($i18n["home_claim_0"], $i18n["home_claim_1"], $i18n["home_claim_2"], $i18n["home_claim_3"]);
	$I = array("recent_changes_256_ccc.png", "document_text_information_256_ccc.png");
	array_rand($A);
	$Aj = implode("','", $A); 
	$Ij = implode("','", $I); 

	$f1= factor(0,1);
	$f2 = factor(2,3);
	$ff = factor($f1, $f2);
	//print $A[$f1] . $ff . "~" . $f1 . ":" . $f2;
	$AA = $A[$f1];
	$BB = $A[$f2];
	$II = $I[array_search($AA, $A)];
	
	if(isset($_COOKIE["iac"]) && $GLOBALS["user_level"] == 3) {
		/* Welcome data */
		$connection_count = $GLOBALS["connection_count"];
		if($connection_count == 1){
			$greeting = $i18n["ui_welcome"];
		} else {
			$greeting = $i18n["ui_welcome_back"];
		}
		$name = $GLOBALS["name"];
		$start = strtolower(substr($GLOBALS["name"], 0, 1));
		$table = "__decas_" . $start;
		$pdo = db_connect("decas");
		$check = $pdo->query("select * from `$table` where `word` = '" . html_entity_decode(trim($name)) . "'");
		if($check->rowCount() > 0){
			while($dato_name = $check->fetch()){
				if($dato_name["sesso"] == "f"){
					$greeting .= "a";
				} else if($dato_name["sesso"] == "m"){
					$greeting .= "o";
				} else {
					if(substr(trim($name), -1, -1) == "a"){
						$greeting .= "a";
					} else {
						$greeting .= "o";
					}
				}
			}
		} else {
			// inserire una funzione per l'autoapprendimento
		}
		/* Box data */
		
		$user = ucwords($decrypted_user);
		$content_title = $greeting . " " . $name . "!";
		$content_subtitle = str_replace("{1}", "<span style=\"font-family: monospace;\">" . $connection_count . "&ordm;</span>", $i18n["ui_access_count"]);
		
		$there_is_string = $i18n["there_is_string"];
		$theres_string = $i18n["theres_string"];
		$message_string = $i18n["message_string"];
		$messages_string = $i18n["messages_string"];
		$total_in_your = $i18n["total_message_in_your_mailbox"];
		$totals_in_your = $i18n["total_messages_in_your_mailbox"];
		$inbox = $i18n["mailbox_inbox"];
		$go_to_the = $i18n["go_to_the"];
		$mailbox_inbox = $i18n["mailbox_inbox"];
		$user_string = $i18n["user_string"];
		$add = $i18n["menu_add"];
		$your_birthday_date = $i18n["your_birthday_date_string"];
		$to_calculate = $i18n["to_calculate_string"];
		$article_il = $i18n["article_string"][0];
		$article_la = $i18n["article_string"][2];
		$birth_day_count = $i18n["birth_day_count_string"];
		$research_string = $i18n["research_string"];
		$personal_settings = $i18n["personal_settings"];
		$page_name_add_research = $i18n["page_name_add_research"];
		$page_title_add_research = $i18n["page_title_add_research"];
		$page_name_research_results = $i18n["page_name_research_results"];
		$page_title_research_results = $i18n["page_title_research_results"];
		$page_name_add_feed = $i18n["page_name_add_feed"];
		$page_title_add_feed = $i18n["page_title_add_feed"];
		$page_title_feed_list = $i18n["page_title_feed_list"];
		$page_title_news_list = $i18n["page_title_news_list"];
		
		$content_body = <<<HOME
		<script src="{ABSOLUTE_PATH}common/js/graphs/Highstock-1.2.5/js/highstock.js"></script>
		<script src="{ABSOLUTE_PATH}common/js/graphs/Highstock-1.2.5/js/highcharts-more.js"></script>
		<script src="{ABSOLUTE_PATH}common/js/graphs/Highstock-1.2.5/js/modules/exporting.js"></script>
		<script type="text/javascript">
		function check_mailbox(){
			var theres_string = "",
			messages_string = "",
			total_in_your_mailbox = "";
			$.get("{ABSOLUTE_PATH}common/include/funcs/_ajax/Mailbox/check.php", {user: "$decrypted_user"}, function(mail_data){
				$.each(mail_data.rows, function(item, data){
					if(mail_data.total == 1){
						theres_string = "$there_is_string";
						messages_string = "$message_string";
						total_in_your_mailbox = "$total_in_your $inbox";
					} else {
						theres_string = "$theres_string";
						messages_string = "$messages_string";
						total_in_your_mailbox = "$totals_in_your $inbox";
					}
					$("#last_mail").html("<p>" + theres_string + " <b>" + mail_data.total + " " + messages_string + "</b> " + total_in_your_mailbox + ".<br /><br />$go_to_the$article_la <a href=\"./Mailbox\">$mailbox_inbox &rsaquo;</a></p>");
				});
			}, "json");
		}
		$(document).ready(function(){
			$.get("common/include/funcs/_ajax/get_day_age.php", {user: "$decrypted_user"}, function(day_data){
				if(day_data == "no date"){
					$("#complegiorno").html('<a href="./$user_string/$personal_settings">$add $article_la $your_birthday_date</a><br />$to_calculate $article_il &laquo;<i>$birth_day_count</i>&raquo;');
				} else {
					$("#complegiorno").html(day_data);
					$("#bioritmi").attr("href", "{ABSOLUTE_PATH}common/include/funcs/_ajax/get_biorhythm.php?user=$decrypted_user").attr("title", "Visualizzazione dei bioritmi odierni").html('<img src="{ABSOLUTE_PATH}common/include/funcs/_ajax/get_biorhythm.php?user=$decrypted_user&thumb=true" />');
				}
				$("#day_age").fadeIn(450);
			});
			setInterval(function(){
				check_mailbox();
			}, 10000);
			check_mailbox();
			$("#last_mail").fadeIn(1000);
			
			Highcharts.setOptions({
				colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
				column: {
					colorByPoint: true
				}
			});
			$.getJSON('{ABSOLUTE_PATH}common/include/funcs/_ajax/manage_users/jsonp.php?user=$decrypted_user&callback=?', function(data) {
				histogram = new Highcharts.StockChart({
					chart: {
						renderTo: 'connection_chart',
						type: 'columnrange',
						backgroundColor:'rgba(255, 255, 255, 0.1)',
						marginTop: 75,
						marginLeft: 20
					},
					rangeSelector: {
						buttonSpacing: 10,
						buttonTheme: {
							fill: 'none',
							stroke: 'none',
							width: null,
							style: {
								color: '#039',
								fontWeight: 'bold'
							},
							states: {
								hover: {
									fill: 'white'
								},
								select: {
									style: {
										color: 'white'
									}
								}
							}
						},
						labelStyle: {
							color: 'silver',
							fontWeight: 'bold'
						},
						buttons: [{
							type: 'month',
							count: 1,
							text: '1 mese'
						}, {
							type: 'month',
							count: 3,
							text: '3 mesi'
						}, {
							type: 'month',
							count: 6,
							text: '6 mesi'
						}, {
							type: 'ytd',
							text: 'Anno corrente'
						}, {
							type: 'year',
							count: 1,
							text: '1 anno'
						}, {
							type: 'all',
							text: 'Tutto'
						}],
						inputEnabled: false,
						selected: 4
					},
					title: {
						style: {
							color: '#666',
							font: 'bold 16px Ubuntu, Arial'
						},
						text: 'Frequenza delle connessioni al Sistema'
					},
					yAxis: {
						startOnTick: false,
						labels: {
							x: -20
						}
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.y}</b>',
						valueDecimals: 2
					},
					scrollbar : {
						enabled : false
					},
					series: [{
						cursor: 'pointer',
						name: 'Ore',
						data: data
					}]
				});
			});
		});
		</script>
		<div id="day_age">
			<div id="complegiorno"></div>
		</div>
		<table cellspacing="0" cellpadding="0" style="width: 100%;">
			<tr>
				<td valign="top" style="width: 500px;">
					<div id="last_mail"></div>
					<hr />
				</td>
				<!--
				<td class="separator"></td>
				-->
				<td class="title" valign="top" rowspan="2">
					<table cellpadding="0" cellspacing="0" style="width: 100%;">
						<tr>
							<td></td>
							<td class="separator"></td>
							<td id="rapid_links">
								<a id="bioritmi" rel="zoombox 1220 468" href="" title=""></a>
								<br />
								<br />
								<br />
								<h3>Collegamenti rapidi</h3>
								<ul>
									<li><a href="./AIR">AIR</a></li>
									<li style="list-style-image: none; padding: 0;">
										<ul>
											<li><a href="./AIR/$research_string/$page_name_add_research">$page_title_add_research</a></li>
											<li><a href="./AIR/$page_name_research_results">$page_title_research_results</a></li>
										</ul>
									</li>
									<li class="container"><a href="./EditoRSS">EditoRSS</a></li>
									<li style="list-style-image: none; padding: 0;">
										<ul>
											<li><a href="./EditoRSS/Feeds/$page_name_add_feed">$page_title_add_feed</a></li>
											<li><a href="./EditoRSS/Feeds">$page_title_feed_list</a></li>
											<li><a href="./EditoRSS/News">$page_title_news_list</a></li>
										</ul>
									</li>
								</ul>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td id="connection_chart" style="width: 75%;"><td>
			</tr>
		</table>
HOME;
		require_once("common/include/conf/replacing_object_data.php");
	} else {
		$content_body = <<<HOME
		<script type="text/javascript">
			function rotate(){
				var A = ['$Aj'],
				I = ['$Ij'],
				f1 = Math.floor(Math.random()*2),
				f2 = 2+ Math.floor(Math.random()*2),
				AA = A[f1],
				BB = A[f2],
				II = I[f1];
				
				$("#a").fadeOut(900, function(){ $(this).text(AA + " x " + BB).fadeIn(2100); });
				$("#aa").delay(300).fadeOut(900, function(){ $(this).find("img").attr("src", "common/media/img/" + II); $("#aa").fadeIn(2100); });
			}
			$(document).ready(function() {
				setTimeout(function(){
					setInterval(function(){
						rotate();
					}, 18000);
				}, 18000);
				
				$("#a").fadeIn(2100);
				$("#aa").delay(300).fadeIn(2100);
			});
		</script>
		<table cellspacing="5" cellpadding="5" style="font-family: 'Enriqueta', serif; margin-top: -25px; height: 365px; width: 100%;">
			<tr>
				<td valign="top" style="text-align: center; font-size: 33px; padding-bottom: 40px; width: 72%;"><span id="a" style="display: none;">$AA x $BB.</span></td>
			</tr>
			<tr>
				<td style="text-align: center; width: 72%;"><span id="aa" id="a" style="display: none;"><img src="common/media/img/$II" /></span></td>
			</tr>
		</table>
HOME;
		require_once("common/include/conf/replacing_object_data.php");
	}
}
?>