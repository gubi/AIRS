<?PHP
// ----------------------------------------------------------------------------------
// Class: NamedGraphMem
// ----------------------------------------------------------------------------------

/**
* NamedGraph implementation that extends a {@link MemModel}
* with a name.
*
* <BR><BR>History:<UL>
* <LI>05-02-2005                : First version of this class.</LI>
*
* @version  V0.9.3
* @author Daniel Westphal <http://www.d-westphal.de>
*
* @package 	dataset
* @access	public
**/
class NamedGraphMem extends MemModel  
{
	
	/**
	* Name of the NamedGraphMem
	*
	* @var		 string
	* @access	private
	*/
	var $graphName;
	
	
	/**
    * Constructor
    * You have to supply a graph name. You can supply a URI
    *
    * @param  string
    * @param  string 
	* @access	public
    */		
	function NamedGraphMem($graphName,$baseURI = null)
	{
		$this->setBaseURI($baseURI);
		$this->indexed = INDEX_TYPE;
		$this->setGraphName($graphName);		
	}
	
	/**
    * Sets the graph name.
    *
    * @param  string 
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