<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AdInactiveCommand extends Command
{
    // Command name
    protected $signature = 'ads:inactive';

    // Command description
    protected $description = 'Automatically call the adInactive API route';

    public function __construct()
    {
        parent::__construct();
    }

    // The logic to be executed when this command is called
    public function handle()
    {
        // Send a POST request to the adInactive route
        $response = Http::post(url('/adInactive'));

        // Check the response status
        if ($response->successful()) {
            $this->info('Ad Inactive API called successfully!');
        } else {
            $this->error('Failed to call Ad Inactive API. Status: ' . $response->status());
        }
    }
}
