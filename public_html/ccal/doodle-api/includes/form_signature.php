<?
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * form_signature.php 
     *
     * Defines the function that would be used to form
     * the HMAC-SHA1 encrypted signature for http
     * requests to the RESTful Doodle API.
     *
     * PLEASE NOTE FOR GRADING. Because the request
     * method is HTTPS, it is not necessary to use an 
     * HMAC-SHA1 encryption method in signing requests
     * to the Doodle API. PLAINTEXT is more than adequate.
     * I did, however, want to learn how to sign the
     * requests using the HMAC-SHA1 encryption method,
     * for the sake of just learning how to do it, and
     * to widen the scope of my project. I believe that the
     * code below works, for I have used it to form
     * successful HTTP requests to Vimeo's API. However,
     * for some reason, even though I am using all of
     * the parameters required for a 2-legged OAuth
     * request to the Doodle API, (as defined in their
     * own specification) using this signature method
     * returns an error response from the Doodle API.
     * As such, I have used PLAINTEXT as my signature
     * method in the functional code. Even though the
     * following code has not been used in the functionality
     * of my program, I believe that it is not broken, and
     * for this reason, thought it would add to the scope
     * of my project if I included it.
     * I have also included the authorization parameters
     * that would have been different if using the HMAC-
     * SHA1 method in each of the requests. This is
     * located below the following function.
     * Finally, I have included, for your convenience
     * the specification from OAuth for formatting the
     * request signiture.
     *
     * PS. I have also emailed Doodle about this issue.
     * I have included this message below as well, as
     * I believe it is important to this topic.
     *
     * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        
    /*
     * string
     * form_signature($request, $tokens)
     *
     * Takes the request parameters required for each
     * of the RESTful Doodle API HTTP requests and
     * formats the request signature using the
     * HMAC-SHA1 encryption method as defined in the 
     * OAuth 1.0 protocol at
     * "http://tools.ietf.org/html/rfc5849#section-3.4"
     *
     */
    
    function form_signature($request, $tokens)
    {
        /* STEP 1: Normalize Request Parameters */
            
            // collect parameters alphebetically
            $pars['ckey'] = $request->auth['oauth_consumer_key'];
            $pars['nonce'] = $request->auth['oauth_nonce'];
            $pars['sig_meth'] = $request->auth['oauth_signature_method'];
            $pars['timestamp'] = $request->auth['oauth_timestamp'];
            if(isset($tokens))
            {
                // if accessing information
                if(isset($tokens['access_token']))
                {    
                    $pars['token'] = "oauth_token=\"".$tokens['access_token']."\"";
                    $token_secret = $tokens['token_secret'];
                }
                    
                // if getting access token
                else
                {
                    $pars['token'] = "oauth_token=\"".$tokens['oauth_token']."\"";
                    $token_secret = $tokens['oauth_token_secret'];
                }
            }
            // get rid of quotes
            $pars = str_replace('"', NULL, $pars);
        
            // initialize parameter string
            $par_string = NULL;
        
            // concatenate values into one string
            foreach($pars as $key => $value)
                $par_string .= "&".$value;
                
            // get rid of preceeding "&"
            $par_string = substr($par_string, 1);
        
        /* STEP 2: Construct Request URL */
        
            // remember url (already formatted as "https://example.com/resourse")
            $url = $request->url;
        
        /* STEP 3: Concatenate Request Elements into Signature Base String */
        
            // 1st element: HTTP request method (already encoded)    
            $method = $request->method;
        
            // 2nd element: request URL (needs to be encoded)
            $url = urlencode($url);
        
            // 3rd element: normalized request parameters (needs to be encoded)
            $par_string = urlencode($par_string);
        
            // concatenate 3 elements separated by "&"
            $base_string = $method."&".$url."&".$par_string;
            
        /* STEP 4: Generate encryption key */
            
            // make sure consumer secret is encoded
            $consumer_secret = urlencode(CSECRET);
            
            // key is consumer secret and token secret concatenated with "&"
            if(isset($tokens))
            {    
                // make sure token secret is encoded
                $token_secret = urlencode($token_secret);
            
                // form key
                $key = $consumer_secret."&".$token_secret;
            }
            
            // if getting request token, no oauth token yet
            else
                $key = $consumer_secret."&";
                
        /* STEP 5: HMAC-SHA1 */        
            
            // hash signature
            $signature = hash_hmac("SHA1", $base_string, $key, true);
            
            // signature must be base64 encoded
            $signature = base64_encode($signature);
            
            // signature must be then url encoded
            $signature = urlencode($signature);
            
        /* Signature has been created. */
            
            // format signature as request parameter
            $signature = "oauth_signature=\"$signature\"";
            
            return $signature;
    }
 
    /*
     * Modified request parameters for access_poll()
     */
     
     $request->auth['oauth_signature_method'] = "oauth_signature_method=\"HMAC-SHA1\"";
     $request->auth['oauth_signature'] = form_signature($request, $tokens);
     
     /*
      * Modified request parameters for access_token()
      */
     
     $request->auth['oauth_signature_method'] = "oauth_signature_method=\"HMAC-SHA1\"";
     $request->auth['oauth_signature'] = form_signature($request, $tokens);
     
     /*
      * Modified request parameters for request_token()
      */
     
     $request->auth['oauth_signature_method'] = "oauth_signature_method=\"HMAC-SHA1\"";
     $request->auth['oauth_signature'] = form_signature($request, NULL);
     
     /*
      * Modified request parameters for submit_poll()
      */
     
     $request->auth['oauth_signature_method'] = "oauth_signature_method=\"HMAC-SHA1\"";
     $request->auth['oauth_signature'] = form_signature($request, $input['tokens']);

