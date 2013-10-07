<?php
/**
* Generates right panel buttons
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
?>
<ul id="panel_btn" class="<?php print $panel_btn_class; ?>">
	<?php
	if ($show_right_panel == 1){ // Menu orizzontale
		?>
		<li>
			<a href="javascript: void(0);" id="toggle_btn" class="close" title="Chiudi il pannello laterale" onclick="show_hide_lateral_panel()" alt="»"></a>
		</li>
		<?php
	} else {
		?>
		<li>
			<a href="javascript: void(0);" id="toggle_btn" class="open" title="Apri il pannello laterale" onclick="show_hide_lateral_panel()" alt="«"></a>
		</li>
		<?php
		if ($show_right_panel_toc == 1 || $show_right_panel_tocs == 1){
			?>
			<li class="sep"></li>
			<?php
		}
		if ($show_right_panel_toc == 1){
			?>
			<li><a id="toc_btn" href="javascript: void(0);" class="toc" title="Indice"></a></li>
			<?php
		}
		if ($show_right_panel_tocs == 1){
			?>
			<li><a id="tocs_btn" href="javascript: void(0);" class="tocs" title="Sottopagine"></a></li>
			<?php
		}
	}
	?>
	<li class="sep"></li>
	<li><a href="/<?php print str_replace($GLOBALS["page"], "Pdf:" . $GLOBALS["page"], $GLOBALS["current_pos"]); ?>" class="pdf" title="Visualizza la pagina in formato pdf" alt="pdf"></a></li>
	<li><a href="javascript: void(0);" onclick="javascript:window.print()" class="print" title="Stampa la pagina" alt="stampa"></a></li>
</ul>