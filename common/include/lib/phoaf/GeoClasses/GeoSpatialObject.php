<?
/* $Id: GeoSpatialObject.php,v 1.2 2006/01/31 22:34:26 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class GeoSpatialObject extends FoafResource {

    // http://www.schemaweb.info/schema/SchemaInfo.aspx?id=42

    function getLat() {
        return parent::getLabel(GEO::LAT());
    }
    
    function getLong() {
        // Some people use 'geo:lon' instead of 'geo:long'
        return ($long = parent::getLabel(GEO::LONG())) ? $long : parent::getLabel(GEO::LON());
    }

    function getAlt() {
        return parent::getLabel(GEO::ALT());
    }

    function getLatLong() {
        return parent::getLabel(GEO::LAT_LONG());
    }

    function getLocation() {
        return ($lat_long = $this->getLatLong()) ? $lat_long : 
            (($lat = $this->getLat()) && ($lng = $this->getLong())) ? "$lat, $lng" : null;
    }
}

?>
