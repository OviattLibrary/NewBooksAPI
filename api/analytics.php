<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'slim/vendor/autoload.php';

const API_KEY               = 'API_KEY';
const URL_ANALYTICS_REPORTS = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?';

$app = new \Slim\App;

// API Routes
$app->get('/get/newBooks', 'getNewBooks');
$app->get('/get/newBooks/{style}/', 'getNewBooks');
$app->get('/get/newBooks/readingRoom', 'getReadingRoomBooks');
$app->get('/get/newBooks/readingRoom/{style}/', 'getReadingRoomBooks');

// API Implementations
function getNewBooks(Request $request, Response $response, array $args) {
  $path  = $request->getQueryParams()['path'];
  $limit = $request->getQueryParams()['limit'];
  $style = (!empty($args['style'])) ? $args['style'] : '';

  // $data = array(
  //   'path'   => $path,
  //   'limit'  => $limit,
  //   'apikey' => API_KEY,
  // );

  //$json = getData($data, 'json');

  print '<div class="row" style="display: flex; flex-wrap: wrap;">';

  do {
    if (empty($resumptionToken)) {
      $resumptionToken = (!empty($json2) && !empty($json2->QueryResult->ResumptionToken)) ? $json2->QueryResult->ResumptionToken : '';
    }

    $data = array(
      'path'   => $path,
      'limit'  => $limit,
      'apikey' => API_KEY,
      'token'  => $resumptionToken,
      'format' => 'xml',
    );

    $json2 = getData($data, 'json');

    $books = $json2->QueryResult->ResultXml->rowset->Row;

    displayBooks($books, $style);

  } while ($json2->QueryResult->IsFinished == 'false');

  print '</div>';

  // echo '<pre>';
  // print_r($json_string);
  // echo '</pre>';

  // $new_books_array = $json->QueryResult->ResultXml->rowset->Row;
  // $books_array = array();

  // print '<div class="row" style="display: flex; flex-wrap: wrap;">';

  // foreach ($new_books_array as $k => $v) {
  //   $book = new stdClass;

  //   $author                = '';
  //   $creation_date         = '';
  //   $isbn                  = '';
  //   $mms_id                = '';
  //   $publication_date      = '';
  //   $publisher             = '';
  //   $title                 = '';
  //   $permanent_call_number = '';
  //   $library_name          = '';
  //   $location_name         = '';

  //   if (!empty($v->Column1)) {
  //     $book->author = $v->Column1;
  //     $author = $v->Column1;
  //   }
  //   if (!empty($v->Column2)) {
  //     $book->creation_date = $v->Column2;
  //     $creation_date = $v->Column2;
  //   }
  //   if (!empty($v->Column3)) {
  //     $book->isbn = explode(';', $v->Column3);
  //     $isbn = explode(';', $v->Column3);
  //     //print '<img src="https://books.google.com/books?vid=ISBN' . $book->isbn[0] . '&printsec=frontcover&img=1&zoom=1" alt="book cover"/><br>';
  //     //print '<img src="https://proxy-na.hosted.exlibrisgroup.com/exl_rewrite/syndetics.com/index.aspx?isbn=' . $book->isbn[0] . '/SC.JPG&client=primo" alt="book cover"/><br>';
  //     //print '<img src="https://books.google.com/books?vid=ISBN1443871303&printsec=frontcover&img=1&zoom=1" alt="book cover"/><br>';
  //   }
  //   if (!empty($v->Column4)) {
  //     $book->mms_id = $v->Column4;
  //     $mms_id = $v->Column4;
  //     //print 'https://csun-primo.hosted.exlibrisgroup.com/primo-explore/search?query=any,exact,' . $v->Column4 . '&tab=everything&search_scope=EVERYTHING&vid=01CALS_UNO&facet=rtype,include,books&offset=0';
  //   }
  //   if (!empty($v->Column5)) {
  //     $book->publication_date = $v->Column5;
  //     $publication_date = $v->Column5;
  //   }
  //   if (!empty($v->Column6)) {
  //     $book->publisher = $v->Column6;
  //     $publisher = $v->Column6;
  //   }
  //   if (!empty($v->Column7)) {
  //     $book->title = $v->Column7;
  //     $title = $v->Column7;
  //   }
  //   if (!empty($v->Column8)) {
  //     $book->permanent_call_number = $v->Column8;
  //     $permanent_call_number = $v->Column8;
  //   }
  //   if (!empty($v->Column9)) {
  //     $book->library_name = $v->Column9;
  //     $library_name = $v->Column9;
  //   }
  //   if (!empty($v->Column10)) {
  //     $book->location_name = $v->Column10;
  //     $location_name = $v->Column10;
  //   }    
    
  //   print_html($author, $creation_date, $isbn, $mms_id, $publication_date, $publisher, $title, $permanent_call_number, $library_name, $location_name);
    
  //   //print_r($book);
  //   $books_array[] = $book;
  // }
  // print '</div>';
  //print_r($books_array);
  //print_r($json);
  //echo json_encode($xml);

  //echo $json->QueryResult->ResultXml->rowset->Row[0];

  //$response->getBody()->write('Hello there!' . $xml);

  //return $response->withJson($xml);;
  return $response;
}
function getReadingRoomBooks(Request $request, Response $response, array $args) {
  $path  = $request->getQueryParams()['path'];
  $limit = $request->getQueryParams()['limit'];
  $style = (!empty($args['style'])) ? $args['style'] : '';

  print '<div class="row" style="display: flex; flex-wrap: wrap;">';

  do {
    if (empty($resumptionToken)) {
      $resumptionToken = (!empty($json2) && !empty($json2->QueryResult->ResumptionToken)) ? $json2->QueryResult->ResumptionToken : '';
    }

    $data = array(
      'path'   => $path,
      'limit'  => $limit,
      'apikey' => API_KEY,
      'token'  => $resumptionToken,
    );

    $json2 = getData($data, 'json');

    $books = $json2->QueryResult->ResultXml->rowset->Row;

    displayBooks($books, $style);

  } while ($json2->QueryResult->IsFinished == 'false');

  print '</div>';






  // $data = array(
  //   'path'   => $path,
  //   'limit'  => $limit,
  //   'apikey' => API_KEY,
  // );

  // $json = getData($data, 'json');








  // echo '<pre>';
  // print_r($json);
  // echo '</pre>';

  // if ($json->QueryResult->IsFinished != 'false') {
  //   echo 'All finished: ' . $json->QueryResult->IsFinished;
  // } else {
  //   echo 'NOt finished!';
  //   echo '<br>token: ' . $json->QueryResult->ResumptionToken;

  //   $data = array(
  //     'path'   => $path,
  //     'limit'  => $limit,
  //     'apikey' => API_KEY,
  //     'token'  => $json->QueryResult->ResumptionToken,
  //   );

  //   $json2 = getData($data, 'json');

  //   echo '<pre>';
  //   print_r($json2);
  //   echo '</pre>';
  // }









  // do {
  //   $resumptionToken = !empty($json->QueryResult->ResumptionToken) ? $json->QueryResult->ResumptionToken : '';

  //   $data = array(
  //     'path'   => $path,
  //     'limit'  => $limit,
  //     'apikey' => API_KEY,
  //     'token'  => $resumptionToken,
  //   );

  //   $json2 = getData($data, 'json');

  //   echo '<pre>';
  //   print_r($json2);
  //   echo '</pre>';

  // } while ($json->QueryResult->IsFinished != 'false');

  // $new_books_array = $json->QueryResult->ResultXml->rowset->Row;
  // $books_array = array();

  // print '<div class="row" style="display: flex; flex-wrap: wrap;">';
  // foreach ($new_books_array as $k => $v) {
  //   $book = new stdClass;

  //   $author                = '';
  //   $creation_date         = '';
  //   $isbn                  = '';
  //   $mms_id                = '';
  //   $publication_date      = '';
  //   $publisher             = '';
  //   $title                 = '';
  //   $permanent_call_number = '';
  //   $library_name          = '';
  //   $location_name         = '';

  //   if (!empty($v->Column1)) {
  //     $book->author = $v->Column1;
  //     $author = $v->Column1;
  //   }
  //   if (!empty($v->Column2)) {
  //     $book->creation_date = $v->Column2;
  //     $creation_date = $v->Column2;
  //   }
  //   if (!empty($v->Column3)) {
  //     $book->isbn = explode(';', $v->Column3);
  //     $isbn = explode(';', $v->Column3);
  //   }
  //   if (!empty($v->Column4)) {
  //     $book->mms_id = $v->Column4;
  //     $mms_id = $v->Column4;
  //   }
  //   if (!empty($v->Column5)) {
  //     $book->publication_date = $v->Column5;
  //     $publication_date = $v->Column5;
  //   }
  //   if (!empty($v->Column6)) {
  //     $book->publisher = $v->Column6;
  //     $publisher = $v->Column6;
  //   }
  //   if (!empty($v->Column7)) {
  //     $book->title = $v->Column7;
  //     $title = $v->Column7;
  //   }
  //   if (!empty($v->Column8)) {
  //     $book->permanent_call_number = $v->Column8;
  //     $permanent_call_number = $v->Column8;
  //   }
  //   if (!empty($v->Column9)) {
  //     $book->library_name = $v->Column9;
  //     $library_name = $v->Column9;
  //   }
  //   if (!empty($v->Column10)) {
  //     $book->location_name = $v->Column10;
  //     $location_name = $v->Column10;
  //   }    
    
  //   print_html($author, $creation_date, $isbn, $mms_id, $publication_date, $publisher, $title, $permanent_call_number, $library_name, $location_name, $style);
    
  //   $books_array[] = $book;
  // }
  // print '</div>';

  return $response;
}

