#!/usr/bin/env php
<?php
  $html = '';
  $ch = curl_init();
  $url = 'https://librarydev.csun.edu/oviattphp/api-alma/alma/new-books.php/all';

  $data = array(
    'path' => 'reading-room',
  );

  $queryParams = http_build_query($data);

  $url .= '?' . $queryParams;

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  $response = curl_exec($ch);

  $info = curl_getinfo($ch);

  if ($info['http_code'] !== 200) {
    $html = '
    <p>No items to display</p>
    ';
    return writeOutput($html);
  }

  curl_close($ch);

  $json = json_decode($response);

  if ($json->totalItems === 0) {
    $html = '
    <p>No items to display</p>
    ';
    return writeOutput($html);
  }

  $html = '<div class="row" style="display: flex; flex-wrap: wrap;">';

  foreach ($json->items as $i => $item) {
    $isbn = (!empty($item->isbn[0])) ? $item->isbn[0] : '';
    $img = 'https://proxy-na.hosted.exlibrisgroup.com/exl_rewrite/syndetics.com/index.aspx?isbn=' . $isbn . '/SC.JPG&client=primo';
    $html .= '
    <div class="col-sm-3" style="margin-bottom: 16px;">
      <div style="height: 100%; padding: 8px; margin: 12px;">
        <div style="display: flex; flex-direction: column; text-align: center;">
          <div style="flex: 0 1 50%; margin-bottom: 16px;">
            <a href="https://csu-un.primo.exlibrisgroup.com/discovery/search?query=any,exact,' . $mms_id . '&tab=LibraryCatalog&search_scope=MyInst_and_CI&sortby=rank&vid=01CALS_UNO:01CALS_UNO&facet=rtype,include,books&offset=0" target="_blank"><img src="' . $img . '" alt="book cover" style="max-height: 105px; border: 1px solid #ddd;"/></a>
          </div>
          <div style="flex: 0 1 50%;">
            <p class="bold"><a href="https://csu-un.primo.exlibrisgroup.com/discovery/search?query=any,exact,' . $mms_id . '&tab=LibraryCatalog&search_scope=MyInst_and_CI&sortby=rank&vid=01CALS_UNO:01CALS_UNO&facet=rtype,include,books&offset=0" target="_blank">' . $item->title . '</a></p>
            <p>' . $item->author . '</p>
          </div>
        </div>
      </div>
    </div>
    ';
  }

  $html .= '</div>';

  writeOutput($html);

  function writeOutput($html) {
    $file = fopen('/home/httpd/pages/oviattphp/api-alma/alma/reading_books.txt', 'w') or die('Unable to open file!');
    fwrite($file, $html);
    fclose($file);
  }
?>
