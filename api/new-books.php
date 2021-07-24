<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'slim/vendor/autoload.php';
require_once 'config.php';

const URL_ANALYTICS_REPORTS = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?';
const REPORTS = array(
  'new-books'              => '/shared/California State University, Northridge/Reports/new books',
  'reading-room'           => '/shared/California State University, Northridge/Reports/new books reading room',
  'e-books'                => '/shared/California State University, Northridge/Reports/new ebooks',
  'print-and-e'            => '/shared/California State University, Northridge/Reports/new print and ebooks',
  'new-books-combo'        => '/shared/California State University, Northridge/Reports/new books combo filter',
  'new-books-combo-ranked' => '/shared/California State University, Northridge/Reports/new book reports/new books ranked',
);

$app = new \Slim\App;

// API Routes
$app->get('/hello', 'hello');
$app->get('/', 'getBooks');
$app->get('/random', 'getRandomNewBooks');
$app->get('/all', 'getAllBooks');
$app->get('/{limit}/', 'getNewBooks');

// API Implementations
function hello(Request $request, Response $response, array $args) {
  $response->getBody()->write("Hello");
  return $response;
}
function getNewBooks(Request $request, Response $response, array $args) {
  $path  = (!empty($request->getQueryParams()['path'])) ? $request->getQueryParams()['path'] : '';
  $user_limit = (!empty($request->getQueryParams()['user_limit'])) ? $request->getQueryParams()['user_limit'] : '0';
  $page_limit = (!empty($request->getQueryParams()['limit'])) ? $request->getQueryParams()['limit'] : '25';

  $result = new stdClass;
  $result->response_status = 'OK';

  if (empty($path)) {
    return $response->withJson(exitWithError('Provide a path'));
  } else {
    $report_path = REPORTS[$path];
    if (empty($report_path)) {
      return $response->withJson(exitWithError('Provide a valid path'));
    }
    $result->path = $path . ': ' . $report_path;
  }

  if (!is_numeric($user_limit)) {
    return $response->withJson(exitWithError('Invalid user limit'));
  } else {
    $result->user_limit = $user_limit;
  }

  if (!is_numeric($page_limit)) {
    return $response->withJson(exitWithError('Invalid limit'));
  } else if ($page_limit < 25) {
    return $response->withJson(exitWithError('Invalid page limit number (minimum 25)'));
  } else {
    $result->page_limit = $page_limit;
  }

  $resumptionToken = '';

  $data = array(
    'path'   => $report_path,
    'limit'  => $page_limit,
    'token'  => $resumptionToken,
    'apikey' => API_KEY,
  );

  $queryParams = http_build_query($data);

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, URL_ANALYTICS_REPORTS . $queryParams);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  $res = curl_exec($ch);

  curl_close($ch);

  $result->res = json_decode(json_encode(simplexml_load_string($res)));

  $resumptionToken = $result->res->QueryResult->ResumptionToken;
  $result->resumptionToken = $resumptionToken;

  $books = $result->res->QueryResult->ResultXml->rowset->Row;
  $items = array();
  

  foreach ($books as $k => $book) {
    $item = new stdClass;

    $item->author                = $book->Column1;
    $item->creation_date         = $book->Column2;
    $item->isbn                  = explode('; ', $book->Column3);
    $item->mms_id                = $book->Column4;
    $item->publication_date      = $book->Column5;
    $item->publisher             = $book->Column6;
    $item->title                 = str_replace(' /', '', $book->Column7);
    $item->permanent_call_number = $book->Column8;
    $item->library_name          = $book->Column9;
    $item->location_name         = $book->Column10;

    $items[] = $item;
  }

  $result->items = $items;

  return $response->withJson($result);
}
function getRandomNewBooks(Request $request, Response $response, array $args) {
  $path       = (!empty($request->getQueryParams()['path'])) ? $request->getQueryParams()['path'] : '';
  $user_limit = (!empty($request->getQueryParams()['user_limit'])) ? $request->getQueryParams()['user_limit'] : '0';
  $page_limit = (!empty($request->getQueryParams()['limit'])) ? $request->getQueryParams()['limit'] : '25';

  $result = new stdClass;
  $result->response_status = 'OK';

  if (empty($path)) {
    return $response->withJson(exitWithError('Provide a path'));
  } else {
    $report_path = REPORTS[$path];
    if (empty($report_path)) {
      return $response->withJson(exitWithError('Provide a valid path'));
    }
    $result->path = $path . ': ' . $report_path;
  }

  if (!is_numeric($user_limit)) {
    return $response->withJson(exitWithError('Invalid user limit'));
  } else {
    $result->user_limit = $user_limit;
  }

  if (!is_numeric($page_limit)) {
    return $response->withJson(exitWithError('Invalid limit'));
  } else if ($page_limit < 25) {
    return $response->withJson(exitWithError('Invalid page limit number (minimum 25)'));
  } else {
    $result->page_limit = $page_limit;
  }

  $resumptionToken = '';

  $data = array(
    'path'   => $report_path,
    'limit'  => $page_limit,
    'token'  => $resumptionToken,
    'apikey' => API_KEY,
  );

  $queryParams = http_build_query($data);

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, URL_ANALYTICS_REPORTS . $queryParams);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  $res = curl_exec($ch);

  curl_close($ch);

  $result->res = json_decode(json_encode(simplexml_load_string($res)));

  $resumptionToken = $result->res->QueryResult->ResumptionToken;
  $result->resumptionToken = $resumptionToken;

  $books = $result->res->QueryResult->ResultXml->rowset->Row;
  $items = array();
  

  foreach ($books as $k => $book) {
    $item = new stdClass;

    $item->author                = $book->Column1;
    $item->creation_date         = $book->Column2;
    $item->isbn                  = explode('; ', $book->Column3);
    $item->mms_id                = $book->Column4;
    $item->publication_date      = $book->Column5;
    $item->publisher             = $book->Column6;
    $item->subjects              = explode('; ', $book->Column7);
    $item->title                 = str_replace(' /', '', $book->Column8);
    $item->permanent_call_number = $book->Column9;
    $item->group1                = $book->Column10;
    $item->library_name          = $book->Column11;
    $item->location_name         = $book->Column12;

    $items[] = $item;
  }

  $result->items = $items;

  return $response->withJson($result);
}
function getBooks(Request $request, Response $response, array $args) {
  $path       = (!empty($request->getQueryParams()['path'])) ? $request->getQueryParams()['path'] : '';
  $page_limit = 50; // Minimum 25 (multiples of 25)

  $result = new stdClass;
  $result->response_status = 'OK';

  if (empty($path)) {
    return $response->withJson(exitWithError('Provide a path'), 400);
  } else {
    $report_path = REPORTS[$path];
    if (empty($report_path)) {
      return $response->withJson(exitWithError('Provide a valid path'), 400);
    }
    $result->path = $path . ': ' . $report_path;
  }
  
  $result->resumptionToken = '';
  $items  = array();
  $books  = [];
  $report = null;
  $pages  = 0;

  do {
    $data = array(
      'path'   => $report_path,
      'limit'  => $page_limit,
      'token'  => $result->resumptionToken,
      'apikey' => API_KEY,
    );

    // Get Data from Alma API
    $res = getData('https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports', $data);
    // Check for any errors returned by call to Alma API and exit as necessary
    if ($res->response_info->http_code !== 200) {
      return $response->withJson(exitWithError('Error executing Alma API', $res->response_info), 500);
    }
    // Convert to XML so that it can be used to extract schema headers and to convert it to JSON
    $xml    = simplexml_load_string($res->response);
    // Convert XML to JSON
    $report = json_decode(json_encode($xml));

    // Set resumptionToken if it does not exist
    if (empty($result->resumptionToken)) {
      $result->resumptionToken = $report->QueryResult->ResumptionToken;
    }
    // Set isFinished flag, allows one to break the do...while loop
    $result->isFinished = $report->QueryResult->IsFinished;

    // Fetch column headings from XML if array does not exist
    if (empty($columnHeadings)) {
      // Fetch column headings (e.g. 'Creation Date')
      $columnHeadings = getColumnHeadingAttrib($xml);
      // Normalize headings to make them easier to reference in PHP (e.g. 'Creation Date' -> 'creation_date')
      $columnHeadings = normalizeColumnHeadings($columnHeadings);

      $result->columnHeadings = $columnHeadings;
    }

    // If there are available books, then add them to the books array
    if (!empty($report->QueryResult->ResultXml->rowset->Row)) {
      // Grab current row items
      $rows = $report->QueryResult->ResultXml->rowset->Row;
      // Check that item is an array or object
      if (is_object($rows)) {
        // If object, simply append to end of array
        $books[] = $rows;
      } else if (is_array($rows)) {
        // If no books have been saved, save row items, else merge current book list with new row items
        $books = array_merge($books, $rows);
      }
      
    }
    // Keeps track of pages per set of items, this also means that it serves as a count of how many loops were performed
    $pages++;
  
  } while ($result->isFinished === 'false');
  
  // Store the information for each book
  foreach ($books as $k => $book) {
    $item = new stdClass;
    // Use array containing column headings to store corresponding value
    // e.g. columnHeadings[1] = 'Title' and Column1: 'Book Title', meaning $item->'Title' = $book->Column1
    foreach($columnHeadings as $index => $columnHeading) {
      $bookColumn = 'Column' . $index;
      $item->$columnHeading = !empty($book->$bookColumn) ? $book->$bookColumn : '';
    }

    // Custom format specific fields (if they exist)
    if (!empty($item->isbn)) {
      $item->isbn = explode('; ', $item->isbn);
    }
    if (!empty($item->subjects)) {
      $item->subjects = explode('; ', $item->subjects);
    }
    if (!empty($item->title)) {
      $item->title = str_replace(' /', '', $item->title);
    }

    $items[] = $item;
  }

  $result->pages = $pages;
  $result->totalItems = count($items);
  $result->items = $items;
  $result->report = $report;

  return $response->withJson($result);
}
function exitWithError($message = 'Unknown Error', $info = null) {
  $result = new stdClass;
  $result->response_status = 'Error';
  $result->error_message = $message;
  if (!empty($info)) {
    $result->info = $info;
  }
  return $result;
}
function getSchemaAttributes($xsd) {
  $schemaElements = array();
  // Get schema portion from XML to get the attributes
  // One of those attributes is the columnHeading which represents the mapping to each Column in each Row element
  $xsd->registerXPathNamespace("xsd", "http://www.w3.org/2001/XMLSchema");
  $elements = $xsd->xpath("//xsd:element");
  // Save attributes from each schema element
  foreach ($elements as $element)   {
    $schemaElement = new stdClass;

    $schemaElement->elementName = (string) $element->attributes()->name;
    // Save normal attributes
    foreach($element->attributes() as $a => $b) {
      $schemaElement->$a = (string) $b;
    }
    // Save namespace attributes (e.g. saw-sql:columnHeading)
    foreach($element->attributes('saw-sql', true) as $a => $b) {
      $schemaElement->$a = (string) $b;
    }
    // Add schema element to array
    $schemaElements[] = $schemaElement;
  }

  return $schemaElements;
}
function getColumnHeadingAttrib($xsd) {
  $columns = array();
  // Prepare schema portion of XML for traversal
  $xsd->registerXPathNamespace("xsd", "http://www.w3.org/2001/XMLSchema");
  $elements = $xsd->xpath("//xsd:element");
  // Save columnHeading attribute value
  foreach ($elements as $element)   {
    $columns[] = (string) $element->attributes('saw-sql', true)->columnHeading;
  }

  return $columns;
}
function normalizeColumnHeadings($columnHeadings) {
  foreach($columnHeadings as $i => $columnHeading) {
    $columnHeadings[$i] = strtolower(str_replace(' ', '_', trim($columnHeading)));
  }

  return $columnHeadings;
}
function getData($url, $data = null) {

  if (!empty($data)) {
    $url .= '?' . http_build_query($data);
  }

  $ch = curl_init();

  curl_setopt_array($ch, array(
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HEADER         => FALSE,
    CURLOPT_CUSTOMREQUEST  => 'GET',
  ));

  $response = curl_exec($ch);

  // Check for any errors to Alma API and exit as necessary
  $info = curl_getinfo($ch);
  $response_info = new stdClass;
  $response_info->http_code = $info['http_code'];
  $response_info->content_type = $info['content_type'];
  
  if ($response_info->http_code !== 200) {
    if ($response_info->content_type === 'application/json;charset=UTF-8') {
      $response_info->body = json_decode($response);
    } else {
      $response_info->body = simplexml_load_string($response);
    }
  }
    
  curl_close($ch);

  $result = new stdClass;
  $result->response = $response;
  $result->response_info = $response_info;

  return $result;
}

$app->run();
