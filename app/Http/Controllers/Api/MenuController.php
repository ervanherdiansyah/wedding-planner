<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function getMenu()
    {
        try {
            // Get all menus ordered by order
            $menus = Menu::orderBy('order', 'asc')->get();

            // Build hierarchical menu structure
            $menuTree = $this->buildMenuTree($menus);

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $menuTree
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getMenuByProjectId($project_id)
    {
        try {
            $menus = Menu::orderBy('order', 'asc')
                ->where('project_id', $project_id)
                ->get();

            // Build hierarchical menu structure
            $menuTree = $this->buildMenuTree($menus);

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $menuTree
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Build hierarchical menu tree structure
     */
    private function buildMenuTree($menus, $parentId = null)
    {
        $tree = [];

        foreach ($menus as $menu) {
            // Check if this menu belongs to the current parent level
            if ($menu->parent == $parentId) {
                // Get permissions for this menu
                $permissions = Permission::where('name', 'LIKE', "% {$menu->name}")
                    ->get()
                    ->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                        ];
                    });

                // Build menu item
                $menuItem = [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'parent' => $menu->parent,
                    'icon' => $menu->icon,
                    'url' => $menu->url,
                    'order' => $menu->order,
                    'is_active' => $menu->is_active,
                    'permissions' => $permissions,
                    'created_at' => $menu->created_at,
                    'updated_at' => $menu->updated_at,
                ];

                // Recursively get children
                $children = $this->buildMenuTree($menus, $menu->id);
                if (!empty($children)) {
                    $menuItem['children'] = $children;
                }

                $tree[] = $menuItem;
            }
        }

        return $tree;
    }

    public function getMenuById($id)
    {
        try {
            $menu = Menu::findOrFail($id);

            // Get all permissions that contain menu name
            $permissions = Permission::where('name', 'LIKE', "% {$menu->name}")
                ->get()
                ->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                });

            $data = [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'parent' => $menu->parent,
                'icon' => $menu->icon,
                'url' => $menu->url,
                'order' => $menu->order,
                'is_active' => $menu->is_active,
                'permissions' => $permissions,
                'created_at' => $menu->created_at,
                'updated_at' => $menu->updated_at,
            ];

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function createMenu(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'icon' => 'nullable|string|max:255',
                'parent' => 'nullable|integer|exists:menus,id',
                'order' => 'nullable|integer',
                'url' => 'nullable|string|max:255',
                'is_active' => 'required|boolean',
                'permissions' => 'nullable|array',
            ]);

            $menu = null;
            $permissionsCreated = [];

            DB::transaction(function () use ($validated, &$menu, &$permissionsCreated) {
                // Create Menu
                $menu = Menu::create([
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']),
                    'icon' => $validated['icon'] ?? null,
                    'parent' => $validated['parent'] ?? null,
                    'order' => $validated['order'] ?? 0,
                    'url' => $validated['url'] ?? null,
                    'is_active' => $validated['is_active'],
                ]);

                // Create Permissions with menu name
                if (!empty($validated['permissions'])) {
                    foreach ($validated['permissions'] as $permission) {
                        $perm = Permission::create([
                            'name' => "{$permission} {$validated['name']}", // Use menu name here
                        ]);
                        $permissionsCreated[] = $perm;
                    }
                }
            });

            return response()->json([
                'message' => 'Create Data Successfully',
                'data' => [
                    'menu' => $menu,
                    'permissions' => $permissionsCreated
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateMenu(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'icon' => 'nullable|string|max:255',
                'parent' => 'nullable|integer|exists:menus,id',
                'order' => 'nullable|integer',
                'url' => 'nullable|string|max:255',
                'is_active' => 'required|boolean',
                'permissions' => 'nullable|array',
            ]);

            $menu = Menu::findOrFail($id);
            $permissionsCreated = [];

            DB::transaction(function () use ($validated, &$menu, &$permissionsCreated) {
                // Update menu
                $menu->update([
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']),
                    'icon' => $validated['icon'] ?? null,
                    'parent' => $validated['parent'] ?? null,
                    'order' => $validated['order'] ?? 0,
                    'url' => $validated['url'] ?? null,
                    'is_active' => $validated['is_active'],
                ]);

                if (!empty($validated['permissions'])) {
                    foreach ($validated['permissions'] as $permission) {
                        Permission::updateOrCreate(
                            ['name' => "{$permission} {$menu->getOriginal('name')}"],
                            ['name' => "{$permission} {$validated['name']}"]
                        );
                    }
                }
            });

            return response()->json([
                'message' => 'Update Data Successfully',
                'data' => [
                    'menu' => $menu,
                    'permissions' => $permissionsCreated
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteMenu($id)
    {
        try {
            Menu::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function getMenuAccess(Request $request)
    {
        try {
            $packageId = $request->input('package_id');

            // Ambil semua menu
            $menus = DB::table('menus')
                ->select('id', 'name', 'slug', 'parent', 'order', 'icon', 'url', 'is_active')
                ->orderBy('order', 'asc')
                ->get();

            // Ambil semua permission
            $permissions = DB::table('permissions')
                ->select('id', 'name')
                ->get();

            // Ambil data assign dari table menu_packages
            $assigned = collect();
            if ($packageId) {
                $assigned = DB::table('menu_packages')
                    ->where('package_id', $packageId)
                    ->get()
                    ->map(fn($row) => [
                        'menu_id' => $row->menu_id,
                        'permission_id' => $row->permission_id
                    ]);
            }

            // Recursive function untuk bangun tree menu
            $buildTree = function ($parentId) use ($menus, $permissions, $assigned, &$buildTree) {
                return $menus
                    ->where('parent', $parentId)
                    ->sortBy('order')
                    ->map(function ($menu) use ($permissions, $assigned, $buildTree) {
                        // Ambil permission untuk menu ini
                        $menuPerms = $permissions->filter(function ($perm) use ($menu) {
                            return str_contains(strtolower($perm->name), strtolower($menu->name));
                        })->map(function ($perm) use ($menu, $assigned) {
                            $action = explode(' ', $perm->name)[0];
                            return [
                                'permission_id' => $perm->id,
                                'permission_name' => $action,
                                'assigned' => $assigned->contains(
                                    fn($a) =>
                                    $a['menu_id'] == $menu->id &&
                                        $a['permission_id'] == $perm->id
                                )
                            ];
                        })->values();

                        // Menu assigned jika minimal 1 permission assigned
                        $menuAssigned = $assigned->contains(
                            fn($a) => $a['menu_id'] == $menu->id
                        );

                        return [
                            'menu_id' => $menu->id,
                            'menu_name' => $menu->name,
                            'menu_slug' => $menu->slug,
                            'menu_parent' => $menu->parent,
                            'menu_icon' => $menu->icon,
                            'menu_url' => $menu->url,
                            'menu_order' => $menu->order,
                            // 'menu_is_active' => $menu->is_active,
                            'assigned' => $menuAssigned,
                            'permissions' => $menuPerms,
                            'children' => $buildTree($menu->id)
                        ];
                    })->values();
            };

            // Build menu mulai dari root
            $tree = $buildTree(null);

            // Ambil list menu yang parent-nya null untuk form input parent menu
            $parents = $menus->whereNull('parent')->values();

            return response()->json([
                'message' => 'Successfully retrieved access data',
                'data' => [
                    'menuAccess' => $tree,
                    'menuParents' => $parents
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getParentMenus()
    {
        try {
            $parentMenus = Menu::whereNull('parent')
                ->orderBy('order', 'asc')
                ->get()
                ->map(function ($menu) {
                    // Get all permissions that contain menu name
                    $permissions = Permission::where('name', 'LIKE', "% {$menu->name}")
                        ->get()
                        ->map(function ($permission) {
                            return [
                                'id' => $permission->id,
                                'name' => $permission->name,
                                'action' => explode(' ', $permission->name)[0]
                            ];
                        });

                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'slug' => $menu->slug,
                        'icon' => $menu->icon,
                        'url' => $menu->url,
                        'order' => $menu->order,
                        'is_active' => $menu->is_active,
                        'permissions' => $permissions,
                        'created_at' => $menu->created_at,
                        'updated_at' => $menu->updated_at
                    ];
                });

            return response()->json([
                'message' => 'Fetch Parent Menus Successfully',
                'data' => $parentMenus
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // public function getMenuAccess()
    // {
    //     try {
    //         $user = auth()->user();
    //         $menus = Menu::whereHas('packages', function ($q) use ($user) {
    //             $q->where('packages.id', $user->package);
    //         })->get();
    //         return response()->json(['message' => 'Fetch Data Successfully', 'data' => $menus], 200);
    //     } catch (\Exception $th) {
    //         return response()->json(['message' => $th->getMessage()], 500);
    //     }
    // }

    // public function getMenu()
    // {
    //     try {
    //         // Get all menus ordered by order
    //         $menus = Menu::orderBy('order', 'asc')->get()
    //             ->map(function ($menu) {
    //                 // Get all permissions that contain menu name
    //                 $permissions = Permission::where('name', 'LIKE', "% {$menu->name}")
    //                     ->get()
    //                     ->map(function ($permission) {
    //                         // Extract action name (Create, Read, etc)
    //                         return [
    //                             'id' => $permission->id,
    //                             'name' => $permission->name,
    //                         ];
    //                     });

    //                 return [
    //                     'id' => $menu->id,
    //                     'name' => $menu->name,
    //                     'slug' => $menu->slug,
    //                     'parent' => $menu->parent,
    //                     'icon' => $menu->icon,
    //                     'url' => $menu->url,
    //                     'order' => $menu->order,
    //                     'is_active' => $menu->is_active,
    //                     'permissions' => $permissions,
    //                     'created_at' => $menu->created_at,
    //                     'updated_at' => $menu->updated_at,
    //                 ];
    //             });

    //         return response()->json([
    //             'message' => 'Fetch Data Successfully',
    //             'data' => $menus
    //         ], 200);
    //     } catch (\Exception $th) {
    //         return response()->json([
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    // public function getMenuByProjectId($project_id)
    // {
    //     try {
    //         $menus = Menu::orderBy('order', 'asc')->where('project_id', $project_id)
    //             ->get()
    //             ->map(function ($menu) {
    //                 // Get all permissions that contain menu name
    //                 $permissions = Permission::where('name', 'LIKE', "% {$menu->name}")
    //                     ->get()
    //                     ->map(function ($permission) {
    //                         return [
    //                             'id' => $permission->id,
    //                             'name' => $permission->name,
    //                         ];
    //                     });

    //                 return [
    //                     'id' => $menu->id,
    //                     'name' => $menu->name,
    //                     'slug' => $menu->slug,
    //                     'parent' => $menu->parent,
    //                     'icon' => $menu->icon,
    //                     'url' => $menu->url,
    //                     'order' => $menu->order,
    //                     'is_active' => $menu->is_active,
    //                     'permissions' => $permissions,
    //                     'created_at' => $menu->created_at,
    //                     'updated_at' => $menu->updated_at,
    //                 ];
    //             });

    //         return response()->json([
    //             'message' => 'Fetch Data Successfully',
    //             'data' => $menus
    //         ], 200);
    //     } catch (\Exception $th) {
    //         return response()->json([
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    // }
}
