var http = require('http');
var util = require('util');
var fs = require('fs');
var stomp = require('stomp');

// Set debug to true for more verbose output.
// login and passcode are optional (required by rabbitMQ)
var stomp_args = {
	port: 61613,
	host: 'localhost',
	debug: false,
	login: 'guest1',
	passcode: 'guest',
};

var client = new stomp.Stomp(stomp_args);

var headers = {
	destination: '/action',
	ack: 'client',
};

var messages = 0;

client.connect();

client.on('connected', function() {
	client.subscribe(headers);
	console.log('Connected');
});

var queue=new Array();

client.on('message', function(message) {
	//client.ack(message.headers['message-id']);
	//console.log(message);
	var obj={ id: message.headers['message-id'], data: message.body };
	console.log(message.body);
	queue.unshift(obj);
	messages++;
});

client.on('error', function(error_frame) {
	console.log(error_frame.body);
	//client.disconnect();
});

http.createServer(function(req, res) {
	if (req.headers.accept && req.headers.accept == 'text/event-stream') {
		if (req.url == '/events') {
			sendSSE(req, res);
		} else {
			res.writeHead(404);
			res.end();
		}
	} else {
		res.writeHead(200, {'Content-Type': 'text/html'});
		res.write(fs.readFileSync(__dirname + '/sse-node.html'));
		res.end();
	}
}).listen(1337);

function sendSSE(req, res) {
	res.writeHead(200, {
		'Content-Type': 'text/event-stream',
		'Cache-Control': 'no-cache',
		'Connection': 'keep-alive'
	});

	var id = (new Date()).toLocaleTimeString();

	// Sends out event notifications
	setInterval(function() {
		if (queue.length) {
			var obj=queue.pop();
			constructSSE(res, obj.id, obj.data);
		}
	}, 100);
	
	// A little keep-alive
	setInterval(function() {
		constructSSE(res, unixTime(), unixTime());
	}, 10000);
}

function constructSSE(res, id, data) {
	res.write('id: ' + id + '\n');
	res.write("data: " + data + '\n\n');
}

function debugHeaders(req) {
	util.puts('URL: ' + req.url);
	for (var key in req.headers) {
		util.puts(key + ': ' + req.headers[key]);
	}
	util.puts('\n\n');
}

function unixTime() {
	var d=new Date();
	return parseInt(d.getTime() / 1000);
}