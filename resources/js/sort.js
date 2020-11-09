$(document).ready(init)
function init() {
    addSearchButtonListener()
    document.cookie = `nofbed=${""}`;
    document.cookie = `nofroom=${""}`;
}
function addSearchButtonListener(){
    $('#search-button').click(sendRequestSearch);
}
function sendRequestSearch() {
  var nofbed = $('#nofbed').val(); //prende il valore dall HTML
  var nofroom = $('#nofroom').val();

  document.cookie = `nofbed=${nofbed}`; 
  document.cookie = `nofroom=${nofroom}`;


   var services = [];
    $("input[name='service[]']:checked").each(function (){
        services.push($(this).val());
    });
    service = services.join(); //trasforma array in stringa
    $.ajax({
        url: '/api/search',
        data: { 'service' : service},
        method: 'GET',
        success: function(data) {
            if(data.length == 0){
              $('#message').show()  //mostra la scirtta "non ci sono appartamenti corrispondenti" 
              var target = $('.blocco-flat');
              target.hide();
            } else {
              $('#message').hide(); //nasconde scritta
                var target = $('.blocco-flat');
                target.hide(); //nascondo i vari blocco flat
                $.each(data, function(index, flat){ 
                  var id = flat.id
                  var target = $('.blocco-flat[data-id="' + id + '"]') //ciclo su tutti  blocco flat con data-id  uguale allíd del flat e faccio show
                  target.show();
                });
                  }
            },
            error: function(err) {
                console.log('err', err);
            }
          });
        }
