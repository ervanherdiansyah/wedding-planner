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
            $Menu = Menu::get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Menu], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getMenuByProjectId($project_id)
    {
        try {
            $Menu = Menu::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Menu], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getMenuById($id)
    {
        try {
            $Menu = Menu::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Menu], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function createMenu(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'icon' => 'nullable|string|max:255',
                'parent' => 'nullable|integer|exists:menus,id',
                'order' => 'nullable|integer',
                'url' => 'nullable|string|max:255',
                'is_active' => 'required|boolean',
                'permissions' => 'required|array',
                'permissions.*' => 'string|in:Create,Update,Delete'
            ]);

            $menu = null;
            $permissionsCreated = [];

            DB::transaction(function () use ($validated, &$menu, &$permissionsCreated) {
                // Buat Menu
                $menu = Menu::create([
                    'title' => $validated['title'],
                    'slug' => Str::slug($validated['title']),
                    'icon' => $validated['icon'] ?? null,
                    'parent' => $validated['parent'] ?? null,
                    'order' => $validated['order'] ?? 0,
                    'url' => $validated['url'] ?? null,
                    'is_active' => $validated['is_active'],
                ]);

                // Buat Permissions
                foreach ($validated['permissions'] as $permission) {
                    $perm = Permission::create([
                        'name' => "{$permission} {$menu->title}",
                    ]);
                    $permissionsCreated[] = $perm;
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
                'title' => 'required|string|max:255',
                'icon' => 'nullable|string|max:255',
                'parent' => 'nullable|integer|exists:menus,id',
                'order' => 'nullable|integer',
                'url' => 'nullable|string|max:255',
                'is_active' => 'required|boolean',
                'permissions' => 'required|array',
                'permissions.*' => 'string|in:Create,Update,Delete'
            ]);

            $menu = Menu::findOrFail($id);
            $permissionsCreated = [];

            DB::transaction(function () use ($validated, &$menu, &$permissionsCreated) {
                // Update menu
                $menu->update([
                    'title' => $validated['title'],
                    'slug' => Str::slug($validated['title']),
                    'icon' => $validated['icon'] ?? null,
                    'parent' => $validated['parent'] ?? null,
                    'order' => $validated['order'] ?? 0,
                    'url' => $validated['url'] ?? null,
                    'is_active' => $validated['is_active'],
                ]);

                // Hapus permission lama
                Permission::where('name', 'like', "%{$menu->title}%")->delete();

                // Buat permission baru
                foreach ($validated['permissions'] as $permission) {
                    $perm = Permission::create([
                        'name' => "{$permission} {$menu->title}",
                    ]);
                    $permissionsCreated[] = $perm;
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
                ->select('id', 'name', 'slug', 'parent', 'order')
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
                            'menu_title' => $menu->name,
                            'menu_slug' => $menu->slug,
                            'assigned' => $menuAssigned, // assign untuk menu
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
}
