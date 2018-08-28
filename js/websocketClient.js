$(document).ready(function() {

    var qrcode = $('#qrcode');
    var socket, guid, data;

    var host = "wss://ezattendance.com/socket/connect";
    try {
        socket = new WebSocket(host);
        socket.onopen = function(msg) {
            data = {
                'action': 'getCode',
                'meetingID': meetingID
            };
            socket.send(JSON.stringify(data));
        };
        socket.onmessage = function(msg) {
            data = JSON.parse(msg.data);
            if(data.action === 'redraw') {
                guid = data.guid;
                redrawQrCode(qrcode, guid);
            }
        };
        socket.onclose = function(msg) {
            alert('Disconnected from server. This page will automatically refresh. If this persists, something is probably broken...');
            location.reload();
        };

        // avoid disconnects
        setInterval(function() {
            data = {
                'action': 'ping'
            };
            socket.send(JSON.stringify(data));
        }, 30000);

    } catch(ex) {
        console.log(ex);
    }

    $(window).resize(function() {
        redrawQrCode(qrcode, guid)
    });

    // Utilities
    function redrawQrCode(qrcode, guid) {
        qrcode.empty();
        size = Math.min( $(window).height(), $(window).width() ) * 0.8;
        new QRCode(qrcode[0], {
            text: "https://ezattendance.com/signin.php?guid=" + guid,
            width: size,
            height: size
        });
    }
});