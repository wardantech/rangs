$(document).ready( function () {
    $('#print').on('click', function() { // select print button with class "print," then on click run callback function
        $.print(".print"); // inside callback function the section with class "content" will be printed
    });
 });