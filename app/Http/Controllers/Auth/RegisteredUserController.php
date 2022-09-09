<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\SingleModel\iSingleModelRepository;

class RegisteredUserController extends Controller
{
    private $singleModel;

    public function __construct(iSingleModelRepository $singleModel)
    {
        $this->singleModel = $singleModel;
    }
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $employeeTypes = $this->singleModel->employeeTypes->all();
        return view('custom.auth.register')->with(compact('employeeTypes'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
    
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'country' => 'required|string',
            'city' => 'required|string'
        ]);

        $password = Hash::make($request->password);
        $request['password'] = $password;
        if(!$request->has('is_student')){
            $request['is_student'] = 0;
        }
     
        $user = User::create($request->all());

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
