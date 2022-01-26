<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <!-- Card de busca -->
                <card-component titulo="Busca de marcas">
                    
                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">

                                <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca">

                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="Id da marca" v-model="busca.id"/>

                                </input-container-component>
                            </div>

                            <div class="col mb-3">

                                <input-container-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">

                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da marca" v-model="busca.nome"/>

                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">
                        Pesquisar
                        </button>
                    </template>

                </card-component>
                <!-- /Card de busca -->

                <!-- Card de listagem -->
                <card-component titulo="Relação de marcas">
                    
                    <!-- Conteúdo Card de listagem -->
                    <template v-slot:conteudo>
                        <table-component 
                        :dados="marcas.data" 
                        :visualizar = "true"
                        :atualizar = "true"
                        :remover = "true"
                        :titulos="{
                            id:{titulo: 'ID', tipo: 'texto'},
                            nome:{titulo: 'Nome', tipo: 'texto'},
                            imagem:{titulo: 'Logo', tipo: 'imagem'},
                            created_at: {titulo: 'Data criação', tipo: 'data'},

                        }">
                            
                        </table-component>
                    </template>
                    <!-- /Conteúdo Card de listagem -->

                    <!-- Rodapé Card de listagem -->
                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="link, key in marcas.links" :key="key" :class="link.active ? 'page-item active' : 'page-item'" @click="paginacao(link)">
                                        <a class="page-link" v-html="link.label"></a>
                                        
                                    </li>
                                    
                                </paginate-component>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#modalMarca">
                                Adicionar
                                </button>
                            </div>

                            
                        </div>
                        
                        
                    </template>
                    <!-- /Rodapé Card de listagem -->

                </card-component>
                <!-- /Card de listagem -->

            </div>
        </div>

        <!-- Modal -->
        <modal-component id="modalMarca" titulo="Adicionar marca">
            
            <!-- Alerts Modal--->
            <template v-slot:alertas> 
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>
            <!-- /Alerts Modal--->

            <!-- Conteúdo Modal- Formulário --->
            <template v-slot:conteudo>

                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca">

                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca"
                        v-model="nomeMarca"/>

                    </input-container-component>

                    {{ nomeMarca }}

                    <input-container-component titulo="Imagem" id="novaImagem" id-help="novoImagemHelp" texto-ajuda="Seleciona uma imagem PNG">

                        <input type="file" class="form-control-file" id="inputNome" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)"/>

                    </input-container-component>
                    {{arquivoImagem}}
                </div>

            </template>
            <!-- /Conteúdo Modal- Formulário --->

            <!-- Rodapé Modal--->
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
            <!-- /Rodapé Modal--->

        </modal-component>
        <!-- /Modal -->

        

    </div>
</template>

<script>
    export default{
        computed:{
            token(){
                let token = document.cookie.split(';').find(indice =>{
                    return indice.includes('token=')
                })
                token = token.split('=')[1]
                token = 'Bearer ' + token
                return token
            }
        },

        data(){
            return {
                urlBase:'http://localhost:8000/api/v1/marca',
                urlPaginacao: '',
                urlFiltro: '',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: {},
                marcas: {
                    data: []
                },
                busca: {
                    id: '',
                    nome: ''
                }
            }
        },

        methods:{

            pesquisar(){
                //console.log(this.busca)

                let filtro = ''

                for (let chave in this.busca) {
                    //console.log(chave, this.busca[chave])

                    if (this.busca[chave]) {

                        if (filtro != '') {
                            filtro += ';'
                        }
                        
                        filtro += chave+':like:'+this.busca[chave]
                    }
          
                }

                //console.log(filtro)

                if (filtro != '') {
                    this.urlPaginacao = 'page=1'
                    this.urlFiltro = '&filtro='+filtro
                }else{
                    this.urlFiltro = ''
                }

                this.carregarLista()
                
            },

            paginacao(link){
                //console.log(link)
                if (link.url) {
                    //this.urlBase = link.url //ajustando a url de consulta com o parâmetro de página
                    this.urlPaginacao = link.url.split('?')[1]
                    this.carregarLista() //requisitando novamente os dados para noss API
                }
                
            },

            carregarLista(){

                let url = this.urlBase +'?'+ this.urlPaginacao + this.urlFiltro

                let config = {
                    headers: {                      
                        'Accept':'application/json',
                        'Authorization': this.token
                    }
                }

                axios.get(url, config)
                    .then(response => {
                        this.marcas = response.data
                        //console.log(this.marcas)
                    })
                    .catch(errors => {
                        console.log(errors)

                    })
            },

            carregarImagem(event){
                this.arquivoImagem = event.target.files
            },

            salvar(){
                //console.log(this.nomeMarca, this.arquivoImagem)

                let formData = new FormData()
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept':'application/json',
                        'Authorization': this.token
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response =>{
                        //console.log(response)
                        this.transacaoDetalhes = {
                            mensagem: 'Id do registro: '+response.data.id
                        }
                        this.transacaoStatus = 'adicionado'
                    })
                    .catch(errors => {
                        
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = {
                            mensagem: errors.response.data.message,
                            dados: errors.response.data.errors
                        }
                        //errors.response.data.message
                    })
            }

        },

        mounted(){
            this.carregarLista()
        }
    }

</script>
