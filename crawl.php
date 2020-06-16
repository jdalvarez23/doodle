<?php
include("config.php");
include("classes/DomDocumentParser.php");

// initialize array for crawled links
$alreadyCrawled = array();
// initialize array for remaining links to crawl
$crawling = array();
// initialize array for found images
$alreadyFoundImages = array();


// method that inserts link to database
function insertLink($url, $title, $description, $keywords) {

    // define variable as global
    global $con;

    /* Better to use this than procedural mysqli because it prevents sql injections */
    // prepare MySQL query
    $query = $con -> prepare("INSERT INTO sites(url, title, description, keywords) VALUES(:url, :title, :description, :keywords)");
    
    // bind parameters (insert values)
    $query -> bindParam(":url", $url);
    $query -> bindParam(":title", $title);
    $query -> bindParam(":description", $description);
    $query -> bindParam(":keywords", $keywords);

    // execute query and return true or false for success state
    return $query -> execute();

}

// method that inserts image to database
function insertImage($url, $src, $alt, $title) {

    // define variable as global
    global $con;

    /* Better to use this than procedural mysqli because it prevents sql injections */
    // prepare MySQL query
    $query = $con -> prepare("INSERT INTO images(siteUrl, imageUrl, alt, title) VALUES(:siteUrl, :imageUrl, :alt, :title)");
    
    // bind parameters (insert values)
    $query -> bindParam(":siteUrl", $url);
    $query -> bindParam(":imageUrl", $src);
    $query -> bindParam(":alt", $alt);
    $query -> bindParam(":title", $title);

    // execute query and return true or false for success state
    return $query -> execute();

}

// method that retrieves true or false if link exists in database
function linkExists($url) {

    // define variable as global
    global $con;

    /* Better to use this than procedural mysqli because it prevents sql injections */
    // prepare MySQL query
    $query = $con -> prepare("SELECT * FROM sites WHERE url = :url");
    
    // bind parameters (insert values)
    $query -> bindParam(":url", $url);

    // execute query
    $query -> execute();

    // return row count if it is equal to 0 or not
    return $query -> rowCount() != 0;

}

// method that retrieves formatted url
function createLink($src, $url) {

    $scheme = parse_url($url)['scheme']; // http
    $host = parse_url($url)['host']; // www.reecekenney.com
    
    if (substr($src, 0, 2) == "//") {
        $src = $scheme . ":" . $src;
    } else if (substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    } else if (substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)['path']) . substr($src, 1);
    } else if (substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    } else if (substr($src, 0, 5) !== "https" && substr($src, 0, 4) !== "http") {
        $src = $scheme . "://" . $host . "/" . $src;
    }

    return $src;

}

// method that retrieves website details in url
function getDetails($url) {

    // refer to global variables
    global $alreadyFoundImages;

    // create a new instance of the parser
    $parser = new DomDocumentParser($url);

    // retrieve list of titles in url
    $titleList = $parser -> getTitles();

    // check if title exist in pages
    if (sizeof($titleList) == 0 || $titleList -> item(0) == NULL) {
        return;
    }

    // retrieve first item in array
    $title = $titleList -> item(0) -> nodeValue;

    // delete any new lines in title
    $title = str_replace("\n", "", $title);

    // check if there is no title
    if ($title == "") {
        $return;
    } 

    // initialize variables for description and keywords
    $description = "";
    $keywords = "";

    // retrieve list of meta tags in url
    $metasArray = $parser -> getMetaTags();

    // loop through meta tags
    foreach($metasArray as $meta) {
        // retrieve value of description meta tag
        if ($meta -> getAttribute('name') == "description") {
            $description = $meta -> getAttribute('content');
        }
        // retrieve value of keywords meta tag
        if ($meta -> getAttribute('name') == "keywords") {
            $keywords = $meta -> getAttribute('content');
        }
    }

    // retrieve paragraph list
    $headerList = $parser -> getHeaders();

    // check if description is empty
    if (strlen($description) == 0) {
        // initialize index of array
        $index = 0;

        // retrieve the first paragraph and assign to description
        $description = $headerList -> item($index) -> textContent;

        // increment index
        $index++;

        // check if description is still empty
        if (strlen($description) == 0) {
            // check if length of array is greater than 1
            if (count($headerList) > 5) {
                do {

                    $description = $headerList -> item($index) -> textContent;

                    $index++;
            
                } while (strlen($description) == 0);
            } else {
                $description = "";
            }
        }
    }


    // replace any new lines in description and keywords
    $description = str_replace("\n", "", $description);
    $keywords = str_replace("\n", "", $keywords);

    // echo "URL: $url, Title: $title, Description: $description, Keywords: $keywords<br>";

    // check if link already exists in database
    if (linkExists($url)) {
        echo "$url already exists<br>";
    } else if (insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url<br>";
    } else {
        echo "ERROR: Failed to insert $url<br>";
    }

    // retrieve list of images in url
    $imageList = $parser -> getImages();

    // loop through image tags
    foreach ($imageList as $image) {
        // retrieve the image source value
        $src = $image -> getAttribute('src');
        // retrieve the image alt value
        $alt = $image -> getAttribute('alt');
        // retrieve the image title value
        $title = $image -> getAttribute('title');

        // check if image does not contain title and alt
        if (!$alt && !$title) {
            continue;
        }

        // retrieve formatted url
        $src = createLink($src, $url);

        // check if link is not in already found images array
        if (!in_array($src, $alreadyFoundImages)) {
            // insert into already found images array
            $alreadyFoundImages[] = $src;
            // call method that inserts image to database
            echo "INSERT IMAGE: " . insertImage($url, $src, $alt, $title);
        } /* else {
            return;
        } */

    }


}

// method that parses links in url
function followLinks($url) {

    // refer to global variables
    global $alreadyCrawled;
    global $crawling;
    
    // create a new instance of the parser
    $parser = new DomDocumentParser($url);

    // retrieve list of links in url
    $linkList = $parser -> getLinks();

    // loop through array of links
    foreach($linkList as $link) {
        // retrieve link url
        $href = $link -> getAttribute('href');

        // check if url is invalid and continue
        if (strpos($href, '#') !== false) {
            continue;
        } else if (substr($href, 0, 11) == "javascript:") {
            continue;
        }

        // properly format links
        $href = createLink($href, $url);

        // check if link is not in already crawled array
        if (!in_array($href, $alreadyCrawled)) {
            // insert into already crawled array
            $alreadyCrawled[] = $href;
            // insert into remaining list to be crawled
            $crawling[] = $href;

            // call method that retrieves website details in url
            getDetails($href);

            // insert href to database

        } /* else {
            return;
        } */

        // echo $href . '<br>';
    }


    // get rid of top item in array
    array_shift($crawling);

    // loop through the remaining crawling list
    foreach($crawling as $site) {

        // call method that parses and crawls link in url
        followLinks($site);

    }

}

$startUrl = "https://www.cnn.com";
followLinks($startUrl);