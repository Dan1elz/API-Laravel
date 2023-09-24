<?php
namespace App\Http\Controllers;

// require ('./vendor/autoload.php');

use Illuminate\Http\Request;
use App\Models\User;
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;


class UserControler extends Controller
{
    public function RegisterUser(Request $request)
    {
        $params = ['name','lastname','email','password'];
        if($request->has($params)) {
            
            $data = $request->all();

            $registry = User::where('email', $data['email'])->first();

            if($registry) {
                return response()->json([
                    'error' => true,
                    'message' => 'Email ja esta sendo utilizado!'
                ]); 
            }
           
            $newRegistry = new User();
            $newRegistry->name = $data['name'];
            $newRegistry->lastname = $data['lastname'];
            $newRegistry->email = $data['email'];
            $newRegistry->password = $data['password'];
            $newRegistry->save();
           
            return response()->json([
                'error' => false,
                'message' => 'Usuario cadastrado com sucesso!'
               ]); 
        }
        return response()->json([
            'error' => true,
            'message' => 'Não tem todos os parametros necessarios!'
           ]); 
    }
    public function loginUser(Request $request)
    {
   
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            $user = Auth::user();
            $token = $user->createToken($request->tokenName)->plainTextToken;

            return response()->json([
                'error'=> false,
                'message'=> 'Usuario Autentificado com sucesso!',
                'token' => $token
            ]);
        }
        return response()->json([
            'error' => true,
            'message' => 'Credenciais inválidas',
        ], 401);
    }
    public function GetUser()
    {

    }
    public function DeleteUser()
    {

    }
    public function UpdateUser()
    {

    }
}
