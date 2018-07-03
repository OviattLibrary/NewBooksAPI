<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'slim/vendor/autoload.php';

const API_KEY  = 'API_KEY';
const URL_BIBS = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/bibs';
const URL_PNX  = 'https://api-na.hosted.exlibrisgroup.com/primo/v1/pnxs';

$app = new \Slim\App;

// API Routes
$app->get('/', 'getListStatus');
$app->get('/{mms_id}', 'getStatus');
$app->get('/{context}/{docid}', 'getRecordStatus');

// API Implementations
function getListStatus(Request $request, Response $response, array $args) {
  $mms_ids = (!empty($request->getQueryParams()['mms_id'])) ? $request->getQueryParams()['mms_id'] : '';
  $mms_ids = explode(',', $mms_ids);
  $holdings_links = array();

  $result = new stdClass;

  // Make sure that there is at least 1 ID and no more than 100
  if (empty($mms_ids[0]) or count($mms_ids) > 100) {
    $result->response_status = 'Error';
    $result->errorMessage    = 'Check ID(s)';
    return $response->withJson($result);
  }

  // Make sure that IDs are all numerical
  foreach ($mms_ids as $k => $v) {
    if (!is_numeric($v)) {
      $result->response_status = 'Error';
      $result->errorMessage    = 'Invalid ID(s)';
      return $response->withJson($result);
    }

    $holdings_links[] = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/bibs/' . $v . '/holdings';
  }
  $result->response_status = 'OK';
  //$result->mms_ids = implode(',', $mms_ids);

  //print_r($holdings_links);

  $api_params = array(
    'apikey' => API_KEY,
  );

  $holdings_records = array();
  foreach ($holdings_links as $k => $v) {
    $holdings_records[] = getData($api_params, $v);
  }

  //print_r($holdings_records);

  $items_links = array();

  foreach ($holdings_records as $k => $v) {
    $items_links[] = $v->holding->attributes()->{'link'} . '/items';
  }

  //print_r($items_links);

  $items_lists = array();
  foreach ($items_links as $k => $v) {
    $items_lists[] = getData($api_params, $v);
  }

  //print_r($items_lists);

  $items = array();
  foreach ($items_lists as $k => $v) {
    $item = new stdClass;

    //echo 'Base_status: ' . $v->item->item_data->base_status;
    //echo '\nDesc: ' . $v->item->item_data->base_status->attributes()->{'desc'};
    $item->status = (string) $v->item->item_data->base_status;
    $item->description = (string) $v->item->item_data->base_status->attributes()->{'desc'};
    $items[] = $item;
  }

  $result->items = $items;
  // $bib_records = getData($bibs_params, URL_BIBS);

  //$result->bib_records = $bib_records;

  // foreach ($bib_records->bib as $k => $v) {
  //   print_r($v->holdings->attributes()->{'link'});
  // }

  //return $response->withJson($result);
  return json_encode($result);
}
function getStatus(Request $request, Response $response, array $args) {

  $mms_id = $args['mms_id'];

  $result = new stdClass;

  if (!is_numeric($mms_id)) {
    $result->response_status = 'Error';
    $result->status          = 'Invalid value';
    return $response->withJson($result);
  }

  $result->response_status = 'OK';
  $result->status          = 'Unknown';

  $data = array(
    'mms_id' => $mms_id,
    'apikey' => API_KEY,
  );
  $key_data = array(
    'apikey' => API_KEY,
  );

  // Obtain Bib record
  $bib_record = getData($data, URL_BIBS);

  // Extract holdings record link and obtain holdings record
  $holdings_link   = $bib_record->bib->holdings->attributes()->{'link'};
  $holdings_record = getData($key_data, $holdings_link);

  // Extract items list link and obtain items list to get items's status
  $items_link = $holdings_record->holding->attributes()->{'link'} . '/items';
  $items_list = getData($key_data, $items_link);

  // Convert to string to access first element, else use json_decode(json_encode(...), true)[0]
  $status = json_decode(json_encode($items_list->item->item_data->base_status), true)[0];
  $desc   = (string) $items_list->item->item_data->base_status->attributes()->{'desc'};

  $result->status = $status;
  $result->desc   = $desc;

  return $response->withJson($result);
}
function getRecordStatus(Request $request, Response $response, array $args) {
  $context = $args['context'];
  $docid   = $args['docid'];

  $result = new stdClass;

  // Context can only be 'L' or 'PC'; docid should have an Alma ID which should have '01CALS_ALMA' at the beginning
  if ($context !== 'L' and $context !== 'PC' or strpos($docid, '01CALS_ALMA') === false) {
    $result->reponse_status = 'Error';
    $result->status         = 'Invalid context/record';
    return $response->withJson($result);
  }

  // If params OK, set default response
  $result->response_status = 'OK';
  $result->status          = 'Unknown';

  // Build URL
  $api_url = URL_PNX . '/' . $context . '/' . $docid;
  //$result->api_url = $api_url;
  $key_data = array(
    'apikey' => API_KEY,
  );

  $pnx_record = getJSONData($key_data, $api_url);
  $result->availability = $pnx_record->delivery->availability;
  //$result->holding = $pnx_record->delivery->holding;
  foreach ($pnx_record->delivery->holding as $k => $v) {
    // If CSUN (01CALS_UNO) is found, gather holding record info
    if ($v->organization === '01CALS_UNO') {
      $result->availabilityStatus = $v->availabilityStatus;
      $result->holding_record = $v;

      // Set item's status
      if ($v->availabilityStatus === 'unavailable') {
        $result->status = 'Item Unavailable';
      } else if ($v->availabilityStatus === 'available') {
        $result->status = 'Item Available';
      }

      // No need to keep looking for matches, exit loop
      break;
    }
  }

  return $response->withJson($result);
}

function getData($params, $path, $format = 'xml') {

  if (!empty($params)) {
    $params = '?' . http_build_query($params);
  }
  
  $url = $path . $params;

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  $result = curl_exec($ch);

  curl_close($ch);

  $result = simplexml_load_string($result);

  if ($format == 'json') {
    $result = json_decode(json_encode($result));
  }

  return $result;
}
function getJSONData($params, $path) {
  if (!empty($params)) {
    $params = '?' . http_build_query($params);
  }
  
  $url = $path . $params;

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  $result = curl_exec($ch);

  curl_close($ch);

  return json_decode($result);
}

$app->run();
