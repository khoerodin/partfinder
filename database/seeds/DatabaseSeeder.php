<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        factory('App\User')->create([
            'username' => 'super',
            'name' => 'Super Administrator',
            'email' => 'mamaskhoir@yahoo.com',
        ]);

        factory('App\User')->create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'labkecil@gmail.com',
        ]);

        factory('App\User')->create([
            'username' => 'khoerodin',
            'name' => 'Khoerodin',
            'email' => 'khoerodin@live.com',
        ]);

        factory('App\Role')->create([
            'name' => 'super',
            'label' => 'Super Administrator',
        ]);

        factory('App\Role')->create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);

        factory('App\Role')->create([
            'name' => 'user',
            'label' => 'User',
        ]);

        factory('App\Permission')->create([
            'name' => 'partnumber.view',
            'label' => 'Mambuka Halaman Pencarian',
        ]);

        factory('App\Permission')->create([
            'name' => 'partnumber.search',
            'label' => 'Melakukan Pencarian',
        ]);

        factory('App\Permission')->create([
            'name' => 'partnumber.download',
            'label' => 'Melakukan Download Hasil Pencarian',
        ]);

        factory('App\Permission')->create([
            'name' => 'import.view',
            'label' => 'Membuka Halaman Import',
        ]);

        factory('App\Permission')->create([
            'name' => 'import.import',
            'label' => 'Meng-import data',
        ]);

        factory('App\Permission')->create([
            'name' => 'user.register',
            'label' => 'Me-registrasikan User',
        ]);
    }
}
