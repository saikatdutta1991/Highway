
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
var conn;

function handleDisconnect() {
	conn = mysql.createConnection(config.mysql); // Recreate the connection, since
	// the old one cannot be reused.

	conn.connect(function (err) {              // The server is either down
		if (err) {                                     // or restarting (takes a while sometimes).
			console.log('error when connecting to db:', err);
			setTimeout(handleDisconnect, 2000); // We introduce a delay before attempting to reconnect,
		}                                     // to avoid a hot loop, and to allow our node script to
	});                                     // process asynchronous requests in the meantime.
	// If you're also serving http, display a 503 error.
	conn.on('error', function (err) {
		console.log('db error', err);
		if (err.code === 'PROTOCOL_CONNECTION_LOST') { // Connection to the MySQL server is usually
			handleDisconnect();                         // lost due to either server restart, or a
		} else {                                      // connnection idle timeout (the wait_timeout
			throw err;                                  // server variable configures this)
		}
	});

	console.log("mysql database connected");

}

handleDisconnect();





var helper = require('./Helper')(conn);


/* initializing socket io server */
var io = socketio(server);





io.on('connection', function (socket) {

	console.log('New socket connected, Socket Id : ' + socket.id);
	var socket_room = '';

	if (config.DEBUG) {
		try {
			socket.auth_entity = {
				id: socket.handshake.query.e_id,
				type: socket.handshake.query.e_type.toUpperCase(),
				access_token: socket.handshake.query.access_token
			};

			//update driver is_connected_to_socket column
			if (socket.auth_entity.type == 'DRIVER') {
				helper.updateDriverSocketConnectionStatus(socket.auth_entity.id, 1);
			}


			//join to room
			socket_room = socket.auth_entity.type + '_' + socket.auth_entity.id;
			socket.join(socket_room);
			console.log('room : ', socket_room);


		} catch (e) { console.log('debug connction auth entity error') }
	}




	/**
	 * authenticate socket client and if from internal server them make auth true default
	 */

	if (socket.handshake.query
		&& socket.handshake.query.server_key
		&& socket.handshake.query.server_key == config.SERVER_INTERNAL_COMMUNICATION_KEY) {
		socket.auth = true;

		socket_room = 'admin'.toUpperCase();
		socket.join(socket_room);
		console.log('room : ', socket_room);

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

		//update driver is_connected_to_socket column
		if (socket.auth_entity.type == 'DRIVER') {
			helper.updateDriverSocketConnectionStatus(eId, 1);
		}

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

			//update driver location
			helper.updateDriverLocation(socket.auth_entity.id, data.latitude, data.longitude);

			//if ride request is available send user also latitude longitude
			if (data.ride_request_id) {
				helper.getRideRequest(data.ride_request_id, socket.auth_entity.id, function (err, result) {
					if (err && !result.length) return;
					console.log('ride request fetched', result);
					io.sockets.in('USER_' + result[0].user_id).emit('driver_location_updated', data);
				});
			}

			//if trip_id is avaialbel send trip user latitude longitude
			if (data.trip_id) {
				helper.getUserIdsByTripId(data.trip_id, function (err, result) {
					if (err && !result.length) return;

					//loop through all user ids for a specific trip 
					result.forEach(function (row) {
						console.log('trip id user ', row.user_id);
						io.sockets.in('USER_' + row.user_id).emit('driver_location_updated', data);
					})

				});
			}


			//send location to admin
			io.sockets.in('ADMIN').emit('driver_location_updated', {
				latitude: data.latitude,
				longitude: data.longitude,
				driver_id: socket.auth_entity.id
			});



		} catch (e) {
			console.log(e);
			return;
		}

	});




	/**
	 * sending ride request to driver
	 * user sends ride request to driver directly with request id and user details
	 */
	socket.on('send_ride_request_driver', function (data) {

		console.log('new ride request', data)

		if (!data.request_id && !data.user_id && !data.driver_id && !data.user_fname && !data.user_lname
			&& !data.pickup_latitude && !data.pickup_longitude) {
			return;
		}

		//send request to driver
		io.sockets.in('DRIVER_' + data.driver_id).emit('new_ride_request', data);

	});



	/**
	 * reject new ride request from user by driver
	 */
	socket.on('reject_ride_request', function (data) {

		if (!data.user_id && !data.request_id && !data.driver_id) {
			reutrn;
		}

		//send request to user
		io.sockets.in('USER_' + data.user_id).emit('ride_request_rejected', data);

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
				console.log('send event to room ', room)
				io.sockets.in(room).emit(data.event_type, data.data);

			} catch (e) {
				console.log('send_event error : ' + e.message);
			}

		});


	});




	socket.on('disconnect', function (data) {
		console.log('disconnect', socket_room);
		socket.leave(socket_room);


		//if driver disconnected change is connected to socket column to 0
		if (socket.auth && socket.auth_entity && socket.auth_entity.type == 'DRIVER') {
			helper.updateDriverSocketConnectionStatus(socket.auth_entity.id, 0);
		}


	});




});



/* starting server on port and listen */
server.listen(config.SERVER_PORT, function () {
	console.log('listening on localhost:' + config.SERVER_PORT);
});
