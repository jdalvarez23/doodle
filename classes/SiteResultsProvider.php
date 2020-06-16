<?php

class SiteResultsProvider {

    // initialize connection variable
    private $con;

    // constructor
    public function __construct($con) {
        // retrieve connection variable
        $this -> con = $con;
    }

    // method that retrieves the number of results found
    public function getNumResults($term) {

        // prepare MySQL query
        $query = $this -> con -> prepare("SELECT COUNT(*) total FROM sites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term");

        // add % wildcard to search term
        $searchTerm = "%" . $term . "%";

        // bind parameters
        $query -> bindParam(":term", $searchTerm);

        // execute query
        $query -> execute();

        // retrieve the row result
        $row = $query -> fetch(PDO::FETCH_ASSOC);

        // return total value
        return $row['total'];

    }

    // method that retrieves results and formats html for results page
    public function getResultsHTML($page, $pageSize, $term) {

        // retrive limit
        $fromLimit = ($page - 1) * $pageSize;

        // prepare MySQL query
        $query = $this -> con -> prepare("SELECT * FROM sites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term ORDER BY clicks DESC LIMIT :fromLimit, :pageSize");

        // add % wildcard to search term
        $searchTerm = "%" . $term . "%";

        // bind parameters
        $query -> bindParam(":term", $searchTerm);
        $query -> bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query -> bindParam(":pageSize", $pageSize, PDO::PARAM_INT);

        // execute query
        $query -> execute();

        // opening html
        $resultsHTML = "<div class='siteResults'>";

        // retrieve site details
        while ($row = $query -> fetch(PDO::FETCH_ASSOC)) {
            // retrieve site details
            $id = $row['id'];
            $url = $row['url'];
            $title = $row['title'];
            $description = $row['description'];

            // trim title
            $title = $this -> trimField($title, 55);
            // trim description
            $description = $this -> trimField($description, 230);
            // trim url
            $url = $this -> trimField($url, 76);

            // format html
            $resultsHTML .= "<div class='resultContainer'>
                                <h3 class='title'>
                                    <a class='result' href='$url' data-linkId='$id'>$title</a>
                                </h3>
                                <span class='url'>$url</span>
                                <span class='description'>$description</span>
                            </div>";

        }

        // closing html
        $resultsHTML .= "</div>";

        // return results html
        return $resultsHTML;

    }

    // method that trims text
    private function trimField($string, $characterLimit) {

        // add dots if string length is greater than limit
        $dots = strlen($string) > $characterLimit ? "..." : "";

        return substr($string, 0, $characterLimit) . $dots;

    }

}