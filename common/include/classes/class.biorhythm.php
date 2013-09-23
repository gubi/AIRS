<?php
/**
* Generates a biorythm graph
* 
* PHP versions 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt. If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	Plugin
* @package	Biorhythm
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com> (taken from script by Till Gerken - http://www.zend.com/zend/tut/dynamic.php) 
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

class Biorhythm {
	public function set_date($date){
		// Date-format: AAAA-MM-DD
		$birthday = explode("-", $date);
		
		$this->birth["year"] = $birthday[0];
		$this->birth["month"] = $birthday[1];
		$this->birth["day"] = $birthday[2];
		
		return $this->birth;
	}
	private function get_date($type) {
		switch(strtolower($type)){
			case "d":		return $this->birth["day"]; break;
			case "m":		return $this->birth["month"]; break;
			case "y":		return $this->birth["year"]; break;
		}
	}
	public function set_image($width = 710, $height = 400, $days_length = 30) {
		$this->image_params["width"] = $width;
		$this->image_params["height"] = $height;
		$this->image_params["days_length"] = $days_length;
		
		return $this->image_params;
	}
	public function get_image_params() {
		return $this->image_params["width"];
	}
	//
	// Function to draw a curve of the biorythm
	// Parameters are the day number for which to draw,
	// period of the specific curve and its color
	//
	private function drawRhythm($image, $days_alive, $period, $color) {
		// get day on which to center
		$center_day = $days_alive - ($this->image_params["days_length"] / 2);
		
		// calculate diagram parameters
		$plot_scale = ($this->image_params["height"] - 25) / 2;
		$plot_center = ($this->image_params["height"] - 25) / 2;
		
		// draw the curve
		for($x = 0; $x <= $this->image_params["days_length"]; $x++) {
			// calculate phase of curve at this day, then Y value
			// within diagram
			$phase = (($center_day + $x) % $period) / $period * 2 * pi();
			$y = 1 - sin($phase) * (float)$plot_scale + (float)$plot_center;
			
			// draw line from last point to current point
			if($x > 0) {
				imageLine($image, $this->old_x, $this->old_y, $x * $this->image_params["width"] / $this->image_params["days_length"], $y, $color);
			}
			// save current X/Y coordinates as start point for next line
			$this->old_x = $x * $this->image_params["width"] / $this->image_params["days_length"];
			$this->old_y = $y;
		}
	}
	
	public function generate_biorhythm() {
		// calculate the number of days this person is alive
		// this works because Julian dates specify an absolute number
		// of days -> the difference between Julian birthday and
		// "Julian today" gives the number of days alive
		$days_gone = abs(GregorianToJD($birth["month"], $this->birth["day"], $this->birth["year"]) - GregorianToJD(date("m"), date( "d"), date("Y")));
		
		// create image
		$image = imageCreate($this->image_params["width"], $this->image_params["height"]);
		
		// allocate all required colors
		$color_bg = imageColorAllocate($image, 192, 192, 192);
		$color_fg = imageColorAllocate($image, 255, 255, 255);
		$color_grid = imageColorAllocate($image, 120, 120, 120);
		$color_cross = imageColorAllocate($image, 200, 200, 200);
		$color_text = imageColorAllocate($image, 27, 27, 27);
		$color_physical = imageColorAllocate($image, 0, 0, 255);
		$color_emotional = imageColorAllocate($image, 255, 0, 0);
		$color_intellectual = imageColorAllocate($image, 0, 145, 0);
		
		// clear the image with the background color
		imageFilledRectangle($image, 0, 0, $this->image_params["width"] - 1, $this->image_params["width"] - 1, $color_fg);
		
		// calculate start date for diagram and start drawing
		$seconds_per_day = 60 * 60 * 24;
		$diagram_date = time() - ($this->image_params["days_length"] / 2 * $seconds_per_day) + $seconds_per_day;
		
		for ($i = 1; $i < $this->image_params["days_length"]; $i++) {
			$this_date = getDate($diagram_date);
			$x_coord = ($this->image_params["width"] / $this->image_params["days_length"]) * $i;
			
			// draw day mark and day number
			imageLine($image, $x_coord, $this->image_params["height"] - 25, $x_coord, $this->image_params["height"] - 20, $color_grid);
			imageString($image, 3, $x_coord - 5, $this->image_params["height"] - 16, $this_date["mday"], $color_text);
			
			$diagram_date += $seconds_per_day;
		}
		// draw middle cross
		imageLine($image, 0, ($this->image_params["height"] - 20) / 2, $this->image_params["width"], ($this->image_params["height"] - 20) / 2, $color_cross);
		imageLine($image, $this->image_params["width"] / 2, 0, $this->image_params["width"] / 2, $this->image_params["height"] - 20, $color_cross);
		
		// draw rectangle around diagram (marks its boundaries)
		imageRectangle($image, 0, 0, $this->image_params["width"] - 1, $this->image_params["height"] - 20, $color_grid);
		
		// now draw each curve with its appropriate parameters
		$this->drawRhythm($image, $days_gone, 23, $color_physical);
		$this->drawRhythm($image, $days_gone, 28, $color_emotional);
		$this->drawRhythm($image, $days_gone, 33, $color_intellectual);
		
		// print descriptive text into the diagram
		//imageString($image, 2, 10, $this->image_params["height"] - 42, "Fisico", $color_physical);
		//imageString($image, 2, 10, $this->image_params["height"] - 58, "Emotivo", $color_emotional);
		//imageString($image, 2, 10, $this->image_params["height"] - 74, "Intellettivo", $color_intellectual);
		imageString($image, 3, 10, 3, "Fisico", $color_physical);
		imageString($image, 3, 62.5, 3, "Emotivo", $color_emotional);
		imageString($image, 3, 120, 3, "Intellettivo", $color_intellectual);
		//imageString($image, 3, 10, 10, "Data di nascita: " . $this->birth["day"] . "/" . $this->birth["month"] . "/" . $this->birth["year"], $color_text);
		//imageString($image, 2, 10, 7.5, "Bioritmi del ". date("d/m/Y"), $color_text);
		
		// set the content type
		header("Content-type: image/png");
		
		// create an interlaced image for better loading in the browser
		imageInterlace($image, 1);
		
		// mark background color as being transparent
		imageColorTransparent($image, $color_bg);
		
		// now send the picture to the client (this outputs all image data directly)
		imagepng($image);
	}
}
?>