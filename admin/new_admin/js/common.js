$(document).ready(function() {
    $(document).on("click", "#popup .close", closePopup);
    $(document).on("click", "#light", closePopup);
});
/* POPUP */
function popup(markUp) {
    $("body").css("overflow", "hidden");
    if (markUp) {
        $("#popup").html(markUp);
    }
    $("#light").fadeIn(500);
    $("#popup").show();
}
function closePopup() {
    $("body").css("overflow", "auto");
    $("#light").fadeOut(100);
    $("#popup").hide();
}