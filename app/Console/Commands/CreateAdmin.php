<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Hash;


class CreateAdmin extends Command
{

    protected $signature = 'create-admin';
    protected $description = 'used to create admin';

    public function handle()
    { 
        $this->askAdminDetails();
        $this->createAdmin();
    }


    protected function createAdmin()
    {
        try {

            DB::table('admins')->insert([

                'name'         => $this->name,
                'email'        => $this->email,
                'password'     => Hash::make($this->password),
                'role'         => $this->role,
                'purpose'      => $this->purpose,
                'last_login_time' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ]);

            $this->showFakeProgressbar();


            $this->info("\n".'Admin created successfully'."\n");

        } catch(\Exception $e) {
            $this->error('Admin create failed: '.$e->getMessage());
        }

    }




    protected function showFakeProgressbar()
    {
        $bar = $this->output->createProgressBar(3);
        $count = 1;
        while($count <= 3) {
            $bar->advance();
            sleep(1);
            $count++;
        }

        $bar->finish();
    }




    protected function askAdminDetails()
    {


        $this->line('To create admin answer the following questions');
        $this->line('----------------------------------------------');


        //taking name
        $this->isNameValid = false;
        while(!$this->isNameValid) {

            $this->name = $this->ask('Enter Admin Name(3-128)');

            if(strlen($this->name) < 3 || strlen($this->name) > 128) {
                $this->isNameValid = false;
                $this->error('Name length must be 3-128');
            } else {
                $this->isNameValid = true;
            }
        }



        //taking email
        $this->isEmailValid = false;
        while(!$this->isEmailValid) {

            $this->email = strtolower(trim($this->ask('Enter Email')));

            if(strlen($this->email) < 3 || strlen($this->email) > 128) {
                $this->isEmailValid = false;
                $this->error('Email length must be 3-128');
            } else {
                $this->isEmailValid = true;
            }
        }


        //taking role
        $this->role = $this->choice('Choose Admin Role?', ['ROOT', 'GUEST'], 0);

        //taking purpose
        $this->purpose = $this->ask('Enter Admin Purpose');



        //taking password 
        $this->isPasswordValid = false;
        while(!$this->isPasswordValid) {

            $this->password = $this->secret('Enter Password');

            if(strlen($this->password) < 6 || strlen($this->password) > 100) {
                $this->isPasswordValid = false;
                $this->error('Password length must be 6-100');
                continue;
            } 

            $this->cnf_password = $this->secret('Enter Password Confirm');


            if($this->password != $this->cnf_password) {
                $this->isPasswordValid = false;
                $this->error('Password not matched');
            } else {
                $this->isPasswordValid = true;
            }



        }

  
    }

}