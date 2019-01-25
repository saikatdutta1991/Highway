<?php

//socket server configuration
return [
	"socket_url" => env('SOCKET_URL', 'https://highway.capefox.in:3000'),
	"server_internal_communication_key" => env('SOCKET_COMM_KEY', '123456789')
];