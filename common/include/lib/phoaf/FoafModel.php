<?
/* $Id: FoafModel.php,v 1.7 2006/03/16 00:17:32 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class FoafModel extends InfModelF {

    function __construct($model) {
        parent::__construct();
        parent::load($model);
        $this->addRelationshipProperties();
    }

    /* Add relationship properties as subclass of foaf:knows() */
    function addRelationshipProperties() {
        $this->add(new Statement(RELATIONSHIP::ACQUAINTANCE_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::AMBIVALENT_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::ANCESTOR_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::ANTAGONIST_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::APPRENTICE_TO(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::CHILD_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::CLOSE_FRIEND_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::COLLABORATES_WITH(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::COLLEAGUE_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::DESCENDANT_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::EMPLOYED_BY(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::EMPLOYER_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::ENEMY_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::ENGAGED_TO(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::FRIEND_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::GRANDCHILD_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::GRANDPARENT_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::HAS_MET(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::KNOWS_BY_REPUTATION(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::KNOWS_IN_PASSING(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::KNOWS_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::LIFE_PARTNER_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::LIVES_WITH(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::LOST_CONTACT_WITH(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::MENTOR_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::NEIGHBOR_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::PARENT_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::PARTICIPANT(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::PARTICIPANT_IN(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::SIBLING_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::SPOUSE_OF(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::WORKS_WITH(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
        $this->add(new Statement(RELATIONSHIP::WOULD_LIKE_TO_KNOW(), RDFS::SUB_PROPERTY_OF(), FOAF::KNOWS()));
    }

    /* Get "root" people of the FOAF file  
      As it could be any"Thing", we limite to Person and Group. */
    function root() {
        // Some people use foaf:PersonalProfileDocument (the proper way to do)
        if ($profile = $this->getFirstMatchingStatement(NULL, RDF::TYPE(), FOAF::PERSONAL_PROFILE_DOCUMENT())) {
            $ppd = $profile->getSubject();
            // Then Some then use foaf:Primary_Topic
            if (!$root = $this->getFirstMatchingStatement($ppd, FOAF::PRIMARY_TOPIC(), NULL)) {
                // And some use foaf:Topic
                $root = $this->getFirstMatchingStatement($ppd, FOAF::TOPIC(), NULL); 
            }
            $root = ($root) ? $root->getObject() : NULL;
            // Get resource type (we create a temporary FoarResource, code is cleaner this way even if it use a bit more memory)
            $tmproot = ($root) ? new FoafResource($this, $root) : NULL;
            $type = $tmproot->getResourceType();
            if(!$type) throw new Exception("Cannot get resource type for $root");
        } else if ($people = $this->getFirstMatchingStatement(NULL, RDF::TYPE(), FOAF::GROUP())) {
            // For Group file, with no PPD
            $root = ($people) ? $people->getSubject() : NULL;
            $type = 'Group';
        } else if ($person = $this->getFirstMatchingStatement(NULL, RDF::TYPE(), FOAF::PERSON())) {
            // Some use directly foaf:Person
            $root = ($person) ? $person->getSubject() : NULL;
            $type = 'Person';
        } else if ($person = $this->getFirstMatchingStatement(NULL, RDF::TYPE(), WORDNET::PERSON())) { 
            // Yet, some using worndet:people
            $root = ($person) ? $person->getSubject() : NULL;
            $type = 'Person';
        }
        $foaftype = "Foaf$type";
        return ($root) ? new $foaftype($this, $root) : NULL;
    }

    /* Get all matching statements */
    function getMatchingStatements($subject, $predicate, $object) {
        /*print_r($subject);
        print_r($predicate);
        print_r($object);*/
        $statements = parent::find($subject, $predicate, $object);
        $st = $statements->getStatementIterator();
        if(!$st->hasNext()) {
            return NULL;
        } else {
            return $st;
        }
    }

    function getFirstMatchingStatement($subject, $predicate, $object) {
        return ($statements = $this->getMatchingStatements($subject, $predicate, $object)) ? $statements->next() : NULL;
    }

}

?>
