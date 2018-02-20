var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var port = process.env.PORT || 3000;

http.listen(port);

var Redis = require('ioredis');
var redis = new Redis(6379, 'localhost');

io.on('connection', function(socket) {
    console.log('EVENT connection', socket.handshake.query.uid);
});

io.on('disconnect', function(socket) {
    console.log('EVENT disconnect', socket.handshake.query.uid);
});

redis.subscribe('message', function(err, count) {});

redis.on('message', function(channel, message) {
    message = JSON.parse(message);

    if (message.deferred) {
        setTimeout(function () {
            console.log('message', message);
            io.emit('message', message);
        }, message.deferred * 1000)
    } else {
        if (message.type === 'votacion.abierta') {
            if (message.data.duracion) {
                let duracion = message.data.duracion
                let interval = setInterval(function () {
                    duracion--;

                    message.data.tiempo = duracion

                    if (duracion <= 0) {
                        clearInterval(interval)
                    } else {
                        io.emit('message', {
                            type: 'votacion.tick',
                            data: message.data
                        })
                    }
                }, 1000)
            }
        }

        console.log('message', message);
        io.emit('message', message);
    }
});
