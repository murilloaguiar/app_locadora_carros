<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome','imagem'];

    public function rules(){
        return [
            "nome" => "required|unique:marcas,nome, $this->id|min:3",
            "imagem" => "required|file|mimes:png"
        ];

        /*
            Parametro do unique:
            1 - tabela onde será feita a pesquisa da existência unica
            2 - nome da coluna que será pesquisa na tabela, por padrão é o nome do imput
            3- id do registro que será desconsiderado na pesquisa
        */
    }

    public function feedback(){
        return [
            "required" => "O campo :attribute é obrigatório",
            "nome.unique" => "O nome da marca já existe",
            "nome.min" => "O nome deve ter no mínimo 3 caracteres",
            "imagem.mimes" => "O arquivo precisa ser do tipo png"
        ];
    }
}
