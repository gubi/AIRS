<?
/* $Id: FoafImage.php,v 1.1 2005/11/02 22:59:33 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class FoafImage extends FoafResource {

    /* foaf:depicts */
    function depicts() {
        return parent::getNodes(FOAF::DEPICTS(), FOAF_RESOURCE);
    }

    /* foaf:thumbnail */
    // an image should get a unique thumb ?
    function thumbnails() {
        return parent::getNodes(FOAF::THUMBNAIL, FOAF_IMAGE);
    }

    function thumbnail() {
        return parent::getNode(FOAF::THUMBNAIL, FOAF_IMAGE);
    }

}

?>
