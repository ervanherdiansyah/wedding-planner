<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPackages;
use App\Models\MenuPackage;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function getPackage()
    {
        try {
            $Package = Package::with(['detailPackage', 'menus' => function ($query) {
                // $query->orderBy('order', 'asc');
            }])
                ->get()
                ->map(function ($package) {
                    // Susun menu bertingkat
                    $menus = $this->buildMenuHierarchy($package->menus->unique('id'));

                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'description' => $package->description,
                        'price' => $package->price,
                        'invited' => $package->invited,
                        'detail_package' => $package->detailPackage,
                        'menus' => $menus
                    ];
                });

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $Package
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function getPackageByProjectId($project_id)
    {
        try {
            $Package = Package::with(['detailPackage', 'menus.permissions' => function ($query) {
                $query->select('permissions.id', 'permissions.name');
            }])
                ->where('project_id', $project_id)
                ->get()
                ->map(function ($package) {
                    // Susun menu bertingkat
                    $menus = $this->buildMenuHierarchy($package->menus->unique('id'));

                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'description' => $package->description,
                        'invited' => $package->invited,
                        'price' => $package->price,
                        'detail_package' => $package->detailPackage,
                        'menus' => $menus
                    ];
                });

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $Package
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Rekursif untuk build parent-child menu
     */
    private function buildMenuHierarchy($menus, $parentId = null)
    {
        return $menus
            ->where('parent', $parentId) // hanya ambil child sesuai parent
            ->sortBy('order')
            ->map(function ($menu) use ($menus) {
                // Get unique permissions based on id
                $uniquePermissions = $menu->permissions
                    ->unique('id')
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
                    'parent' => $menu->parent,
                    'icon' => $menu->icon,
                    'url' => $menu->url,
                    'order' => $menu->order,
                    'is_active' => $menu->is_active,
                    'permissions' => $uniquePermissions->values(),
                    'children' => $this->buildMenuHierarchy($menus, $menu->id)
                ];
            })
            ->values();
    }


    public function getPackageById($id)
    {
        try {
            $Package = Package::with(['detailPackage', 'menus' => function ($query) {
                // $query->orderBy('order', 'asc');
            }])
                ->where('id', $id)
                ->get()
                ->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'description' => $package->description,
                        'price' => $package->price,
                        'invited' => $package->invited,
                        'detail_package' => $package->detailPackage,
                        'menus' => $package->menus->map(function ($menu) use ($package) {
                            // Get permissions from pivot table for this specific package and menu
                            $permissions = DB::table('menu_packages')
                                ->join('permissions', 'menu_packages.permission_id', '=', 'permissions.id')
                                ->where('menu_packages.package_id', $package->id)
                                ->where('menu_packages.menu_id', $menu->id)
                                ->select('permissions.id', 'permissions.name')
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
                                'parent' => $menu->parent,
                                'icon' => $menu->icon,
                                'url' => $menu->url,
                                'order' => $menu->order,
                                'is_active' => $menu->is_active,
                                'permissions' => $permissions
                            ];
                        })
                    ];
                });

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $Package
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createPackage(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'invited' => 'required|numeric',
                'detailPackage' => 'array',
                'detailPackage.*.name_feature' => 'required|string',
                'access' => 'array',
                'access.*.menu_id' => 'required|integer|exists:menus,id',
                'access.*.permission_id' => 'required|integer|exists:permissions,id',
            ]);

            // Simpan package
            $Package = Package::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'invited' => $request->invited,
            ]);

            // Simpan detail fitur package
            foreach ($request->detailPackage as $feature) {
                DetailPackages::create([
                    'package_id' => $Package->id,
                    'name_feature' => $feature['name_feature'],
                ]);
            }

            // Simpan akses menu + permission
            foreach ($request['access'] as $access) {
                MenuPackage::create([
                    'menu_id' => $access['menu_id'],
                    'package_id' => $Package->id,
                    'permission_id' => $access['permission_id'], // tambah permission_id
                ]);
            }

            return response()->json([
                'message' => 'Create Data Successfully',
                'data' => $Package
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updatePackage(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'invited' => 'required|numeric',
                'detailPackage' => 'nullable|array',
                'detailPackage.*.id' => 'nullable|integer|exists:detail_packages,id',
                'detailPackage.*.name_feature' => 'required|string',
                'access' => 'nullable|array',
                'access.*.id' => 'nullable|integer|exists:menu_packages,id',
                'access.*.menu_id' => 'required|integer|exists:menus,id',
                'access.*.permission_id' => 'required|integer|exists:permissions,id',
            ]);

            $Package = Package::findOrFail($id);

            DB::transaction(function () use ($request, $Package) {
                // Update package utama
                $Package->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'invited' => $request->invited,

                ]);

                /**
                 * =========================
                 *  SYNC DETAIL PACKAGES
                 * =========================
                 */
                $detailIds = collect($request->detailPackage)->pluck('id')->filter()->toArray();

                DetailPackages::where('package_id', $Package->id)
                    ->whereNotIn('id', $detailIds)
                    ->delete();

                if (!empty($request->detailPackage)) {
                    foreach ($request->detailPackage as $feature) {
                        if (!empty($feature['id'])) {
                            DetailPackages::where('id', $feature['id'])
                                ->update([
                                    'name_feature' => $feature['name_feature'],
                                ]);
                        } else {
                            DetailPackages::create([
                                'package_id' => $Package->id,
                                'name_feature' => $feature['name_feature'],
                            ]);
                        }
                    }
                }

                /**
                 * =========================
                 *  SYNC MENU PACKAGES
                 * =========================
                 */
                $accessIds = collect($request->access)->pluck('id')->filter()->toArray();

                MenuPackage::where('package_id', $Package->id)
                    ->whereNotIn('id', $accessIds)
                    ->delete();

                if (!empty($request->access)) {
                    foreach ($request->access as $access) {
                        if (!empty($access['id'])) {
                            MenuPackage::where('id', $access['id'])
                                ->update([
                                    'menu_id' => $access['menu_id'],
                                    'permission_id' => $access['permission_id'],
                                ]);
                        } else {
                            MenuPackage::create([
                                'package_id' => $Package->id,
                                'menu_id' => $access['menu_id'],
                                'permission_id' => $access['permission_id'],
                            ]);
                        }
                    }
                }
            });

            // Refresh package dengan relasi + nested relasi menu & permission
            $Package->load([
                'detailPackages:id,package_id,name_feature',
                'menuPackages' => function ($q) {
                    $q->with([
                        'menu:id,name,slug,icon,url,parent,order',
                        'permission:id,name'
                    ]);
                }
            ]);

            return response()->json([
                'message' => 'Updated data successfully',
                'data' => $Package
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }



    public function deletePackage($id)
    {
        try {
            Package::where('id', $id)->first()->delete();
            DetailPackages::where('package_id', $id)->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function createPackageMenu(Request $request)
    {
        try {
            //code...

            foreach ($request['access'] as $access) {
                MenuPackage::create([
                    'menu_id' => $access['menu_id'],
                    'package_id' => $access['package_id'],
                    'permission_id' => $access['permission_id'],
                ]);
            }

            return response()->json(['message' => 'Create Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
