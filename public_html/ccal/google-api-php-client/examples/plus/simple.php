<?php
require_once '../../src/apiClient.php';
require_once '../../src/contrib/apiPlusService.php';
session_start();

$client = new apiClient();
$client->setApplicationName("Google+ PHP Starter Application");

// Visit https://code.google.com/apis/console?api=plus to generate your
// client id, client secret, and to register your redirect uri.
// $client->setClientId('insert_your_oauth2_client_id');
// $client->setClientSecret('insert_your_oauth2_client_secret');
// $client->setRedirectUri('insert_your_oauth2_redirect_uri');
// $client->setDeveloperKey('insert_your_developer_key');
$plus = new apiPlusService($client);

if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_GET['code'])) {
  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    die("The session state ({$_SESSION['state']}) didn't match the state parameter ({$_GET['state']})");
  }
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $me = $plus->people->get('me');
  print "Your Profile: <pre>" . print_r($me, true) . "</pre>";

  $params = array('maxResults' => 100);
  $activities = $plus->activities->listActivities('me', 'public', $params);
  print "Your Activities: <pre>" . print_r($activities, true) . "</pre>";
  
  $params = array(
    'orderBy' => 'best',
    'maxResults' => '20',
  );
  $results = $plus->activities->search('Google+ API', $params);
  foreach($results['items'] as $result) {
    print "Search Result: <pre>{$result['object']['content']}</pre>\n";
  }

  // The access token may have been updated lazily.
  $_SESSION['token'] = $client->getAccessToken();
} else {
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
}