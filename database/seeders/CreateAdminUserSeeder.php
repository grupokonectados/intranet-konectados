<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
  
class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Administrador', 
            'email' => 'admin@1.com',
            'password' => bcrypt('123'),
            'password_changed_at' => Carbon::now()->toDateTimeString()
        ]);

        $user2 = User::create([
            'name' => 'supervisor', 
            'email' => 'sp@1.com',
            'password' => bcrypt('123')
        ]);
    
        $role = Role::create(['name' => 'Admin']);
        $role2 = Role::create(['name' => 'Supervisor']);
     
        $permissionsSuperAdmin = Permission::pluck('id','id')->all();

        $permissionsSupervisor = Permission::where('name', 'not like', '%root%')->where('name', 'not like', '%role%')->pluck('id','id');
   
        $role->syncPermissions($permissionsSuperAdmin);
        $role2->syncPermissions($permissionsSupervisor);
     
        $user->assignRole([$role->id]);
        $user2->assignRole([$role2->id]);
    }
}