<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

require_once 'twitteroauth/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

//require_once './app/Http/Controllers/BandBotController.php';

require_once $baseDir . '/Http/Controllers/BandBotController.php';

use App\Http\Controllers;

use App\Http\Controllers\BandBotController;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TestCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TestCommand description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        
        //$class = new App\Http\Controllers\BandBotController();
        
        $class = new \App\Http\Controllers\BandBotController();
        
        $result = $class->Tweet();
       
        echo $result;
    }
}
