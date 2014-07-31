//$('.controlactivitylist .action').click(function(event) {
$('#activityarea .action').click(function(event) {
  event.preventDefault();
  $('#activitycontent').slideUp();
  $('#activitycontent').html("Please Wait...");
  $('#activitycontent').show();
  $.ajax({
    url: 'ajax.control.php',
    dataType: 'html', //'json',
    data: {
      action: 'activitytableaction',
      element: $(this).attr('href')
    },
    success: function(data) {
      $('#activitycontent').hide();
      $('#activitycontent').html(data);
      $('#activitycontent').slideDown();
/*
      switch (data.response) {
        case 'refreshtable': 
          $('#activitypagelist').hide();
          $('#activitypagelist').parent().html(data); //.data);
          $('#activitypagelist').show();
          break;
        case 'modifyrow':
          $('#activitycontent').slideUp();
          $('#activitycontent').html(data);
          $('#activitycontent').slideDown();
          break;
        default:
          alert('response. ' + data); //.response);
          break;
      }
*/
//      $(this).parent().hide();
//          $('#activitycontent').html(data);
//          $('#activitycontent').slideDown();        
    }
  });
});
