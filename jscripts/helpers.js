/**
 * @returns boolean - True if browser supports 'date' input type.
 */
function BrowserSupportsDateInput() {
  var i = document.createElement("input");
  i.setAttribute("type", "date");
  return i.type !== "text";
}

$(function() {
  // look for text based date controls and use the jQuery datepicker
  if(!BrowserSupportsDateInput()) {
    $(".datepicker").datepicker({dateFormat: "yy-mm-dd"});
  };
});


