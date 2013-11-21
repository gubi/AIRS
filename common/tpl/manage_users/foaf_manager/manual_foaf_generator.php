<?php
$foaf_file = <<<Foaf
<?xml version="1.0" encoding="utf-8" ?>
<rdf:RDF 
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:bibo="http://purl.org/ontology/bibo/"
	xmlns:cc="http://web.resource.org/cc/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:dcterms="http://purl.org/dc/terms/"
	xmlns:foaf="http://xmlns.com/foaf/0.1/"
	xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
	xmlns:org="http://www.w3.org/ns/org#"
	xmlns:owl="http://www.w3.org/2002/07/owl#"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	xmlns:rsa="http://www.w3.org/ns/auth/rsa#"
	xmlns:wot="http://xmlns.com/wot/0.1/">

	<foaf:PersonalProfileDocument rdf:about="">
		<foaf:maker rdf:resource="#me"/>
		<foaf:primaryTopic rdf:resource="#me"/>
		<foaf:group rdf:resource="#Ninux"/>
		<admin:generatorAgent rdf:resource="$generatorAgent"/>
		<admin:errorReportsTo rdf:resource="$errorReportsTo"/>
	</foaf:PersonalProfileDocument>
	<foaf:Person rdf:ID="me">
		<foaf:name>$foaf_name</foaf:name>
		<foaf:givenname>$foaf_givenname</foaf:givenname>
		<foaf:family_name>$foaf_family_name</foaf:family_name>
		$gender
		<foaf:dateOfBirth rdf:datatype="http://www.w3.org/2001/XMLSchema#date">$foaf_dateOfBirth</foaf:dateOfBirth>
		<foaf:nick>$foaf_nick</foaf:nick>
		<foaf:mbox rdf:resource="$foaf_mbox" />
		<foaf:mbox_sha1sum>$foaf_mbox_sha1</foaf:mbox_sha1sum>
		<foaf:homepage rdf:resource="$foaf_homepage"/>
		<foaf:weblog rdf:resource="$foaf_weblog"/>
		<foaf:phone rdf:resource="$foaf_phone"/>
		<foaf:workplaceHomepage rdf:resource="$foaf_workplaceHomepage"/>
		<foaf:workInfoHomepage rdf:resource="$foaf_workInfoHomepage"/>
		<foaf:based_near rdf:nodeID="genid$k" />
		<foaf:depiction>
			<foaf:Image rdf:about="$foaf_depiction">
				<foaf:thumbnail rdf:resource="$foaf_thumb"/>
				<foaf:depicts>
					<foaf:Person>
						<foaf:name>$foaf_name</foaf:name>
					</foaf:Person>
				</foaf:depicts>
			</foaf:Image>
		</foaf:depiction>
		<rdfs:comment xml:lang="it"><![CDATA[$foaf_bio]]>
		</rdfs:comment>
		$foaf_account
		
		<foaf:skypeID>$foaf_nick</foaf:skypeID>
		<wot:hasKey rdf:nodeID="KeyA" />
		$foaf_knows
		
		$foaf_publication
		$foaf_interest
		$foaf_cproject
		$foaf_pproject
	</foaf:Person>

	<wot:PubKey rdf:nodeID="KeyA"> 
		<wot:hex_id>$wot_hex_id</wot:hex_id> 
		<wot:length>$wot_length</wot:length> 
		<wot:fingerprint>$wot_fingerprint</wot:fingerprint> 
		<wot:pubkeyAddress rdf:resource="https://airs.inran.it/Utente/Gubi/pub_key.asc"/> 
		<wot:identity>
			<wot:User> 
				<foaf:name>$foaf_name</foaf:name> 
				<foaf:mbox_sha1sum>$foaf_mbox_sha1</foaf:mbox_sha1sum> 
			</wot:User>
		</wot:identity>
	</wot:PubKey>
</rdf:RDF>
Foaf;
?>