require('dotenv').config({ path: '.env.local' });
const app = require('express')()
const http = require('http').Server(app)
const io = require('socket.io')(http)
const port = process.env.PORT || 3000
const hayQuorumCon = process.env.HAY_QUORUM || 7;
const totalConcejales = process.env.TOTAL_CONCEJALES || 14;

http.listen(port)

const Redis = require('ioredis')
const redis = new Redis(6379, 'localhost')
const pub = new Redis(6379, 'localhost')

// array ordenado concejales que pidieron la palabra { id, nombre }
let palabras = []

redis.subscribe('message', function (err, count) { })

function quorum() {
    pub.hgetall('presentes').then((presentes) => {
        presentes = Object.keys(presentes).map(p => parseInt(p))
        let q = presentes.length

        io.emit('message', {
            type: 'quorum',
            data: {
                quorum: q,
                palabras,
                hayQuorum: q > hayQuorumCon,
                ausentes: totalConcejales - q,
                presentes: presentes
            }
        })
    })
}

setInterval(quorum, 5000)

io.on('connection', function (socket) {
    const concejalId = socket.handshake.query.concejalId

    socket.on('disconnect', function () {
        if (concejalId) {
            pub.hdel('presentes', concejalId)
            quorum()
        }
    })
    if (concejalId) {
        pub.hset('presentes', concejalId, 1)
        quorum()
    }
})

redis.on('message', function (channel, message) {
    message = JSON.parse(message)

    if (message.deferred) {
        setTimeout(function () {
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

        if (message.type === 'palabra.pedir') {
            const concejal = message.data.concejal
            // solo se agrega una vez
            if (!palabras.find(c => c.id === concejal.id)) {
                palabras.push(concejal)
            }
            quorum()
            // no se emite el mensaje
            return
        }

        if (message.type === 'palabra.cancelar') {
            const concejal = message.data.concejal

            if (palabras.find(c => c.id === concejal.id)) {
                palabras = palabras.filter(c => c.id !== concejal.id)
            }
            quorum()
            // no se emite el mensaje
            return
        }

        io.emit('message', message)
    }
})
