const app = require('express')()
const http = require('http').Server(app)
const io = require('socket.io')(http)
const port = process.env.PORT || 3000

http.listen(port)

const Redis = require('ioredis')
const redis = new Redis(6379, 'localhost')
const pub = new Redis(6379, 'localhost')

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
                let tiempo = 0
                let interval = setInterval(function () {
                    tiempo++

                    message.data.tiempo = tiempo

                    if (tiempo >= message.data.duracion) {
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
