<?
    session_start();
    
    
    //include needed files
    require_once "google-api-php-client/src/apiClient.php";
    require_once "google-api-php-client/src/contrib/apiCalendarService.php";

    
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
    $counter=0;
    
    //retrieve primary calendar
    $events = $service->events->listEvents('primary');
       
    //save events
    while(true) 
    {
        foreach ($events->getItems() as $event)
        {
        
        //save each event
        if($event->getStart() != null)
            {
            $start = ($event->getStart()->getdateTime());
            $year = intval($start[0].$start[1].$start[2].$start[3]) ; 
            $month = intval($start[5].$start[6]) ;
            $day = intval($start[8].$start[9]) ;
            $hour = intval($start[11].$start[12]) ;
            
            //create arrays with similair index
            
            $events_list= array();
            $events_list_start_unixtime[$counter] =  mktime($hour,0,0,$month,$day,$year);
            $events_list_start_time[$counter] =  getdate(mktime($hour,0,0,$month,$day,$year));
            $events_list_summary[$counter] =  $event->getSummary();
            $events_recurring[$counter]= $event->getRecurrence();
            
            //increment number of events
            $counter ++;
            
            }
        }
    
        
        // reset counter
        $counter = 0;
            
        foreach ($events->getItems() as $event)
        {
            //save each event end
            if($event->getEnd() != null)
            {
                $end = ($event->getEnd()->getdateTime());
                $year = intval($end[0].$end[1].$end[2].$end[3]); 
                $month = intval($end[5].$end[6]);
                $day = intval($end[8].$end[9]);
                $hour = intval($end[11].$end[12]);
            
                //create arrays with similair index
            
                $events_list= array();
                $events_list_end_unixtime[$counter] =  mktime($hour,0,0,$month,$day,$year);
                $events_list_end_time[$counter] =  getdate(mktime($hour,0,0,$month,$day,$year));
            
                //increment number of events
                $counter ++;
            
            }

      }
      $pageToken = $events->getNextPageToken();
      
      if ($pageToken) {
        $optParams = array('pageToken' => $pageToken);
        $events = $service->events->listEvents('primary', $optParams);
      } 
      
      else 
      {
        break;
      }
    }

    /*// if $doodle exists, then this code has been included in check_times
    if(isset($doodle))
    {
    dump($doodle_start);
    /*// re-format date and time for google events
    
    // loop through each doodle event
    foreach($doodle_start)
    {
        // boolean for matched event
        $matched = false;
        
        // set doodle start and end values (both time and date)
        
        // loop through each google event
        foreach()
        {
            // set google start and end values
            
            // if the dates match and intersection not yet found...
            {
            
                // intersection possibility 1
            
                // intersection possibility 2
            
                // intersection possibility 3
            
                // intersection possibility 4
            
                // intersection possibility 5
            
                // intersection possibility 6
            
            }
        }
        
        // if no intersection matched, then this doodle event is free
    }
    
    // loop and create get requests that fill the poll for redirect!!!!!!!
    redirect("display_poll.php?test=1");*/
    }
?>


<!DOCTYPE html>

<html>
  <head>
  <style type="text/css" media="screen">   
  </style>
  </head>
  <body>
    <div id ='title' style="text-align: center;">
        Home Page
    </div>
    
    <div>
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
          $warning  = "<p style='font-weight:bold;text-align:center;'>Please click below to display your event!<br><a href= index.php> Add Selected Event </a></p><br><br>";
        else
          $warning="";  
          
        //display site links
        echo("<p style='text-align:center;'> <a href= events.html> See Events </a>-----<a href= calendars.html>     See Calendars </a>-----<a href= doodle_tool.php>     Doodle Tool </a><br></p>".$warning ."<p style='text-align:center;'>");
        
        //date of the displayed monday
        $displaystr = date("l jS \of F Y", $monday ); 
        
        //
        if($_SESSION['week']==0)
            echo("This is the current week<br>"); 
        
        /* display date for users*/
        echo ("Week Starts on: $displaystr </p>"); 
        echo("<br>");  
        
        //display links to increase, decrease or reset week
        echo("<p style='text-align:left;'><a  href= index.php?week=-1 align='left'> Previous Week </a></p>");
        echo("<p style='text-align:center;'><a href= index.php> This Week </a></p>");
        echo("<p style='text-align:right;'><a href= index.php?week=1> Next Week </a></p>");
        ?>
    </div>
    <br>
    <br>
    <div style = "text-align: center;">
      <table border="1" style = "margin-left: auto ; margin-right: auto">
          <?
            //prep top row of calendar
            echo("<tr style='width : 5000px;'>");
            echo("<td style='width : 200px; text-align: center;'> Day\Time </td>");
            for( $i=0  ; $i<24 ; $i++)
            echo("<td style='width : 200px; text-align: center;'> $i  :00 </td>");
              
            

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
                    echo("<td id='".$i.$j."'style='width: 200px; height: 200px; text-align: center;'> ");
                    
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
  </body>
</html>