?>

/* * * * * * * * * * * * * * * * * * * * * * * * *
 *
 * The following is my email to Doodle about the HMAC
 * encription issue.
 *
 * * * * * * * * * * * * * * * * * * * * * * * * */
 -------------------------------------------------------------------------
To whom it may concern.

I am using the Doodle API to create a program that pre-populates a doodle poll 
by checking with conflicts in one's google calendar. I have previous experience 
with signing Oauth requests and obtaining information from APIs, etc. I have 
gotten the API to work through a PLAINTEXT signature, but for some reason, I 
cannot get it to work with an HMAC-SHA1 encrypted signature. With the following 
http request:

GET /api1/oauth/requesttoken HTTP/1.1
 Host: doodle.com
 Content-type: text/plain 
 Content-length: "0"
 Authorization: OAuth realm="",oauth_consumer_key="nn6lzq3agxbuldi84pjp1h28gx7615cf",oauth_signature_method="HMAC-SHA1",oauth_timestamp="1325240992",oauth_nonce="287791575",oauth_signature="fx3PUXgP9n6zgy87%2BKON%2F6bmq9o%3D"
 Connection: close

I receive an error response of an invalid password. I am assuming that my base 
string and/or key for hashing the signature are invalid, although I'm not sure 
what is wrong.

The following are these values that I formatted for forming the signature.

[base_string] => GET&https%3A%2F%2Fdoodle.com%2Fapi1%2Foauth%2Frequesttoken&oauth_consumer_key%3Dnn6lzq3agxbuldi84pjp1h28gx7615cf%26oauth_nonce%3D287791575%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D1325240992

[key] => wxjss3haigsw2poypxdgs74g7gpbgk3m&

I have tried many things to fix this, for example, a callback url of "oob" 
(however I'm not accessing "/users" so I only need to use 2 legged OAuth, so I 
shouldn't need it anyway) as well as including the OAuth version (but that's 
optional, so it should work either way), etc. so I'm not sure what the problem 
is.

Is there any way that you could help me in fixing this? Your API is very useful 
and helpful, and I would really like to be able to use it for my program. I 
would really appreciate the help.

Thank you so much.

