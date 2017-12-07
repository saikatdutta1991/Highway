
/**
* This file is part of CarRepairNetwork App
* Author : Saikat Dutta
* Company : Provenlogic
*/

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

//fetching server configuration
var config = require('./SocketServerConfig').getConfig();
console.log('config', config);




//importing modules
var app = require('express')();
var fs = require('fs');
var request = require('request');
var mysql = require('mysql');
var socketio = require('socket.io');





//configuration https or http server
if (config.IS_HTTPS) {

	console.log('Https enabled');
	var options = {
		key: fs.readFileSync(config.HTTPS_KEY_PATH),
		cert: fs.readFileSync(config.HTTPS_CERT_PATH),
		ca: fs.readFileSync(config.HTTPS_CA_PATH),
		rejectUnauthorized: false,
		requestCert: false
	};

	var server = require('https').createServer(options, app);

} else {

	console.log('Https not enabled');
	var server = require('http').Server(app);
}



/**
 * initilizing mysql database connection
 */
var conn = mysql.createConnection(config.mysql);
conn.connect(function (err) {

	if (err) {
		console.log('mysql database connection failed', err.message);
		process.exit(1);
	}

	console.log("mysql database connected");

});



/* initializing socket io server */
var io = socketio(server);





io.on('connection', function (socket) {


	if (config.DEBUG) {
		socket.auth_entity = {
			id: socket.handshake.query.e_id,
			type: socket.handshake.query.e_type.toUpperCase(),
			access_token: socket.handshake.query.access_token
		};
	}

	console.log('New socket connected, Socket Id : ' + socket.id);
	var socket_room = '';


	/**
	 * authenticate socket client and if from internal server them make auth true default
	 */

	if (socket.handshake.query
		&& socket.handshake.query.server_key
		&& socket.handshake.query.server_key == config.SERVER_INTERNAL_COMMUNICATION_KEY) {
		socket.auth = true;
		console.log('from server connection');
	} else {

		if (config.DEBUG) {
			socket.auth = true;
		} else {
			socket.auth = false;
		}


		//wait and check for authenticated after 2 second if not disconnect socket
		setTimeout(function () {

			if (!socket.auth) {
				console.log('socket' + socket.id + ' not authenticated. so disconnecting.');
				socket.disconnect('Unauthorized Access');
			}

		}, config.CLIENT_AUTHENTICATE_TIMEOUT);

	}


	//client authentication request handle
	socket.on('authenticate', function (data) {

		console.log('authenticate', data);

		//if already authenticated then skip
		if (socket.auth) return;

		//check authenticate request data OK
		try {
			//data = JSON.parse(data);
			var eId = data.e_id;
			var eType = data.e_type.toUpperCase();
			var accessToken = data.access_token;


			socket.auth_entity = {
				id: eId,
				type: eType,
				access_token: accessToken
			};

		} catch (e) {
			console.log(e);
			return;
		}

		//check database for authenticated
		var sql = "SELECT entity_id FROM access_tokens WHERE entity_id = '"
			+ eId + "' AND entity_type = '"
			+ eType + "' AND access_token = '"
			+ accessToken + "' LIMIT 1";

		conn.query(sql, function (err, result) {
			if (err && !result.length) return;


			//join to room
			socket_room = eType + '_' + eId;
			socket.join(socket_room);
			console.log('room : ', socket_room);

			socket.auth = true;
			socket.emit('authenticated', { message: 'You are authenticated' });
		});

	});





	/**
	 * update driver location(latitude and longitude)
	 */
	socket.on('driver_update_location', function (data) {

		//if already authenticated then skip
		if (!socket.auth) return;

		console.log('driver_update_location', socket.auth_entity, data);

		try {

			//check data contains latitude and longitude
			if (!data.latitude || !data.longitude || socket.auth_entity.type != 'DRIVER') return;

			//check database for authenticated
			var sql = "UPDATE drivers "
				+ "SET latitude = " + data.latitude + ", longitude = " + data.longitude
				+ " WHERE id = " + socket.auth_entity.id;

			console.log('update driver location query : ' + sql);
			conn.query(sql, function (err, result) {
				console.log('update driver location response', err, result);
			});
		} catch (e) {
			console.log(e);
			return;
		}

	});




















	/* send notification to clients form php server */
	socket.on('send_event', function (data) {

		if (!socket.auth) return;

		console.log('send_event', data);

		var ids = ('' + data.to_ids).split(',');
		var room = '';

		ids.forEach(function (id) {

			try {

				room = data.entity_type.toUpperCase() + '_' + id;
				io.sockets.in(room).emit(data.event_type, data.data);

			} catch (e) {
				console.log('send_event error : ' + e.message);
			}

		});


	});




	socket.on('disconnect', function (data) {
		console.log('disconnect', socket_room);
		socket.leave(socket_room);
	});




});



/* starting server on port and listen */
server.listen(config.SERVER_PORT, function () {
	console.log('listening on localhost:' + config.SERVER_PORT);
});
