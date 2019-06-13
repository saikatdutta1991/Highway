<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Hash;
use App\Models\Admin;


class ChangeAdminPassword extends Command
{

    protected $signature = 'change-admin-password';
    protected $description = 'This command is used to change admin password';

    public function handle()
    { 
        $this->line('Change admin password');
        $this->line('----------------------------------------------');

        /** ask user to enter admin email */
        $this->email = $this->ask("Enter admin email");

        /** find admin from db using email */
        $admin = Admin::where('email', $this->email)->first();

        /** if admin not found */
        if(!$admin) {
            return $this->error('Password change failed : invalid email');
        }


        /** ask user to enter current password */
        $this->currentPassword = $this->secret("Enter current password");

        /** match current password */
        if(!password_verify($this->currentPassword, $admin->password)) {
            return $this->error('Password change failed : invalid password');
        }

        /** ask user to enter new password and confirm passworld */
        $this->newPassword = $this->secret("Enter new password");
        $this->confirmedPassword = $this->secret("Enter confirmed password");

        if($this->newPassword !== $this->confirmedPassword) {
            return $this->error('Password change failed : password does not match');
        }


        $admin->password = Hash::make($this->newPassword);
        $admin->save();

        return $this->info('Password changed successfully');

    }


}