Sincerely,
Robert Flitsch
 
 /* * * * * * * * * * * * * * * * * * * * * * * * *
 *
 * The following is the OAuth Specification that I
 * followed in formatting the HMAC-SHA1 signature
 * created by form_signature() and the PLAINTEXT
 * signature used in the functional code.
 *
 * * * * * * * * * * * * * * * * * * * * * * * * */                        
            The OAuth 1.0 Protocol

3.4. Signature .................................................18
   3.4.1. Signature Base String ..............................18
   3.4.2. HMAC-SHA1 ..........................................25
   3.4.3. RSA-SHA1 ...........................................25
   3.4.4. PLAINTEXT ..........................................26

3.4. Signature


   OAuth-authenticated requests can have two sets of credentials: those
   passed via the "oauth_consumer_key" parameter and those in the
   "oauth_token" parameter.  In order for the server to verify the
   authenticity of the request and prevent unauthorized access, the
   client needs to prove that it is the rightful owner of the
   credentials.  This is accomplished using the shared-secret (or RSA
   key) part of each set of credentials.

   OAuth provides three methods for the client to prove its rightful
   ownership of the credentials: "HMAC-SHA1", "RSA-SHA1", and
   "PLAINTEXT".  These methods are generally referred to as signature
   methods, even though "PLAINTEXT" does not involve a signature.  In
   addition, "RSA-SHA1" utilizes an RSA key instead of the shared-
   secrets associated with the client credentials.

   OAuth does not mandate a particular signature method, as each
   implementation can have its own unique requirements.  Servers are
   free to implement and document their own custom methods.
   Recommending any particular method is beyond the scope of this
   specification.  Implementers should review the Security
   Considerations section (Section 4) before deciding on which method to
   support.

   The client declares which signature method is used via the
   "oauth_signature_method" parameter.  It then generates a signature
   (or a string of an equivalent value) and includes it in the
   "oauth_signature" parameter.  The server verifies the signature as
   specified for each method.

   The signature process does not change the request or its parameters,
   with the exception of the "oauth_signature" parameter.

3.4.1. Signature Base String


   The signature base string is a consistent, reproducible concatenation
   of several of the HTTP request elements into a single string.  The
   string is used as an input to the "HMAC-SHA1" and "RSA-SHA1"
   signature methods.

   The signature base string includes the following components of the
   HTTP request:

   o  The HTTP request method (e.g., "GET", "POST", etc.).

   o  The authority as declared by the HTTP "Host" request header field.




Hammer-Lahav                  Informational                    [Page 18]

 
RFC 5849                        OAuth 1.0                     April 2010


   o  The path and query components of the request resource URI.

   o  The protocol parameters excluding the "oauth_signature".

   o  Parameters included in the request entity-body if they comply with
      the strict restrictions defined in Section 3.4.1.3.

   The signature base string does not cover the entire HTTP request.
   Most notably, it does not include the entity-body in most requests,
   nor does it include most HTTP entity-headers.  It is important to
   note that the server cannot verify the authenticity of the excluded
   request components without using additional protections such as SSL/
   TLS or other methods.

3.4.1.1. String Construction


   The signature base string is constructed by concatenating together,
   in order, the following HTTP request elements:

   1.  The HTTP request method in uppercase.  For example: "HEAD",
       "GET", "POST", etc.  If the request uses a custom HTTP method, it
       MUST be encoded (Section 3.6).

   2.  An "&" character (ASCII code 38).

   3.  The base string URI from Section 3.4.1.2, after being encoded
       (Section 3.6).

   4.  An "&" character (ASCII code 38).

   5.  The request parameters as normalized in Section 3.4.1.3.2, after
       being encoded (Section 3.6).

   For example, the HTTP request:

     POST /request?b5=%3D%253D&a3=a&c%40=&a2=r%20b HTTP/1.1
     Host: example.com
     Content-Type: application/x-www-form-urlencoded
     Authorization: OAuth realm="Example",
                    oauth_consumer_key="9djdj82h48djs9d2",
                    oauth_token="kkk9d7dh3k39sjv7",
                    oauth_signature_method="HMAC-SHA1",
                    oauth_timestamp="137131201",
                    oauth_nonce="7d8f3e4a",
                    oauth_signature="bYT5CMsGcbgUdFHObYMEfcx6bsw%3D"

     c2&a3=2+q




