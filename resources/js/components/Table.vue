<template>
    <div>
    
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="titulo, key in titulos" :key="key">{{titulo.titulo}}</th>
                    
                </tr>
            </thead>
            <tbody>

                <tr v-for="objeto, chave in dadosFiltrados" :key="chave">
                    <td v-for="valor, chaveValor in objeto" :key="chaveValor">
                        <span v-if="titulos[chaveValor].tipo=='texto'">{{valor}}</span>
                        <span v-if="titulos[chaveValor].tipo=='data'">{{valor.toDateString()}}</span>
                        <span v-if="titulos[chaveValor].tipo=='imagem'">
                            <img :src="'/storage/'+valor" :alt="'marca-'+valor" width="35" height="35">
                        </span>

                    
                        
                    </td>
                </tr>



                <!--
                <tr v-for="objeto in dados" :key="objeto.id">

                    <td v-for="valor, chave in objeto" :key="chave">
                        
                        <span v-if="chave == 'imagem' && titulos.includes(chave)">
                            <img :src="'/storage/'+valor" :alt="'marca-'+valor" width="35" height="35">
                        </span>

                         <span v-else-if="chave != 'imagem' && titulos.includes(chave)">
                            {{valor}}
                        </span>
                    </td>


                    <th scope="row">{{marcas.id}}</th>
                    <td>{{marcas.nome}}</td>
                    <td><img :src="'/storage/'+marcas.imagem" :alt="'marca-'+marcas.nome" width="35" height="35"></td>
                    
                    
                </tr>
                -->
                
            </tbody>
        </table>
        
    </div>
</template>

<script>
export default {
    props: ['dados', 'titulos'],

    computed: {
        dadosFiltrados(){

            let campos = Object.keys(this.titulos)
            let dadosFiltrados = []

            //console.log(campos)
            //console.log(this.dados)

            this.dados.map((item, chave)=>{
                //console.log(item, chave)

                let itemFiltrado = {}
                campos.forEach(campo => {
                    itemFiltrado[campo] = item[campo]//utilizar a sintaxe de array para atribuir valores a objetos
                    
                    //console.log(chave, item, campo)
                })

                //console.log(itemFiltrado)
                dadosFiltrados.push(itemFiltrado)
            })
            console.log(dadosFiltrados)

            return dadosFiltrados //retornar um array de objetos
        }
    }
};
</script>
