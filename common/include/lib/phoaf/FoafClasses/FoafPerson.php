<?
/* $Id: FoafPerson.php,v 1.1 2005/11/02 22:59:33 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class FoafPerson extends FoafAgent {

/*foaf:geekcode foaf:firstName foaf:surname foaf:family_name foaf:plan foaf:img foaf:myersBriggs 
foaf:workplaceHomepage foaf:workInfoHomepage foaf:schoolHomepage foaf:knows foaf:interest foaf:topic_interest 
foaf:publications foaf:currentProject foaf:pastProject
*/

    /* foaf:geekCode */
    // should be only one ?
    function getGeekcode() {
        return parent::getLabel(FOAF::GEEKCODE());
    }

    /* foaf:firstName */
    function getFirstNames() {
        return parent::getLabels(FOAF::FIRSTNAME());
    }

    function getFirstName() {
        return parent::getLabel(FOAF::FIRSTNAME());
    }

    /* foaf:surname */
    function getSurnames() {
        return parent::getLabels(FOAF::SURNAME());
    }
    
    function getSurname() {
        return parent::getLabel(FOAF::SURNAME());
    }

    /* foaf:familyName */
    function getFamilyNames() {
        return parent::getLabels(FOAF::FAMILY_NAME());
    }

    function getFamilyName() {
        return parent::getLabel(FOAF::FAMILY_NAME());
    }
   
    /*  foaf:plan */
    function getPlans() {
        return parent::getLiterals(FOAF::PLAN());
    }

    function getPlan() {
        return parent::getLiteral(FOAF::PLAN());
    }

    /* foaf:img */
    function getImgs() {
        return parent::getNodes(FOAF::IMG(), FOAF_IMAGE);
    }

    function getImg() {
        return parent::getNode(FOAF::IMG(), FOAF_IMAGE);
    }
    
    /* foaf:myerBriggs */
    function getMyersBriggs() {
        return parent::getLabel(FOAF::MYERS_BRIGGS());
    }

    /* foaf:workplaceHomepage */    
    function getWorkplaceHomepages() {
        return parent::getNodes(FOAF::WORKPLACE_HOMEPAGE(), FOAF_DOCUMENT);
    }

    function getWorkPlaceHomepage() {
        return parent::getNode(FOAF::WORKPLACE_HOMEPAGE(), FOAF_DOCUMENT);
    }
   
    /* foaf:workInfoHomepage */
    function getWorkInfoHomepages() {
        return parent::getNodes(FOAF::WORK_INFO_HOMEPAGE(), FOAF_DOCUMENT);
    }

    function getWorkInfoHomepage() {
        return parent::getNode(FOAF::WORK_INFO_HOMEPAGE(), FOAF_DOCUMENT);
    }

    /* foaf:schoolHomepage */
    function getSchoolHomepages() {
        return parent::getNodes(FOAF::SCHOOL_HOMEPAGE(), FOAF_DOCUMENT);
    }

    function getSchoolHomepage() {
        return parent::getNode(FOAF::SCHOOL_HOMEPAGE(), FOAF_DOCUMENT);
    }

    /* foaf:knows */
    /* no need to get a unique "knows", don't think it's pertinent */
    function knows() {
        return parent::getNodes(FOAF::KNOWS(), FOAF_PERSON);
    }

    /* foaf:interest */
    // No need for a uniique 
    function getInterests() {
        return parent::getNodes(FOAF::INTEREST(), FOAF_DOCUMENT);
    }

    /* foaf:topic_interest */
    function getInterestTopic() {} // deprecated
       
    /* foaf:publications */
    function getPublications() { 
        return parent::getNodes(FOAF::PUBLICATION(), FOAF_DOCUMENT);
    }

    /* foaf:currentProject */
    // no need for unique
    function getCurrentProjects() {
        return parent::getNodes(FOAF::CURRENT_PROJECT(), FOAF_RESOURCE);
    }

    /* foaf!pastProject */
    // no need for unique
    function getPastProjects() {
        return parent::getNodes(FOAF::PAST_PROJECT(), FOAF_RESOURCE);
    }

    // Think it has Person as a domain, but not sure as that's not written in specs
    /* foaf:title */
    // different langs
    function getTitles() {
        return parent::getLiterals(FOAF::TITLE());
    }

    function getTitle() {
        return parent::getLiteral(FOAF::TITLE());
    }

    
}

?>
