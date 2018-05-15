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
    const q = pub.hgetall('presentes').then((presentes) => {
        presentes = Object.keys(presentes).map(p => parseInt(p))
        let q = presentes.length

        io.emit('message', {
            type: 'quorum',
            data: {
                quorum: q,
                hayQuorum: q > 7,
                ausentes: 14 - q,
                presentes: presentes
            }
        })
        console.log('quorum', q, presentes)
    })
}

setInterval(quorum, 5000);

io.on('connection', function(socket) {
    console.log(socket.handshake.query)
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
