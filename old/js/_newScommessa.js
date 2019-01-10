$(document).ready(function (){
	$.ajaxSetup({
		headers : {
			'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
		}
	});
	if($('#newScommessa').attr('ver') != null){
		if($('#newScommessa').attr('ver') == ""){
			setEmpty();
		}else{
			setVerifica();
		}
		scommessa.setTheActive();
	}
});

function setEmpty(){
	var html = "";
	$.ajax({
		url : "/getDisponibili",
		type : "POST",
		data : {},
		success : function(data){
			if(data.length == 0){
				html = '<h5>Non ci sono scommesse disponibili</h5>';
			}else{
				for(var i = 0; i < data.length; i++){
					let chiave = data[i]['typeD'];
					html += '<button class="btn btn-primary verifica" id="'+chiave+'">' + chiave + ' || ';
					switch (data[i]['typeD'].split('_')[0]) {
						case 'EUO':
							html += data[i]['descrizioneD'][1] + ' - ' + data[i]['descrizioneD'][0];
							break;
						case 'SN':
							html += data[i]['descrizioneD'][0] + ' S/N, ...';
							break;
						case 'MT':
							html += data[i]['descrizioneD'][0];
							break;
					}
					html += '</button>';
				}
			}
			$('#newScommessa').html(html);
			$(".verifica").on('click', function(){
				$('#newScommessa').attr('ver', $(this).attr('id'));
				setVerifica();
			});
		}
	});
}
function setVerifica(){
	var html = "";
	$.ajax({
		url : "/getScommessa",
		type : "POST",
		data : {'scommessa': $('#newScommessa').attr('ver')},
		success : function(data){
			html = '<div><div class="card-body"><div class="mb-2 ml-0 mr-0 row"><button class="btn btn-primary col-2 back">Back</button><h5 class="col-10 mb-0 pt-2 pb-2 pl-5">Diponibile fino al <b>' + data['al'] + '</b></h5></div>';
			html += getHtml(data);
			html += '</div></div>';
			$('#newScommessa').html(html);

			$(".click").on("click", function(){
				var n = $("input[type=radio]", this).attr("name");
				var t = "";
				if($("input[type=radio]", this).attr("data-type") != ""){
					t = $("input[type=radio]", this).attr("data-type");
				}
				var v = $("input[type=radio]", this).attr("data-value");
				var q = $("input[type=radio]", this).attr("data-quote");
				var d = $('#dsc').html();
				if(n.includes('SN')){
						d = $('#dsc', $(this).parent().parent().parent()).html()
				}

				$.each($(".click input[name="+n+"]"), function($k, $v){
					$v.checked = false;
					$($v).parent().removeClass('active');
				});
				$("input[type=radio]", this)[0].checked = true
				$(this).addClass("active");
				scommessa.addMultipla(n, t, v, q, d);
			});

			$(".back").on('click', function(){
				$('#newScommessa').attr('ver', "");
				setEmpty();
			});
		}
	});

	function getHtml(data){
		html = '';
		switch (data['type'].split('_')[0]) {
			case "EUO":
				html += '<h5 id="dsc">' + data['descrizione'][0] + ' - ' + data['descrizione'][1] + '</h5>';
				for(let i = 0; i < Object.keys(data['file']).length; i++){
					html += '<div class="card"><div class="btn card-header" data-toggle="collapse" data-target="#' + data['filename'] +'_' + Object.keys(data['file'])[i] + '"><h5 class="m-0">' + Object.keys(data['file'])[i] + '</h5></div><div id="' + data['filename'] + '_' + Object.keys(data['file'])[i] + '" class="collapse"><div class="card-body row pt-0 pb-1"><div class="col-sm-4"><div class="intestazione">VOTO ESATTO</div>';
					for(let k = 0; k <  Object.keys(data['file'][Object.keys(data['file'])[i]]['ESATTO']).length; k++){
						html += '<div class="row click"><div class="col-6 font-weight-bold">' + Object.keys(data['file'][Object.keys(data['file'])[i]]['ESATTO'])[k] + '</div><div class="col-6">' +data['file'][Object.keys(data['file'])[i]]['ESATTO'][Object.keys(data['file'][Object.keys(data['file'])[i]]['ESATTO'])[k]] +'</div><input type="radio" name="' + data['filename'] + '_' + Object.keys(data['file'])[i] + '"data-type="ESATTO" data-value="' + Object.keys(data['file'][Object.keys(data['file'])[i]]['ESATTO'])[k] + '" data-quote="' + data['file'][Object.keys(data['file'])[i]]['ESATTO'][Object.keys(data['file'][Object.keys(data['file'])[i]]['ESATTO'])[k]] + '"/></div>';
					}
					html += '</div><div class="col-sm-4"><div class="intestazione">UNDER</div>';
					for(let k = 0; k <  Object.keys(data['file'][Object.keys(data['file'])[i]]['UNDER']).length; k++){
						html += '<div class="row click"><div class="col-6 font-weight-bold">' + Object.keys(data['file'][Object.keys(data['file'])[i]]['UNDER'])[k] + '</div><div class="col-6">' +data['file'][Object.keys(data['file'])[i]]['UNDER'][Object.keys(data['file'][Object.keys(data['file'])[i]]['UNDER'])[k]] +'</div><input type="radio" name="' + data['filename'] + '_' + Object.keys(data['file'])[i] + '"data-type="UNDER" data-value="' + Object.keys(data['file'][Object.keys(data['file'])[i]]['UNDER'])[k] + '" data-quote="' + data['file'][Object.keys(data['file'])[i]]['UNDER'][Object.keys(data['file'][Object.keys(data['file'])[i]]['UNDER'])[k]] + '"/></div>';
					}
					html += '</div><div class="col-sm-4"><div class="intestazione">OVER</div>';
					for(let k = 0; k <  Object.keys(data['file'][Object.keys(data['file'])[i]]['OVER']).length; k++){
						html += '<div class="row click"><div class="col-6 font-weight-bold">' + Object.keys(data['file'][Object.keys(data['file'])[i]]['OVER'])[k] + '</div><div class="col-6">' +data['file'][Object.keys(data['file'])[i]]['OVER'][Object.keys(data['file'][Object.keys(data['file'])[i]]['OVER'])[k]] +'</div><input type="radio" name="' + data['filename'] + '_' + Object.keys(data['file'])[i] + '"data-type="OVER" data-value="' + Object.keys(data['file'][Object.keys(data['file'])[i]]['OVER'])[k] + '" data-quote="' + data['file'][Object.keys(data['file'])[i]]['OVER'][Object.keys(data['file'][Object.keys(data['file'])[i]]['OVER'])[k]] + '"/></div>';
					}
					html += '</div></div></div></div>';
				}
				break;
			case "SN":
				for(let i = 0; i < data['file'].length; i++){
					html += '<div class="card mt-1"><div class="btn card-header" data-toggle="collapse" data-target="#' + data['filename'] + '_' + i + '"><h5 class="m-0" id="dsc">' + data['descrizione'][i] + '</h5></div><div id="' + data['filename'] + '_' + i + '" class="collapse row pl-2 pr-2"><div class="card-body col-6 pt-0 pb-1 border-right"><div class="intestazione">SI</div><div class="click"><div>' + data['file'][i]['SI'] + '</div><input type="radio" name="' + data['filename'] + '_' + i + '" data-value="SI" data-quote="' + data['file'][i]['SI'] + '"/></div></div><div class="card-body col-6 pt-0 pb-1 border-left"><div class="intestazione">NO</div><div class="click"><div>' + data['file'][i]['NO'] + '</div><input type="radio" name="' + data['filename'] + '_' + i + '" data-value="NO" data-quote="' + data['file'][i]['NO'] + '"/></div></div></div></div>';
				}
				break;
			case "MT":
				html += '<div class="card"><div class="btn card-header"><h5 class="m-0" id="dsc">' + data['descrizione'][0] + '</h5></div>';
				for(let i = 0; i < data['file'].length; i++){
					html += '<div class="card-body row ml-0 mr-0 border pt-1 pb-1"><div class="intestazione col-6">' + data['file'][i]['titolo'] + '</div><div class="click col-6"><div>' + data['file'][i]['quota'] + '</div><input type="radio" name="' + data['filename'] + '" data-value="' + i + '-' + data['file'][i]['titolo'] + '" data-quote="' + data['file'][i]['quota'] + '"/></div></div>';
				}
				html += '</div>';
				break;
			default:
				break;
		}
		return html;
	}
}
