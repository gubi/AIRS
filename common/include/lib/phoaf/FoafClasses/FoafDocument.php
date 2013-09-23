<?
/* $Id: FoafDocument.php,v 1.1 2005/11/02 22:59:33 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class FoafDocument extends FoafResource {

    /* foaf:sha1 */
    // string
    function getSha1()  {
        return parent::getLabel(FOAF::SHA1(), FOAF_DOCUMENT);
    }

    /* foaf:topic */
    function getTopics() {
        return parent::getNodes(FOAF::TOPIC(), FOAF_RESOURCE);
    }

    /* foaf:primaryTopic */
    function getPrimaryTopic(){
        return parent::getNode(FOAF::PRIMARY_TOPIC(), FOAF_RESOURCE);
    }

}

?>
