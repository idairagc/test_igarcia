<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\User; 
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	//creamos el admin
        DB::table('users')->insert([
            'name' 		=> 'Admin',
            'surname' 	=> 'Admin',
            'email' 	=> 'admin@mitest.com',
            'password' 	=> bcrypt('12345678abc'), //12345678abc
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //creamos los roles
        DB::table('roles')->insert([
            'name' => 'Admin',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('roles')->insert([
            'name' => 'Employee',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' => 'App\User',
            'model_id' => 1,
        ]);
    }
}
