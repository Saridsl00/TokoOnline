<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ImageHelper;

class CustomerController extends Controller
{
    //redirect ke Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    //callback dari Google
    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();

            //cek apakah email sudah terdaftar
            $registeredUser = User::where('email', $socialUser->email)->first();

            if (!$registeredUser) {
                // buat user baru
                $user = User::create([
                    'nama' => $socialUser->name,
                    'email' => $socialUser->email,
                    'role' => '2',
                    'status' => 1,
                    'password' => Hash::make('default_password'),
                    'hp' => '0000000000',
                    'foto' => $socialUser->avatar,
                ]);

                //Buat data customer
                Customer::create([
                    'user_id' => $user->id,
                    'google_id' => $socialUser->id,
                    'google_token' => $socialUser->token,
                   
                    
                    
                    
                ]);
                
                
                //login pengguna baru
                Auth::login($user);
            } else {
                //jika email sudah terdaftar, langsung login
                Auth::login($registeredUser);
            }
            //redirect ke halaman utama
            return redirect()->intended('/beranda');
        } catch (\Exception $e) {
            // redirect ke halama utama jika terjadi kesalahan
            return redirect('/')->with('error', 'Terjadi kesalahan saat login menggunakan Google.');
        }
        
    }

    public function logout(Request $request)
    {
      Auth::logout();
      $request->session()->invalidate();
      $request->session()->regenerate();

      return redirect('/')->with('success', 'Anda telah berhasil logout');
    }
    public function index() 
    { 
        $customer = Customer::orderBy('id', 'desc')->get(); 
        return view('backend.v_customer.index', [ 
            'judul' => 'Customer', 
            'sub' => 'Halaman Customer', 
            'index' => $customer 
        ]); 
    }
    public function show(string $id) 
    { 
        $customer = Customer::with('user')->findOrFail($id); 
        return view('backend.v_customer.show', [ 
            'judul' => 'Detail Customer', 
            'show' => $customer, 
            
        ]); 
    } 
    public function edit(string $id)
    {
    $customer = Customer::findOrFail($id); 
    return view('backend.v_customer.edit', [ 
        'judul' => 'Edit Customer', 
        'edit' => $customer 
    ]); 
    }

    public function update(Request $request, string $id)
    {
        $rules_customer = [
            'alamat' => 'required|max:255',
            'pos' => 'required|max:10',
        ];
        $validatedData_customer = $request->validate($rules_customer);

        //update data customer
        $customer = Customer::where('user_id', $id)->first();
        if ($customer) {
            $customer->update($validatedData_customer);
        }

        $rules_user = [
            'nama' => 'required|max:255',
            'hp' => 'required|min:10|max:13',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',

        ];
        $messages = [
                'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
                'foto.max' => 'Ukuran file gambar Maksimal adalah 1024 KB.'
            ];

        $validatedData_user = $request->validate($rules_user, $messages);
       

        // update data user
         $user = $customer->user; // mengakses relasi user
        if ($user) {

            if ($request->file('foto')) {
                //hapus foto lama
                if ($user->foto) {
                    $oldImagePath = public_path('storage/img-customer/') . 
                    $user->foto;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }

                }

            
                $file = $request->file('foto');
                $extension = $file->getClientOriginalExtension();
                $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
                $directory = 'storage/img-customer/';

                // simpan gambar asli, // Simpan gambar dengan ukuran yang ditentukan
                $fileName = ImageHelper::uploadAndResize($file, $directory, 
                $originalFileName, 385, 400);
                $validatedData_user['foto'] = $fileName;
              
                // null (jika tinggi otomatis)

                // Simpan nama file asli di database
                $validatedData_user['foto'] = $originalFileName;
            }
            $user->update($validatedData_user);
            
                    return redirect()->route('backend.customer.index')->with('success', 'Data customer berhasil diperbarui.');
        }
    
        
    }

       public function destroy(string $id)
    {
    $customer = Customer::findOrFail($id);

    $customer->destroy($id);
    return redirect()->route('backend.customer.index')->with('success', 'Data customer berhasil dihapus.');  
    
    }


public function akun($id) 
{ 
    $loggedInCustomerId = Auth::user()->id; 
    // Cek apakah ID yang diberikan sama dengan ID customer yang sedang login 
    if ($id != $loggedInCustomerId) { 
        // Redirect atau tampilkan pesan error 
        return redirect()->route('customer.akun', ['id' => $loggedInCustomerId])->with('msgError', 'Anda tidak berhak mengakses akun ini.'); 
    } 
    $customer = Customer::where('user_id', $id)->firstOrFail(); 
    return view('v_customer.edit', [ 
        'judul' => 'Customer', 
        'subJudul' => 'Akun Customer', 
        'edit' => $customer 
    ]); 
} 
 
public function updateAkun(Request $request, $id) 
{ 
    $customer = Customer::where('user_id', $id)->firstOrFail(); 
    $rules = [ 
        'nama' => 'required|max:255', 
        'hp' => 'required|min:10|max:13', 
        'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024', 
    ]; 
    $messages = [ 
        'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau 
gif.', 
        'foto.max' => 'Ukuran file gambar Maksimal adalah 1024 KB.' 
    ]; 
 
    if ($request->email != $customer->user->email) { 
        $rules['email'] = 'required|max:255|email|unique:customer'; 
    } 
    if ($request->alamat != $customer->alamat) { 
        $rules['alamat'] = 'required'; 
    } 
    if ($request->pos != $customer->pos) { 
        $rules['pos'] = 'required'; 
    } 
 
    $validatedData = $request->validate($rules, $messages); 
    // menggunakan ImageHelper 
    if ($request->file('foto')) { 
        //hapus gambar lama 
        if ($customer->user->foto) { 
            $oldImagePath = public_path('storage/img-customer/') . $customer->user->foto; 
            if (file_exists($oldImagePath)) { 
                unlink($oldImagePath); 
            } 
        } 
        $file = $request->file('foto'); 
        $extension = $file->getClientOriginalExtension(); 
        $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension; 
        $directory = 'storage/img-customer/'; 
        // Simpan gambar dengan ukuran yang ditentukan 
        ImageHelper::uploadAndResize($file, $directory, $originalFileName, 385, 400); 
        // null (jika tinggi otomatis) 
        // Simpan nama file asli di database 
        $validatedData['foto'] = $originalFileName; 
    } 
 
    $customer->user->update($validatedData); 
 
    $customer->update([ 
        'alamat' => $request->input('alamat'), 
        'pos' => $request->input('pos'), 
    ]); 
    return redirect()->route('customer.akun', $id)->with('success', 'Data berhasil diperbarui');

}}