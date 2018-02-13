var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var port = process.env.PORT || 3000;

http.listen(port);

var Redis = require('ioredis');
var redis = new Redis(6379, 'localhost');

io.on('connection', function(socket) {
    console.log('EVENT connection');
});

redis.subscribe('message', function(err, count) {});

redis.on('message', function(subscribed, message) {
    io.emit('message', message);
});