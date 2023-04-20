<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Orutu Akposieyefa Williams',
                'email' => 'info@kerdibank.com',
                'password' => bcrypt('Password@1!')
            ],
            [
                'name' => 'Eniola Sarah Akposieyefa',
                'email' => 'orutu1@gmail.com',
                'password' => bcrypt('Password@1!')
            ]
        ];
        collect($users)->each(function($user) {
            $create = (new \App\Models\User)->create([
                'name' => $user['name'],
                "email" =>  $user['email'],
                "password" =>  $user['password'],
                "email_verified_at"=>  now()
            ]);
        });
    }
}
