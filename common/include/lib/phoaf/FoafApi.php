<?
/* $Id: FoafApi.php,v 1.2 2006/01/09 00:59:38 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/


define("RDFAPI_INCLUDE_DIR", dirname(__FILE__) . "/RdfAPI/");
require_once(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
require_once(RDFAPI_INCLUDE_DIR . "/vocabulary/VocabularyClass.php");

require_once(dirname(__FILE__) . "/constants.php");

function __autoload ($class_name) {
    $includes = array(".", "FoafClasses", "GeoClasses");
    foreach($includes as $dir) {
        $file = dirname(__FILE__) . "/$dir/$class_name.php";
        if(file_exists($file)) {
            require_once ($file);
        }
    }
}

?>
