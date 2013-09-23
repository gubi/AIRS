<?
/* $Id: FoafAgent.php,v 1.3 2006/02/25 17:35:13 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class FoafAgent extends FoafResource {

    //in-domain-of:   foaf:mbox foaf:mbox_sha1sum foaf:gender foaf:jabberID foaf:aimChatID foaf:icqChatID foaf:yahooChatID 
    // foaf:msnChatID foaf:weblog foaf:tipjar foaf:made foaf:holdsAccount foaf:birthday

    /* foaf:mbox */
    // array of resources
    function getMboxes() {
        return parent::getNodes(FOAF::MBOX(), FOAF_RESOURCE);
    }

    // resource
    function getMbox() {
        return parent::getNode(FOAF::MBOX(), FOAF_RESOURCE);
    }

    function getMail() {
        if($mail = $this->getMbox()->resource) {
            $mail = (get_class($mail) == 'Literal') ? $mail->getLabel() : $mail->getURI();
            return (substr($mail, 0, 7) == 'mailto:') ? substr($mail, 7) : $mail;
        }
    }
    // @todo getMails() ?? getMailTo ?? (direct use in HTML links)
    
    /* foaf:mbox_sha1sum */
    // Labels array - don't need lang properties
    function getMboxSha1Sums() {
        return parent::getLabels(FOAF::MBOX_SHA1SUM());
    }

    function getMboxSha1Sum() {
        return parent::getLabel(FOAF::MBOX_SHA1SUM());
    }

    /* foaf:gender */
    // hummm
    /*function getGenders() {
        return parent::getLabels(FOAF::GENDER());
    }*/
    
    function getGender() {
        return parent::getLabel(FOAF::GENDER());
    }

    /* foaf:jabberID */
    function getJabberIDs() {
        return parent::getLabels(FOAF::JABBER_ID());
    }

    function getJabberID() {
        return parent::getLabel(FOAF::JABBER_ID());
    }
    
    /* foaf:aimChatID */
    function getAimChatIDs() {
        return parent::getLabels(FOAF::AIM_CHAT_ID());
    }

    function getAimChatID() {
        return parent::getLabels(FOAF::AIM_CHAT_ID());
    }
    
    /* foaf:icqChatID */
    function getIcqChatIDs() {
        return parent::getLabels(FOAF::ICQ_CHAT_ID());
    }

    function getIcqChatID() {
        return parent::getLabels(FOAF::ICQ_CHAT_ID());
    }

     /* foaf:icqChatID */
     // String array
    function getYahooChatIDs() {
        return parent::getLabels(FOAF::YAHOO_CHAT_ID());
    }

    function getYahooChatID() {
        return parent::getLabel(FOAF::YAHOO_CHAT_ID());
    }

    /* foaf:icqChatID */
    function getMsnChatIDs() {
        return parent::getLabels(FOAF::MSN_CHAT_ID());
    }

    function getMsnChatID() {
        return parent::getLabel(FOAF::MSN_CHAT_ID());
    }

    // foaf:weblog foaf:tipjar foaf:made foaf:holdsAccount foaf:birthday
    function getWeblogs() {
        return parent::getNodes(FOAF::WEBLOG(), FOAF_DOCUMENT);
    }

    function getWeblog() {
        return parent::getNode(FOAF::WEBLOG(), FOAF_DOCUMENT);
    }

    function getWeblogsURLs() {
        return array_map(create_function('$n', 'return $n ? $n->resource->getURI() : null;'), $this->getWeblogs()); 
    }

    function getWeblogURL() {
        return ($weblog = $this->getWeblog()) ? $weblog->resource->getURI() : null;
    }
    
    /* foaf:tipjar */
    function getTipjars() {
        return parent::getLabels(FOAF::TIPJAR());
    }

    function getTipjar() {
        return parent::getLabel(FOAF::TIPJAR());
    }
   
    /* foaf:holdsaccount */
    function holdsAccounts() {
        return parent::getNodes(FOAF::HOLDS_ACCOUNT(), FOAF_RESOURCE);
    }

    function getBirthday() {
        return parent::getLabel(FOAF::BIRTHDAY());
    }


}

?>
