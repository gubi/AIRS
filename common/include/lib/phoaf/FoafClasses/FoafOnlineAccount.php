<?
/* $Id: FoafOnlineAccount.php,v 1.1 2005/11/02 22:59:33 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class FoafOnlineAccount extends FoafResource {

    /* foaf:accountServiceHomepage */
    function getAccountServiceHomepages() {
        return parent::getNodes(FOAF::ACCOUNT_SERVICE_HOMEPAGE(), FOAF_DOCUMENT);
    }

    function getAccountServiceHomepage() {
        return parent::getNode(FOAF::ACCOUNT_SERVICE_HOMEPAGE(), FOAF_DOCUMENT);
    }

    /* foaf:accountName */
    // ok let's keep it, but no really useful
    function getAccountNames() {
        return parent::getLabels(FOAF::ACCOUNT_NAME());
    }

    function getAccountName() {
        return parent::getLabel(FOAF::ACCOUNT_NAME());
    }
}

?>
