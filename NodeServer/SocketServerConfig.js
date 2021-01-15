
/**
* This file is part of Highway App
* Author : Saikat Dutta
* Use : This file is used for Node Socket server configuration
* installtion guide :

    npm install express
    npm install fs
    npm install socket.io
    npm install mysql
*/

module.exports = {
    SERVER_INTERNAL_COMMUNICATION_KEY: process.env.SOCKET_COMM_KEY,
    SERVER_PORT: parseInt(process.env.SOCKET_PORT, 10),
    IS_HTTPS: false,
    HTTPS_KEY_PATH: '', // /etc/letsencrypt/live/highway.capefox.in/privkey.pem
    HTTPS_CERT_PATH: '', // /etc/letsencrypt/live/highway.capefox.in/fullchain.pem
    HTTPS_CA_PATH: '', // /etc/letsencrypt/live/highway.capefox.in/privkey.pem
    BASE_URL: process.env.APP_URL || '', //without ritht '/'
    mysql: {
        host: process.env.DB_HOST,
        user: process.env.DB_USERNAME,
        password: process.env.DB_PASSWORD,
        database: process.env.DB_DATABASE,
        connectionLimit: 100,
        waitForConnections: true,
        queueLimit: 0,
        debug: true,
        wait_timeout: 28800,
        connect_timeout: 10
    },
    CLIENT_AUTHENTICATE_TIMEOUT: 5000,
    DEBUG: true
}