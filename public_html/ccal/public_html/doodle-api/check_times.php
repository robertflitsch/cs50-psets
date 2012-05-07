<?

/****************************************************************
 * check_times.php
 * 
 * Defines functionality (between this and index.php)
 * to print out the html for a preppulater form on
 *  display_poll.php.
 *
 ****************************************************************/

    /*
     * void
     * check_times($poll, $url)
     *
     * Sets up index.php to generate a prepopulated doodle
     * poll, and redirect to 
     */    
    
    function check_times($poll, $url)
    {
        // initialize counter
        $time = 0;
    
        // create time strings
        foreach($poll['keys']['OPTION'] as $key => $value)
        {
            // update counters
            $time++;
            
            // break once out of dates
            if($poll['text'][$value]['level'] != TIME_LEVEL)
            {    
                // correct $day variable
                $time--;
             
                break;
            }
            
            // set start and end values
            $start_year = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 0, 4);
            $start_month = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 5, 2);
            $start_day = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 8, 2);
            $start_hours = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 11, -6);
            $start_minutes = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 14, -3);
            
            $end_year = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 0, 4);
            $end_month = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 5, 2);
            $end_day = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 8, 2);
            $end_hours = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 11, -6);
            $end_minutes = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 14, -3); 
            
            // reformat times to unix time
            $doodle['start'][$time] = mktime($start_hours, $start_minutes, 0, $start_month, $start_day, $start_year);
            $doodle['end'][$time] = mktime($end_hours, $end_minutes, 0, $end_month, $end_day, $end_year);
        }
        
        // require index.php to get calendar information
        require_once("index.php");
    }
    
    
?>
