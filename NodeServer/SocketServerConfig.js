
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

    IS_PRODUCTION : false,


    getConfig()
    {
        return this.IS_PRODUCTION ?  this.PRODUCTION_CONFIGS : this.LOCAL_CONFIGS;
    },

    LOCAL_CONFIGS : {

        SERVER_INTERNAL_COMMUNICATION_KEY: '123456789',
        SERVER_PORT: 3000,
        IS_HTTPS: false,
        HTTPS_KEY_PATH: '/home/ubuntu/.acme.sh/ghealth.net/ghealth.net.key',
        HTTPS_CERT_PATH: '/home/ubuntu/.acme.sh/ghealth.net/ghealth.net.cer',
        HTTPS_CA_PATH: '/home/ubuntu/.acme.sh/ghealth.net/ca.cer',

        BASE_URL: 'http://localhost/Highway/public', //without ritht '/'

        mysql : {
            host : 'localhost',
            user : 'root',
            password : 'root',
            database : 'highway'
        },
        
        CLIENT_AUTHENTICATE_TIMEOUT : 2000,

        DEBUG : true

    },

    PRODUCTION_CONFIGS : {

        SERVER_INTERNAL_COMMUNICATION_KEY: '123456789',
        SERVER_PORT: 3000,
        IS_HTTPS: false,
        HTTPS_KEY_PATH: '/home/ubuntu/.acme.sh/ghealth.net/ghealth.net.key',
        HTTPS_CERT_PATH: '/home/ubuntu/.acme.sh/ghealth.net/ghealth.net.cer',
        HTTPS_CA_PATH: '/home/ubuntu/.acme.sh/ghealth.net/ca.cer',

        BASE_URL: 'http://139.59.79.130', //without ritht '/'


        mysql: {
            host: 'localhost',
            user: 'root',
            password: 'root',
            database : 'highway'
        },

        CLIENT_AUTHENTICATE_TIMEOUT: 2000,


        DEBUG: false

    },

    
}