require("./bootstrap");

$(document).ready(function () {
    // Log CSRF token for debugging
    console.log($('meta[name="csrf-token"]').attr('content'));
});
