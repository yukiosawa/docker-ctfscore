var io = require('socket.io')(8080);
var redis = require('socket.io-redis');
var adapter = io.adapter(redis({ host: '127.0.0.1', port: 6379}));

io.on('connection', function (socket) {
    var remoteAddr = socket.client.conn.remoteAddress;
    var msg = 'Connected from ' + remoteAddr + ':' + socket['id'];
    console.log(msg);
    socket.broadcast.emit('message', msg);

    socket.on('message', function (data) {
    	console.log('Message from ' +remoteAddr + ': ' + data);
    	socket.broadcast.emit('message', data);
    });

    socket.on('disconnect', function (data) {
	var msg = 'Disconnected: ' + remoteAddr + ':' + socket['id'];
	console.log(msg);
	socket.broadcast.emit('message', msg);
    });
});

