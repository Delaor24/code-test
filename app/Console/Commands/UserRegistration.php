<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserRegistration extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User Create Command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->info("User Create Start");
        $datas = [
            [
                'name' => 'Deloar Hossain',
                'account_type' => 'Individual',
                'email' => 'deloar.engr@gmail.com',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Sadia Afrin',
                'account_type' => 'Individual',
                'email' => 'sadia@gmail.com',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Iqbal Hossain',
                'account_type' => 'Business',
                'email' => 'iqbal@gmail.com',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Rahat Hossain',
                'account_type' => 'Business',
                'email' => 'rahat@gmail.com',
                'password' => bcrypt('123456'),
            ],

        ];

        foreach ($datas as $user) {
            try {
                $checkExistUser = User::where('email', $user['email'])->first();
                if (!$checkExistUser) {
                    User::create($user);
                } else {
                    $this->info("This user allready created. Email:" . $user['email']);
                }
            } catch (\Throwable $th) {
                $this->info("Something wrong!");
            }
        }

        $this->info("User Create End");
    }
}
