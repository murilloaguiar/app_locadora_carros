<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller{

    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){ 
        
        $modelos = array();

        //verifica se um determinado parâmetro no request existe/está definido
        if ($request->has('atributos')) {
            //dd($request->atributos);
            $atributos = $request->atributos;

            $modelos = $this->modelo->selectRaw($atributos.',marca_id')->with('marca')->get();
        }else{
            $modelos = $this->modelo->with('marca')->get();
        }

        //$this->modelo->with('marca')->get()
        return response()->json($modelos,200); 
        // método all() cria um objeto de consulta e executa ela pelo get() = collection
        // por meio do método get diretamente temos a possibilidade de modificar a consulta previamente
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos','public'); //retorna o nome da imagem persistida
        
        $modelo = $this->modelo->create([
            'marca_id' =>$request->marca_id,
            'nome' => $request->nome,
            'imagem' =>$imagem_urn,
            'numero_portas' =>$request->numero_portas,
            'lugares' =>$request->lugares,
            'air_bag' =>$request->air_bag,
            'abs' =>$request->abs
        ]);
        return response()->json($modelo,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id){

        $modelo = $this->modelo->with('marca')->find($id);
        if ($modelo === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); //array -> json
        }
        return response()->json($modelo,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['erro' => 'Não foi possível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no model
            foreach ($modelo->rules() as $input => $regra) {

                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            //dd($regrasDinamicas);
            $request->validate($regrasDinamicas);

        }else{
            $request->validate($modelo->rules());
        }

        //removendo arquivo antigo
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos','public'); //retorna o nome da imagem persistida

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();

        // $modelo->update([
        //     'marca_id' =>$request->marca_id,
        //     'nome' => $request->nome,
        //     'imagem' =>$imagem_urn,
        //     'numero_portas' =>$request->numero_portas,
        //     'lugares' =>$request->lugares,
        //     'air_bag' =>$request->air_bag,
        //     'abs' =>$request->abs
        // ]);

        return response()->json($modelo,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['erro' => 'Não foi possível excluir. O recurso solicitado não existe'], 404);
        }

        //removendo arquivo antigo
        
        Storage::disk('public')->delete($modelo->imagem);
        

        $modelo->delete();
        return response()->json(['msg'=>'o modelo foi removida com sucesso'],200); 
    }
}
