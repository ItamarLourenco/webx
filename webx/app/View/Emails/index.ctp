<?php echo $this->Html->script('jquery-1.11.0.min.js');?>
<script type='text/javascript'>
$(function(){
	showEmails();
	setInterval(function(){
		showEmails();
	}, 10000);
})

function showEmails(){
	var url = 'ajax/';
		var emails = '';
		$.ajax({
			url: url,
			beforeSend:function(){
				$('#log').html('Buscando...');
			},
			success:function(json){
				for(var j in json){
					emails += json[j].Email.id+ ' = ' + json[j].Email.email + "<br />";
				}	
				$('#emails').html(emails);
			},
			beforeSend:function(){
				var d = new Date();
				$('#log').html('Atualizado em ' + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds());
			},
		});
}
</script>
<h1>AJAX LOG - EMAIL</h1>
<div id='log'></div>
<br />
<div id='emails'>

</div>