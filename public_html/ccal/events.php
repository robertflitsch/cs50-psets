<?
    //include needed files
    require_once "google-api-php-client/src/apiClient.php";
    require_once "google-api-php-client/src/contrib/apiCalendarService.php";
    require_once "index.php";
    

    
    //set time to input
    $time = $_GET["startime"][11].$_GET["startime"][12].$_GET["startime"][13].$_GET["startime"][14].$_GET["startime"][15]."-".$_GET["endtime"][12].$_GET["endtime"][13].$_GET["endtime"][14].$_GET["endtime"][15].$_GET["endtime"][16];
  
    //set month to input  
    $month_number = $_GET["startime"][5].$_GET["startime"][6] -1;
    $month= date("F", $month_number);

    //set day to input
    $day = $_GET["startime"][8].$_GET["startime"][9];
    
    
    //prep string
    $string = $_GET["title"]." at ".$_GET['local']." on ".$month." ".$day." ".$time;
    
    
    //create event
    $createdEvent = $service->events->quickAdd(
    'primary',
    $string);


?>