Hammer-Lahav                  Informational                    [Page 19]

 
RFC 5849                        OAuth 1.0                     April 2010


   is represented by the following signature base string (line breaks
   are for display purposes only):

     POST&http%3A%2F%2Fexample.com%2Frequest&a2%3Dr%2520b%26a3%3D2%2520q
     %26a3%3Da%26b5%3D%253D%25253D%26c%2540%3D%26c2%3D%26oauth_consumer_
     key%3D9djdj82h48djs9d2%26oauth_nonce%3D7d8f3e4a%26oauth_signature_m
     ethod%3DHMAC-SHA1%26oauth_timestamp%3D137131201%26oauth_token%3Dkkk
     9d7dh3k39sjv7

3.4.1.2. Base String URI


   The scheme, authority, and path of the request resource URI [RFC3986]
   are included by constructing an "http" or "https" URI representing
   the request resource (without the query or fragment) as follows:

   1.  The scheme and host MUST be in lowercase.

   2.  The host and port values MUST match the content of the HTTP
       request "Host" header field.

   3.  The port MUST be included if it is not the default port for the
       scheme, and MUST be excluded if it is the default.  Specifically,
       the port MUST be excluded when making an HTTP request [RFC2616]
       to port 80 or when making an HTTPS request [RFC2818] to port 443.
       All other non-default port numbers MUST be included.

   For example, the HTTP request:

     GET /r%20v/X?id=123 HTTP/1.1
     Host: EXAMPLE.COM:80

   is represented by the base string URI: "http://example.com/r%20v/X".

   In another example, the HTTPS request:

     GET /?q=1 HTTP/1.1
     Host: www.example.net:8080

   is represented by the base string URI:
   "https://www.example.net:8080/".

3.4.1.3. Request Parameters


   In order to guarantee a consistent and reproducible representation of
   the request parameters, the parameters are collected and decoded to
   their original decoded form.  They are then sorted and encoded in a
   particular manner that is often different from their original
   encoding scheme, and concatenated into a single string.



Hammer-Lahav                  Informational                    [Page 20]

 
RFC 5849                        OAuth 1.0                     April 2010


3.4.1.3.1. Parameter Sources


   The parameters from the following sources are collected into a single
   list of name/value pairs:

   o  The query component of the HTTP request URI as defined by
      [RFC3986], Section 3.4.  The query component is parsed into a list
      of name/value pairs by treating it as an
      "application/x-www-form-urlencoded" string, separating the names
      and values and decoding them as defined by
      [W3C.REC-html40-19980424], Section 17.13.4.

   o  The OAuth HTTP "Authorization" header field (Section 3.5.1) if
      present.  The header's content is parsed into a list of name/value
      pairs excluding the "realm" parameter if present.  The parameter
      values are decoded as defined by Section 3.5.1.

   o  The HTTP request entity-body, but only if all of the following
      conditions are met:

      *  The entity-body is single-part.

      *  The entity-body follows the encoding requirements of the
         "application/x-www-form-urlencoded" content-type as defined by
         [W3C.REC-html40-19980424].

      *  The HTTP request entity-header includes the "Content-Type"
         header field set to "application/x-www-form-urlencoded".

      The entity-body is parsed into a list of decoded name/value pairs
      as described in [W3C.REC-html40-19980424], Section 17.13.4.

   The "oauth_signature" parameter MUST be excluded from the signature
   base string if present.  Parameters not explicitly included in the
   request MUST be excluded from the signature base string (e.g., the
   "oauth_version" parameter when omitted).















