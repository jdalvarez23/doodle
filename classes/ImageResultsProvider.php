<?php

class ImageResultsProvider {

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
        $query = $this -> con -> prepare("SELECT COUNT(*) total FROM images WHERE (title LIKE :term OR alt LIKE :term) AND broken = 0");

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
        $query = $this -> con -> prepare("SELECT * FROM images WHERE (title LIKE :term OR alt LIKE :term) AND broken = 0 ORDER BY clicks DESC LIMIT :fromLimit, :pageSize");

        // add % wildcard to search term
        $searchTerm = "%" . $term . "%";

        // bind parameters
        $query -> bindParam(":term", $searchTerm);
        $query -> bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query -> bindParam(":pageSize", $pageSize, PDO::PARAM_INT);

        // execute query
        $query -> execute();

        // opening html
        $resultsHTML = "<div class='imageResults'>";

        // initialize loop counter
        $counter = 0;

        // retrieve site details
        while ($row = $query -> fetch(PDO::FETCH_ASSOC)) {

            // increment counter
            $counter++;

            // retrieve site details
            $id = $row['id'];
            $imageUrl = $row['imageUrl'];
            $siteUrl = $row['siteUrl'];
            $title = $row['title'];
            $alt = $row['alt'];

            // check if image has title
            if ($title) {
                // set display text
                $displayText = $title;
            } elseif ($alt) {
                // set display text
                $displayText = $alt;
            } else {
                // set display text
                $displayText = $imageUrl;
            }

            // format html
            $resultsHTML .= "<div class='gridItem image$counter'>
                                <a href='$imageUrl' data-fancybox data-caption='$displayText' data-siteurl='$siteUrl'>
                                    <script>
                                        $(document).ready(function() {
                                            loadImage(\"$imageUrl\", \"image$counter\");
                                        });
                                    </script>
                                    <span class='details'>$displayText</span>
                                </a>
                            </div>";

        }

        // closing html
        $resultsHTML .= "</div>";

        // return results html
        return $resultsHTML;

    }

}