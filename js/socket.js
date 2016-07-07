var conn;
$(document).ready(function() {
    conn = new ab.Session('ws://localhost:8080',
        function () {
            $("#status").text("Connected to TravelHub");
            $("#connect").text("Don't touch me").prop("disabled", true);

            // subscribe to terminal events
            conn.subscribe('Peace_Holy-ghost', function (topic, data) {
                // send the data to the database
                $.post('ajax/synch.php', {'op': 'online-synch', 'data': JSON.stringify(data)}, function (d) {
                    console.log(d);
                });
            });

            // subscribe to travel events
            conn.subscribe('Peace', function (topic, data) {
                // send the data to the database
                $.post('ajax/synch.php', {'op': 'travel-events', 'data': JSON.stringify(data)}, function (d) {
                    console.log(d);
                });
            });
        },
        function () {
            $("#status").html("Disconnected from TravelHub");
            $("#connect").text("Reconnect").prop("disabled", false);
            console.warn('WebSocket connection closed');
        },
        {'skipSubprotocolCheck': true}
    );

    conn.onopen = function (session, details) {
        console.log(details);
    }
});