<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data =  [ 
            [
                'name'=>'student',
            ],
            [
                'name'=>'teacher'
            ]
        ];

        foreach($data as $d)
        {
            Role::create($d);
        }
    }
}