function displayBooks($books, $style = '') {

  foreach ($books as $k => $v) {
    //$book = new stdClass;

    $author                = '';
    $creation_date         = '';
    $isbn                  = '';
    $mms_id                = '';
    $publication_date      = '';
    $publisher             = '';
    $subjects              = '';
    $title                 = '';
    $fund_ledger_code      = '';
    $permanent_call_number = '';
    $library_name          = '';
    $location_name         = '';

    if (!empty($v->Column1)) {
      //$book->author = $v->Column1;
      $author = $v->Column1;
    }
    if (!empty($v->Column2)) {
      //$book->creation_date = $v->Column2;
      $creation_date = $v->Column2;
    }
    if (!empty($v->Column3)) {
      //$book->isbn = explode(';', $v->Column3);
      $isbn = explode(';', $v->Column3);
      //print '<img src="https://books.google.com/books?vid=ISBN' . $book->isbn[0] . '&printsec=frontcover&img=1&zoom=1" alt="book cover"/><br>';
      //print '<img src="https://proxy-na.hosted.exlibrisgroup.com/exl_rewrite/syndetics.com/index.aspx?isbn=' . $book->isbn[0] . '/SC.JPG&client=primo" alt="book cover"/><br>';
      //print '<img src="https://books.google.com/books?vid=ISBN1443871303&printsec=frontcover&img=1&zoom=1" alt="book cover"/><br>';
    }
    if (!empty($v->Column4)) {
      //$book->mms_id = $v->Column4;
      $mms_id = $v->Column4;
      //print 'https://csun-primo.hosted.exlibrisgroup.com/primo-explore/search?query=any,exact,' . $v->Column4 . '&tab=everything&search_scope=EVERYTHING&vid=01CALS_UNO&facet=rtype,include,books&offset=0';
    }
    if (!empty($v->Column5)) {
      //$book->publication_date = $v->Column5;
      $publication_date = $v->Column5;
    }
    if (!empty($v->Column6)) {
      //$book->publisher = $v->Column6;
      $publisher = $v->Column6;
    }
    if (!empty($v->Column7)) {
      $subjects = explode('; ', $v->Column7);
    }
    if (!empty($v->Column8)) {
      //$book->title = $v->Column8;
      $title = $v->Column8;
    }
    if (!empty($v->Column9)) {
      $fund_ledger_code = $v->Column9;
    }
    if (!empty($v->Column10)) {
      //$book->permanent_call_number = $v->Column8;
      $permanent_call_number = $v->Column10;
    }
    if (!empty($v->Column11)) {
      //$book->library_name = $v->Column9;
      $library_name = $v->Column11;
    }
    if (!empty($v->Column12)) {
      //$book->location_name = $v->Column10;
      $location_name = $v->Column12;
    }    
    
    print_html($author, $creation_date, $isbn, $mms_id, $publication_date, $publisher, $title, $permanent_call_number, $library_name, $location_name, $style);
    
    //print_r($book);
    //$books_array[] = $book;
  }
}
function getData($params, $format = 'xml') {

  $queryParams = http_build_query($params);

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, URL_ANALYTICS_REPORTS . $queryParams);
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
function print_html($author, $creation_date, $isbn, $mms_id, $publication_date, $publisher, $title, $permanent_call_number, $library_name, $location_name, $style = '') {
  $isbn = (!empty($isbn[0])) ? $isbn[0] : '';
  // \"https://books.google.com/books?vid=ISBN" . $isbn2 . "&printsec=frontcover&img=1&zoom=1\"
  $img = 'https://proxy-na.hosted.exlibrisgroup.com/exl_rewrite/syndetics.com/index.aspx?isbn=' . $isbn . '/SC.JPG&client=primo';
  //$img = 'https://proxy-na.hosted.exlibrisgroup.com/exl_rewrite/syndetics.com/index.aspx?isbn=9780874512649/SC.JPG&client=primo';

  // list($width) = @getimagesize($img);
 
 
  // if ($width > 1) {
  //   //echo '<p>File exists</p>';
  // } else {
  //   //echo '<p>File does not exist!</p>';
  //   $img = 'https://books.google.com/books?vid=ISBN&printsec=frontcover&img=1&zoom=1';
  // }
  //$img = (!empty($isbn[0])) ? "https://proxy-na.hosted.exlibrisgroup.com/exl_rewrite/syndetics.com/index.aspx?isbn=" . $isbn2 . "/SC.JPG&client=primo" : "https://books.google.com/books?vid=ISBN" . $isbn2 . "&printsec=frontcover&img=1&zoom=1";
  //$img = "https://books.google.com/books?vid=ISBN" . $isbn . "&printsec=frontcover&img=1&zoom=1";
  if ($style == 'primo') {
    $img = 'https://proxy-na.hosted.exlibrisgroup.com/exl_rewrite/syndetics.com/index.aspx?isbn=' . $isbn . '/SC.JPG&client=primo';

    $loop = React\EventLoop\Factory::create();
    $client = new React\HttpClient\Client($loop);

    $file = new \React\Stream\WritableResourceStream(fopen('sample.jpg', 'w'), $loop);
    $request = $client->request('GET', $img);

    $request->on('response', function(\React\HttpClient\Response $response) use ($file) {
      $size = $response->getHeaders()['Content-Length'];
      $currentSize = 0;

      $progress = new \React\Stream\ThroughStream();
      $progress->on('data', function($data) use ($size, &$currentSize) {
        $currentSize += strlen($data);
        echo 'Downloading: ', number_format($currentSize / $size * 100), '%\n';
      });

      $response->pipe($progress)->pipe($file);
    });

    $request->end();
    $loop->run();
  } else if ($style == 'google') {
    $img = "https://books.google.com/books?vid=ISBN" . $isbn . "&printsec=frontcover&img=1&zoom=1";
  } else {
    list($width) = @getimagesize($img);
 
    if ($width > 1) {
      //echo '<p>File exists</p>';
    } else {
      //echo '<p>File does not exist!</p>';
      $img = "https://books.google.com/books?vid=ISBN" . $isbn . "&printsec=frontcover&img=1&zoom=1";
    }


  }
  $html = '
  <div class="col-sm-3" style="margin-bottom: 16px;">
  <div style="height: 100%; padding: 8px; margin: 12px;">
  <div style="display: flex; flex-direction: column; text-align: center;">
    <div style="flex: 0 1 50%; margin-bottom: 16px;">
      <a href="https://csun-primo.hosted.exlibrisgroup.com/primo-explore/search?query=any,exact,' . $mms_id . '&tab=everything&search_scope=EVERYTHING&vid=01CALS_UNO&facet=rtype,include,books&offset=0" target="_blank"><img src="' . $img . '" alt="book cover" style="max-height: 105px; border: 1px solid #ddd;"/></a>
    </div>
    <div style="flex: 0 1 50%;">
      <p class="bold"><a href="https://csun-primo.hosted.exlibrisgroup.com/primo-explore/search?query=any,exact,' . $mms_id . '&tab=everything&search_scope=EVERYTHING&vid=01CALS_UNO&facet=rtype,include,books&offset=0" target="_blank">' . $title . '</a></p>
      <p>' . $author . '</p>
    </div>
    </div>
    </div>
  </div>
  ';
  print $html;
}

$app->run();
