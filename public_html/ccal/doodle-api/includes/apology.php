
<!DOCTYPE html>

<html>

  <head>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <title>Crimson Calendar: Sorry!</title>
  </head>

  <body>
    <div class="logo">
      <a href="index.php"><img id ="logo" src="extras/harvard-logo.jpg" alt="Crimson Calendar"><h1 class="logo_text">Crimson Calendar</h1></a>
    </div>

    <div class="border_hor"></div>
    <div class="border_ver"></div>

    <div class="links">
      <h1><a class="link_box" href= "index.php"> Home </a>
      <a class="link_box" href= "events.html"> Events </a>
      <a class="link_box" href= "calendars.html"> Calendars </a>
      <a class="link_box" href= "doodle_tool.html"> Doodle Tool </a>
      </h1>
    </div>

    <div class="body">
      <div class="title">
        <h2>Sorry!</h2>
      </div>
      <div class="submit_message">
        <h3><?=$message?></h3>
        <h3><a href="javascript:history.go(-1);">Back</a></h3>
      </div>
    </div>
  </body>

</html>
