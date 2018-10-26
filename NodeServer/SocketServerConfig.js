
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

    IS_PRODUCTION: true,


    getConfig() {
        return this.IS_PRODUCTION ? this.PRODUCTION_CONFIGS : this.LOCAL_CONFIGS;
    },

    LOCAL_CONFIGS: {

        SERVER_INTERNAL_COMMUNICATION_KEY: '123456789',
        SERVER_PORT: 3000,
        IS_HTTPS: false,
        HTTPS_KEY_PATH: '',
        HTTPS_CERT_PATH: '',
        HTTPS_CA_PATH: '',

        BASE_URL: 'http://localhost/Highway/public', //without ritht '/'

        mysql: {
            host: 'localhost',
            user: 'root',
            password: 'root',
            database: 'highway',
            connectionLimit: 100,
            waitForConnections: true,
            queueLimit: 0,
            debug: true,
            wait_timeout: 28800,
            connect_timeout: 10
        },

        CLIENT_AUTHENTICATE_TIMEOUT: 2000,

        DEBUG: true

    },

    PRODUCTION_CONFIGS: {

        SERVER_INTERNAL_COMMUNICATION_KEY: '123456789',
        SERVER_PORT: 3000,
        IS_HTTPS: true,
        HTTPS_KEY_PATH: '/etc/letsencrypt/live/highway.capefox.in/privkey.pem',
        HTTPS_CERT_PATH: '/etc/letsencrypt/live/highway.capefox.in/fullchain.pem',
        HTTPS_CA_PATH: '/etc/letsencrypt/live/highway.capefox.in/privkey.pem',

        BASE_URL: 'https://highway.capefox.in', //without ritht '/'


        mysql: {
            host: 'localhost',
            user: 'root',
            password: 'root',
            database: 'highway',
            connectionLimit: 100,
            waitForConnections: true,
            queueLimit: 0,
            debug: true,
            wait_timeout: 28800,
            connect_timeout: 10
        },

        CLIENT_AUTHENTICATE_TIMEOUT: 5000,


        DEBUG: false

    },


}
