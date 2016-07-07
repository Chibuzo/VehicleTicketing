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
		//var bus_id  = '1'; 	// Just keep this alive in case...
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


	/*** Store user info and booking details, then print ticket ***/
	$('#customer_info').on('submit', function(e) {
		e.preventDefault();
		$("button[type='submit']").prop("disabled", true);

		var bln_validate = true;
		var seat_no   = $('.picked_seat').text();
		var boarding_vehicle_id = $('.seat_arrangement').data('boarding_vehicle_id');
		var trip_id = $(".seat_arrangement").data('trip_id');
		var travel_date = $(".seat_arrangement").data('travel_date');
		var departure_order = $(".seat_arrangement").data('departure_order');

		// let's make sure murdafucka picked a seat
		if (seat_no.length < 1) {
			$('#details .alert').html("DEAR JOHN! You didn't pick any seat already!!!").fadeIn('fast').fadeOut(8000);
			$("button[type='submit']").prop("disabled", false);
			return false;
		}

		// Validate form inputs
		if ($("#customer-phone").val().length < 1) {
			bln_validate = false;
			$('#details .alert').html("Form not completely filled out").fadeIn('fast').fadeOut(6000);
			$("[name='customer_name']").focus();
			$("button[type='submit']").prop("disabled", false);
			return false;
		}

		if (bln_validate === false) return false;

		// verify customer
		$.ajax({
			type: 'POST',
			url: 'seat_booking.php',
			data: $(this).serialize() + '&op=verify-customer',
			dataType: 'json',
			success: function(d) {
				if (d.id.trim() == "00") {
					$("#div-add-customer").slideDown();
					$("[name='customer_name']").focus();
					$("button[type='submit']").prop("disabled", false);
					return false;
				}
				$.post('seat_booking.php', {'op': 'complete-booking', 'boarding_vehicle_id': boarding_vehicle_id, 'seat_no': seat_no, 'customer_id': d.id},
					function(d) {
						if (d.trim() == "01") {
							alert("Please select a seat to continue");
							$("button[type='submit']").prop("disabled", false);
						} else if (d.trim() == "2" || d.trim == 2) {
							alert("Seat " + seat_no + " is no longer available, pick a different seat");
							$("button[type='submit']").prop("disabled", false);
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
							$("#div-add-customer").hide();
							$('#pick_seat').html('');
							$('.picked_seat').text('');
							$("button[type='submit']").prop("disabled", false);
						}
					}
				);
				// send online
				postBookingOnline(trip_id, travel_date, seat_no, departure_order, d.c_name, d.phone_no, d.next_of_kin_phone);
			}
		});
	});


	/*** Cancel/Reset for a different route ***/
	$('#reset-form').click(function() {
		location.href = 'sell_ticket.php';
	});
});


function postBookingOnline(trip_id, travel_date, seat_no, departure_order, customer_name, phone, next_of_kin_phone) {
	$.post('ajax/synch.php',
		{'op': 'update-seat', 'trip_id': trip_id, 'seat_no': seat_no, 'departure_order': departure_order, 'travel_date': travel_date,
		'customer_name': customer_name, 'customer_phone': phone, 'next_of_kin_phone': next_of_kin_phone},
		function(d) {

		}
	);
}