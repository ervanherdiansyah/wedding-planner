<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
  public function run()
  {
    $menusData = [
      [
        'title' => 'Dashboards',
        'slug' => 'dashboards',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'permissions' => []
      ],
      [
        'title' => 'Overviews',
        'slug' => 'overviews',
        'icon' => 'overviews',
        'is_active' => 1,
        'parent' => 1,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Informasi Pernikahan',
        'slug' => 'informasi-pernikahan',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'permissions' => []
      ],
      [
        'title' => 'Informasi Pengantin',
        'slug' => 'informasi-pengantin',
        'icon' => 'informasi-pengantin',
        'is_active' => 1,
        'parent' => 3,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Informasi Acara',
        'slug' => 'informasi-acara',
        'icon' => 'informasi-acara',
        'is_active' => 1,
        'parent' => 3,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Panitia',
        'slug' => 'panitia',
        'icon' => 'panitia',
        'is_active' => 1,
        'parent' => 3,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'List Peserta Foto',
        'slug' => 'list-peserta-foto',
        'icon' => 'list-peserta-foto',
        'is_active' => 1,
        'parent' => 3,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'List Tamu VIP',
        'slug' => 'list-tamu-vip',
        'icon' => 'list-tamu-vip',
        'is_active' => 1,
        'parent' => 3,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Song List',
        'slug' => 'song-list',
        'icon' => 'song-list',
        'is_active' => 1,
        'parent' => 3,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Perencanaan',
        'slug' => 'perencanaan',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'permissions' => []
      ],
      [
        'title' => 'Budget',
        'slug' => 'budget',
        'icon' => 'budget',
        'is_active' => 1,
        'parent' => 10,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'To-Do List',
        'slug' => 'to-do-list',
        'icon' => 'to-do-list',
        'is_active' => 1,
        'parent' => 10,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Vendor',
        'slug' => 'vendor',
        'icon' => 'vendor',
        'is_active' => 1,
        'parent' => 10,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Seragam',
        'slug' => 'seragam',
        'icon' => 'seragam',
        'is_active' => 1,
        'parent' => 10,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Rundown',
        'slug' => 'rundown',
        'icon' => 'rundown',
        'is_active' => 1,
        'parent' => 10,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'title' => 'Seserahan',
        'slug' => 'seserahan',
        'icon' => 'seserahan',
        'is_active' => 1,
        'parent' => 10,
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ]
    ];

    $menuIds = [];
    $permissions = [];

    foreach ($menusData as $menuData) {

      // Insert menu
      $menuId = DB::table('menus')->insertGetId([
        'name' => $menuData['title'],
        'slug' => $menuData['slug'],
        'parent' => $menuData['parent'],
        'icon' => $menuData['icon'] ?? null,
        'is_active' => $menuData['is_active'],
        'created_at' => now(),
        'updated_at' => now()
      ]);

      // Simpan slug → id untuk child selanjutnya
      $menuIds[$menuData['slug']] = $menuId;

      // Tambahkan permission
      foreach ($menuData['permissions'] as $action) {
        $permissions[] = [
          'name' => "{$action} {$menuData['title']}",
          'created_at' => now(),
          'updated_at' => now()
        ];
      }
    }

    if (!empty($permissions)) {
      DB::table('permissions')->insert($permissions);
    }
  }
}
