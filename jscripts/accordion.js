$(function() {
  $("#activityarea").accordion({
    heightStyle: 'content'
  });
  $(".currentactivesection").click();

  $("#usermessage").hide();
  $("#usermessage").slideDown(500);
  $("#usermessage").click(function() {
    $("#usermessage").slideUp(500);
  });
});
