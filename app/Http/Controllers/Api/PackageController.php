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
            $packages = Package::with([
                'detailPackages',
                'menus.permissions' => function ($q) {
                    // filter by package_id lewat pivot
                    $q->wherePivot('package_id', DB::raw('packages.id'));
                }
            ])->get()
                ->map(function ($package) {
                    $menus = $this->buildMenuHierarchy(
                        $package->menus->unique('id'),
                        null,
                        $package->id // kirim package_id
                    );

                    return [
                        'id'             => $package->id,
                        'name'           => $package->name,
                        'description'    => $package->description,
                        'price'          => $package->price,
                        'invited'        => $package->invited,
                        'status'         => $package->status,
                        'detail_package' => $package->detailPackages,
                        'menus'          => $menus
                    ];
                });

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data'    => $packages
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getPackageById($id)
    {
        try {
            $package = Package::with([
                'detailPackages',
                'menus.permissions' => function ($q) use ($id) {
                    $q->wherePivot('package_id', $id);
                }
            ])->findOrFail($id);

            $menus = $this->buildMenuHierarchy(
                $package->menus->unique('id'),
                null,
                $package->id
            );

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data'    => [
                    'id'             => $package->id,
                    'name'           => $package->name,
                    'description'    => $package->description,
                    'price'          => $package->price,
                    'invited'        => $package->invited,
                    'status'         => $package->status,
                    'detail_package' => $package->detailPackages,
                    'menus'          => $menus
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getPackageByProjectId($project_id)
    {
        try {
            $Package = Package::with(['detailPackages', 'menus.permissions' => function ($query) {
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
                        'status' => $package->status,
                        'detail_package' => $package->detailPackages,
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
    private function buildMenuHierarchy($menus, $parentId = null, $packageId = null)
    {
        return $menus
            ->where('parent', $parentId)
            ->sortBy('order')
            ->map(function ($menu) use ($menus, $packageId) {
                // filter permission sesuai packageId
                $uniquePermissions = $menu->permissions
                    ->filter(function ($permission) use ($packageId) {
                        return $permission->pivot->package_id == $packageId;
                    })
                    ->unique('id')
                    ->map(function ($permission) {
                        return [
                            'id'     => $permission->id,
                            'name'   => $permission->name,
                            'action' => explode(' ', $permission->name)[0],
                        ];
                    });

                return [
                    'id'          => $menu->id,
                    'name'        => $menu->name,
                    'slug'        => $menu->slug,
                    'parent'      => $menu->parent,
                    'icon'        => $menu->icon,
                    'url'         => $menu->url,
                    'order'       => $menu->order,
                    'is_active'   => $menu->is_active,
                    'permissions' => $uniquePermissions->values(),
                    'children'    => $this->buildMenuHierarchy($menus, $menu->id, $packageId)
                ];
            })
            ->values();
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
                'status' => $request->status,
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
                'status' => 'nullable|boolean',
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
                // Update data utama package
                $Package->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'invited' => $request->invited,
                    'status' => $request->status,
                ]);

                /**
                 * =========================
                 *  SYNC DETAIL PACKAGES
                 * =========================
                 */
                $detailIds = collect($request->detailPackage)->pluck('id')->filter()->toArray();

                // Hapus detail yang tidak ada di request
                DetailPackages::where('package_id', $Package->id)
                    ->whereNotIn('id', $detailIds)
                    ->delete();

                if (!empty($request->detailPackage)) {
                    foreach ($request->detailPackage as $feature) {
                        if (!empty($feature['id'])) {
                            // Update jika ada id
                            DetailPackages::where('id', $feature['id'])
                                ->where('package_id', $Package->id)
                                ->update([
                                    'name_feature' => $feature['name_feature'],
                                ]);
                        } else {
                            // Create baru jika tidak ada id
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

                // Hapus access yang tidak ada di request
                MenuPackage::where('package_id', $Package->id)
                    ->whereNotIn('id', $accessIds)
                    ->delete();

                if (!empty($request->access)) {
                    foreach ($request->access as $access) {
                        if (!empty($access['id'])) {
                            // Update record lama
                            MenuPackage::where('id', $access['id'])
                                ->where('package_id', $Package->id)
                                ->update([
                                    'menu_id' => $access['menu_id'],
                                    'permission_id' => $access['permission_id'],
                                ]);
                        } else {
                            // Insert baru
                            MenuPackage::create([
                                'package_id' => $Package->id,
                                'menu_id' => $access['menu_id'],
                                'permission_id' => $access['permission_id'],
                            ]);
                        }
                    }
                }
            });

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

    public function getPackageActive()
    {
        try {
            //code...
            $data = Package::with('detailPackages')->where('status', 1)->get();

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function toggleActivePackage($id)
    {
        try {
            //code...
            $package = Package::findOrFail($id);
            $package->status = !$package->status;
            $package->save();

            return response()->json([
                'message' => 'Toggle Active Successfully',
                'data' => $package
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
