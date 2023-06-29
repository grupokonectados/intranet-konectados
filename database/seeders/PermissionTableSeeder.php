<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
  
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',

           'strategy-list',
           'strategy-create',
           'strategy-edit',
           'strategy-delete',


           'clients-list',
           'clients-create',
           'clients-edit',
           'clients-delete',

           'root-list',
           'root-create',
           'root-edit',
           'root-delete',
        ];
     
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}