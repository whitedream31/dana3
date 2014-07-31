// submit button handler
var element = $('#fld-idname').val();
$("#frm-" + element).submit(function(event){
  var form = $(this);
  var inputs = form.find("input, select, button, textarea");
  var values = form.serialize();
  inputs.prop("disabled", true);
  $.post("ajax.control.php?submit=1&accountid=accid&action=activityclick&element=" + element, values, function(data) {
    var result = $.parseJSON(data);
//alert('data: ' + data);
    switch (result.act) {
      case 'ok':
        ShowUserMessage(result.msg);
        ShowActivityContent();
        break;
      default: // err
        ShowUserMessage("Error: " + result.msg);
        inputs.prop("disabled", false);
        break;
    }
//      StartAccordion();
  });
  event.preventDefault();
});

// cancel button handler
$("#btncancel").click(function(event) {
  $(this).parent().parent().hide();
  ShowActivityContent();
//  StartAccordion();
});
