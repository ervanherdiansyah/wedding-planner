<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Brides;
use App\Models\Budgets;
use App\Models\CategoryBudgets;
use App\Models\CategoryHandover;
use App\Models\CategoryTodolists;
use App\Models\CategoryVendors;
use App\Models\DetailPaymentBudget;
use App\Models\EventCommittees;
use App\Models\Events;
use App\Models\FamilyMemberBrides;
use App\Models\FamilyMemberGrooms;
use App\Models\Grooms;
use App\Models\HandoverBudget;
use App\Models\HandoverBudgetItem;
use App\Models\ListBudgets;
use App\Models\ListPhoto;
use App\Models\ListVendors;
use App\Models\Package;
use App\Models\Payments;
use App\Models\ProjectMemberships;
use App\Models\Projects;
use App\Models\Rundowns;
use App\Models\SongLists;
use App\Models\SubTodolists;
use App\Models\Todolists;
use App\Models\Uniform;
use App\Models\UniformCategories;
use App\Models\User;
use App\Models\VipGuestLists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'registerViaInvite']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            //code...
            request()->validate([
                'name' => 'required',
                'password' => 'required|min:8|max:32',
                'email' => 'required|unique:users',
                'phone_number' => 'required',
                'package' => 'required',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone_number' => $request->phone_number,
                'package' => $request->package,
                'role' => "user",
            ]);

            if ($request->package == 1 || $request->package == 2) {
                $project = Projects::create([
                    'name' => $request->name,
                    'invited_project' => 1,
                    'user_id' => $user->id,
                ]);
            } elseif ($request->package == 3) {
                $project = Projects::create([
                    'name' => $request->name,
                    'invited_project' => 4,
                    'user_id' => $user->id,
                ]);
            }

            $event = Events::create([
                'project_id' => $project->id,
                'bridegroom_name' => $request->bridegroom_name,
                'event_name' => $request->event_name,
                'event_datetime' => $request->event_datetime,
                'address' => $request->address,
                'description' => $request->description,
            ]);

            $bride = Brides::create([
                'project_id' => $project->id,
                'name_bride' => $request->name_bride,
                'child_bride' => $request->child_bride,
                'father_name_bride' => $request->father_name_bride,
                'mother_name_bride' => $request->mother_name_bride,
            ]);

            $FamilyMemberBrides = FamilyMemberBrides::create([
                'bride_id' => $bride->id,
                'relationship' => "Keluarga",
                'name_family' => "Nana",
            ]);
            $groom = Grooms::create([
                'project_id' => $project->id,
                'name_groom' => $request->name_groom,
                'child_groom' => $request->child_groom,
                'father_name_groom' => $request->father_name_groom,
                'mother_name_groom' => $request->mother_name_groom,
            ]);
            $FamilyMemberGrooms = FamilyMemberGrooms::create([
                'groom_id' => $groom->id,
                'relationship' => "Keluarga",
                'name_family' => "Nana",
            ]);

            $package = Package::find($request->package);
            $payment = Payments::create([
                'user_id' => $user->id,
                'status' => "Unpaid",
                'price' => $package->price,
                'bank_type' => $request->bank_type,
                'payment_date' => now(),
            ]);

            $categoryTodolist = CategoryTodolists::create([
                'project_id' => $project->id, // Ganti dengan ID project yang sesuai
                'name' => 'Persiapan Acara',
                'description' => 'Checklist untuk persiapan acara utama',
            ]);

            $todolist = Todolists::create([
                'category_todolist_id' => $categoryTodolist->id,
                'name' => 'Booking Tempat',
                'status' => false,
            ]);

            $subTodolist = SubTodolists::create([
                'todolist_id' => $todolist->id,
                'name' => 'Cek ketersediaan gedung',
                'status' => false,
            ]);

            $budget = Budgets::create([
                'project_id' => 1, // Ganti dengan ID project yang sesuai
                'estimated_payment' => 10000000,
                'actual_payment' => 9000000,
                'paid' => 6000000,
                'unpaid' => 3000000,
                'difference' => 1000000,
                'balance' => 1000000,
            ]);

            $categoryBudget = CategoryBudgets::create([
                'budget_id' => $budget->id,
                'title' => 'Dekorasi',
            ]);

            $listBudget = ListBudgets::create([
                'category_budget_id' => $categoryBudget->id,
                'estimated_payment' => 5000000,
                'title' => 'Dekorasi Panggung',
                'actual_payment' => 4500000,
                'difference' => 500000,
                'paid' => 3000000,
                'remaining_payment' => 1500000,
                'status' => false,
            ]);

            $detailPayment = DetailPaymentBudget::create([
                'list_budgets_id' => $listBudget->id,
                'description' => 'Pembayaran awal ke vendor dekorasi',
                'deadline' => now(), // misal 7 hari
                'paid' => 3000000,
                'payer' => 'Panitia',
                'date_payment' => now(),
                'type' => 'Transfer',
            ]);

            Rundowns::create([
                'project_id' => $project->id, // Ganti dengan ID project yang sesuai jika perlu
                'time' => '09:00:00',
                'title_event' => 'Pembukaan Acara',
                'minute' => 30,
                'address' => 'Jl. Contoh Alamat No.123, Jakarta',
                'vendor' => 'PT Acara Sukses',
                'person_responsible' => 'Budi Santoso',
                'description' => 'Acara dimulai dengan sambutan dari ketua panitia.',
                'status' => true,
                'icon' => 'opening-icon.png',
            ]);

            $categoryVendor = CategoryVendors::create([
                'project_id' => $project->id, // Ganti sesuai ID project yang tersedia
                'name' => 'Dekorasi',
                'icon' => 'dekorasi-icon.png',
            ]);

            $listVendor = ListVendors::create([
                'category_vendor_id' => $categoryVendor->id,
                'vendor_name' => 'Vendor Dekorasi Indah',
                'vendor_price' => 15000000,
                'person_responsible' => 'Siti Aminah',
                'vendor_contact' => '081234567890',
                'social_media' => '@dekorindah',
                'vendor_features' => 'Paket lengkap dekorasi pelaminan dan photobooth',
                'image' => 'vendor-dekorasi.jpg',
                'status' => true,
            ]);
            $eventCommittee = EventCommittees::create([
                'project_id' => $project->id,
                'role' => 'Koordinator Acara',
                'name' => 'Andi Pratama',
                'contact' => '081234567890',
            ]);

            $listPhoto = ListPhoto::create([
                'project_id' => $project->id,
                'name' => 'Budi Santoso',
                'relationship' => 'Saudara Pengantin',
                'type' => 'groom',
            ]);
            $listPhoto = ListPhoto::create([
                'project_id' => $project->id,
                'name' => 'Budi Santoso',
                'relationship' => 'Saudara Pengantin',
                'type' => 'bride',
            ]);

            $vipGuest = VipGuestLists::create([
                'project_id' => $project->id,
                'role' => 'Tamu Kehormatan',
                'name' => 'Ibu Retno Marsudi',
                'contact' => '082198765432',
                'type' => 'groom',
            ]);
            $vipGuest = VipGuestLists::create([
                'project_id' => $project->id,
                'role' => 'Tamu Kehormatan',
                'name' => 'Ibu Retno Marsudi',
                'contact' => '082198765432',
                'type' => 'bride',
            ]);
            $songList = SongLists::create([
                'project_id' => $project->id,
                'singer_name' => 'Raisa',
                'title' => 'Kali Kedua',
                'time' => '19:30',
            ]);

            $uniformCategory = UniformCategories::create([
                'project_id' => $project->id, // Ganti dengan project ID yang sesuai
                'title' => 'Seragam Keluarga Pria',
                'description' => 'Seragam yang dikenakan oleh keluarga laki-laki pengantin.',
            ]);
            $uniform = Uniform::create([
                'uniform_category_id' => $uniformCategory->id,
                'name' => 'Koko Batik Cokelat',
                'status' => 'Aktif',
                'attire' => 'Batik lengan panjang dengan celana kain hitam',
                'note' => 'Dipakai saat acara akad nikah',
            ]);

            $handoverBudget = HandoverBudget::create([
                'project_id' => $project->id,
                'male_budget' => 5000000,
                'female_budget' => 7000000,
                'used_budget_male' => 1500000,
                'used_budget_female' => 2000000,
            ]);
            $categoryHandoverFemale = CategoryHandover::create([
                'handover_budgets_id' => $handoverBudget->id,
                'title' => 'Perhiasan',
            ]);
            $categoryHandoverMale = CategoryHandover::create([
                'handover_budgets_id' => $handoverBudget->id,
                'title' => 'Perhiasan',
            ]);
            $handoverBudgetItem = HandoverBudgetItem::create([
                'category_handover_budgets_id' => $categoryHandoverFemale->id,
                'name' => 'Cincin Emas 24K',
                'category' => 'female',
                'purchase_method' => 'offline',
                'price' => 2500000,
                'detail' => 'Dibeli di toko emas ABC',
                'status' => true,
                'purchase_date' => now()->toDateString(),
            ]);
            $handoverBudgetItem = HandoverBudgetItem::create([
                'category_handover_budgets_id' => $categoryHandoverMale->id,
                'name' => 'Cincin Emas 24K',
                'category' => 'male',
                'purchase_method' => 'offline',
                'price' => 2500000,
                'detail' => 'Dibeli di toko emas ABC',
                'status' => true,
                'purchase_date' => now()->toDateString(),
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Success Register',
            ], 200);
        } catch (ValidationException $e) {
            // Menangkap error validasi
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = [
                        'field' => $field,
                        'message' => $message,
                    ];
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        } catch (\Throwable $th) {
            // Menangkap kesalahan lain
            DB::rollback();
            return response()->json([
                'message' => 'Internal Server Error',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => "Validation failed.", 'code' => "VALIDATION_ERROR", 'details' => 'Incorrect email or password. Please try again.'], 400);
        }

        return response()->json([
            'message' => 'Successfully Login',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();

        // Project yang dimiliki user (owner)
        $ownedProjects = Projects::where('user_id', $user->id)->get();

        // Project yang diikuti user lewat undangan (membership)
        $membershipProjects = Projects::whereIn('id', function ($query) use ($user) {
            $query->select('project_id')
                ->from('project_memberships')
                ->where('user_id', $user->id);
        })->get();

        // Gabungkan keduanya (bisa juga pakai unique jika perlu)
        $allProjects = $ownedProjects->merge($membershipProjects)->unique('id')->values();



        return response()->json([
            'message' => 'Successfully get data user',
            'data' => [
                'user' => $user,
                'projects' => $allProjects
            ]
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'message' => 'Successfully refresh token',
            'data' => [
                'access_token' => auth()->refresh(),
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function registerViaInvite(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $invite = ProjectMemberships::where('token', $request->token)
            ->where('status', 'invited')->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => "user",
            'phone_number' => $request->phone_number,

        ]);

        // Update membership
        $invite->update([
            'user_id' => $user->id,
            'status' => 'registered',
            'token' => null
        ]);

        // Auto generate payment
        Payments::create([
            'user_id' => $user->id,
            'status' => 'paid',
            'tanggal_pembayaran' => now()
        ]);

        return response()->json(['message' => 'Registration complete']);
    }
}
