$(document).ready(function(){
	$('#topWin').height($('#topWin').width()*0.8);
	var time = 5000;
	var active = 1;
	$('*[id^=vincita]:not(#vincita' + active + ')', $('#topWin')).fadeOut(1);
	var int = setInterval(function(){
		active = (active == $('*[id^=vincita]', $('#topWin')).length ? 1 : active + 1);
		$('*[id^=vincita]:not(#vincita' + active + ')', $('#topWin')).slideUp(400);
		$('#vincita' + active, $('#topWin')).slideDown(400);
	}, time);
});
