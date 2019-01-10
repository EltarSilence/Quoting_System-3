$(document).ready(function(){
  $('#list > .card').on('click', function(){
    $('#detail > .card').fadeOut(1);
    $('#detail > .card[data-det="'+$(this).attr("data-det")+'"]').fadeIn();
  });
  $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
  });
});
