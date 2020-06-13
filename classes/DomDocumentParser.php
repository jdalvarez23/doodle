<?php

class DomDocumentParser {

    // initialize private variable to be used within class
    private $doc;

    public function __construct($url) {
        
        // define method and header of request
        $options = array('http' => array('method' => 'GET', 'header' => "User-Agent: doodleBot/0.1\n"));
        
        // create stream context that will be used in request
        $context = stream_context_create($options);
        
        // create new instance of DOMDocument that allows us to perform action on web pages
        $this -> doc = new DomDocument();

        // tell DOMDocument to load HTML | @ ignores errors, or warnings
        @$this -> doc -> loadHTML(file_get_contents($url, false, $context));

    }

    // method that returns links of url
    public function getLinks() {
        return $this -> doc -> getElementsByTagName('a');
    }

    // method that returns titles of url
    public function getTitles() {
        return $this -> doc -> getElementsByTagName('title');
    }

    // method that returns titles of url
    public function getMetaTags() {
        return $this -> doc -> getElementsByTagName('meta');
    }

    // method that returns images of url
    public function getImages() {
        return $this -> doc -> getElementsByTagName('img');
    }

}