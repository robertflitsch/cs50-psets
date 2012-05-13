<?
    /*****************************************************
     * index.php
     *
     * Displays the home page for Crimson Calendar.
     * Also contains some of the code for pre-populating
     * a doodle poll.
     *
     * PLEASE NOTE FOR GRADING:
     * The code before the next "?>" has been somewhat
     * edited and added to by Bobby. Much of it has
     * been typed by Michael, but the code between the
     * sections commented as such have been edited or
     * added to by Bobby. Additionally all of the css
     * for all the pages (minus the calendar display
     * on the home page: I didn't want to mess with
     * Michael's code when I wasn't with him) was done
     * by Bobby.
     *
     *****************************************************/
    
    session_start();
    
    
    //include needed files
    require_once "google-api-php-client/src/apiClient.php";
    require_once "google-api-php-client/src/contrib/apiCalendarService.php";
    require_once("doodle-api/includes/helpers.php");
    //create new API request
    $apiClient = new apiClient();
    $apiClient->setUseObjects(true);
    $service = new apiCalendarService($apiClient);
    
    //set global for use later
    $_SESSION["service"]= $service;
    
    //check for current Authentication token
    if (isset($_SESSION['oauth_access_token'])) 
    {
    $apiClient->setAccessToken($_SESSION['oauth_access_token']);
    } 
    else
    {
    $token = $apiClient->authenticate();
    $_SESSION['oauth_access_token'] = $token;
    }   
    

    //check for increase week input
    if (isset($_GET['week'])) 
    {
        //record total weeks increase
        $week_to_add=$_GET['week'];
        $_SESSION['week'] = $_SESSION['week'] + $week_to_add;
    }
    //else set weeks increased to zero
    else
       $_SESSION['week'] = 0;
       
 
    //to record the number of events
    $counter = 0;
    
    //retrieve primary calendar
    $events = $service->events->listEvents('primary');
   
/* * * * * * * * * * * * * * * * * * * * * *
 * Start of Bobby's editing and additions  *
 * * * * * * * * * * * * * * * * * * * * * */
    
    //save event beginnings
    while(true) 
    {
        foreach ($events->getItems() as $key => $event)
        {
            //save each event start
            if($event->getStart() != NULL)
            {
                $start = ($event->getStart()->getdateTime());
                $year = intval($start[0].$start[1].$start[2].$start[3]) ; 
                $month = intval($start[5].$start[6]) ;
                $day = intval($start[8].$start[9]) ;
                $hour = intval($start[11].$start[12]) ;
                $minutes = intval($start[14].$start[15]);

                //create arrays with similair index
                $events_list= array();
                $events_list_start_unixtime[$counter] =  mktime($hour,0,0,$month,$day,$year);
                $events_list_start_time[$counter] =  getdate(mktime($hour,0,0,$month,$day,$year));
                $events_list_summary[$counter] =  $event->getSummary();
                $events_recurring[$counter]= $event->getRecurrence();
                $events_list_start_unixtime_for_doodle[$counter] =  mktime($hour,$minutes,0,$month,$day,$year);
            }
            
            //save each event end
            if($event->getEnd() != NULL)
            {
                $end = ($event->getEnd()->getdateTime());
                $endyear = intval($end[0].$end[1].$end[2].$end[3]) ; 
                $endmonth = intval($end[5].$end[6]) ;
                $endday = intval($end[8].$end[9]) ;
                $endhour = intval($end[11].$end[12]) ;
                $endminutes = intval($end[14].$end[15]);
                
                //create arrays with similair index
                $events_list= array();
                $events_list_end_unixtime[$counter] =  mktime($endhour,$endminutes,0,$endmonth,$endday,$endyear);
                
                //increment number of events
                $counter ++;
            }
        }
    
        $pageToken = $events->getNextPageToken();
        
        if ($pageToken)
        {
            $optParams = array('pageToken' => $pageToken);
            $events = $service->events->listEvents('primary', $optParams);
        } 
      
        else 
            break;
    }
    
    // if $doodle exists, then this code has been included in check_times
    if(isset($doodle))
    {
        // loop through each doodle event
        foreach($doodle['start'] as $dkey => $dvalue)
        {
            // boolean for matched event
            $matched = false;
            
            // set doodle start and end values (unix time)
            $ds = $dvalue;
            $de = $doodle['end']["$dkey"];
                       
            // loop through each google event
            foreach($events_list_start_unixtime_for_doodle as $gkey => $gvalue)
            {
                // set google start and end values
                $gs = $gvalue;
                $ge = $events_list_end_unixtime["$gkey"];
                
                // break loop if match found
                if($matched == true)
                    break;
                
                // intersection possibility 1
                else if($ds < $gs && $de > $gs)
                    $matched = true;
                
                // intersection possibility 2
                else if($ds == $gs)
                    $matched = true;
                
                // intersection possibility 3
                else if($ds > $gs && $ds < $ge)
                    $matched = true;
            }
            
            // if match found, event is not free
            if($matched == true)
            {
                $option[$dkey] = "0";
            }    
            
            // if no intersection matched, then this doodle event is free
            else
                $option[$dkey] = "1";
        }
        
        // initialize get string
        $get = "display_poll.php?url=$url";
        
        // loop and create get requests that fill the form upon redirect
        for($i = 1; $i <= $dkey; $i++)
           $get .= "&option$i={$option[$i]}";
        
        // redirect back to display_poll before html is printed   
        redirect("$get");
    }
    
/* * * * * * * * * * * * * * * * * * * * * * * * *
 * End of Bobby's editing and additions (except  *
 * for the css following this code)              *
 * * * * * * * * * * * * * * * * * * * * * * * * */

