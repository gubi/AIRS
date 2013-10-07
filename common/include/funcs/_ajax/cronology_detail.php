<?php
/**
* Generates details of Wiki cronology difference
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
* @SLM_status	testing
*/

header("Content-type: text/plain");
#require_once("FirePHPCore/FirePHP.class.php");
#$firephp = FirePHP::getInstance(true);
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["the_id"]) && trim($_GET["the_id"]) !== ""){
	$pdo = db_connect("");
	require_once("../colorize.php");
	require_once("Console/Color.php");
	require_once("Text/Diff.php");
	require_once("Text/Diff/Renderer.php");
	require_once("Text/Diff/Renderer/inline.php");
	
	// Si riaggancia alla cronologia
	$chronology = $pdo->query("select * from airs_chronology where `id` = '" . addslashes($_GET["the_id"]) . "'");
	if ($chronology->rowCount() > 0){
		while ($dato_chronology = $chronology->fetch()){
			$current = $pdo->query("select * from airs_content where `name` = '" . addslashes($dato_chronology["name"]) . "'");
			
			if ($current->rowCount() > 0){
				while ($dato_current = $current->fetch()){
					$current_title = array($dato_current["title"]);
					$current_subtitle = array($dato_current["subtitle"]);
					$current_body = array($dato_current["body"]);
				}
			}
			$items = array(array(
							array($dato_chronology["title"]), 
							$current_title
							), array(
							array($dato_chronology["subtitle"]), 
							$current_subtitle
							), array(
							array($dato_chronology["body"]), 
							$current_body
						));
			foreach($items as $k => $item){
				$diff = new Text_Diff('auto', $item);
				$renderer = new Text_Diff_Renderer_inline(
					array(
						'ins_prefix' => '%g',
						'ins_suffix' => '%n',
						'del_prefix' => '%r',
						'del_suffix' => '%n',
					)
				);
				$output = htmlspecialchars_decode($renderer->render($diff));
				$original .= $output;
				preg_match_all("/%g(.*?)%n/", $output, $added_text);
				preg_match_all("/\%r(.*?)\%n/", $output, $removed_text);
				switch($k){
					case 0:	$type = "Titolo: ";		break;
					case 1:	$type = "Sottotitolo: ";	break;
					default:	$type = "";			break;
				}
				if (strlen($output) > 0){
					foreach($added_text[0] as $added_txt){
						$txt["added"][] = array($k,  $type . $added_txt);
					}
					foreach($removed_text[0] as $removed_txt){
						$txt["removed"][] = array($k, $type . $removed_txt);
					}
				}
			}
		}
	}
	if (count($txt) > 0){
		if (is_array($txt["removed"])){
			?>
			Testo rimosso:
			<ul>
				<?php
				foreach($txt["removed"] as $removed){
					print "<li>" . colorize(stripslashes($removed[1])) . "</li>";
				}
				?>
			</ul>
			<?php
		}
		if (is_array($txt["added"])){
			?>
			Testo aggiunto:
			<ul>
				<?php
				foreach($txt["added"] as $added){
					print "<li>" . colorize(stripslashes($added[1])) . "</li>";
				}
				?>
			</ul>
			<?php
		}
		?>
		<p style="color: #999;" id="content_body_<?php print $_GET["the_id"]; ?>"><?php print colorize(stripslashes(str_replace("\n", "<br />", $original))); ?></p>
		<?php
	} else {
		$cc = 0;
		foreach($items as $k => $item){
			$cc++;
			switch($k){
				case 0:	$type = "%kTitolo:%n ";		break;
				case 1:	$type = "%kSottotitolo:%n ";	break;
				default:	$type = "";				break;
			}
			if ($cc == 3){
				?>
				<p style="color: #999;" id="content_body_<?php print $_GET["the_id"]; ?>"><?php print colorize(stripslashes(str_replace("\n", "<br />", $type . $item[0][0]))); ?></p>
				<?php
			} else {
				?>
				<p style="color: #999;"><?php print colorize(stripslashes(str_replace("\n", "<br />", $type . $item[0][0]))); ?></p>
				<?php
			}
		}
	}
}
?>