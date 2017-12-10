/**
 * this file stores helper functions
 */
module.exports = function (dbConn) {

    var module = {};

    module.conn = dbConn;
    module.updateDriverSocketConnectionStatus = function (driverId, isConnectedToSocket) {
        var sql = "UPDATE drivers SET is_connected_to_socket = " + isConnectedToSocket + " WHERE id = '" + driverId + "';";
        module.conn.query(sql, function (err, result) {
            console.log('driver is_connected_to_socket update query');
            console.log(err, result)
        });
    }


    module.updateDriverLocation = function (driverId, latitude, longitude) {
        var sql = "UPDATE drivers "
            + "SET latitude = " + latitude + ", longitude = " + longitude
            + " WHERE id = " + driverId;

        console.log('update driver location query : ' + sql);
        module.conn.query(sql, function (err, result) {
            console.log('update driver location response', err, result);
        });
    }


    module.getRideRequest = function (rideRequestId, driverId, callback) {
        var sql = "SELECT * FROM ride_requests WHERE "
            + "id = " + rideRequestId + " AND "
            + "driver_id = " + driverId;
        console.log('fetch ride request query: ', sql)
        module.conn.query(sql, function (err, result) {
            callback(err, result);
        });
    }


    return module;

};