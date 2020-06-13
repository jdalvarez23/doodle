<?php
    include("config.php");
    
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

?>
<!DOCTYPE html>
<html>
<head>
	<title>Welcome to Doodle</title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
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
							<input class="searchBox" type="text" name="q">
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
	</div>

</body>
</html>