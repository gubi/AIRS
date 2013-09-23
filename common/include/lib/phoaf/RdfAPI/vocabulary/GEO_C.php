<?PHP
/* $Id: GEO_C.php,v 1.2 2006/03/07 22:44:50 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

// ----------------------------------------------------------------------------------
// GEO Vocabulary (Resource)
// ----------------------------------------------------------------------------------

class GEO{


	function SPATIAL_THING()
	{
		return  new Resource(GEO_NS . 'SpatialThing');

  }

	function POINT()
	{
		return  new Resource(GEO_NS . 'Point');
	
    }

	function LAT()
	{
		return  new Resource(GEO_NS . 'lat');
	
    }

	function LONG()
	{
		return  new Resource(GEO_NS . 'long');
	
    }

	function ALT()
	{
		return  new Resource(GEO_NS . 'alt');
	
    }

	function LAT_LONG()
	{
		return  new Resource(GEO_NS . 'lat_long');
	
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
