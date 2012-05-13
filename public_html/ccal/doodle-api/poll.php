<?
 
/****************************************************************
 * poll.php
 * 
 * Defines functions to handle a poll from the doodle
 * api.
 *
 ****************************************************************/

    // requirements
    require_once("doodleClient.php");
    require_once("xml.php");

    /*
     * array
     * acquire_poll($url)
     *
     * Sends HTTP request and returns response as an
     * array containing the necessary poll information
     */    
    
    function acquire_poll($url)
    {
        // acquire request token and access token for future requests
        $token_string = request_token();
        $token_string2 = access_token($token_string);
        $tokens = form_tokens($token_string, $token_string2);
        
        // retrieve poll's 16-character id
        $id = extract_id($url);
        
        // access this poll
        $poll = access_poll($id, $tokens);
        
        // format poll data
        $xml_array = parse($poll);
        
        // store tokens for future use in submitting poll
        $xml_array['tokens'] = $tokens;
        
        return $xml_array;
    }
    
    /*
     * void
     * display_poll($poll, $url)
     * 
     * Generates html to display the Doodle poll
     * on display_poll.html.
     */
     
    function display_poll($poll, $url)
    {
        // open form
        echo("<form action=\"submit_poll.php?url=$url\" method=\"post\">");
        
        // open the table
        echo("<table class=\"poll\">");
        
        // counter for participants
        $participants = 0;
        
        // get preliminary participant data
        foreach($poll['keys']['NAME'] as $key => $value)
        {
            $name[$participants] = $poll['text'][$value]['value'];    
            $participants++;
        } 
              
        // print dates row
        echo("<tr>");
        
        // print blank space before times
        echo("<td></td>");
                
        // counter for day entries
        $day = 0;
               
        // create date strings
        foreach($poll['keys']['OPTION'] as $key => $value)
        {
            // break once out of dates
            if($poll['text'][$value]['level'] != TIME_LEVEL)
                break;
            
            // update counter
            $day++;
            
            // set string value
            if(isset($poll['text'][$value]['attributes']['STARTDATETIME']))
                $string[$day] = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 0, 10);
        
            else if(isset($poll['text'][$value]['attributes']['DATETIME']))
                $string[$day] = substr($poll['text'][$value]['attributes']['DATETIME'], 0, 10);
        }

        // counter for number of days per entry
        $num_days = 1;
        
        for($i = 1, $j = 2; $i < $day; $i++, $j++)
        {
            // if multiple dates of same, continue
            if($string[$i] == $string[$j])
                $num_days++;
        
            // if last of this date, print html
            else if($string[$i] != $string[$j])
            {                
                // print html
                echo("<td class=\"date\" colspan=\"{$num_days}\">");
                echo("$string[$i]");
                echo("</td>");
                
                // reset counter
                $num_days = 1;
            }
        }
        
        // if last possible date, print html (must be last of this
        echo("<td class=\"date\" colspan=\"{$num_days}\">");
        echo("$string[$day]");
        echo("</td>");                            
        
        // close dates row
        echo("</tr>");
    
        // print time slots row
        echo("<tr>");
        
        // print number of participants
        echo("<td>");
        
        // update counter (ignore initiator)
        $participants--;
        
        echo("Participants: $participants");
        echo("</td>");
                      
        // counter for time entries
        $time = 0;
        
        // create time strings
        foreach($poll['keys']['OPTION'] as $key => $value)
        {
            // break once out of times
            if($poll['text'][$value]['level'] != TIME_LEVEL)
                break;
            
            // update counter
            $time++;
            
            // display time if only 1 time exists
            if(isset($poll['text'][$value]['attributes']['DATETIME']))
                $time_range[$time] = substr($poll['text'][$value]['attributes']['DATETIME'], 11, -3);
            
            else if(isset($poll['text'][$value]['attributes']['STARTDATETIME']))
            {
                // set start string value (save total string for later use)
                $start[$time] = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 11, -3);
            
                // set end string value
                $end[$time] = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 11, -3);
                
                // set time range value
                $time_range[$time] = $start[$time]." - <br>".$end[$time];
            }
            
            else
                apologize("Sorry, an error occurred. Please try again.");
        }
        
        for($i = 1; $i <= $time; $i++)
        {
                echo("<td class=\"time\">");
                echo("$time_range[$i]");
                echo("</td>");
        }
            
        // close time slots row
        echo("</tr>");
               
        // check if any participants exist
        if($participants != 0)
        {   
            // initialize counters
            $checks = 1;
            $participant_num = 1;
             
            // print out previous submissions
            foreach($poll['keys']['OPTION'] as $key => $value)
            {
                // check if this "option" array is for a participant and not a time
                if($poll['text'][$value]['level'] == PARTICIPANT_LEVEL)
                {
                        
                    if($checks <= $time)
                    {
                        // set array value of response
                        $participant[$participant_num][$checks] = $poll['text'][$value]['value'];
                        
                        // update counter
                        $checks++;
                    }
                    
                    else
                    {
                        // update counters
                        $checks = 1;
                        $participant_num++;
                        
                        // set array value of response
                        $participant[$participant_num][$checks] = $poll['text'][$value]['value'];
                    
                        // add to checks
                        $checks++;
                    }
                }
            }
            
            // update counter
            $checks--;
            
            // initialize counter for 'yesses'
            for($collumn = 1; $collumn <= $time; $collumn++)
                $yes_num[$collumn] = 0;
            
            // error check for api limitations
            $posts = 0;
            
            // loop through participants
            for($i = 1; $i <= $participant_num; $i++)
            {
                // open participants row
                echo("<tr>");
                
                // print participant name
                echo("<td class=\"name\">");
                echo("$name[$i]");
                echo("</td>");
                
                for($j = 1; $j <= $time; $j++)
                {
                    // update counter
                    $posts++;
                
                    // print html
                    echo("<td class=\"check\">");
                    
                    // check (or not) availibility
                    if($participant[$i][$j] == 1)
                    {
                        echo("<img src=\"extras/check.jpg\" alt=\"X\">");
                        
                        // add to counter for number of 'yesses'
                        $yes_num[$j]++;
                    }
                 
                    else
                        echo("<img src=\"extras/x.jpg\" alt=\"O\">");
                     
                    echo("</td>");
                }
                
                // close participants row
                echo("</tr>");
                
                // break if post limit reached (prevent printing server error messages)
                if($posts >= POST_LIM)
                    break;
            }
        }
           
        // print out blank submission field
        echo("<tr>");
        echo("<td>");
        
        // print text field
        echo("<input class=\"poll_box\" name=\"name\" type=\"text\" value=\"Your Name\">");
        echo("</td>");
        
        // print check boxes
        for($i = 1; $i <= $time; $i++)
        {
            echo("<td class=\"checkbox\">");
                
            // initialize checkbox variable
            $checked = NULL;
                
            // decide whether box is checked or not
            if(isset($_GET["option$i"]))
            {
                // if prepopulated, change value of $selected
                if(htmlspecialchars($_GET["option$i"]) == "1")
                    $checked = "checked=\"checked\"";
            }
                
            echo("<input class=\"box\" type=\"checkbox\" name=\"option$i\" {$checked} value=\"1\">");
            echo("</td>");
        }          
        
        // close row
        echo("</tr>");
        
        // print dates at bottom if more than 10 participants
        if($participants > 10)
        {
            // print time slots row again
            echo("<tr>");
        
            // print blank space
            echo("<td>");
            echo("</td>");
                      
            for($i = 1; $i <= $time; $i++)
            {
                    echo("<td class=\"time\">");
                    echo("$time_range[$i]");
                    echo("</td>");
            }
                
            // close time slots row
            echo("</tr>");
        
            // print dates row again
            echo("<tr>");
        
            // print blank space before dates
            echo("<td></td>");
                    
            // counter for number of days per entry
            $num_days = 1;
            
            for($i = 1, $j = 2; $i < $day; $i++, $j++)
            {
                // if multiple dates of same, continue
                if($string[$i] == $string[$j])
                    $num_days++;
            
                // if last of this date, print html
                else if($string[$i] != $string[$j])
                {                
                    // print html
                    echo("<td class=\"date\" colspan=\"{$num_days}\">");
                    echo("$string[$i]");
                    echo("</td>");
                    
                    // reset counter
                    $num_days = 1;
                }
            }
        
            // if last possible date, print html (must be last of this
            echo("<td class=\"date\" colspan=\"{$num_days}\">");
            echo("$string[$day]");
            echo("</td>");                            
            
            // close dates row
            echo("</tr>");
        }
        
        // print out number of 'yesses' per time
        echo("<tr>");
        
        // print blank space
        echo("<td>");
        echo("</td>");
        
        // print values
        for($i = 1; $i <= $time; $i++)
        {
            echo("<td class=\"yesses\">");
            echo("$yes_num[$i]");
            echo("</td>");
        }
        
        // close row
        echo("</tr>");  
            
        // print blank space, then...
        echo("<tr>");
        echo("<td colspan=\"$time\">");
        echo("</td>");
        
        // print submit button
        echo("<td>");
        echo("<input class=\"poll_submit\" type=\"submit\" value=\"Save\">");
        echo("</td>");
        echo("</tr>");
            
        // close tags
        echo("</table>");
        echo("</form>");
        
        // print disclaimer if neccessary
        if($posts >= POST_LIM)
        {
            echo("<div class=\"disclaimer\">");
            echo(DISCLAIMER);
            echo("<a href=\"$url\"> $url </a>. ");
            echo(THANKS);
            echo("</div>");
        }
    }   
?>
