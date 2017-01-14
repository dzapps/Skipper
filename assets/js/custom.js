var autoOpen = false;

function Bypass() {
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: {"action":"bypass", "url":$(".url").val()},
		success: function(x){
			$(".btn").html("Çevir");
			$(".response").html("");
			$(".response").html(x.HTMLGui);
			$(".btn").prop('disabled', false);			
			$("input").prop('disabled', false);
			if (autoOpen && x.Code == 200) {
				window.open(x.Message, '_blank');
			}
		}
	});
}

$(document).ready(function() {
	$(".btn").click(function() {		
		Bypass();
		$(".response").html("");
		$(this).prop('disabled', true);
		$("input").prop('disabled', true);		
		$(this).html("<i class='fa fa-spinner fa-pulse fa-2x' aria-hidden='true'></i>");
	});
	$("#autoOpen").change(function() {
		if (this.checked) {
			autoOpen = true;
		}else {
			autoOpen = false;
		}
	});
});
