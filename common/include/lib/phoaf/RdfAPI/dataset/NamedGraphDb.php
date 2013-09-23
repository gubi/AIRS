<?PHP
// ----------------------------------------------------------------------------------
// Class: NamedGraphDb
// ----------------------------------------------------------------------------------

/**
* Persistent NamedGraph implementation that extends a {@link DbModel}.
* The graphName is not stored in the database. As soon as the namedGraph is added
* to a RDF dataset the graphName is saved.
*
* <BR><BR>History:<UL>
* <LI>05-30-2005                : First version of this class.</LI>
*
* @version  V0.9.3
* @author Daniel Westphal (http://www.d-westphal.de)
*
* @package 	dataset
* @access	public
**/
class NamedGraphDb extends DbModel  
{

	/**
	* Name of the NamedGraphDb
	*
	* @var		string
	* @access	private
	*/
	var $graphName;
	
	/**
	* Constructor
	* Do not call this directly.
	* Use the method getModel,getNewModel or putModel of the Class NamedGraphDb instead.
	*
	* @param   ADODBConnection
	* @param   string
	* @param   string
	* @param   string
	* @access	public
	*/		
	function NamedGraphDb(&$dbConnection, $modelURI, $modelID,$graphName, $baseURI=NULL)
	{
		$this->dbConn =& $dbConnection;
		$this->modelURI = $modelURI;
		$this->modelID = $modelID;
		$this->baseURI = $this->_checkBaseURI($baseURI);
		$this->graphName = $graphName;
	}
	
	/**
    * Sets the graph name.
    *
    * @param string 
	* @access	public
    */	
	function setGraphName($graphName)
	{
		$this->graphName=$graphName;
	}
	
	/**
    * Return the graph name.
    *
    * @return string
	* @access	public
    */
	function getGraphName()
	{
		return $this->graphName;
	}
}
?>