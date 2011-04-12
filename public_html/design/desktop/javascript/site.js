$(document).ready(function() {

  $.facebox.settings.closeImage = '/design/desktop/images/closelabel.png';
  $.facebox.settings.loadingImage = '/design/desktop/images/loading.gif';

  $("#accountsToDomains #accountName").autocomplete({
    source: '/service/ajax/getAccount/json/',
    minLength: 2,
    select: function( event, ui ) {
      $("#accountID").val(ui.item.id);
      $("#accountName").val(ui.item.value);
      return false;
    }
  });

  $("#search").submit(function(e){
    e.preventDefault();
    $("#searchQuery").autocomplete( "close" );
    $("#searchQuery").autocomplete( "disable" );
    var type = $("#search_form input:radio:checked").val();
    var query = $("#searchQuery").val();
    var wildcards = ( $("#wildcards").attr('checked') ? 'both' : 'single' )  ;
    $.ajax({
      url: '/service/ajax/search/json/'+ type +'/'+ query +'/'+ wildcards,
      dataType: 'json',
      success: function( data ) {
        $("#result tbody").html('');
        for (var i in data)
        {
          $("#result tbody").append(data[i].desc);
        }
      }
      });
    $("#searchQuery").autocomplete( "enable" );
  });

  $("#searchQuery").autocomplete({
    source: function( request, response ) {
              $.ajax({
                url: '/service/ajax/search/json/'+ $("#search_form input:radio:checked").val() +'/'+ request.term,
                dataType: 'json',
                success: function( data ) {
                  response( data );
                }
              });
            },
    minLength: 2,
    select: function( event, ui ) {
      $("#result tbody").html(ui.item.desc);
      return false;
    }
  });

  $("#accountsToDomains").submit(function(e) {
    e.preventDefault();
    $.ajax({
      url: '/service/ajax/accountsToDomains/json/',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(data){
        if (data.error)
        {
          setMessage(data.msg,data.msg_type);
        }
        else
        {
          setMessage(data.msg,data.msg_type);
          $("#domains").html(data.content);
          $("#accountName").val('');
          $("#accountID").val('');
        }
      }
    });
  });

  $(".loadDomains").click(function(e) {
    e.preventDefault();

    var url = this.href;

    $.ajax({
      url: url,
      dataType: 'json',
      success: function(data){
      if (data.error)
      {
        setMessage(data.msg,data.msg_type);
      }
      else
      {
        $.facebox(data.content);
      }
      }
    });
  });

  $(".tablesorter").tablesorter({widgets: ['zebra']});

  $(".tooltip_trigger").tooltip().dynamic({ bottom: { direction: 'down', bounce: true } });
});

function setMessage(msg,type)
{
  $("#messages").toggle();  
  $("#messages").addClass(type);
  $("#messages").html(msg);
  $("#messages").fadeOut(2000);    
  $("#messages").addClass(type);
}
