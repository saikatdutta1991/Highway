<?php


namespace App\Repositories;


use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;


class SocketIOClient
{



	public function __construct()
	{
        $this->socketUrl = config('socket_server.socket_url');
        $this->server_key = config('socket_server.server_internal_communication_key');
        $this->url = $this->socketUrl.'?server_key='.$this->server_key;
		$this->client = new Client(new Version2X($this->url));
	}




	public function sendEvent($data = [], $closeAfter = true)
	{
		try {

			if(!isset($this->is_init) || !$this->is_init) {
				$this->client->initialize();
				$this->is_init = true;
			}

			$this->client->emit('send_event', $data);

			if($closeAfter) {
				$this->is_init = false;
				$this->client->close();
			}
			

			return $this;

		} catch (\Exception $e) {
			return false;
		}

	}


	public function close()
	{
		$this->is_init = false;
		$this->client->close();
	}


	
}