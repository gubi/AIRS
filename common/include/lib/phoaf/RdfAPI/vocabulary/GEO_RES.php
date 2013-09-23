<?PHP
/* $Id: GEO_RES.php,v 1.1 2005/11/02 22:59:35 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

// ----------------------------------------------------------------------------------
// GEO Vocabulary (ResResResource)
// ----------------------------------------------------------------------------------

class GEO_RES{


	function SPATIAL_THING()
	{
		return  new ResResource(GEO_NS . 'SpatialThing');

	}

	function POINT()
	{
		return  new ResResource(GEO_NS . 'Point');

	}

	function LAT()
	{
		return  new ResResource(GEO_NS . 'lat');

	}

	function LONG()
	{
		return  new ResResource(GEO_NS . 'long');

	}

	function ALT()
	{
		return  new ResResource(GEO_NS . 'alt');

	}

	function LAT_LONG()
	{
		return  new ResResource(GEO_NS . 'lat_long');

	}

     // The following properties/class are not in the Geo vocabulary, 
    // yet, some foaf profiles use it
    function LOCATION() {
		return  new Resource(GEO_NS . 'location');
    }

    function POSITION() {
		return  new Resource(GEO_NS . 'Position');
    }

    function LON() {
		return  new Resource(GEO_NS . 'lon');
    }

}


?>
