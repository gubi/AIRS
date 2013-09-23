<?php
/**
* Scan the directory and return first, last or list all contents in different methods (plain text, html or json)
*
* PARAMETERS:
* - functions:
*	- get_older($file_or_dir ["dir", "file", "dir_first (default)", "file_first"], $order ["ASC (default)", "DESC"])
*	- get_latest($file_or_dir ["dir", "file", "dir_first (default)", "file_first"], $order ["ASC (default)", "DESC"])
*	- list_all($file_or_dir ["dir", "file", "dir_first (default)", "file_first"], $order ["ASC (default)", "DESC"], $method ["text", "json", "html (default)"])
* Note: if you don't set the path to scan, default is current dir.
* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
* Example of use:
* $scan_dir = new read_dir();
* $scan_dir->set_dir("./");
* print $scan_dir->list_all();
*
* @category	SystemScript
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @license	http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @link		http://airs.inran.it/
* 
* @SLM_is_core	true
* @SLM_status	testing
 */
//
//
// Developed by Alessandro Gubitosi 
// Enjoy happyness... :)
//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// PARAMETERS:
//
// - functions:
//	- get_older($file_or_dir ["dir", "file", "dir_first (default)", "file_first"], $order ["ASC (default)", "DESC"])
//	- get_latest($file_or_dir ["dir", "file", "dir_first (default)", "file_first"], $order ["ASC (default)", "DESC"])
//	- list_all($file_or_dir ["dir", "file", "dir_first (default)", "file_first"], $order ["ASC (default)", "DESC"], $method ["text", "json", "html (default)"])
//
// Note: if you don't set the dir to scan default is current
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Example of use:
//
// $scan_dir = new read_dir();
// $scan_dir->set_dir("./");
// print $scan_dir->list_all();



class read_dir{
	private $files;
	
	public function set_dir($directory){
		$this->dir = $directory;
	}
	
	function scan_dir($the_dir = "", $file_or_dir = "", $order = ""){
		$the_dir .= "/";
		$the_dir = str_replace("//", "/", $the_dir);
		
		if ($file_or_dir == ""){ $order = "dir_first"; }
		if ($order == ""){ $order = "DESC"; }
		$this->files = array();
		
		chdir("..");
		$dir = @opendir($the_dir); // Open the dir
		if (opendir($the_dir)){ // If it can open
			while (false != ($file = readdir($dir))){
				if ($file !== "." && $file !== ".." && $file !== "class.read_dir.php"){ // If file names are not upper dir and not this file
					if (is_dir($the_dir . "/" . $file)){
						$this->files["dir"][] = $file;
					} else {
						$this->files["file"][] = $file;
					}
				}
			}
			closedir($dir); // Close the directory
		}
		if (strtoupper($order) == "DESC"){
			if ($this->files["dir"]){	rsort($this->files["dir"]);	}
			if ($this->files["file"]){	rsort($this->files["file"]);	}
		} else {
			if ($this->files["dir"]){	sort($this->files["dir"]);	}
			if ($this->files["file"]){	sort($this->files["file"]);	}
		}
		switch($file_or_dir){
			case "dir":
				return $this->files["dir"];
				break;
			case "file":
				return $this->files["file"];
				break;
			case "file_first":
				if ($this->files["file"]){
					foreach($this->files["file"] as $file){
						$this->files_dir[$dir] = "dir";
					}
				}
				if ($this->files["file"]){
					foreach($this->files["file"] as $file){
						$this->files_dir[$file] = "file";
					}
				}
				return $this->files_dir;
				break;
			case "dir_first":
			default:
				if ($this->files["dir"]){
					foreach($this->files["dir"] as $dir){
						$this->files_dir[$dir] = "dir";
					}
				}
				if ($this->files["file"]){
					foreach($this->files["file"] as $file){
						$this->files_dir[$file] = "file";
					}
				}
				return $this->files_dir;
				break;
		}
	}
	
	public function get_older($file_or_dir = "file", $order = ""){
		if ($this->dir == ""){ $this->dir = "./"; }
		$this->files = $this->scan_dir($this->dir, $file_or_dir, $order);
		if ($this->files){
			foreach($this->files as $file){
				$this->file[] = $this->dir . "/" . $file;
			}
			array_multisort(array_map("filemtime", $this->file), SORT_NUMERIC, SORT_ASC, $this->files); // Sort files by modified time, older
			print $this->files[0]; // print last file
		} else {
			print "no item";
		}
	}
	public function get_latest($file_or_dir = "file", $order = ""){
		if ($this->dir == ""){ $this->dir = "./"; }
		$this->files = $this->scan_dir($this->dir, $file_or_dir, $order);
		if ($this->files){
			foreach($this->files as $file){
				$this->file[] = $this->dir . "/" . $file;
			}
			array_multisort(array_map( 'filemtime', $this->file), SORT_NUMERIC, SORT_DESC, $this->files); // Sort files by modified time, latest to earliest 
			print $this->files[0]; // print last file
		} else {
			print "no item";
		}
	}
	
	public function list_all($file_or_dir = "", $order = "", $method = ""){
		if ($this->dir == ""){ $this->dir = "./"; }
		$this->files = $this->scan_dir($this->dir, $file_or_dir, $order);
		
			if ($method == "" || $method == "html"){
				$this->listed = "<ul class=\"tree filetree\">";
			}
		
		foreach ($this->files as $file => $file_type){
			switch ($method){
				case "text";
					header("Content-type: text/plain");
					return $file . "\n";
					break;
				case "html";
				default:
					if ($file_type == "dir"){
						$this->listed .= "<li class=\"hasChildren\"><span class=\"" . $file_type . "\">" . $file . "</span></li>";
					} else {
						$this->listed .= "<li><span class=\"" . $file_type . "\">" . $file . "</span></li>";
					}
					break;
			}
		}
		switch ($method){
			case "json";
				header("Content-type: text/plain");
				return json_encode($this->files);
				break;
			case "html";
			default:
				return $this->listed . "</ul>";
				break;
		}
	}
}
/*
$scan_dir = new read_dir();
$scan_dir->set_dir("./");
print $scan_dir->list_all();
*/
?>