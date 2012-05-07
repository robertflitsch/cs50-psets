<?

/****************************************************************
 * doodleClient.php
 * 
 * Defines functions for various HTTP requests to RESTful Doodle
 * api.
 *
 ****************************************************************/

    // requirements
    require_once("request.php");
    require_once("includes/helpers.php");
    require_once("includes/constants.php");

    /*
     * array
     * send($request)
     *
     * Sends HTTP request and returns response as an array
     * containing the response status, header, and content.
     */
     
    function send($request)
    {
        // Convert the data array into URL Parameters like a=b&foo=bar etc.
        if($request->data != NULL)
        {
            $data = $request->data;
            $content_type = "application/xml";
        }
            
        else
        {
            $data = NULL;
            $content_type = "text/plain";
        }
        
        // parse the given URL
        $url = parse_url($request->url);
     
        // check for correct request scheme
        if ($url['scheme'] != 'http' && $url['scheme'] != 'https')
        { 
            die('Error: Only HTTP or HTTPS request are supported!');
        }
     
        // extract host and path:
        $host = $url['host'];
        $path = $url['path'];
        
        // open a socket connection on port 80 - timeout: 30 sec
        $fp = fsockopen($host, 80, $errno, $errstr, 30);
     
        if ($fp)
        { 
            // create the base string:
            fputs($fp, "{$request->method} $path HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "Content-type: $content_type \r\n");
            fputs($fp, "Content-length: ".strlen($data)."\r\n");
            fputs($fp, "{$request->auth_header}\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            
            // add body
            if($data != NULL)
                fputs($fp, "$data\r\n\r\n");
            
            $result = ''; 
            while(!feof($fp))
            {
                // receive the results of the request
                $result .= fgets($fp, 128);
            }
        }
        
        else
        { 
            return array(
                'status' => 'err', 
                'error' => "$errstr ($errno)"
            );
        }
    
        // close the socket connection:
        fclose($fp);
    
        // split the result status from the response
        $result = explode("\r\n", $result, 2);
        
        $status = isset($result[0]) ? $result[0] : NULL;
        $result = isset($result[1]) ? $result[1] : NULL;
        
        // split the result header from the remaining content
        $result = explode("\r\n\r\n", $result, 2);
    
        $header = isset($result[0]) ? $result[0] : NULL;
        $content = isset($result[1]) ? $result[1] : NULL;
    
        // return as structured array:
        return array(
            'status' => $status,
            'header' => $header,
            'content' => $content
        );
    }   
    
    /*  
     * HttpRequest object
     * form_header($request)
     *
     * Formats the authorization header for the oauth request.
     * Returns the Http Request object including the auth
     * header.
     */
    
    function form_header($request)
    {
        // form request data
        $header = $request->authorization;
        
        foreach($request->auth as $key => $value)
            $header = $header.$value.",";
        
        // get rid of tail-ing comma
        $header = substr($header, 0, -1);
            
        // update request string
        $request->auth_header = $header;
        
        return $request;
    }
    
    /*
     * string
     * request_token(void)
     *
     * Forms and sends an HTTP request as defined in "Request
     * Token" at http://doodle.com/xsd1/AAforRESTfulDoodle.pdf
     * Returns a string containing the oauth_token, 
     * oauth_token_secret, and callback confirmation. 
     */
     
    function request_token()
    {
        // variables for HTTP requests
        $oauth_nonce = rand();
        $oauth_timestamp = time() + OFFSET;
        
        // make new HttpRequest Object
        $request = new HttpRequest;
        
        // set url for request
        $request->url = URL."oauth/requesttoken";
        
        // set method for request
        $request->method = "GET";
        
        // set authorisation for request
        $request->authorization = "Authorization: OAuth ";
        
        // set required authorization parameters for request
        $request->auth['realm'] = "realm=\"\"";
        $request->auth['oauth_consumer_key'] = "oauth_consumer_key=\"".CKEY."\"";
        $request->auth['oauth_signature_method'] = "oauth_signature_method=\"PLAINTEXT\"";
        $request->auth['oauth_timestamp'] = "oauth_timestamp=\"".$oauth_timestamp."\"";
        $request->auth['oauth_nonce'] = "oauth_nonce=\"".$oauth_nonce."\"";
        
        /* FORMATTING HMAC-SHA1 SIGNATURE
        // no tokens yet!
        $tokens = NULL;
        
        // format base string and key for oauth_signature
        $signature = form_signature($request, $tokens);
        
        // hash signature
        $oauth_signature =hash_hmac("sha1", $signature['base_string'], $signature['key'], true);
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".$oauth_signature."\"";
        */
    
        $request->auth['oauth_signature'] = "oauth_signature=\"".CSECRET."&\"";    
    
        // format authorization header
        $request = form_header($request);
         
        // send request
        $response = send($request);
        
        // respond to error messages
        $err = substr($response['status'], 9, 3);
        
        if($err == "401")
            apologize("Sorry, you have not been authorized to access this information.");
        
        else if($err == "500")
            echo(substr($response['content'], 4, -5));
            
        // return oauth_token etc        
        return $response['content'];
    }
    
    /*
     * string
     * access_token($token_string)
     *
     * Forms and sends an HTTP request as defined in "Access
     * Token" at http://doodle.com/xsd1/AAforRESTfulDoodle.pdf
     * Returns an array containing the auth_token and
     * oauth_token_secret.
     */
     
    function access_token($token_string)
    {
        // form oauth token and token secret from string
        $tokens = explode("&", $token_string, 3);
        
        // add values to tokens array
        $tokens['oauth_token'] = substr($tokens[0], 12);
        $tokens['oauth_token_secret'] = substr($tokens[1], 19);
        $tokens['oauth_callback_confirmed'] = substr($tokens[2], 25);
        
        // variables for HTTP requests
        $oauth_nonce = rand();
        $oauth_timestamp = time() + OFFSET;
        
        // make new HttpRequest Object
        $request = new HttpRequest;
        
        // set url for request
        $request->url = URL."oauth/accesstoken";
        
        // set method for request
        $request->method = "GET";
        
        // set authorization for request
        $request->authorization = "Authorization: OAuth ";
        
        // set required authorization parameters for request
        $request->auth['realm'] = "realm=\"\"";
        $request->auth['oauth_token'] = "oauth_token=\"".$tokens['oauth_token']."\"";
        $request->auth['oauth_consumer_key'] = "oauth_consumer_key=\"".CKEY."\"";
        $request->auth['oauth_signature_method'] = "oauth_signature_method=\"PLAINTEXT\"";
        $request->auth['oauth_timestamp'] = "oauth_timestamp=\"".$oauth_timestamp."\"";
        $request->auth['oauth_nonce'] = "oauth_nonce=\"".$oauth_nonce."\"";
        
        /* FORMATTING HMAC-SHA1 SIGNATURE
       
        // format base string and key for oauth_signature
        $signature = form_signature($request, $tokens);
        
        // hash signature
        $oauth_signature = hash_hmac("sha1", $signature['base_string'], $signature['key'], true);
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".$oauth_signature."\"";
        */
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".CSECRET."&".$tokens['oauth_token_secret']."\"";
    
        // format authorization header
        $request = form_header($request);
        
        // send request
        $response = send($request);
        
        // respond to error messages
        $err = substr($response['status'], 9, 3);
        
        if($err == "401")
            apologize("Sorry, you have not been authorized to access this information.");
        
        else if($err == "500")
            echo(substr($response['content'], 4, -5));
        
        // return oauth_token etc
        return $response['content'];
    }
    
    /*
     * array
     * form_signature($request, $token)
     *
     * Formats the base string and key for the 
     * oauth_signature.
     */
    /* TO DO!!!!!!!!!!!!!!
    function form_signature($request, $token)
    {
        // construct base string array
        $bs['method'] = $request->method;
        $bs['url'] = $request->url;
        //$bs['parameters'] = PARAMETERS;
        $bs['ckey'] = $request->auth['oauth_consumer_key'];
        $bs['nonce'] = $request->auth['oauth_nonce'];
        $bs['sig_method'] = $request->auth['oauth_signature_method'];
        $bs['timestamp'] = $request->auth['oauth_timestamp'];
        if($tokens != NULL && $tokens['auth_token'] == NULL)
            $bs['token'] = $tokens['oauth_token']; 
        //$bs['version'] = VERSION NEEDED?;
            
        // initialize utility strings
        $temp = NULL;
        $base_string = NULL;
        
        // reproduce string while escaping "bad" characters
        $strlen = strlen($bs);
        
        for($i = 0; $i < $strlen; $i++)
        {
            if($bs[$i] == "!" || $bs[$i] == "*" || $bs[$i] == "'" || $bs[$i] == "(" || $bs[$i] == ")" || $bs[$i] == ";" || $bs[$i] == ":" || $bs[$i] == "@" || $bs[$i] == "&" || $bs[$i] == "=" || $bs[$i] == "+" || $bs[$i] == "$" || $bs[$i] == "," || $bs[$i] == "/" || $bs[$i] == "?" || $bs[$i] == "%" || $bs[$i] == "#" || $bs[$i] == "[" || $bs[$i] == "]")
                $temp = "%".strtoupper(dechex(ord("{$bs[$i]}")));
                        
            else
                $temp = $bs[$i];
                    
            // produce final base string
            $base_string = $base_string.$temp;
        }
        
        // produce key
        if($tokens == NULL)
            $key = CKEY."&";
            
        else
            $key = CKEY."&".$tokens['oauth_token_secret'];
        
        // form associative array for formatting signature
        $signature['base_string'] = $base_string;
        $signature['key'] = $key;    
    
        return $signature;
    }*/
    
    /*
     * array
     * form_tokens($token_string, $token_string2)
     *
     * Formats an array containing the request token, access token
     * and token secret used for future get requests
     */
    
    function form_tokens($token_string, $token_string2)
    {
        // extract and insert oauth_token and oauth_token_secret
        $token_set = explode("&", $token_string, 3);
    
        $tokens['request_token'] = substr($token_set[0], 12);
        $tokens['token_secret'] = substr($token_set[1], 19);
        
        // extract and insert oauth(access)_token
        $token_set2 = explode("&", $token_string2, 2);    
        
        $tokens['access_token'] = substr($token_set2[0], 12);
        
        return $tokens;
    }
    
    /*
     * string
     * extract_id($url)
     *
     * Takes a url to a Doodle Poll and extracts and returns
     * the poll's 16-character identifier.
     */
     
    function extract_id($url)
    {
        $url = parse_url($url);
        $id = substr($url['path'], 1);
        
        return $id;
    }
    
    /*
     * XML object
     * access_poll($id, $tokens)
     *
     * Retrieves (via HTTP Request) and returns the XML 
     * encoded object including the poll information for
     * the poll identified by $id.
     */
    
    function access_poll($id, $tokens)
    {
        // variables for HTTP requests
        $oauth_nonce = rand();
        $oauth_timestamp = time() + OFFSET;
        
        // make new HttpRequest Object
        $request = new HttpRequest;
        
        // set url for request
        $request->url = URL."polls/$id";
        
        // set method for request
        $request->method = "GET";
        
        // set authorization for request
        $request->authorization = "Authorization: OAuth ";
        
        // set required authorization parameters for GET requesttoken
        $request->auth['realm'] = "realm=\"\"";
        $request->auth['oauth_token'] = "oauth_token=\"".$tokens['access_token']."\"";
        $request->auth['oauth_consumer_key'] = "oauth_consumer_key=\"".CKEY."\"";
        $request->auth['oauth_signature_method'] = "oauth_signature_method=\"PLAINTEXT\"";
        $request->auth['oauth_timestamp'] = "oauth_timestamp=\"".$oauth_timestamp."\"";
        $request->auth['oauth_nonce'] = "oauth_nonce=\"".$oauth_nonce."\"";
        
        /* FORMATTING HMAC-SHA1 SIGNATURE
       
        // format base string and key for oauth_signature
        $signature = form_signature($request, $tokens);
        
        // hash signature
        $oauth_signature = hash_hmac("sha1", $signature['base_string'], $signature['key'], true);
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".$oauth_signature."\"";
        */
         
        $request->auth['oauth_signature'] = "oauth_signature=\"".CSECRET."&".$tokens['token_secret']."\"";
    
        // format authorization header
        $request = form_header($request);
        
        // send request
        $response = send($request);
        
        // respond to error messages
        $err = substr($response['status'], 9, 3);
        
        if($err == "404")
            apologize("Sorry, this poll either doesn't exist, or is hidden. Please try again.");
        
        else if($err == "410")
            apologize("Sorry, this poll has been deleted.");
        
        else if($err == "500")
            echo(substr($response['content'], 4, -5));
        
        // return the poll as xml
        return $response['content'];
    }
    
    /*
     * bool
     * submit_poll($input)
     *
     * Forms and sends an HTTP request as defined in "Accessing
     * Polls" at http://doodle.com/xsd1/AAforRESTfulDoodle.pdf
     * Returns true if the request sent.
     */
     
        
    function submit_poll($input)
    {
        // variables for HTTP requests
        $oauth_nonce = rand();
        $oauth_timestamp = time() + OFFSET;
        
        // make new HttpRequest Object
        $request = new HttpRequest;
        
        // set url for request
        $request->url = URL."polls/{$input['id']}/participants";
        
        // set method for request
        $request->method = "POST";
        
        // add extra header before authorization
        $request->authorization = "Authorization: OAuth ";
        
        // set required authorization parameters for GET requesttoken
        $request->auth['realm'] = "realm=\"\"";
        $request->auth['oauth_token'] = "oauth_token=\"".$input['tokens']['access_token']."\"";
        $request->auth['oauth_consumer_key'] = "oauth_consumer_key=\"".CKEY."\"";
        $request->auth['oauth_signature_method'] = "oauth_signature_method=\"PLAINTEXT\"";
        $request->auth['oauth_timestamp'] = "oauth_timestamp=\"".$oauth_timestamp."\"";
        $request->auth['oauth_nonce'] = "oauth_nonce=\"".$oauth_nonce."\"";
        
        /* FORMATTING HMAC-SHA1 SIGNATURE
       
        // format base string and key for oauth_signature
        $signature = form_signature($request, $tokens);
        
        // hash signature
        $oauth_signature = hash_hmac("sha1", $signature['base_string'], $signature['key'], true);
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".$oauth_signature."\"";
        */
         
        $request->auth['oauth_signature'] = "oauth_signature=\"".CSECRET."&".$input['tokens']['token_secret']."\"";
        
        // format authorization header
        $request = form_header($request);
        
        // include xml as request data
        $request->data = $input['xml'];
        
        // send request
        $response = send($request);
        
        // respond to error messages
        $err = substr($response['status'], 9, 3);
        
        if($err == "400")
            apologize("Sorry, a name is required.");
        
        else if($err == "409")
            apologize("Sorry, row constraint has been exceeded for this poll");
        
        else if($err == "500")
            echo(substr($response['content'], 4, -5));        

        // return true if poll was created
        else if($err == "201")
            return true;
            
        // return false if an unknown error happens, and poll not submitted
        else
            return false;
    }
    
?>  
