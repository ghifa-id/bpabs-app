<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $users = [
                [
                    'name' => 'Admin',
                    'nik' => '1201',
                    'alamat' => 'Sungai Sariak',
                    'no_hp' => '0811000000',
                    'role' => 'admin',
                    'email' => 'admin@gmail.com',
                    'username' => 'admin',
                    'password' => bcrypt('admin123'),
                    'status' => 'active',
                ],
                [
                    'name' => 'Pelanggan',
                    'nik' => '1202',
                    'alamat' => 'Sungai Sariak',
                    'no_hp' => '0811000001',
                    'role' => 'pelanggan',
                    'email' => 'pelanggan@gmail.com',
                    'username' => 'pelanggan',
                    'password' => bcrypt('pelanggan123'),
                    'status' => 'active',
                ],
                [
                    'name' => 'Superuser',
                    'nik' => '1203',
                    'alamat' => 'Sungai Sariak',
                    'no_hp' => '0811000002',
                    'role' => 'superuser',
                    'email' => 'superuser@gmail.com',
                    'username' => 'superuser',
                    'password' => bcrypt('superuser123'),
                    'status' => 'active',
                ],
                [
                    'name' => 'ghifa',
                    'nik' => '1204',
                    'alamat' => 'Sungai Sariak',
                    'no_hp' => '0811000003',
                    'role' => 'superuser',
                    'email' => 'ghifa@gmail.com',
                    'username' => 'ghifa',
                    'password' => bcrypt('ibnudarma'),
                    'status' => 'active',
                ],
                [
                    'name' => 'edi amanto',
                    'nik' => '1205',
                    'alamat' => 'Sungai Sariak',
                    'no_hp' => '0811000004',
                    'role' => 'pelanggan',
                    'email' => 'edi.amanto@gmail.com',
                    'username' => 'ediganteng',
                    'password' => bcrypt('ediamanto123'),
                    'status' => 'active',
                ],
                [
                    'name' => 'deri',
                    'nik' => '1206',
                    'alamat' => 'Sungai Sariak',
                    'no_hp' => '0812000005',
                    'role' => 'petugas',
                    'email' => 'deri@gmail.com',
                    'username' => 'deriganteng',
                    'password' => bcrypt('deria123'),
                    'status' => 'active',
                ],
            ];

            foreach ($users as $key => $user) {
                User::create($user);
        }
    }
}
