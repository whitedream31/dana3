function RunAction(ra) {
  window.location = 'control.php?' + ra;
}

window.onload = function() {
  var accid = $('#fldaccountid').val();

  $(document).ajaxError(function(e, x, settings, exception) {
    var message;
    var statusErrorMap = {
      '400' : "Server understood the request but request content was invalid.",
      '401' : "Unauthorised access.",
      '403' : "Forbidden resouce can't be accessed",
      '500' : "Internal Server Error.",
      '503' : "Service Unavailable"
    };
    if (x.status) {
      message = statusErrorMap[x.status];
      if(!message) {
        message = "Unknow Error " + exception;
      }
    } else if(exception == 'parsererror') {
      message = "Error.\nParsing JSON Request failed.";
    } else if(exception == 'timeout') {
      message="Request Time out.";
    } else if(exception == 'abort') {
      message = "Request was aborted by the server";
    } else {
      message = "Unknow Error. " + exception;
    }
    $("#usermessage").append("<p>" + message + "</p>");
//    $("#usermessage").append("<p>Error requesting page " + settings.url + ". " + request.responseText + "</p>");
  });

  $.ajax({
    url: "ajax.control.php",
    dataType: "json",
    data: {
      accountid: accid,
      action: "logindetails" // WriteControlHeader('Control Page', '$username', '$contact->firstname' . ' ' . '$contact->lastname');
    },
    success: function(data) {
      $("#headertitle").html(data.headertitle);
      $("#loginline span").html(data.loginline);
    }
  });

  $.ajax({
    url: "ajax.control.php",
    dataType: "html",
    data: {
      accountid: accid,
      action: "statusarea" 
    },
    success: function(data) {
      $("#statusarea").html(data);
    }
  });

  $.ajax({
    url: "ajax.control.php",
    dataType: "html",
    data: {
      accountid: accid,
      action: "helparea"
    },
    success: function(data) {
      $("#helparea").html(data);
    }
  });

  $.ajax({
    url: "ajax.control.php",
    dataType: "html",
    data: {
      action: "footerarea"
    },
    success: function(data) {
      $("#footercopyright").html(data);
    }
  });

  ShowActivityContent();
};
