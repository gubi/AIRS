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
		
		<foaf:skypeID>ale.gubi</foaf:skypeID>
		<wot:hasKey rdf:nodeID="KeyA" />
		<foaf:knows>
			<foaf:Person>
				<foaf:name>Aida Turrini</foaf:name>
				<foaf:mbox_sha1sum>4bb2d607c958460dc7fa43d6ec97f27dc5498d9d</foaf:mbox_sha1sum>
				<rdfs:seeAlso rdf:resource="http://airs.inran.it/Utente/Aida.turrini/foaf.rdf"/>
			</foaf:Person>
		</foaf:knows>
		<foaf:knows>
			<foaf:Person>
				<foaf:name>Antonella Pettinelli</foaf:name>
				<foaf:mbox rdf:resource="mailto:pettinelli@inran.it" />
				<foaf:mbox_sha1sum>c7b291b5df8c9ecce0297df9de97740b80549690</foaf:mbox_sha1sum>
				<rdfs:seeAlso rdf:resource="http://airs.inran.it/Utente/Pettinelli/foaf.rdf"/>
			</foaf:Person>
		</foaf:knows>
		<foaf:publications rdf:resource="https://api.zotero.org/users/1318598/items/VWW3XS5V?format=rdf_zotero" />
		<foaf:interest rdf:resource="http://it.dbpedia.org/page/Programmazione_%28informatica%29"/>
		<foaf:interest rdf:resource="http://it.dbpedia.org/page/Web_semantico"/>
		<foaf:interest rdf:resource="http://it.dbpedia.org/page/Wireless_community_network"/>
		<foaf:interest rdf:resource="http://it.dbpedia.org/page/Fantascienza"/>
		<foaf:currentProject>
			<foaf:Project>
				<dc:title xml:lang="en">AIRS - Automatic Intelligent Research System</dc:title>
				<dc:description xml:lang="en">An Open "Heuristically programmed Algorithmic calculator" for nutrition researches</dc:description>
				<foaf:logo rdf:resource="https://airs.inran.it/common/media/svg/logo_airs.svg"/>
				<rdfs:seeAlso rdf:resource="http://airs.inran.it/index.rdf"/>
				<foaf:homepage rdf:resource="http://www.inran.it"/>
			</foaf:Project>
		</foaf:currentProject>
		<foaf:currentProject>
			<foaf:Project>
				<dc:title xml:lang="en">PICOL - PIctorial COmmunication Language</dc:title>
				<dc:description xml:lang="en">PICOL stands for PIctorial COmmunication Language and is a project to find a standard and reduced sign system for electronic communication. PICOL is free to use and open to alter.</dc:description>
				<foaf:logo rdf:resource="http://picol.org/images/logo/picol_logo.svg"/>
				<foaf:homepage rdf:resource="http://www.picol.org"/>
			</foaf:Project>
		</foaf:currentProject>
		<foaf:currentProject>
			<foaf:Project>
				<dc:title xml:lang="it">IANNC - Indice Analitico Notizie Non Convenzionali</dc:title>
				<dc:description xml:lang="it">IANNC è un progetto di catalogazione ed indicizzazione di articoli su temi non convenzionali pubblicati su periodici di informazione autentica (per il momento il bimestrale "Nexus New Times" e alcune notizie selezionate dalla rete), al fine di rendere disponibili ulteriori strumenti di ricerca per approfondimenti "alternativi" is free to use and open to alter.</dc:description>
				<foaf:logo rdf:resource="http://www.iannc.org/common/media/img/logo_web_big.png"/>
				<foaf:homepage rdf:resource="http://www.iannc.org"/>
			</foaf:Project>
		</foaf:currentProject>
		<foaf:memberOf rdf:nodeID="Ninux" />
		<foaf:memberOf rdf:nodeID="Naaa" />
	</foaf:Person>

	<foaf:Group rdf:ID="Ninux">
		 <foaf:name>Ninux.org</foaf:name>
		 <foaf:logo rdf:resource="https://svn.ninux.org/ninuxdeveloping/export/907/graphics/Logo_Ninux_2011.png" />
		 <foaf:homepage rdf:resource="http://wiki.ninux.org"/>
		 <foaf:member rdf:resource="#me"/>
	</foaf:Group>
	<foaf:Group rdf:ID="Naaa">
		 <foaf:name>Naaa</foaf:name>
		 <foaf:logo rdf:resource="https://svn.ninux.org/ninuxdeveloping/export/907/graphics/Logo_Ninux_2011.png" />
		 <foaf:homepage rdf:resource="http://wiki.ninux.org"/>
		 <foaf:member rdf:resource="#me"/>
	</foaf:Group>

	<foaf:Organization>
		<foaf:name xml:lang="it">INRAN - Istituto Nazionale di Ricerca per gli Alimenti e la Nutrizione</foaf:name>
		<foaf:homepage rdf:resource="http://www.inran.it/"/>
		<rdfs:seeAlso rdf:resource="http://www.inran.it/index.rdf"/>
		<rdfs:seeAlso>http://it.dbpedia.org/page/Palestrina</rdfs:seeAlso>
	</foaf:Organization>
	<wot:PubKey rdf:nodeID="KeyA"> 
		<wot:hex_id>71FA534A</wot:hex_id> 
		<wot:length>2048</wot:length> 
		<wot:fingerprint>7C4D 3533 C21C 608B 39E8 EAB2 56B4 AFB7 71FA 534A</wot:fingerprint> 
		<wot:pubkeyAddress rdf:resource="https://airs.inran.it/Utente/Gubi/pub_key.asc"/> 
		<wot:identity>
			<wot:User> 
				<foaf:name>Alessandro Gubitosi</foaf:name> 
				<foaf:mbox_sha1sum>3d376316b766c3caeb248e28992c692e78ac1f77</foaf:mbox_sha1sum> 
			</wot:User>
		</wot:identity>
	</wot:PubKey>

	<geo:Point rdf:nodeID="littlegym36">
		<rdfs:isDefinedBy rdf:resource="http://sws.geonames.org/3171606/about.rdf"/>
		<rdfs:label xml:lang="it">Palestrina</rdfs:label>
		<geo:lat>41.8407</geo:lat>
		<geo:long>12.8930</geo:long>
	</geo:Point>
</rdf:RDF>
Foaf;
?>