<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{

    public function __construct(Marca $marca){
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        $marcaRepository = new MarcaRepository($this->marca);

        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;


            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        }else{
            
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }


        if ($request->has('filtro')) {
            //dd(explode(':', $request->filtro));
            $marcaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);

        }

        
        return response()->json($marcaRepository->getResultadoPaginado(3),200); 
        
        
        
        /*-------------------------------------------------
        $marcas = array();

        //verifica se um determinado parâmetro no request existe/está definido
        if ($request->has('atributos_modelos')) {
            $atributos_modelos = $request->atributos_modelos;
            $marcas = $this->marca->with('modelos:id,'.$atributos_modelos);
        }else{
            $marcas = $this->marca->with('modelos');
        }

        if ($request->has('filtro')) {
            //dd(explode(':', $request->filtro));
            $filtros = explode(';', $request->filtro);

            foreach ($filtros as $key => $condicao) {
                $condicoes = explode(':', $condicao);
                $marcas = $marcas->where($condicoes[0], $condicoes[1], $condicoes[2]);
            }
            
            
        }


        if ($request->has('atributos')) {
            //dd($request->atributos);
            $atributos = $request->atributos;
            
            $marcas = $marcas->selectRaw($atributos.',id')->get();

        }else{
            $marcas = $marcas->get();
        }

        //$marcas = Marca::all();
        //$marcas = $this->marca->with('modelos')->get();
        return response()->json($marcas,200); 
        ------------------------------------------------------*/
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
        //$marca = Marca::create($request->all());
        //tratamento

        $request->validate($this->marca->rules(), $this->marca->feedback());

        // dd($request->nome);
        // dd($request->get('nome'));
        // dd($request->input('nome'));
        // dd($request->imagem);
        // dd($request->file('imagem'));
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens','public'); //retorna o nome da imagem persistida
        
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' =>$imagem_urn
        ]);
        return response()->json($marca,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $marca = $this->marca->with('modelos')->find($id);
        if ($marca === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); //array -> json
        }
        return response()->json($marca,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        /*
        print_r($request->all()); //dados atualizados
        echo '<hr>';
        print_r($marca->getAttributes()); //dados antigos
        */

        //$marca->update($request->all());

        
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Não foi possível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no model
            foreach ($marca->rules() as $input => $regra) {

                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            //dd($regrasDinamicas);
            $request->validate($regrasDinamicas, $marca->feedback());

        }else{
            $request->validate($marca->rules(), $marca->feedback());
        }

        //preencher o objeto marca com os dados do request. Os atributos enviado sobrescrevem os já existentes, e os não enviados permanecem os mesmos
        $marca->fill($request->all());

        //removendo arquivo antigo caso um novo arquivo tenha sido enviado no request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens','public'); //retorna o nome da imagem persistida
            $marca->imagem = $imagem_urn;
        }
    
        $marca->save();
        //dd($marca);


        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn
        // ]);

        return response()->json($marca,200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //print_r($marca->getAttributes());
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Não foi possível excluir. O recurso solicitado não existe'], 404);
        }

        //removendo arquivo antigo
        
        Storage::disk('public')->delete($marca->imagem);
        

        $marca->delete();
        return response()->json(['msg'=>'a marca foi removida com sucesso'],200); 
    }
}