Hammer-Lahav                  Informational                    [Page 21]

 
RFC 5849                        OAuth 1.0                     April 2010


   For example, the HTTP request:

       POST /request?b5=%3D%253D&a3=a&c%40=&a2=r%20b HTTP/1.1
       Host: example.com
       Content-Type: application/x-www-form-urlencoded
       Authorization: OAuth realm="Example",
                      oauth_consumer_key="9djdj82h48djs9d2",
                      oauth_token="kkk9d7dh3k39sjv7",
                      oauth_signature_method="HMAC-SHA1",
                      oauth_timestamp="137131201",
                      oauth_nonce="7d8f3e4a",
                      oauth_signature="djosJKDKJSD8743243%2Fjdk33klY%3D"

       c2&a3=2+q

   contains the following (fully decoded) parameters used in the
   signature base sting:

               +------------------------+------------------+
               |          Name          |       Value      |
               +------------------------+------------------+
               |           b5           |       =%3D       |
               |           a3           |         a        |
               |           c@           |                  |
               |           a2           |        r b       |
               |   oauth_consumer_key   | 9djdj82h48djs9d2 |
               |       oauth_token      | kkk9d7dh3k39sjv7 |
               | oauth_signature_method |     HMAC-SHA1    |
               |     oauth_timestamp    |     137131201    |
               |       oauth_nonce      |     7d8f3e4a     |
               |           c2           |                  |
               |           a3           |        2 q       |
               +------------------------+------------------+

   Note that the value of "b5" is "=%3D" and not "==".  Both "c@" and
   "c2" have empty values.  While the encoding rules specified in this
   specification for the purpose of constructing the signature base
   string exclude the use of a "+" character (ASCII code 43) to
   represent an encoded space character (ASCII code 32), this practice
   is widely used in "application/x-www-form-urlencoded" encoded values,
   and MUST be properly decoded, as demonstrated by one of the "a3"
   parameter instances (the "a3" parameter is used twice in this
   request).








Hammer-Lahav                  Informational                    [Page 22]

 
RFC 5849                        OAuth 1.0                     April 2010


3.4.1.3.2. Parameters Normalization


   The parameters collected in Section 3.4.1.3 are normalized into a
   single string as follows:

   1.  First, the name and value of each parameter are encoded
       (Section 3.6).

   2.  The parameters are sorted by name, using ascending byte value
       ordering.  If two or more parameters share the same name, they
       are sorted by their value.

   3.  The name of each parameter is concatenated to its corresponding
       value using an "=" character (ASCII code 61) as a separator, even
       if the value is empty.

   4.  The sorted name/value pairs are concatenated together into a
       single string by using an "&" character (ASCII code 38) as
       separator.

   For example, the list of parameters from the previous section would
   be normalized as follows:

                                 Encoded:

               +------------------------+------------------+
               |          Name          |       Value      |
               +------------------------+------------------+
               |           b5           |     %3D%253D     |
               |           a3           |         a        |
               |          c%40          |                  |
               |           a2           |       r%20b      |
               |   oauth_consumer_key   | 9djdj82h48djs9d2 |
               |       oauth_token      | kkk9d7dh3k39sjv7 |
               | oauth_signature_method |     HMAC-SHA1    |
               |     oauth_timestamp    |     137131201    |
               |       oauth_nonce      |     7d8f3e4a     |
               |           c2           |                  |
               |           a3           |       2%20q      |
               +------------------------+------------------+











