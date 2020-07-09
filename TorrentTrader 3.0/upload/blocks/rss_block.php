<?php
class SimpleXmlParser{
    // @ Variable Holding Parser
	var $SimpleParser;

	//@ Feed Source URL
	var $feedUrl;

	//@ Variables to Hold the Data
	var $title = "";
	var $description = "";
	var $link = "";
	var $author="";
	var $pubDate="";
    var $insideitem = false;
	var $tag = "";


	// Purpose : Constructor, Which will initialize the XML Parser
	function SimpleXmlParser($MyFeed){
        //To begin, I create an instance of the XML parser
	    $this->SimpleParser = xml_parser_create();   // creates a new XML parser and returns a reousurce handle referencing 
        
		$this->feedUrl=$MyFeed;    // Assigns the Feed Source to the Member Variable
		
		xml_set_object($this->SimpleParser,$this);	  // allows to use parser inside object
		xml_set_element_handler($this->SimpleParser, "XmlParserFirstElement", "XmlParserendElement");	  // Sets the element handler functions for the XML parser parser
		xml_set_character_data_handler($this->SimpleParser, "characterData");   // Sets the character data handler function for the XML parser parser		
		
		$this->ParseFeed();   // Call to Parser Function
	}
	
	function XmlParserFirstElement($parser, $tagName, $attrs) {
        //The Function Will be called, when ever the XML_PARSER Encounters a start Tag, in the XML File
		if ($this->insideitem) {
			$this->tag = $tagName;
		} elseif ($tagName == "ITEM") {
			$this->insideitem = true;
		}
	}

    function XmlParserendElement($parser, $tagName) {
        //The Function Will be called, when ever the XML_PARSER Encounters a end Tag, in the XML File
		if ($tagName == "ITEM") {
		  	printf("<dt><b><a href='%s'>%s</a></b></dt>",
			trim($this->link),htmlspecialchars(trim($this->title)));    //Display the Title Element from the XML file to HTML
			//printf("<dd>%s</dd>",htmlspecialchars(trim($this->description)));  // Description element is made to display in HTML
            // Deallocation of all Global Variables
			$this->title = "";
			$this->description = "";
			$this->link = "";
			$this->insideitem = false;
		}
	}	
	
    //Function will be called by the Parser when the end tage of the element is processed. Requires Two Permeters
    function characterData($parser, $data) {
        //Permeters: the parser instance and the string containing the data.
		if ($this->insideitem) {
			switch ($this->tag) {
				case "TITLE":
				$this->title .= $data;
				break;
				case "DESCRIPTION":
				$this->description .= $data;
				break;
				case "LINK":
				$this->link .= $data;
				break;
			}
		}
	}

	function ParseFeed(){
        // This is the Place we need to Do error Handling for the XML file, which is not done in this class
		$fp = fopen($this->feedUrl,"r")       // Open the XML file for reading.
			or die("Oops!!! Unexpected Error While Reading the Feed");   // This part will be executed when we compiler is Unable to Open the XML Feed
        //Starts reading the contents of the file, 4K at a time, and put it in the variable $data
		while ($data = fread($fp, 4096)){
			xml_parse($this->SimpleParser, $data, feof($fp));
		}
        //Perform Some Clean Up Work Before Closing the File.
        //Closing the XML File
        fclose($fp);
        //Release the XML Parser
        xml_parser_free($this->SimpleParser);
	}
}	



begin_block("RSS");
$FeedUrl = "";
if (!$FeedUrl) {
	echo "<center>This would need editing with an rss feed of your choice.</center>";
} else {
	$XMLpar = new SimpleXmlParser($FeedUrl);
}
end_block();
?>