#!/usr/bin/env php
<?php
  const PATH_TO_JSON_FILE = '/home/httpd/pages/oviattphp/api-alma/alma/json/reading_room_books.json';

  $ch = curl_init();
  $url = 'https://library.csun.edu/oviattphp/api-alma/alma/new-books.php/random?';

  $data = array(
    'path'       => 'reading-room',
    'user_limit' => 5,
  );

  $queryParams = http_build_query($data);
  curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  $response = curl_exec($ch);
  curl_close($ch);

  $response = json_decode($response);

  $items = new stdClass;
  $items->user_items = $response->items;
  
  $file = fopen(PATH_TO_JSON_FILE, 'w') or die('Unable to open file!');
  fwrite($file, json_encode($items));
  fclose($file);
?>
