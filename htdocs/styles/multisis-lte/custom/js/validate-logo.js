$(document).on("ready", function(){
	/*$('body').on('submit', '#formupload1', function(ev){
		if(!checkForm()) {
			ev.preventDefault();
		}
	});*/
});

function checkForm() {
	msg = new Array();
	//if (document.formupload1.theimgfile.value == "") msg.push("No file send");
	if (msg != "") {
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}