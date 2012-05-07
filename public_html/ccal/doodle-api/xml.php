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
    * send($request)
    *
    * Sends HTTP request and returns response as an array
    * containing the response status, header, and content.
    */

    function parse($poll)
    {
        // get rid of junk
        $poll = substr($poll, 5, -7);    
        file_put_contents("poll.xml", $poll);       
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
    
?>
