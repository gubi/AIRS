<?php
require_once("_fpdf.php");
require_once("htmlparser.inc");
// Per correggere eventuali output che generano errore
ob_end_clean();
function pdf($title, $body, $filename){
	$title = utf8_decode($title);
	if (!isset($filename)){
		$filename = $GLOBALS["page"];
	}
	
	class PDF extends FPDF{
		var $B = 0;
		var $I = 0;
		var $U = 0;
		var $HREF = "";
		var $ALIGN = "";
		function PDF($orientation = "P", $unit = "mm", $size = "A4") {
			$this->FPDF($orientation, $unit, $size);
			
			$this->B = 0;
			$this->I = 0;
			$this->U = 0;
			$this->HREF = "";
		}

		function WriteHTML2($html) {
			//HTML parser
			$html=str_replace("\n", " ", $html);
			$a=preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
			foreach($a as $i=>$e) {
				if($i%2 == 0) {
					//Text
					if($this->HREF)
						$this->PutLink($this->HREF, $e);
					else
						$this->Write(5, $e);
				} else {
					//Tag
					if($e[0] == "/") {
						$this->CloseTag(strtoupper(substr($e, 1)));
					} else {
						//Extract attributes
						$a2=explode(" ", $e);
						$tag=strtoupper(array_shift($a2));
						$attr=array();
						foreach($a2 as $v) {
							if(preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3)) {
								$attr[strtoupper($a3[1])]=$a3[2];
							}
						}
						$this->OpenTag($tag, $attr);
					}
				}
			}
		}
		function OpenTag($tag, $prop){
			//Opening tag
			if($tag == "H1" || $tag == "H2" || $tag == "H3" || $tag == "H4" || $tag == "H5" || $tag == "H6"){
				$this->Ln(10);
				$this->SetStyle("B", true);
			}
			//Opening tag
			if($tag == "B" || $tag == "I" || $tag == "U"){
				$this->SetStyle($tag, true);
			}
			if($tag == "A"){
				$this->HREF=$prop["HREF"];
			}
			if($tag == "BR"){
				$this->Ln(10);
			}
			if($tag == "P"){
				$this->ALIGN = $prop["ALIGN"];
			}
			if($tag == "HR"){
				if(!empty($prop["WIDTH"])) {
					$Width = $prop["WIDTH"];
				} else {
					$Width = $this->w - $this->lMargin-$this->rMargin;
				}
				$this->Ln(15);
				$x = $this->GetX();
				$y = $this->GetY();
				$this->SetLineWidth(0.2);
				$this->Line($x, $y, $x+$Width, $y);
				$this->SetLineWidth(0.2);
				$this->Ln(5);
			}
		}
		function CloseTag($tag){
			//Closing tag
			if($tag == "H1" || $tag == "H2" || $tag == "H3" || $tag == "H4" || $tag == "H5" || $tag == "H6"){
				$this->SetStyle("B", false);
				$this->Ln(7.5);
			}
			if($tag == "B" || $tag == "I" || $tag == "U"){
				$this->SetStyle($tag, false);
			}
			if($tag == "A"){
				$this->HREF="";
			}
			if($tag == "P"){
				$this->ALIGN="";
			}
		}
		function SetStyle($tag, $enable) {
			//Modify style and select corresponding font
			$this->$tag+=($enable ? 1 : -1);
			$style="";
			foreach(array("B", "I", "U") as $s) {
				if($this->$s>0) {
					$style.=$s;
				}
			}
			$this->SetFont("", $style);
		}
		function PutLink($URL, $txt) {
			//Put a hyperlink
			$this->SetTextColor(0, 0, 255);
			$this->SetStyle("U", true);
			$this->Write(5, $txt, $URL);
			$this->SetStyle("U", false);
			$this->SetTextColor(0);
		}
		function WriteTable($data, $w) {
			$this->SetLineWidth(.1);
			$this->SetFont("Eco font", "", 8);
			$row = 0;
			$header_txt = $data[0];
			foreach($data as $column) {
				$nb = 0;
				$row++;
				for($i = 0; $i<count($column); $i++) {
					$nb = max($nb, $this->NbLines($w[$i], trim($column[$i])));
				}
				$h = 5*$nb;
				$hh[] = $h;
				$this->CheckPageBreak($h, $header_txt, $w);
				if ($row == 1){
					// Table header
					$this->SetFont("Eco font", "B", 7);
					$this->SetFillColor(100);
					$this->SetDrawColor(120);
					$this->SetTextColor(255);
					
				} else {
					$this->SetFont("Eco font", "", 7.5);
					if (($row % 2) == 0) {
						// Righe pari
						$this->SetFillColor(255);
						$this->SetDrawColor(180);
						$this->SetTextColor(75);
					} else {
						$this->SetFillColor(230);
						$this->SetDrawColor(180);
						$this->SetTextColor(75);
					}
				}
				// Colonne
				for($i = 0; $i<count($column); $i++) {
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Rect($x, $y, $w[$i], $h);
					if (strlen(trim($column[$i])) < 13){
						$hhh = max($hh);
					} else {
						$hhh = 5;
					}
					$this->MultiCell($w[$i], $hhh, trim($column[$i]), 1, "L", 1);
					//Put the position to the right of the cell
					$this->SetXY($x+$w[$i], $y);					
				}
				$this->Ln($h);
			}
		}
		function NbLines($w, $txt) {
			//Computes the number of lines a MultiCell of width w will take
			$cw = &$this->CurrentFont["cw"];
			if($w == 0) {
				$w=$this->w-$this->rMargin-$this->x;
			}
			$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
			$s = str_replace("\r", "", $txt);
			$nb = strlen($s);
			if($nb>0 && $s[$nb-1] == "\n") {
				$nb--;
			}
			$sep = -1;
			$i = 0;
			$j = 0;
			$l = 0;
			$nl = 1;
			while($i<$nb) {
				$c = $s[$i];
				if($c == "\n") {
					$i++;
					$sep = -1;
					$j = $i;
					$l = 0;
					$nl++;
					continue;
				}
				if($c == " ") {
					$sep = $i;
				}
				$l += $cw[$c];
				if($l>$wmax) {
					if($sep == -1) {
						if($i == $j) {
							$i++;
						}
					} else {
						$i = $sep+1;
					}
					$sep = -1;
					$j = $i;
					$l = 0;
					$nl++;
				} else {
					$i++;
				}
			}
			return $nl;
		}
		function CheckPageBreak($h, $header_txt, $w) {
			//If the height h would cause an overflow, add a new page immediately
			if($this->GetY()+$h>$this->PageBreakTrigger) {
				$this->AddPage($this->CurOrientation);
				$this->SetFillColor(100);
				$this->SetDrawColor(120);
				$this->SetTextColor(255);
				foreach($header_txt as $k => $txt){
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Rect($x, $y, $w[$k], $h);
					$this->MultiCell($w[$k], 5, trim($txt), 1, "L", 1);
					//Put the position to the right of the cell
					$this->SetXY($x+$w[$k], $y);					
				}
				$this->Ln(5);
			}
		}
		function ReplaceHTML($html) {
			$html = str_replace( "<li>", "\n<br> - " , $html );
			$html = str_replace( "<LI>", "\n - " , $html );
			$html = str_replace( "</ul>", "\n\n" , $html );
			$html = str_replace( "<strong>", "<b>" , $html );
			$html = str_replace( "</strong>", "</b>" , $html );
			$html = str_replace( "&#160;", "\n" , $html );
			$html = str_replace( "&nbsp;", " " , $html );
			$html = str_replace( "&quot;", "\"" , $html ); 
			$html = str_replace( "&#39;", "'" , $html );
			return $html;
		}
		function ParseTable($Table) {
			$_var="";
			$htmlText = $Table;
			$parser = new HtmlParser($htmlText);
			while ($parser->parse()) {
				if(strtolower($parser->iNodeName) == "table") {
					if($parser->iNodeType == NODE_TYPE_ENDELEMENT) {
						$_var .="/::";
					} else {
						$_var .="::";
					}
				}
				if(strtolower($parser->iNodeName) == "tr") {
					if($parser->iNodeType == NODE_TYPE_ENDELEMENT) {
						$_var .="!-:"; //opening row
					} else {
						$_var .=":-!"; //closing row
					}
				}
				if(strtolower($parser->iNodeName) == "td" && $parser->iNodeType == NODE_TYPE_ENDELEMENT) {
					$_var .="#, #";
				}
				if ($parser->iNodeName == "Text" && isset($parser->iNodeValue)) {
					$_var .= $parser->iNodeValue;
				}
			}
			$elems = explode(":-!", str_replace("::", "", str_replace("!-:", "", $_var))); //opening row
			foreach($elems as $key=>$value) {
				if(trim($value)!="") {
					$elems2 = explode("#, #", $value);
					array_pop($elems2);
					$data[] = $elems2;
				}
			}
			return $data;
		}
		function WriteHTML($html) {
			$html = $this->ReplaceHTML($html);
			//Search for a table
			$start = strpos(strtolower($html), "<table");
			$end = strpos(strtolower($html), "</table");
			if($start !== false && $end !== false) {
				$this->WriteHTML2(substr($html, 0, $start)."<BR>");
				$tableVar = substr($html, $start, $end-$start);
				$tableData = $this->ParseTable($tableVar);
				for($i=1; $i<=count($tableData[0]); $i++) {
					if($this->CurOrientation == "L") {
						$w[] = abs(120/(count($tableData[0])-1))+24;
					} else {
						$w[] = abs(96/(count($tableData[0])-1))+5;
					}
				}
				$this->WriteTable($tableData, $w);
				$this->WriteHTML2(substr($html, $end+8, strlen($html)-1)."<BR>");
			} else {
				$this->WriteHTML2($html);
			}
		}
		function DisplayPreferences($preferences) {
			$this->DisplayPreferences.=$preferences;
		}
		function _putcatalog() {
			parent::_putcatalog();
			if(is_int(strpos($this->DisplayPreferences, "FullScreen"))){
				$this->_out("/PageMode /FullScreen");
			}
			if($this->DisplayPreferences) {
				$this->_out("/ViewerPreferences<<");
				if(is_int(strpos($this->DisplayPreferences, "HideMenubar"))){
					$this->_out("/HideMenubar true");
				}
				if(is_int(strpos($this->DisplayPreferences, "HideToolbar"))){
					$this->_out("/HideToolbar true");
				}
				if(is_int(strpos($this->DisplayPreferences, "HideWindowUI"))){
					$this->_out("/HideWindowUI true");
				}
				if(is_int(strpos($this->DisplayPreferences, "DisplayDocTitle"))){
					$this->_out("/DisplayDocTitle true");
				}
				if(is_int(strpos($this->DisplayPreferences, "CenterWindow"))){
					$this->_out("/CenterWindow true");
				}
				if(is_int(strpos($this->DisplayPreferences, "FitWindow"))){
					$this->_out("/FitWindow true");
				}
				$this->_out(">>");
			}
		}
		function ImageEps($file, $x, $y, $w = 0, $h = 0, $link= "", $useBoundingBox = true){
			require "_fpdf.eps.php";
		}
		
		//Page header
		function Header(){
			$this->SetTextColor(108);
			$this->SetX(-80);
			//Logo
			$this->ImageEps($_SERVER["DOCUMENT_ROOT"] . "/common/media/ai/logo_airs.ai", $this->GetX(), 7, 72);
			//$this->Image($_SERVER["DOCUMENT_ROOT"] . "/common/media/img/logo_300.jpg", $this->GetX(), 7, 72);
			$this->Ln(36);
		}
		//Page footer
		function Footer(){
			$this->SetY(-15);
			$this->SetX(-28);
			$this->SetDrawColor(207);
			$this->Line(36, $this->GetY(), $this->GetX(), $this->GetY());
			
			//Page number
			$this->SetTextColor(63);
			$this->SetFont("Eco font", "I", 8);
			$this->SetX(36);
			$this->WriteHTML('<a href="' . str_replace("Pdf:", "", $_POST["page_uri"]) . '"><i>' . utf8_decode(str_replace("Pdf:", "", $_POST["page_uri"])) . '</i></a>');
			$this->SetY(-17);
			$this->SetX(-51);
			$this->Cell(28, 10, $this->PageNo() . " di {nb}", 0, 0, "R");
		}
	}
	//Instanciation of inherited class
	$pdf = new PDF("L");
	$pdf->AddFont("Eco font", "", "spranq_eco_sans_regular.php");
	$pdf->AddFont("Eco font", "B", "spranq_eco_sans_regular.php");
	$pdf->AddFont("Eco font", "I", "spranq_eco_sans_regular.php");
	$pdf->AliasNbPages();
		if (isset($_POST["zoom"])){
			if ($_POST["zoom"] == "custom"){
				$_POST["zoom"] = (int)$_POST["custom_zoom"];
			}
			
			if (isset($_POST["layout"])){
				$pdf->SetDisplayMode($_POST["zoom"], $_POST["layout"]);
			} else {
				$pdf->SetDisplayMode($_POST["zoom"]);
			}
		}
		if ($_POST["FullScreen"] == "on"){
			$preferences[] = "FullScreen";
		}
		if ($_POST["HideMenubar"] == "on"){
			$preferences[] = "HideMenubar";
		}
		if ($_POST["HideToolbar"] == "on"){
			$preferences[] = "HideToolbar";
		}
		if ($_POST["HideWindowUI"] == "on"){
			$preferences[] = "HideWindowUI";
		}
		if ($_POST["CenterWindow"] == "on"){
			$preferences[] = "CenterWindow";
		}
		if ($_POST["FitWindow"] == "on"){
			$preferences[] = "FitWindow";
		}
		if (count($preferences) > 0){
			$le_preferenze = implode(", ", $preferences);
			$pdf->DisplayPreferences($le_preferenze);
		}
			
	$pdf->AddPage();
		$pdf->SetAuthor("INRAN");
		$pdf->SetCreator("AIRS - Automatic Intelligent Research System");
		$pdf->SetTitle("INRAN - Istituto Nazionale di Ricerca per gli Alimenti e la Nutrizione");
		$pdf->SetSubject($title);
	$pdf->SetFont("Eco font", "", 9);
	$pdf->SetLeftMargin(36);
	$pdf->SetRightMargin(27);
	if ($pdf->PageNo() == 1 && strlen($title) > 0){
		$pdf->SetTextColor(9);
		$pdf->SetFont("Eco font", "B", 10.5);
		$pdf->Cell(0, 9, $title, 0, 1, "L");
		$pdf->Ln(10);
	}
	$pdf->SetTextColor(54);
	$pdf->SetFont("Eco font", "", 10.5);
	if(strlen($body) == 0){
		$pdf->SetTextColor(72);
		$pdf->SetFont("Eco font", "I", 9);
		$pdf->WriteHTML("Nessun contenuto documentabile");
	} else {
		$pdf->WriteHTML($body);
	}
	
	if (isset($_POST["save"])){
		$pdf->Output($filename . ".pdf", "D");
	} else {
		$pdf->Output($filename . ".pdf", "I");
	}
}
?>