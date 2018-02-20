var app = require('express')()
var http = require('http').Server(app)
var io = require('socket.io')(http)
var port = process.env.PORT || 3000

http.listen(port)

var Redis = require('ioredis')
var redis = new Redis(6379, 'localhost')
var pub = new Redis(6379, 'localhost')

redis.subscribe('message', function(err, count) {})

function quorum() {
    const q = pub.hlen('presentes').then((q) => {
        io.emit('message', {
            type: 'quorum',
            data: {
                quorum: q
            }
        });
        console.log('quorum', q)
    })
}

io.on('connection', function(socket) {
    const concejalId = socket.handshake.query.concejalId
    console.log('EVENT connection', concejalId)

    socket.on('disconnect', function() {
        console.log('EVENT disconnect', concejalId)
        if (concejalId) {
            pub.hdel('presentes', concejalId)
            quorum()
        }
    });
    if (concejalId) {
        pub.hset('presentes', concejalId, 1)
        quorum()
    }
});

redis.on('message', function(channel, message) {
    message = JSON.parse(message);

    if (message.deferred) {
        setTimeout(function () {
            console.log('message', message)
            io.emit('message', message)
        }, message.deferred * 1000)
    } else {
        if (message.type === 'votacion.abierta') {
            if (message.data.duracion) {
                let duracion = message.data.duracion
                let interval = setInterval(function () {
                    duracion--

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

        console.log('message', message)
        io.emit('message', message)
    }
});
