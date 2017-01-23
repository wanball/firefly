<?php
require ("../inc/config.inc.php");

require_once  'vendor/autoload.php';

$analytics = initializeAnalytics();
$profile = getFirstProfileId($analytics);
$results = getResults($analytics, $profile);
printResults($results);

function initializeAnalytics()
{

  $client = new Google_Client(); //new Google_Client();
  $client->setApplicationName("Hello Analytics Reporting");
  $client->setAuthConfig(_KEY_FILE_LOCATION_);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new  Google_Service_Analytics($client);

  return $analytics;
}

function getFirstProfileId($analytics) {

  $accounts = $analytics->management_accounts->listManagementAccounts();

  if (count($accounts->getItems()) > 0) {
    $items = $accounts->getItems();
    $firstAccountId = $items[0]->getId();


    $properties = $analytics->management_webproperties
        ->listManagementWebproperties($firstAccountId);

    if (count($properties->getItems()) > 0) {
      $items = $properties->getItems();
      $firstPropertyId = $items[0]->getId();


      $profiles = $analytics->management_profiles
          ->listManagementProfiles($firstAccountId, $firstPropertyId);

      if (count($profiles->getItems()) > 0) {
        $items = $profiles->getItems();


        return $items[0]->getId();

      } else {
        throw new Exception('No views (profiles) found for this user.');
      }
    } else {
      throw new Exception('No properties found for this user.');
    }
  } else {
    throw new Exception('No accounts found for this user.');
  }
}

function getResults($analytics, $profileId) {

   return $analytics->data_ga->get(
       'ga:' . $profileId,
       $_GET['end_date'],
       $_GET['start_date'],
       'ga:users',
		  array(
		    'dimensions'  => 'ga:date',
		    'metrics'  => 'ga:users,ga:newUsers'
		  ));
}

function printResults($results) {

  if (count($results->getRows()) > 0) {


    $profileName = $results->getProfileInfo()->getProfileName();

	//
	$xml = simplexml_load_file('geocodes.xml');
	$visitorsData = array();
	$value = '';

    $rows = $results->getRows();
    $max = count($rows);

    for($i = 0; $i<$max;$i++){
	    $date 	  = $rows[$i][0];
		$num1 	  = $rows[$i][1];
		$num2 	  = $rows[$i][2];
		
		$visitorsData[$date][0] = $num1;
		$visitorsData[$date][1] = $num2;
	}
	echo json_encode($visitorsData);
  } 
}

?>