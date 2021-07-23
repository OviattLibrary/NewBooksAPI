#!/usr/bin/env php
<?php
  $ch = curl_init();
  $url = 'https://librarydev.csun.edu/oviattphp/api-alma/alma/new-books.php/random?';

  $data = array(
    'path' => 'reading-room',
    'user_limit' => '5',
  );

  $queryParams = http_build_query($data);
  curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  $response = curl_exec($ch);
  curl_close($ch);

  $response = json_decode($response, true);
  //print_r($response['user_items']);

  $xml = '
  <rss version="2.0">
    <channel>
      <title>CSU Northridge Oviatt Library - New Leisure Reading Room Titles</title>
      <link>http://library.csun.edu</link>
      <description>New Reading Room Titles acquired by the CSU Northridge Oviatt Library</description>
      <lastBuildDate>' . date('l, F j, Y h:i:s A') . '</lastBuildDate>
  ';

  //$title = 'Dying every day: Seneca at the court of Nero';
  //$author = 'Romm, James S.';
  //$link = 'https://csu-un.primo.exlibrisgroup.com/discovery/search?query=any,exact,9000000&tab=LibraryCatalog&search_scope=MyInst_and_CI&sortby=rank&vid=01CALS_UNO:01CALS_UNO&facet=rtype,include,books&offset=0';

  foreach ($response['user_items'] as $k => $v) {
    $title  = htmlspecialchars($v['title']);
    $author = htmlspecialchars($v['author']);
    $mms_id = htmlspecialchars($v['mms_id']);
    $pub_date = str_replace('[', '', $v['publication_date']);
    $pub_date = htmlspecialchars(str_replace(']', '', $pub_date));
    // $link = 'https://csun-primo.hosted.exlibrisgroup.com/primo-explore/search?query=any,exact,' . $mms_id . '&amp;tab=everything&amp;search_scope=EVERYTHING&amp;vid=01CALS_UNO&amp;facet=rtype,include,books&amp;offset=0';
    $link = 'https://csu-un.primo.exlibrisgroup.com/discovery/search?query=any,exact,' . $mms_id . '&tab=LibraryCatalog&search_scope=MyInst_and_CI&sortby=rank&vid=01CALS_UNO:01CALS_UNO&facet=rtype,include,books&offset=0';
    $xml .= '
    <item>
      <title>' . $title . '</title>
      <author>' . $author . '</author>
      <link>' . $link . '</link>
      <publication_date>' . $pub_date . '</publication_date>
    </item>
    ';
  }
      
  
  $xml .= '
    </channel>
  </rss>
  ';
  
  $file = fopen('/home/httpd/pages/oviattphp/api-alma/alma/reading_books_rss.xml', 'w') or die('Unable to open file!');
  fwrite($file, $xml);
  fclose($file);
?>
