<?

/****************************************************************
 * xml.php
 * 
 * Defines functions used to format and parse xml documents
 * for use with the doodle api.
 *
 ****************************************************************/

    /*
    * array
    * parse($poll)
    *
    * Reformats the xml acquired from doodle into an
    * array. This array is returned.
    */

    function parse($poll)
    {
        // get rid of junk
        $poll = substr($poll, 5, -7);    
               
        // create parser and format array
        $p = xml_parser_create();
        xml_parse_into_struct($p, $poll, $text, $keys);
            
        // free parser
        xml_parser_free($p);
    
        // construct array containing text and keys
        $xml_array['text'] = $text;
        $xml_array['keys'] = $keys;
        
        return $xml_array;     
    }
    
    /*
    * string
    * xml_generate($input)
    *
    * Generates the necessary xml to post a poll. Returns
    * this xml as a string.
    */
    
    function update_xml($input)
    {
        // write header to xml
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        
        // write participant tag
        $xml .= "<participant xmlns=\"http://doodle.com/xsd1\">";
        
        // write participant name
        $xml .= "<name>";
        $xml .= $input['name'];
        $xml .= "</name>";
        
        // write preferences
        $xml .= "<preferences>";
        
        // write answers
        foreach ($input['option'] as $key => $value)
        {
            $xml .= "<option>";
            $xml .= "$value";
            $xml .= "</option>";
        }
        
        // close tags
        $xml .= "</preferences>";
        $xml .= "</participant>";
        
        return $xml;
    }
    
?>
