<?php

namespace App\Models\Events;

use App\Models\Driver;
use Illuminate\Queue\SerializesModels;

class DriverCreated
{
    use SerializesModels;

    public $driver;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Driver  $driver
     * @return void
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }
}