?>
<!DOCTYPE html>

<html>
  <head>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <title>Crimson Calendar: Home Page</title>
  </head>
  <body>
    <div class="logo">
        <a href="index.php"><img id ="logo" alt="Crimson Calendar" src="extras/harvard-logo.jpg"><h1 class="logo_text">Crimson Calendar</h1></a>
      </div>

      <div class="border_hor"></div>
      <div class="border_ver"></div>

      <div class="links">
        <h1><a class="active_link_box" href= "index.php"> Home </a>
        <a class="link_box" href= "events.html"> Events </a>
        <a class="link_box" href= "calendars.html"> Calendars </a>
        <a class="link_box" href= "doodle_tool.html"> Doodle Tool </a>
        </h1>
      </div>

      <div class="body">
        <div class="title">
          <h2>Home Page</h2>
        </div>
      
      <div class="submit_message">
        <?
        
         //find current time
         $current_time = getdate();
             
         //set unix timestamp for the first day of the week
         if($current_time["weekday"] == "Monday")
         {
             $monday= mktime(0,0,0, $current_time["mon"],$current_time["mday"],$current_time["year"] );
         }
            
         if($current_time["weekday"] == "Tuesday")
         {
             $monday=mktime(0,0,0, $current_time["mon"],$current_time["mday"]-1,$current_time["year"] );
         }
           
         if($current_time["weekday"] == "Wednesday")
         {
             $monday=mktime(0,0,0, $current_time["mon"],$current_time["mday"]-2,$current_time["year"] );;;
         }
             
         if($current_time["weekday"] == "Thursday")
         {
             $monday=mktime(0,0,0, $current_time["mon"],$current_time["mday"]-3,$current_time["year"] );;
         }
             
         if($current_time["weekday"] == "Friday")
         {
             $monday=mktime(0,0,0, $current_time["mon"],$current_time["mday"]-4,$current_time["year"] );;
         }
             
         if($current_time["weekday"] == "Saturday")
         {
             $monday=mktime(0,0,0, $current_time["mon"],$current_time["mday"]-5,$current_time["year"] );;
         }
            
         if($current_time["weekday"] == "Sunday")
         {
             $monday = mktime(0,0,0, $current_time["mon"],$current_time["mday"]-6,$current_time["year"] );
         }
        
         //account for user increasing weeks
         $monday = $monday + $_SESSION['week']*7*24*60*60;
        
        //if the user has added an event display the option to display
        if(isset($_GET['startime']))
          $warning  = "Please click below to display your event!<br><a href= index.php> Add Selected Event </a>";
        else
          $warning="";  
        
        //date of the displayed monday
        $displaystr = date("l jS \of F Y", $monday ); 
        
        // display warning(if exists)
        if($warning != null)
            echo("<h3>$warning</h3>");
        
        //
        if($_SESSION['week']==0)
            echo("<h3>This is the current week</h3>"); 
        
        // display date for users
        echo ("<h3>Week Starts on: $displaystr </h3>"); 
       
       ?>
       <h3><a class="link_box" href= "index.php?week=-1"> Previous Week </a><a class="link_box" href= "index.php"> This Week </a><a class="link_box" href= "index.php?week=1"> Next Week </a></h3>
    </div>
    <br>
    <br>
    <div style = "text-align: center;">
      <table border="1" style = "margin-left: auto ; margin-right: auto">
          <?
            //prep top row of calendar
            echo("<tr style='width : 5000px;'>");
            echo("<td style='width : 200px, text-align: center;'> Day\Time </td>");
            for( $i=0  ; $i<24 ; $i++)
            echo("<td style='width : 200px, text-align: center;'> $i  :00 </td>");
              
            

            //prep user display field
            for( $i=0  ; $i<7 ; $i++)
            {
             
                 if($i==0)
                 $wday="Monday";
                 if($i==1)
                 $wday="Tuesday";
                 if($i==2)
                 $wday="Wednesday";
                 if($i==3)
                 $wday="Thursday";
                 if($i==4)
                 $wday="Friday";
                 if($i==5)
                 $wday="Saturday";
                 if($i==6)
                 $wday="Sunday";
               
                echo("<tr style='width : 5000% px;'> <td style='width : 200px; text-align: center;'> $wday </td>");
                 
                for($j=0 ; $j<24 ; $j++)
                {
                    //print cell
                    echo("<td id='".$i.$j."' style='width: 200px; height: 200px; text-align: center;'> ");
                    
                    //prep needed cell time information
                    $cell_timestamp = $monday + ($j*(60*60)) + ($i*(24*60*60));
                    
                    $cell_date = getdate($cell_timestamp);
                    
                    $weekday_abrev = $cell_date['weekday'][0].$cell_date["weekday"][1];

                  
                     
                    //check events
                    for ($s=0 ; $s<$counter ; $s++)
                    {
                   
                    //for single events
                    if($events_recurring[$s]==null && $events_list_start_unixtime[$s] ==$cell_timestamp )
                        {
                        //display event
                        echo( $events_list_summary[$s]);
                        echo("<br>");
                        }
                    //check for recursion
                    if($events_recurring[$s]!=null &&  strpos($events_recurring[$s][0], strtoupper($weekday_abrev),20)!=false &&  $events_list_start_time[$s]["hours"] == $cell_date["hours"])
                      { 
                        //display event
                        
                        echo($events_list_summary[$s]);
                        echo("<br>");
                      }
                    }
                    echo("</td>");
                  }
                  echo("</tr>");
             }                                                                        
          ?>            
        </table>
      </div>
    </div> 
  </body>
</html>
