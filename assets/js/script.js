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