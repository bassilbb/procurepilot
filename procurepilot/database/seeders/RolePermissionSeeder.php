<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Organization::all() as $org) {
            foreach (RolePermission::defaultGrants() as $role => $grants) {
                foreach ($grants as $module => $actions) {
                    RolePermission::updateOrCreate(
                        ['organization_id' => $org->id, 'role' => $role, 'module' => $module],
                        [
                            'can_view'    => str_contains($actions, 'v'),
                            'can_create'  => str_contains($actions, 'c'),
                            'can_edit'    => str_contains($actions, 'e'),
                            'can_delete'  => str_contains($actions, 'd'),
                            'can_approve' => str_contains($actions, 'a'),
                        ]
                    );
                }
            }
        }
    }
}
