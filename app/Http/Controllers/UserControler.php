<?php
namespace App\Http\Controllers;

// require ('./vendor/autoload.php');

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sanctum;
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Facades\Hash;


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
                ], 401);
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
        ], 401);
    }
    public function loginUser(Request $request)
    {
   
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            /** @var \App\Models\MyUserModel $user **/
            $user = Auth::user();

            $expiration = now()->addMinutes(120);
            $tokenName = 'access_token';
            $token = $user->createToken($tokenName, ['id' => $user->id, 'exp' => $expiration->timestamp])->plainTextToken;

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
        $user = Auth::User();
        if($user)
        {
            return response()->json([
                'error'=> false,
                'message'=> 'Usuario Autentificado com sucesso!',
                'data' =>$user,
            ]);
        }
        return response()->json([
            'error'=> true,
            'message'=> 'Usuario Não Encontrado!',
        ], 401);
    }
    public function DisconectUser(Request $request) 
    {
        /** @var \App\Models\MyUserModel $user **/
        $user = Auth::User();
        if($user){
            $user->tokens()->delete();
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'error'=> false,
                'message'=> 'Usuario Desconectado Com Sucesso!',
            ]);
        }
        return response()->json([
            'error'=> true,
            'message'=> 'Usuario Invalido!',
        ], 401);
        
    }
    public function DestroyUser()
    {
        /** @var \App\Models\MyUserModel $user **/
        $user = Auth::User();
        if($user)
        {
            $user->tokens()->delete();
            $user->delete();

            return response()->json([
                'error'=>false,
                'message'=>'Conta Deletada!',
            ]);
            
        }
        return response()->json([
            'error'=> true,
            'message'=> 'Usuario Invalido!',
        ], 401);
    }
    public function EditUser()
    {
        $user = Auth::User();
        if($user)
        {
            return response()->json([
                'error'=> false,
                'message'=> 'Pode atualizar!',
                'data' =>$user,
            ]);
        }
        return response()->json([
            'error'=> true,
            'message'=> 'Usuario Não Encontrado!',
        ], 401);
    }
    public function UpdateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'lastname' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = Auth::User();
        if($user)
        {
             /** @var \App\Models\MyUserModel $user **/
            if (Hash::check($request->input('password'), $user->password)) 
            {
                $user->tokens()->delete();

                $user->update([
                    'name' => $request->input('name'),
                    'lastname' => $request->input('lastname'),
                ]);
    
                $expiration = now()->addMinutes(120);
                $tokenName = 'access_token';
                $token = $user->createToken($tokenName, ['id' => $user->id, 'exp' => $expiration->timestamp])->plainTextToken;
    
                return response()->json([
                    'error' => false,
                    'message' => 'Dados de usuário alterados com sucesso!',
                    'token' => $token,
                ]);
            }
            return response()->json([
                'error'=> true,
                'message'=> 'Senha Invalida!',
            ]);
        }
        return response()->json([
            'error'=> true,
            'message'=> 'Usuario nao encontrado!',
        ]);
    
    }
}
