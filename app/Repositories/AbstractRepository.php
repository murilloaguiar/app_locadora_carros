<?php 
namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository{
    public function __construct($model){
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos){
        $this->model = $this->model->with($atributos);
        //a query está sendo montada. Sempre se atualizando
    }

    public function filtro($filtros){
        $filtros = explode(';', $filtros);

        foreach ($filtros as $key => $condicao) {
            $condicoes = explode(':', $condicao);
            $this->model = $this->model->where($condicoes[0], $condicoes[1], $condicoes[2]);
            //a query está sendo montada. Sempre se atualizando
        }
    }

    public function selectAtributos($atributos){
        $this->model = $this->model->selectRaw($atributos.',id');
    }

    public function getResultado(){
        return $this->model->get();
    }
}