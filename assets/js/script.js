// initialize timer variable
var timer;

$(document).ready(function() {
    
    // add event handler to result container
    $('.result').on('click', function() {

        // retrieve url address
        var url = $(this).attr('href');
        // retrieve result id
        var id = $(this).attr('data-linkId');

        // check if id is empty
        if (!id) {
            alert('data-linkId attribute not found.');
        }

        // increase link clicks
        increaseLinkClicks(id, url);

        // prevent the link from processing
        return false;
    });

    // retrieve image grid element
    var grid = $('.imageResults');

    // add event listener for when layout completed
    grid.on('layoutComplete', function() {
        $('.gridItem img').css('visibility', 'visible');
    });

    // initialize masonry grid
    grid.masonry({
        itemSelector: ".gridItem",
        columnWidth: 200,
        gutter: 5
    });

    // initialize fancybox module
    $('[data-fancybox]').fancybox({
        caption : function( instance, item ) {
            var caption = $(this).data('caption') || '';
            var siteUrl = $(this).data('siteurl') || '';
    
            return ( caption.length ? '<h6 style="font-size: 1rem; color: #eee; margin: 0;">' + caption + '</h6>' + '<br />' : '' ) + '<a href="' + item.src + '" style="margin-right: 50px;">View Image</a><a href="' + siteUrl + '">Visit page<a></a><br><br>Image <span data-fancybox-index></span> of <span data-fancybox-count></span>';
        },
        afterShow: function( instance, item ) {

            // call method that increases image clicks
            increaseImageClicks(item.src);

        }
    });

});

// method that updates clicks on a link
function increaseLinkClicks(linkId, url) {

    // call POST request
    $.post('ajax/updateLinkCount.php', {linkId: linkId}).done(function(result) {
        // check if result was invalid
        if (result != "") {
            alert(result);
            return;
        }

        // redirect to page
        window.location.href = url;

    });

}

// method that updates clicks on an image
function increaseImageClicks(imageUrl) {

    // call POST request
    $.post('ajax/updateImageCount.php', {imageUrl: imageUrl}).done(function(result) {
        // check if result was invalid
        if (result != "") {
            alert(result);
            return;
        }

    });

}

// method that loads the image
function loadImage(src, className) {

    // create image object element
    var image = $('<img>');

    // add event listener for when image loads
    image.on('load', function() {

        // clear timer
        clearTimeout(timer);

        // set timer 
        timer = setTimeout(function() {
            $('.imageResults').masonry();
        }, 500);

        // insert image
        $('.' + className + ' a').append(image);

    });

    // add event listener for when image does not load
    image.on('error', function() {

        // remove broken images
        $('.' + className).remove();

        // call method that flags image as broken
        $.post('ajax/setBroken.php', {src: src});

    });

    image.attr('src', src);

}