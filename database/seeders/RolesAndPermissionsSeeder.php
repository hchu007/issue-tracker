<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create projects']);
        Permission::create(['name' => 'view projects']);
        Permission::create(['name' => 'edit projects']);
        Permission::create(['name' => 'delete projects']);

        Permission::create(['name' => 'create tickets']);
        Permission::create(['name' => 'view tickets']);
        Permission::create(['name' => 'edit tickets']);
        Permission::create(['name' => 'delete tickets']);

        // create roles and assign created permissions
        $role1 = Role::create(['name' => 'admin']);
        $role1->givePermissionTo(Permission::all());

        $role2 = Role::create(['name' => 'manager'])
            ->givePermissionTo([
                'create projects',
                'view projects',
                'edit projects',

                'create tickets',
                'view tickets',
                'edit tickets',
                'delete tickets',
            ]);

        $role3 = Role::create(['name' => 'developer'])
            ->givePermissionTo([
                'view projects',

                'view tickets',
                'edit tickets',

            ]);

        $role4 = Role::create(['name' => 'guest'])
            ->givePermissionTo([
                'view projects',
                'view tickets',
            ]);

        // create demo users
        $userAdmin = User::factory()->create();
        $userAdmin->assignRole($role1);

        $userCollection = User::factory()->count(3)->create();
        foreach ($userCollection as $user) {
            $user->assignRole($role2);
        }

        $userCollection = User::factory()->count(6)->create();
        foreach ($userCollection as $user) {
            $user->assignRole($role3);
        }

        $userGuest = User::factory()->create();
        $userGuest->assignRole($role4);
    }
}
