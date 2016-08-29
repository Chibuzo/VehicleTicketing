var timer_id;

// fetch travel terminal details
function fetchTerminalDetails() {
    $.getJSON('ajax/misc_fns.php', {'op': 'get-terminal-details'}, function(d) {
        var travel_abbr = d.abbr;
        var park        = d.park;

        // do socket shit
        var terminal_sub_id = travel_abbr + "_" + park;
        var travel_sub_id = travel_abbr;
        fireSocketConnection(terminal_sub_id, travel_sub_id);

        conn.onopen = function (session, details) {
            console.log(details);
        }
    });
}


function pushBooking() {
    alert('Pushed!');
}


function fireSocketConnection(terminal_sub_id, travel_sub_id) {
    $("#status").text("Connecting to TravelHub...");
    conn = new ab.Session('ws://travelhub.ng:8080',
        function () {
            clearInterval(timer_id);
            $("#status").text("Connected to TravelHub");
            $("#connect").text("Don't touch me").prop("disabled", true);

            // check for failed booking synch and fix
            $.post('ajax/synch.php', {'op': 'fix-failed-synch'});

            // subscribe to terminal events
            conn.subscribe(terminal_sub_id, function (topic, data) {
                // send the data to the database
                $.post('ajax/synch.php', {'op': 'online-synch', 'data': JSON.stringify(data)}, function (d) {
                    console.log(d);
                });
            });

            // subscribe to travel events
            conn.subscribe(travel_sub_id, function (topic, data) {
                // send the data to the database
                $.post('ajax/synch.php', {'op': 'travel-events', 'data': JSON.stringify(data)}, function (d) {
                    console.log(d);
                });
            });
        },
        function () {
            $("#status").html("Disconnected from TravelHub");
            $("#connect").text("Reconnect").prop("disabled", false);
            timer_id = setInterval(fireSocketConnection(terminal_sub_id, travel_sub_id), 60 * 1000);
            console.warn('WebSocket connection closed');
        },
        {'skipSubprotocolCheck': true}
    );
}