<?PHP
/* $Id: GEO.php,v 1.1 2005/11/02 22:59:35 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

// ----------------------------------------------------------------------------------
// GEO Vocabulary
// ----------------------------------------------------------------------------------

$GEO_SpatialThing = new Resource(GEO_NS . 'SpatialThing');
$GEO_Point = new Resource(GEO_NS . 'Point');

$GEO_lat = new Resource(GEO_NS . 'lat');
$GEO_long = new Resource(GEOF_NS . 'long');
$GEO_alt = new Resource(GEO_NS . 'alt');
$GEO_lat_long = new Resource(GEO_NS . 'lat_long');
// The following properties/class are not in the Geo vocabulary, 
// yet, some foaf profiles use it
$GEO_location = new Resource(GEO_NS . 'location');
$GEO_position = new Resource(GEO_NS . 'Position');
$GEO_lon = new Resource(GEO_NS . 'lon');

?>
