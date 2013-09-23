<?

# Unicode class... be sure you use charset=utf-8 in your html header if you use this.
#
# You are free to use this code, change whatever and redistribute, just leave info about me and
# don't forget to drop me a line if you think this code is useful ;)
#
# Romans (2000)
# romans@lv.net

class utf{
 var $map; # loaded charset mappings. You can obtain them at ftp://ftp.unicode.org/Public/MAPPINGS/

 function loadmap($filename,$alias){
  # Load table with mapping into array for latter use. Pass alias to cp2utf function..
  $f=fopen($filename,'r') or die();
  while(!feof($f)){
   if($s=chop(fgets($f,1023))){
    list($x,$a,$b)=split('0x',$s);
    $a=hexdec(substr($a,0,2));
    $b=hexdec(substr($b,0,4));
    if($a&&$b)$this->map[$alias][$a]=$b;
   }
  }
 }

 function cp2utf($str,$alias=''){
  # Translate string ($str) to UTF-8 from given charset ($xcp)
  #  if charset is not present, ISO-8859-1 will be used.

  if($alias==''){
   for($x=0;$x<strlen($str);$x++){
    $xstr.=$this->code2utf(ord(substr($str,$x,1)));
   }
   return $xstr;
  }

  for($x=0;$x<strlen($str);$x++){
   $xstr.=$this->code2utf($this->map[$alias][ord(substr($str,$x,1))]);
  }
  return $xstr;
 }


 function code2utf($num){
  # Translate numeric code of UTF-8 character code to corresponding character sequence. Refer to www.unicode.org for info.

  if($num<128)return chr($num); // ASCII
  if($num<1024)return chr(($num>>6)+192).chr(($num&63)+128);
  if($num<32768)return chr(($num>>12)+240).chr((($num>>6)&63)+128).chr(($num&63)+128);
  if($num<2097152)return chr($num>>18+240).chr((($num>>12)&63)+128).chr(($num>>6)&63+128).chr($num&63+128);

  return '';
 }
}
# EOF
?>
