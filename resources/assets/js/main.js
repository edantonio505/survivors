$(document).foundation();
//-----------------------Show Password-----------------------
$("#show-password").click(function(e){
	if($("#password").attr("type") == "text"){
		$("#password").attr("type", "password");
	}else {
		$("#password").attr("type", "text");
	}
});
// -------------------------------------------------------------


// -----------------------------create slug----------------------
$("#title").keypress(function(e){
	createSlug();
});


$("#title").focusout(function(e){
	createSlug();
});

function createSlug()
{
	var str = $("#title").val();
 	str = str.replace(/ /g,"-");
	$("#slug").val(str);
}
// ----------------------------------------------------------------




// ------------create new tags if the option of tags dont exist-------
$('#selective').selectize({
	create: function(input) {
        $.post({
        	url: '/hash_tags',
        	data: {name: input, _token:$("#token").val()}
        }).done(function(response){
        	console.log(response);
        });

        return {
            value: input,
            text: input
        }
    }
});
// ---------------------------------------------------------------------------

$('.grid').masonry({
  // options
  itemSelector: '.grid-item',
  percentPosition: true
});


// ------------------------------inspire - uninspire------------------------------
$(".link").click(function(e){
	e.preventDefault();
	var $this  = $(this);
	var id = $this.attr('data-id');

	if($this.hasClass('uninspire'))
	{
		$this.removeClass('uninspire')
		.addClass('inspire')
		.html('This inpires me');

		$.get({
			url: '/topic/'+id+'/uninspire'
		});
	} else if($this.hasClass('inspire')) {
		$this.removeClass('inspire')
		.addClass('uninspire')
		.html('inspired');

		$.get({
			url: '/topic/'+id+'/inspires'
		});
	}

});
// ----------------------------------------------------------------------------------