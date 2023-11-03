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
    public function registerUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:3|max:25',
            'lastname' => 'required|min:3|max:25',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:32',
        ]);
       
        $registry = User::where('email', $validatedData['email'])->first();

        if ($registry)
        {
            return $this->error('Email já está sendo utilizado!',401);
        }
        $newRegistry = new User();
        $newRegistry->name = $validatedData['name'];
        $newRegistry->lastname = $validatedData['lastname'];
        $newRegistry->email = $validatedData['email'];
        $newRegistry->password = Hash::make($validatedData['password']);
        $newRegistry->save();
        
        return $this->success('Usuário cadastrado com sucesso!', null);
    }
    public function loginUser(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' =>'required|min:6|max:32',
        ]);
        if (Auth::attempt($credentials)) 
        {
            /** @var \App\Models\MyUserModel $user **/
            $user = Auth::user();

            $expiration = now()->addMinutes(120);
            $tokenName = 'access_token';
            $token = $user->createToken($tokenName, ['id' => $user->id, 'exp' => $expiration->timestamp])->plainTextToken;

            return $this->success('Usuario Autentificado com sucesso!', $token);
        }
        return $this->error('Credenciais inválidas',401);
    }
    public function getUser()
    {
        $user = Auth::User();
        if($user)
        {
            return $this->success('Usuario Autentificado com sucesso!', $user);
        }
        return $this->error('Usuario Não Encontrado!',401);
    }
    public function disconectUser(Request $request) 
    {
        /** @var \App\Models\MyUserModel $user **/
        $user = Auth::User();
        if($user)
        {
            $user->tokens->each(function ($token) {
                $token->delete();
            });
            $request->user()->currentAccessToken()->delete();
            return $this->success('Usuario Desconectado Com Sucesso!', false);
        }
        return $this->error('Usuario Invalido!',401);
        
    }
    public function destroyUser()
    {
        /** @var \App\Models\MyUserModel $user **/
        $user = Auth::User();
        if($user)
        {
            $user->tokens->each(function ($token) {
                $token->delete();
            });
            $user->delete();
            return $this->success('Conta Deletada!', false);
        }
        return $this->error('Usuario Invalido!',401);
    }
    public function editUser()
    {
        $user = Auth::User();
        if($user)
        {
            return $this->success('Pode atualizar!', $user);
        }
        return $this->error('Usuario Não Encontrado!',401);
    }
    public function updateUser(Request $request)
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
                $user->tokens->each(function ($token) {
                    $token->delete();
                });

                $user->update([
                    'name' => $request->input('name'),
                    'lastname' => $request->input('lastname'),
                ]);
    
                $expiration = now()->addMinutes(120);
                $tokenName = 'access_token';
                $token = $user->createToken($tokenName, ['id' => $user->id, 'exp' => $expiration->timestamp])->plainTextToken;
    
                return $this->success('Dados de usuário alterados com sucesso!', $token);
            }
            return $this->error('Senha Invalida!',401);
        }
        return $this->error('Usuario nao encontrado!',401);
    
    }
    public function success($message, $data)
    {
        return response()->json([
            'error' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }
    public function error($message, $statuscode)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
        ], $statuscode);
    }
}
