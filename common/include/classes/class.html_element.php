<?php
class Element{
        private $tag; // the html element to create
	private $uni; // the remaining (ten) non-deprecated w3c recognized self-closing unitags
	private $atts; // attributes are entered into an associative array
	private $obj; // the object to be nested
	
	public function __construct($tag, $atts=array(), $uni = array("meta", "base", "link", "img", "br", "hr", "param", "input", "col")){
		$this->tag = strtolower($tag);
		$this->atts = $atts;
		$this->uni = $uni;
		if($atts) {
			foreach($atts as $key => $val){
				$this->set($key, $val);
			}
		}
	}
	// object emulates string after calling echo or print
	public function __toString(){
		return $this->build();
	}
	// solicits a value for a given attribute
	public function get($key){
		if(property_exists($this, $key)) {
			return $this->atts[$key];
		} else {
			echo 'Cannot get attribute – "' . $key . '" is not a property of "' . $this . '".';
		}
		return $this;
	}
	// sets an attribute value,  can pass array or key
	public function set($key, $val = ""){
		if(property_exists($this, $key)){
			echo "Cannot set an existing property – instead use re_set.";
			return $this;
		}
		if(!is_array($key)) {
			$temp = array($key => $val); // make it into an array if its not an array
		}
		if($this->atts = array_merge($this->atts, $temp)){
			return $this;
		} else {
			echo "Cannot merge supplied parameters with existing object " . $this . ".\n";
		}
	}
	// changes value of an attribute – not for resetting pointer
	public function re_set($key, $val){
		if($this->remove($key)) {
			$this->set($key, $val);
		} else {
			echo 'Cannot change attribute, "' . $key . '" is not a property of "' . $this . '".';
		}
		return $this;
	}
	// removes a single attribute
	public function remove($key){
		if(isset($this->atts[$key])){
			unset($this->atts[$key]);
		}
		return $this;
	}
	// clears all attributes
	public function clear(){
		$this->atts = array();
		return $this;
	}
	// appends nested object
	public function nest($obj){
		if(@get_class($obj) == __class__){
			$this->atts["text"] .= $obj->build();
		}
	}
	// build element and return text or but not text and certainly not some text
	private function build(){
		$el = "\n\t\t\t<" . $this->tag; // tag opening
		// attributes
		if(count($this->atts)){
			foreach($this->atts as $key => $val){
				if($key != "text") {
					$el .= " " . $key . '="' . $val . '"';
				}
			}
		}
		if(in_array($this->tag, $this->uni)) {
			return $el .= " />\n"; // return self-closing tag
		}
		if(!$this->obj) {
			return $el .= ">" . $this->atts["text"] . "</" . $this->tag . ">"; // return tag with inserted text
		} else {
			return $el .= ">" . "</" . $this->tag . ">"; // return paired tag with inserted object
		}
	}
	// take a dump before pulling yer hair out
	public function dumpy(){
		echo "";
		var_dump($this);
		echo "";
		return;
	}
	// __constructor-like magic
	public function update($obj){
		$this->obj = $obj;
		return $this;
	}
}
?>