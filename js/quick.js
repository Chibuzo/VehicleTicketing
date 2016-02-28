$(document).ready(function() {
	$('#manifest').submit(function(e) {
		e.preventDefault();
		var tickets = [];
		$('.ticket-row').each(function() {
			tickets.push({id         : $(this).attr('id'),
						  c_name     : $(this).find('#c_name').val(),
						  phone_no   : $(this).find('#phone_no').val(),
						  next_of_kin: $(this).find('#next_of_kin').val(),
						  seat_no    : $(this).find('#seat_no').val()
						});
		});
		
		$.ajax({
			type: 'POST',
			url : "quick_ajax.php",
			data: 'posted_data=' + encodeURIComponent(JSON.stringify(tickets)),
			success : function() {
				location.href = 'reports.php';
			},
			error : function(xhr, i) {
				alert(i);
			}
		});
	});
});