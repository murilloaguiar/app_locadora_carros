<template>
    <div class="container">
        <!-- Cards -->
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
                        :visualizar="{
                            visivel: true,
                            dataBsToggle: 'modal',
                            dataBsTarget: '#modalMarcaVisualizar'
                        }"
                        :atualizar ="{
                            visivel: true,
                            dataBsToggle: 'modal',
                            dataBsTarget: '#modalMarcaAtualizar'
                        }"
                        :remover ="{
                            visivel: true,
                            dataBsToggle: 'modal',
                            dataBsTarget: '#modalMarcaRemover'
                        }"
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
        <!-- /Cards -->

        <!-- Modal de inclusão de marca -->
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

                    <input-container-component titulo="Imagem" id="novaImagem" id-help="novoImagemHelp" texto-ajuda="Seleciona uma imagem PNG">

                        <input type="file" class="form-control-file" id="novaImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)"/>

                    </input-container-component>
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
        <!-- /Modal de inclusão de marca-->

        <!-- Modal de visualização de marca -->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar marca">
            <template v-slot:alertas></template>

            <template v-slot:conteudo>
                
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

                <input-container-component titulo="Data de criação">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-container-component>

                <input-container-component titulo="Imagem">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </template>

        </modal-component>
        <!-- /Modal de visualização de marca -->

        <!-- Modal de exclusão de marca -->
        <modal-component id="modalMarcaRemover" titulo="Remover marca">
            <template v-slot:alertas>

                <alert-component v-if="$store.state.transacao.status == 'sucesso'" tipo="success" titulo="Transação realizada" :detalhes="$store.state.transacao">
                    
                </alert-component>

                <alert-component v-if="$store.state.transacao.status == 'erro'" tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao">
                    
                </alert-component>


            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status!='sucesso'">
                
                <input-container-component titulo="id">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button v-if="$store.state.transacao.status!='sucesso'" type="button" class="btn btn-danger" @click="remover()">Remover</button>
            </template>

        </modal-component>
        <!-- /Modal de exclusão de marca -->

        <!-- Modal de atualização de marca -->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar marca">
            
            <!-- Alerts Modal  de atualização --->
            <template v-slot:alertas> 
                <alert-component v-if="$store.state.transacao.status == 'sucesso'" tipo="success" titulo="Transação realizada" :detalhes="$store.state.transacao">
                    
                </alert-component>

                <alert-component v-if="$store.state.transacao.status == 'erro'" tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao">
                    
                </alert-component>
            </template>
            <!-- /Alerts Modal  de atualização --->

            <!-- Conteúdo Modal de atualização - Formulário --->
            <template v-slot:conteudo>

                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNomeAtualizado" id-help="novoAtualizadoNomeHelp" texto-ajuda="Informe o nome da marca">

                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoAtualizadoNomeHelp" placeholder="Nome da marca"
                        v-model="$store.state.item.nome"/>

                    </input-container-component>

                    <input-container-component titulo="Imagem" id="novaImagemAtualizada" id-help="novoImagemAtualizadaHelp" texto-ajuda="Seleciona uma imagem PNG">

                        <input type="file" class="form-control-file" id="novaImagemAtualizada" aria-describedby="novoImagemAtualizadaHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)"/>

                    </input-container-component>
                    
                </div>

            </template>
            <!-- /Conteúdo Modal de atualização - Formulário --->

            <!-- Rodapé Modal de atualização --->
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>
            </template>
            <!-- /Rodapé Modal de atualização --->

        </modal-component>
        <!-- /Modal de atualização de marca -->

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

            atualizar(){

                let url = this.urlBase + '/' + this.$store.state.item.id

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Accept:'application/json',
                        Authorization: this.token
                    }
                }

                let formData = new FormData()
                formData.append('_method', 'PATCH')
                formData.append('nome', this.$store.state.item.nome)
                if (this.arquivoImagem[0]) {
                    formData.append('imagem', this.arquivoImagem[0])
                }
                

                axios.post(url, formData, config)
                    .then(response => {
                        novaImagemAtualizada.value = '' //atribuindo vazio para o campo imagem após o registro ter sido atualizado
                        
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.mensagem = 'Registro de marca atualizado com sucesso'
                        this.carregarLista()
                    })
                    .catch(errors=>{
                        
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.message
                        this.$store.state.transacao.dados = errors.response.data.errors
                    })

            },

            remover(){
                let confirmacao = confirm('tem certeza que deseja remover esse registro?')

                if (!confirmacao) 
                    return false;

                let url = this.urlBase + '/' + this.$store.state.item.id

                //console.log(url)

                let config = {
                    headers: {
                        Accept: 'application/json',
                        Authorization: this.token
                    }
                }

                let formData = new FormData()
                formData.append('_method','delete')

                

                axios.post(url, formData, config)
                    .then(response=> {
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.mensagem = response.data.msg
                        this.carregarLista()
                    })
                    .catch(errors=> {
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.erro

                    })
            },

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
                        Accept:'application/json',
                        Authorization: this.token
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
