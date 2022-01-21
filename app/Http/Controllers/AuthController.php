<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    //retornar JWT
    public function login(Request $request){
        
        
        //autenticação do usuário
        $token = auth('api')->attempt($request->all(['email','password']));
        //dd($token);

        if (!$token) {
            return response()->json(['erro'=>'Usuário ou senha inválido'],403);
            //401 unauthorized -> não autorizado
            //403 forbidden -> proíbido (login inválido)
           
        }
        
        return response()->json(['token'=>$token]);
    }

    public function logout(){
        auth('api')->logout(); //cliente encaminhe um jwt válido
        return response()->json(['msg'=>'o logout foi realizado com sucesso']);
    }

    //renovação da autorização
    public function refresh(){
        $token = auth('api')->refresh(); //cliente encaminhe um jwt válido
        
        return response()->json(['token'=>$token]);
    }

    public function me(){
        return response()->json(auth()->user());
        
    }
}
