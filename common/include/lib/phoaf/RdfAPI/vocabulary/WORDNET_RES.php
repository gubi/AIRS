<?PHP
/* $Id: WORDNET_RES.php,v 1.1 2005/11/02 22:59:35 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

// ----------------------------------------------------------------------------------
// WORDNET Vocabulary (ResResource)
// ----------------------------------------------------------------------------------

class WORDNET_RES{

	// WORDNET concepts (constants are defined in constants.php)
	function PERSON()
	{
		return  new ResResource(WORDNET_NS . 'Person');

	}
}


?>
