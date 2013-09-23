<?php
/**
 * PHP version 5
 *
 * @package	UASparser
 * @author	Jaroslav Mallat (http://mallat.cz/)
 * @copyright  Copyright (c) 2008 Jaroslav Mallat
 * @copyright  Copyright (c) 2010 Alex Stanev (http://stanev.org)
 * @version	0.4.2 beta
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       	http://user-agent-string.info/download/UASparser
 * @example	http://user-agent-string.info/ua_rep/UASparser_example.php
 */

// view source this file and exit
if ($_GET['source'] == "y") {     show_source(__FILE__);     exit; }

class UASparser 
{
    public $IniUrl           = 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini';
    public $VerUrl           = 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini&ver=y';
    public $md5Url            = 'http://user-agent-string.info/rpc/get_data.php?format=ini&md5=y';
    public $InfoUrl           = 'http://user-agent-string.info';
    public $cache_dir       = null;
    public $updateInterval    = 86400; // 1 day

    private $_data            = array();
    private $_ret            = array();
    private $test            = null;
    private $id_browser        = null;
    private $os_id            = null;
    
    public function __construct() {    
    }

    public function Parse($useragent = null) {
        $_ret['typ']            = 'unknown';
        $_ret['ua_family']        = 'unknown';
        $_ret['ua_name']        = 'unknown';
        $_ret['ua_url']            = 'unknown';
        $_ret['ua_company']        = 'unknown';
        $_ret['ua_company_url']        = 'unknown';
        $_ret['ua_icon']        = 'unknown.png';
        $_ret["ua_info_url"]        = 'unknown';
        $_ret["os_family"]        = 'unknown';
        $_ret["os_name"]        = 'unknown';
        $_ret["os_url"]            = 'unknown';
        $_ret["os_company"]        = 'unknown';
        $_ret["os_company_url"]        = 'unknown';
        $_ret["os_icon"]        = 'unknown.png';
        
        if (!isset($useragent)) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
        }
        $_data = $this->_loadData();
        if($_data) {

            // crawler
            foreach ($_data['robots'] as $test) {
                if ($test[0] == $useragent) {
                    $_ret['typ']                                        = 'Robot';
                    if ($test[1]) $_ret['ua_family']                            = $test[1];
                    if ($test[2]) $_ret['ua_name']                                = $test[2];
                    if ($test[3]) $_ret['ua_url']                                = $test[3];
                    if ($test[4]) $_ret['ua_company']                            = $test[4];
                    if ($test[5]) $_ret['ua_company_url']                        = $test[5];
                    if ($test[6]) $_ret['ua_icon']                                = $test[6];
                    if ($test[7]) { // OS set
                        if ($_data['os'][$test[7]][0]) $_ret['os_family']         = $_data['os'][$test[7]][0];
                        if ($_data['os'][$test[7]][1]) $_ret['os_name']            = $_data['os'][$test[7]][1];
                        if ($_data['os'][$test[7]][2]) $_ret['os_url']            = $_data['os'][$test[7]][2];
                        if ($_data['os'][$test[7]][3]) $_ret['os_company']        = $_data['os'][$test[7]][3];
                        if ($_data['os'][$test[7]][4]) $_ret['os_company_url']    = $_data['os'][$test[7]][4];
                        if ($_data['os'][$test[7]][5]) $_ret['os_icon']            = $_data['os'][$test[7]][5];
                    }
                    if ($test[8]) $_ret['ua_info_url']                            = $this->InfoUrl.$test[8];
                    return $_ret;
                }
            }
            
            // browser
            foreach ($_data['browser_reg'] as $test) {
                if (@preg_match($test[0],$useragent,$info)) { // $info contains version
                    $id_browser = $test[1];
                    break;
                  }
             }
            if ($id_browser) { // browser detail
                if ($_data['browser_type'][$_data['browser'][$id_browser][0]][0]) $_ret['typ']    = $_data['browser_type'][$_data['browser'][$id_browser][0]][0];
                if ($_data['browser'][$id_browser][1]) $_ret['ua_family']                        = $_data['browser'][$id_browser][1];
//                if ($info[2]) { //it's inside
//                    $_ret["ua_name"] = $_data['browser'][$id_browser][1].' '.$info[3].' ('.$info[1].' '.$info[2].' inside)';
//                  } 
//                else {
                    $_ret['ua_name'] = $_data['browser'][$id_browser][1].' '.$info[1];
//                }
                if ($_data['browser'][$id_browser][2]) $_ret['ua_url']                            = $_data['browser'][$id_browser][2];
                if ($_data['browser'][$id_browser][3]) $_ret['ua_company']                        = $_data['browser'][$id_browser][3];
                if ($_data['browser'][$id_browser][4]) $_ret['ua_company_url']                    = $_data['browser'][$id_browser][4];
                if ($_data['browser'][$id_browser][5]) $_ret['ua_icon']                            = $_data['browser'][$id_browser][5];
                if ($_data['browser'][$id_browser][6]) $_ret['ua_info_url']                        = $this->InfoUrl.$_data['browser'][$id_browser][6];
            }
            
            // browser OS
            if (isset($_data['browser_os'][$id_browser])) { // os detail
                $os_id = $_data['browser_os'][$id_browser][1];
                if ($_data['os'][$os_id][0]) $_ret['os_family']         = $_data['os'][$os_id][0];
                if ($_data['os'][$os_id][1]) $_ret['os_name']            = $_data['os'][$os_id][1];
                if ($_data['os'][$os_id][2]) $_ret['os_url']            = $_data['os'][$os_id][2];
                if ($_data['os'][$os_id][3]) $_ret['os_company']        = $_data['os'][$os_id][3];
                if ($_data['os'][$os_id][4]) $_ret['os_company_url']    = $_data['os'][$os_id][4];
                if ($_data['os'][$os_id][5]) $_ret['os_icon']            = $_data['os'][$os_id][5];
                return $_ret;
            }
            foreach ($_data['os_reg'] as $test) {
                if (@preg_match($test[0],$useragent)) {
                    $os_id = $test[1];
                    break;
                  }
             }
            if ($os_id) { // os detail
                if ($_data['os'][$os_id][0]) $_ret['os_family']         = $_data['os'][$os_id][0];
                if ($_data['os'][$os_id][1]) $_ret['os_name']            = $_data['os'][$os_id][1];
                if ($_data['os'][$os_id][2]) $_ret['os_url']            = $_data['os'][$os_id][2];
                if ($_data['os'][$os_id][3]) $_ret['os_company']        = $_data['os'][$os_id][3];
                if ($_data['os'][$os_id][4]) $_ret['os_company_url']    = $_data['os'][$os_id][4];
                if ($_data['os'][$os_id][5]) $_ret['os_icon']            = $_data['os'][$os_id][5];
            }
            return $_ret;
        }
        return $_ret;
    }

    private function _loadData() {
        if (file_exists($this->cacheDir.'/cache.ini')) {
            $cacheIni = parse_ini_file($this->cacheDir.'/cache.ini');
        }
        else {
            $this->_downloadData();
        }
        if ($cacheIni['lastupdate'] < time() - $this->updateInterval || $cacheIni['lastupdatestatus'] != "0") {
            $this->_downloadData();
        }
        if (file_exists($this->cacheDir.'/uasdata.ini')) {
            return @parse_ini_file($this->cacheDir.'/uasdata.ini', true);
        }
        else {
            die('ERROR: No datafile (uasdata.ini in Cache Dir), maybe update the file manually.');
        }
    }
    private function _downloadData() {
        if(ini_get('allow_url_fopen')) {
            $status = 1;
            if (file_exists($this->cacheDir.'/cache.ini')) {
                $cacheIni = parse_ini_file($this->cacheDir.'/cache.ini');
            }
            $ctx = stream_context_create(array('http' => array('timeout' => 5)));
            !$ver = @file_get_contents($this->VerUrl, 0, $ctx);
            if (strlen($ver) != 11) {
                if($cacheIni['localversion']) {
                    $ver = $cacheIni['localversion'];
                }
                else {
                    $ver = 'none';
                }
            }
            
            if($ini = @file_get_contents($this->IniUrl, 0, $ctx)) {
                $md5hash = @file_get_contents($this->md5Url, 0, $ctx);
                if(md5($ini) == $md5hash) {
                    @file_put_contents($this->cacheDir.'/uasdata.ini', $ini);
                    $status = 0;
                }
            }

            $cacheIni = "; cache info for class UASparser - http://user-agent-string.info/download/UASparser\n";
            $cacheIni .= "[main]\n";
            $cacheIni .= "localversion = \"$ver\"\n";
            $cacheIni .= 'lastupdate = "'.time()."\"\n";
            $cacheIni .= "lastupdatestatus = \"$status\"\n";
            @file_put_contents($this->cacheDir.'/cache.ini', $cacheIni);
        }
        else {
            die('ERROR: function file_get_contents not allowed URL open. Update the datafile (uasdata.ini in Cache Dir) manually.');
        }
    }
    public function SetCacheDir($cache_dir) {
        if (!is_writable($cache_dir)) {
            die('ERROR: Cache dir('.$cache_dir.') is not writable');
        }
        $cache_dir = realpath($cache_dir);
        $this->cacheDir = $cache_dir;
    }
}
?>