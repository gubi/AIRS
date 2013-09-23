<?
/* $Id: FoafResource.php,v 1.9 2006/02/25 12:30:35 terraces Exp $
   This file is part of PHOAF - http://gna.org/projects/phoaf/
   Copyright (c) 2005 Alexandre Passant <alex@passant.org>
   Realeased under GPL version 2 or later, see LICENSE file
   or <http://www.gnu.org/copyleft/gpl.html>
*/

class FoafResource {

    function __construct(&$model, $resource) {
        $this->model = $model;
        $this->resource = $resource;
    }

    /* Return Resource type
      Ensure that the resource type is a FOAF type, as we can get many types for the same resource  */
    function getResourceType() {
        $types = $this->model->getMatchingStatements($this->resource, RDF::TYPE(), NULL);
        if(!$types) return NULL;
        while($types->hasNext()) {
            $type = $types->next()->getObject();
            if($type->getNamespace()==FOAF_NS) return $type->getLocalName();
        }
    }

    /* Returns URI of the resource */
    function getURI() {
        return $this->resource->getURI();
    }

    /* Returns an arry of Literals 
    literal = (string, lang) */
    function getLiterals($resource) {
        if($s = $this->model->getMatchingStatements($this->resource, $resource, NULL)) {
            while ($s->hasNext()) {
                $nodes[] = $s->next()->getObject();
            }
            return $nodes;
        }
        return NULL;
    }

    /* returns a Literal  = (string, lang) */
    function getLiteral($resource) {
        return ($lit = $this->getLiterals($resource)) ? $lit[0] : NULL;
    }
    
    /* Returns an array of labels (strings) */
    function getLabels($resource) {
        if($s = $this->model->getMatchingStatements($this->resource, $resource, NULL)) {
            while ($s->hasNext()) {
                $nodes[] = $s->next()->getObject()->getLabel();
            }
            return $nodes;
        }
        return NULL;
    }

    function getLabel($resource) {
        return ($lab = $this->getLabels($resource)) ? $lab[0] : NULL;
    }

    
    /*  Returns an array of FoafClasses subclasses (Agent ...) */
    function getNodes($resource, $class) {
        if($s = $this->model->getMatchingStatements($this->resource, $resource, NULL)) {
            while ($s->hasNext()) {
                $nodes[] = new $class($this->model, $s->next()->getObject());
            }
            return $nodes;
        }
        return NULL;
    }
   
    /*  Returns an array of FoafClasses subclasses (Agent ...) */
    function getNode($resource, $class) {
        return ($node = $this->getNodes($resource, $class)) ? $node[0] : NULL;
    }

    /* Returns SeeAlso URL */   
    function seeAlso() {
        $s = $this->model->getMatchingStatements($this->resource, RDFS::SEEALSO(), NULL);
        return $s ? $s->next()->getObject()->getURI() : NULL;  
    }

    // Physical location of the resource.
    // Actually, its supposed to be a property of GeoSpatialObject, but as I don't handle
    // multi-inheritance, let's put it here
    function getLocation() {
        if($loc = $this->getNode(GEO::LOCATION(), GEO_SPATIAL_OBJECT)) {
            $lat_long = $loc->getLocation();
        } else if($loc = $this->getNode(FOAF::BASED_NEAR(), GEO_SPATIAL_OBJECT)) {
            $lat_long = $loc->getLocation();
        } else if($loc = $this->getNode(CONTACT::NEAREST_AIRPORT(), GEO_SPATIAL_OBJECT)) {
            $lat_long = $loc->getLocation();
        }
        return $lat_long;
    } 

    // Property available for all FOAF objects and more (airports ...)
    function getName() {
        return $this->getLabel(FOAF::NAME());
    }

    // foaf:homepage, property of "thing"
    function getHomepages() {
        return $this->getNodes(FOAF::HOMEPAGE(), FOAF_DOCUMENT);
    }

    function getHomepage() {
        return $this->getNode(FOAF::HOMEPAGE(), FOAF_DOCUMENT);
    }

    function getHomepagesURLs() {
        return array_map(create_function('$n', 'return $n ? $n->resource->getURI() : null;'), $this->getHomepages()); 
    }

    function getHomepageURL() {
        return ($home = $this->getHomepage()) ? $home->resource->getURI() : null;
    }

    // Depictions
    function getDepictions() {
        return $this->getNodes(FOAF::DEPICTION(), FOAF_IMAGE);
    } 

    function getDepiction() {
        return $this->getNode(FOAF::DEPICTION(), FOAF_IMAGE);
    } 


}

?>
