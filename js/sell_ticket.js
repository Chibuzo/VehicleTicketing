$(document).ready(function() {

	/*** Submit travel details and load seating arrangement ***/
	$("#book").submit(function(e) {
		e.preventDefault();
		if ($('#park_map_id').val().length == '' || $('#vehicle_type').val().length == '') {
			$('#main_bus_search .alert-error').html("Fill out all the form fields to continue.").show().fadeOut(6000);
			return false;
		}
		var num_of_seat = $(this).find("#vehicle_type option:selected").data('num_of_seat');
		var data = $(this).serialize() + '&num_of_seat=' + num_of_seat;

		// Load seating arrangement
		$.post('seat_booking.php', data + '&op=get_seating', function(d) {
			if (d.trim() == "04") {
				$('#main_bus_search .alert-error').html("The selected route is invalid").show().fadeOut(6000);
			} else if (d.trim() == "05") {
				$('#pick_seat').html("<div class='alert alert-error'>The selected vehicle is not setup for this route. Please contact the park manager.</div>");
				$('#pick_seat').find(".alert-error").show();
			} else {
				$('#pick_seat').html(d);
			}
		});
	});


	/*** Select/book and unselect seat [ Toggle ] ***/
	$('#pick_seat').on('click', '.seat', function() {
		var seat_no = $(this).attr('id');
		var $this_parent = $(this).parents('.seat_arrangement');
		var bus_id  = '1'; 	// Just keep this alive in case...
		var fare    = '₦' + $this_parent.data('fare');

		if ($(this).data('hidden') == 'no') {

			if ($this_parent.find('.picked_seat').text().length == 0) {
				$this_parent.find('.picked_seat').text(seat_no);
				$this_parent.find('.show_fare').text(fare);
			} else if ($this_parent.find('.picked_seat').text() != seat_no) { // Check if there's an already selected seat
				$('div.seat').css('background-image', 'url("images/seat.gif")').data('hidden', 'no');
				$this_parent.find('.picked_seat').text(seat_no);
				$this_parent.find('.show_fare').text(fare);
			}
			$(this).css('background-image', 'url("images/selected_seat.gif")');
			$this_parent.find('#show_fare').text(fare);
			$(this).data('hidden', 'Yes');
		} else {
			$(this).css('background-image', 'url("images/seat.gif")');
			$this_parent.find('.picked_seat').text('');
			$this_parent.find('.show_fare').text('');
			$(this).removeData('hidden').data('hidden', 'no');
		}
	});


	/*** Put the selected seat in a placeholder, and display inputs for customer's details ***/
	$('#pick_seat').on('click', '.continue', function(e) {
		e.preventDefault();
		var picked_seats = $('.picked_seat').text();
		var destination = $('#destination').val();
		if (picked_seats.length < 1) {
			alert("Pick a seat before you continue");
			return false;
		}
		$('#details').fadeIn();
		$('#cid').val('');
		$('input[name=address]').val(destination);
		$("input[name='customer_name']").focus();
	});


	/*** Store user info and booking details, then print ticket ***/
	$('#details').on('submit', '#customer_info', function(e) {
		e.preventDefault();
		$("button[type='submit']").prop("disabled", true);

		$('#c_id').val($('#cid').val());
		var bln_validate = true;
		var to = $("#destination").val();
		var travel_date = $('#t_date').val();
		//var vehicle_type_id = $('#picked_seat').data('vehicle_type_id');
		var seat_no   = $('.picked_seat').text();
		var bus_type  = $('.seat_arrangement').data('seating_arrangement');
		var boarding_vehicle_id = $('.seat_arrangement').data('boarding_vehicle_id');
		var park_map_id = $('.seat_arrangement').data('park_map_id');
		var fare = $('.seat_arrangement').data('fare');
		var merged    = '';

		// let's make sure murdafucka picked a seat
		if (seat_no.length < 1) {
			$('#details .alert').html("DEAR JOHN! You didn't pick a seat already!!!").fadeIn('fast').fadeOut(8000);
			$("button[type='submit']").prop("disabled", false);
			return false;
		}

		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0 && val.name != 'address' && val.name != 'traveler_phone' && val.name != 'c_id') {
				bln_validate = false;
				$('#details .alert').html("Form not completely filled out").fadeIn('fast').fadeOut(6000);
				$("[name='" + val.name + "']").focus();
				$("button[type='submit']").prop("disabled", false);
				return false;
			}
		});

		if (bln_validate === false) return false;

		$.post('seat_booking.php',
			$(this).serialize()
			+ '&to=' + to
			+ '&op=complete-booking'
			+ '&boarding_vehicle_id=' + boarding_vehicle_id
			+ '&seat_no=' + seat_no
			//+ '&vehicle_type_id=' + vehicle_type_id
			+ '&park_map_id=' + park_map_id,
			function(d) {
				if (d.trim() == "01") {
					alert("Please select a seat to continue");
				} else if (d.trim() == "02") {
					alert("Seat " + seat_no + " is no longer available, pick a different seat");
				} else if (d.trim() == "03") {
					alert("This operation failed, please refresh the browser and start again");
				} else {
					$('input[type=reset]').click();

					/*** Print ticket ***/
					var iframe_body = $("#receipt").contents().find("#details");
					iframe_body.html(d);
					window.frames['receipt'].print();
					iframe_body.html("");

					/*** Reset for the next customer ***/
					$('#pick_seat').html('');
					$('.picked_seat').text('');
					$("button[type='submit']").prop("disabled", false);
				}
			}
		);
	});


	/*** Find customer details, using cid ***/
	$('#find-customer').click(function() {
		var cid = $('#cid').val();

		// Get customer details
		$.ajax({
			type: 'POST',
			url: 'ajax/customer.php',
			data: 'cid=' + cid + '&op=get_customer',
			dataType: 'json',
			success: function(d) {
				if (d.status.trim() == 'true') {
					$('input[name=customer_name]').val(d.c_name);
					$('input[name=address]').val(d.address);
					$('input[name=traveler_phone]').val(d.phone_no);
					$('input[name=next_of_kin_phone]').val(d.next_of_kin_phone);
					$('#c_id').val(cid);
					$('#addnew').val('No');

					$('#customer_info').submit();
				}
			}
		});
	});

	/*** Cancel/Reset for a different route ***/
	$('#reset-form').click(function() {
		location.href = 'sell_ticket.php';
	});
});
