<?php
    include("config.php");
    include("classes/SiteResultsProvider.php");

    if (isset($_GET['q'])) {
        $query = $_GET['q'];
    } else {
        exit("You must search a query.");
    }

    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    } else {
        $type = "sites";
    }

    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }

?>
<!DOCTYPE html>
<html>
<head>
	<title>Welcome to Doodle</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <script src="http://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>
<body>

	<div class="wrapper">
		<div class="header">
			<div class="headerContent">
				<div class="logoContainer">
					<a href="index.php">
						<img src="assets/images/doodleLogo.png">
					</a>
				</div>
				<div class="searchContainer">
					<form action="search.php" method="GET">
						<div class="searchBarContainer">
                            <input type="hidden" name="type" value="<?php echo $type ?>">
							<input class="searchBox" type="text" name="q" value="<?php echo $query ?>">
							<button class="searchButton">
								<img src="assets/images/icons/search.png" title="Doodle" alt="Doodle Logo">
							</button>
						</div>
					</form>
				</div>
            </div>
            <div class="tabsContainer">
                <ul class="tabList">
                    <li class="<?php echo $type == 'sites' ? 'active' : '' ?>"><a href='<?php echo "search.php?q=$query&type=sites"; ?>'>Sites</a></li>
                    <li class="<?php echo $type == 'images' ? 'active' : '' ?>"><a href='<?php echo "search.php?q=$query&type=images"; ?>'>Images</a></li>
                </ul>
            </div>
        </div>
        <div class="mainResultsSection">
            <?php 
                // create a new instance of the site result provider
                $resultProvider = new SiteResultsProvider($con);
                
                // declare page limit
                $pageSize = 20;
                
                // retrieve number of results
                $numResults = $resultProvider -> getNumResults($query);

                echo "<p class='resultsCount'>$numResults results found</p>";
            
                // retrieve results
                echo $resultProvider -> getResultsHTML($page, $pageSize, $query);
            
            ?>
        </div>
        <div class="paginationContainer">
            <div class="pageButtons">
                <div class="pageNumberContainer">
                    <img src="assets/images/pageStart.png" alt="">
                </div>  
                <?php 
                    // declare number of pages to show
                    $pagesToShow = 10;

                    // calculate number of pages to show
                    $numPages = ceil($numResults / $pageSize);
                    
                    // retrieve number of pages left to show (minimum, 10 or less than 10)
                    $pagesLeft = min($pagesToShow, $numPages);

                    // calculate and retrieve current page
                    $currentPage = $page - floor($pagesToShow / 2);

                    // check if current page goes below current page
                    if ($currentPage < 1) {
                        $currentPage = 1;
                    }

                    // prevent the pagination to show less pages than 10
                    if ($currentPage + $pagesLeft > $numPages + 1) {
                        $currentPage = $numPages + 1 - $pagesLeft;
                    }

                    // loop until all pages have been accounted for
                    while ($pagesLeft != 0 && $currentPage <= $numPages) {

                        // check if user is in current page
                        if ($currentPage == $page) {
                            echo "<div class='pageNumberContainer'>
                                <img src='assets/images/pageSelected.png'>
                                <span class='pageNumber'>$currentPage</span>
                              </div>";
                        } else {
                            echo "<div class='pageNumberContainer'>
                                <a href='search.php?q=$query&type=$type&page=$currentPage'>
                                    <img src='assets/images/page.png'>
                                    <span class='pageNumber'>$currentPage</span>
                                </a>
                              </div>";
                        }

                        // increment current page
                        $currentPage++;
                        // decrement pages left
                        $pagesLeft--;
                    }


                ?>
                <div class="pageNumberContainer">
                <img src="assets/images/pageEnd.png" alt="">
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="assets/js/script.js"></script>

</body>
</html>