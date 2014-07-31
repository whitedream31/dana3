function ShowUserMessage(msg) {
  $("#usermessage").hide();
  $("#usermessage").html("<div class='usermessage'><h2>" + msg + "</h2></div>");
  $("#usermessage").slideDown(500);
  $("#usermessage").click(function() {
    $("#usermessage").slideUp(500);
  });
//  $("#usermessage").delay(5000).slideUp(500);
}

function StartAccordion() {
  $("#activitycontent").accordion({
    heightStyle: 'content'
  });
  $('html, body').animate({
    scrollTop: $("#usermessage").offset().top
  }, 2000);
}

function ShowActivityContent() {
  var accid = $('#fldaccountid').val();
  $.get("ajax.control.php", {accountid: accid, action: "activityarea"})
  .done(function(data) {
    $("#activitycontent").html(data);
    $("#activitycontent .item").click(function(event) {
      $("#activitycontent").hide();
      $.get("ajax.control.php", {accountid: accid, action: "activityclick", element: event.target.id})
      .done(function(data) {
        $("#activitycontent").html(data);
        $("#activitycontent").show();
      });
    });
    StartAccordion();
  }); 
}