Hammer-Lahav                  Informational                    [Page 23]

 
RFC 5849                        OAuth 1.0                     April 2010


                                  Sorted:

               +------------------------+------------------+
               |          Name          |       Value      |
               +------------------------+------------------+
               |           a2           |       r%20b      |
               |           a3           |       2%20q      |
               |           a3           |         a        |
               |           b5           |     %3D%253D     |
               |          c%40          |                  |
               |           c2           |                  |
               |   oauth_consumer_key   | 9djdj82h48djs9d2 |
               |       oauth_nonce      |     7d8f3e4a     |
               | oauth_signature_method |     HMAC-SHA1    |
               |     oauth_timestamp    |     137131201    |
               |       oauth_token      | kkk9d7dh3k39sjv7 |
               +------------------------+------------------+

                            Concatenated Pairs:

                  +-------------------------------------+
                  |              Name=Value             |
                  +-------------------------------------+
                  |               a2=r%20b              |
                  |               a3=2%20q              |
                  |                 a3=a                |
                  |             b5=%3D%253D             |
                  |                c%40=                |
                  |                 c2=                 |
                  | oauth_consumer_key=9djdj82h48djs9d2 |
                  |         oauth_nonce=7d8f3e4a        |
                  |   oauth_signature_method=HMAC-SHA1  |
                  |      oauth_timestamp=137131201      |
                  |     oauth_token=kkk9d7dh3k39sjv7    |
                  +-------------------------------------+

   and concatenated together into a single string (line breaks are for
   display purposes only):

     a2=r%20b&a3=2%20q&a3=a&b5=%3D%253D&c%40=&c2=&oauth_consumer_key=9dj
     dj82h48djs9d2&oauth_nonce=7d8f3e4a&oauth_signature_method=HMAC-SHA1
     &oauth_timestamp=137131201&oauth_token=kkk9d7dh3k39sjv7









Hammer-Lahav                  Informational                    [Page 24]

 
RFC 5849                        OAuth 1.0                     April 2010


3.4.2. HMAC-SHA1


   The "HMAC-SHA1" signature method uses the HMAC-SHA1 signature
   algorithm as defined in [RFC2104]:

     digest = HMAC-SHA1 (key, text)

   The HMAC-SHA1 function variables are used in following way:

   text    is set to the value of the signature base string from
           Section 3.4.1.1.

   key     is set to the concatenated values of:

           1.  The client shared-secret, after being encoded
               (Section 3.6).

           2.  An "&" character (ASCII code 38), which MUST be included
               even when either secret is empty.

           3.  The token shared-secret, after being encoded
               (Section 3.6).

   digest  is used to set the value of the "oauth_signature" protocol
           parameter, after the result octet string is base64-encoded
           per [RFC2045], Section 6.8.

3.4.3. RSA-SHA1


   The "RSA-SHA1" signature method uses the RSASSA-PKCS1-v1_5 signature
   algorithm as defined in [RFC3447], Section 8.2 (also known as
   PKCS#1), using SHA-1 as the hash function for EMSA-PKCS1-v1_5.  To
   use this method, the client MUST have established client credentials
   with the server that included its RSA public key (in a manner that is
   beyond the scope of this specification).

   The signature base string is signed using the client's RSA private
   key per [RFC3447], Section 8.2.1:

     S = RSASSA-PKCS1-V1_5-SIGN (K, M)

   Where:

   K     is set to the client's RSA private key,

   M     is set to the value of the signature base string from
         Section 3.4.1.1, and




Hammer-Lahav                  Informational                    [Page 25]

 
RFC 5849                        OAuth 1.0                     April 2010


   S     is the result signature used to set the value of the
         "oauth_signature" protocol parameter, after the result octet
         string is base64-encoded per [RFC2045] section 6.8.

   The server verifies the signature per [RFC3447] section 8.2.2:

     RSASSA-PKCS1-V1_5-VERIFY ((n, e), M, S)

   Where:

   (n, e) is set to the client's RSA public key,

   M      is set to the value of the signature base string from
          Section 3.4.1.1, and

   S      is set to the octet string value of the "oauth_signature"
          protocol parameter received from the client.

3.4.4. PLAINTEXT


   The "PLAINTEXT" method does not employ a signature algorithm.  It
   MUST be used with a transport-layer mechanism such as TLS or SSL (or
   sent over a secure channel with equivalent protections).  It does not
   utilize the signature base string or the "oauth_timestamp" and
   "oauth_nonce" parameters.

   The "oauth_signature" protocol parameter is set to the concatenated
   value of:

   1.  The client shared-secret, after being encoded (Section 3.6).

   2.  An "&" character (ASCII code 38), which MUST be included even
       when either secret is empty.

   3.  The token shared-secret, after being encoded (Section 3.6).
   
   
