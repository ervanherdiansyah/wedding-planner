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
        'name' => 'Dashboards',
        'slug' => 'dashboards',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'order' => 0,
        'url' => '/dashboards',
        'permissions' => ['Read']
      ],
      [
        'name' => 'Overviews',
        'slug' => 'overviews',
        'icon' => 'overviews',
        'is_active' => 1,
        'parent' => 1,
        'order' => 0,
        'url' => '/dashboards/overviews',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Informasi Pernikahan',
        'slug' => 'informasi-pernikahan',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'order' => 1,
        'url' => '/informasi-pernikahan',
        'permissions' => ['Read']
      ],
      [
        'name' => 'Informasi Pengantin',
        'slug' => 'informasi-pengantin',
        'icon' => 'informasi-pengantin',
        'is_active' => 1,
        'parent' => 3,
        'order' => 0,
        'url' => '/informasi-pernikahan/informasi-pengantin',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Informasi Acara',
        'slug' => 'informasi-acara',
        'icon' => 'informasi-acara',
        'is_active' => 1,
        'parent' => 3,
        'order' => 1,
        'url' => '/informasi-pernikahan/informasi-acara',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Panitia',
        'slug' => 'panitia',
        'icon' => 'panitia',
        'is_active' => 1,
        'parent' => 3,
        'order' => 2,
        'url' => '/informasi-pernikahan/panitia',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'List Peserta Foto',
        'slug' => 'list-peserta-foto',
        'icon' => 'list-peserta-foto',
        'is_active' => 1,
        'parent' => 3,
        'order' => 3,
        'url' => '/informasi-pernikahan/list-peserta-foto',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'List Tamu VIP',
        'slug' => 'list-tamu-vip',
        'icon' => 'list-tamu-vip',
        'is_active' => 1,
        'parent' => 3,
        'order' => 4,
        'url' => '/informasi-pernikahan/list-tamu-vip',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Song List',
        'slug' => 'song-list',
        'icon' => 'song-list',
        'is_active' => 1,
        'parent' => 3,
        'order' => 5,
        'url' => '/informasi-pernikahan/song-list',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Perencanaan',
        'slug' => 'perencanaan',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'order' => 2,
        'url' => '/perencanaan',
        'permissions' => ['Read']
      ],
      [
        'name' => 'Budget',
        'slug' => 'budget',
        'icon' => 'budget',
        'is_active' => 1,
        'parent' => 10,
        'order' => 0,
        'url' => '/perencanaan/budget',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'To-Do List',
        'slug' => 'to-do-list',
        'icon' => 'to-do-list',
        'is_active' => 1,
        'parent' => 10,
        'order' => 1,
        'url' => '/perencanaan/to-do-list',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Vendor',
        'slug' => 'vendor',
        'icon' => 'vendor',
        'is_active' => 1,
        'parent' => 10,
        'order' => 2,
        'url' => '/perencanaan/vendor',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Seragam',
        'slug' => 'seragam',
        'icon' => 'seragam',
        'is_active' => 1,
        'parent' => 10,
        'order' => 3,
        'url' => '/perencanaan/seragam',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Rundown',
        'slug' => 'rundown',
        'icon' => 'rundown',
        'is_active' => 1,
        'parent' => 10,
        'order' => 4,
        'url' => '/perencanaan/rundown',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Seserahan',
        'slug' => 'seserahan',
        'icon' => 'seserahan',
        'is_active' => 1,
        'parent' => 10,
        'order' => 5,
        'url' => '/perencanaan/seserahan',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      // Tambahan Master Data
      [
        'name' => 'Master Data',
        'slug' => 'master-data',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'order' => 3,
        'url' => '/master-data',
        'permissions' => ['Read']
      ],
      [
        'name' => 'User',
        'slug' => 'user',
        'icon' => 'user',
        'is_active' => 1,
        'parent' => 17,
        'order' => 0,
        'url' => '/master-data/user',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      // Tambahan Transaction
      [
        'name' => 'Transaction',
        'slug' => 'transaction',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'order' => 4,
        'url' => '/transaction',
        'permissions' => ['Read']
      ],
      [
        'name' => 'Order',
        'slug' => 'order',
        'icon' => 'order',
        'is_active' => 1,
        'parent' => 19,
        'order' => 0,
        'url' => '/transaction/order',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Payment',
        'slug' => 'payment',
        'icon' => 'payment',
        'is_active' => 1,
        'parent' => 19,
        'order' => 1,
        'url' => '/transaction/payment',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      // Tambahan Settings
      [
        'name' => 'Settings',
        'slug' => 'settings',
        'icon' => null,
        'is_active' => 1,
        'parent' => null,
        'order' => 5,
        'url' => '/settings',
        'permissions' => ['Read']
      ],
      [
        'name' => 'Menu',
        'slug' => 'menu',
        'icon' => 'menu',
        'is_active' => 1,
        'parent' => 22,
        'order' => 0,
        'url' => '/settings/menu',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ],
      [
        'name' => 'Package',
        'slug' => 'package',
        'icon' => 'package',
        'is_active' => 1,
        'parent' => 22,
        'order' => 1,
        'url' => '/settings/package',
        'permissions' => ['Create', 'Read', 'Update', 'Delete']
      ]
    ];


    $menuIds = [];
    $permissions = [];

    foreach ($menusData as $menuData) {

      // Insert menu
      $menuId = DB::table('menus')->insertGetId([
        'name' => $menuData['name'],
        'slug' => $menuData['slug'],
        'parent' => $menuData['parent'],
        'icon' => $menuData['icon'] ?? null,
        'order' => $menuData['order'] ?? null,
        'url' => $menuData['url'] ?? null,
        'is_active' => $menuData['is_active'],
        'created_at' => now(),
        'updated_at' => now()
      ]);

      // Simpan slug → id untuk child selanjutnya
      $menuIds[$menuData['slug']] = $menuId;

      // Tambahkan permission
      foreach ($menuData['permissions'] as $action) {
        $permissions[] = [
          'name' => "{$action} {$menuData['name']}",
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
