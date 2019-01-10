$(document).ready(function() {
  $("#importo").on("change", function(){
    if($(this).val() < 100){
      $(this).val(100);
    }
    setPossibileVincita();
  });
  $("#aum").on("click", function(){
    if ($("input#importo").val() < 10000)
      $("input#importo").val(parseInt($("input#importo").val())+100);
    setPossibileVincita();
  });
  $("#decr").on("click", function(){
    if ($("input#importo").val() >= 200)
      $("input#importo").val($("input#importo").val()-100);
    setPossibileVincita();
  });

});
function setPossibileVincita(){
  $("#quota_finale").html(scommessa.getQuotaFinale());
  $("#vincita").html(Math.round(scommessa.getQuotaFinale()*parseInt($("#importo").val())*100)/100 + '<i class="icon icon-exacoin"></i>');
}
