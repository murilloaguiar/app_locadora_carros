<template>
    <div>
    
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="titulo, key in titulos" :key="key">{{titulo}}</th>
                    
                </tr>
            </thead>
            <tbody>
                <tr v-for="marcas in dados" :key="marcas.id">
                    <th scope="row">{{marcas.id}}</th>
                    <td>{{marcas.nome}}</td>
                    <td><img :src="'/storage/'+marcas.imagem" :alt="'marca-'+marcas.nome" width="35" height="35"></td>
                    
                </tr>
                
            </tbody>
        </table>
        
    </div>
</template>

<script>
export default {
    props: ['dados', 'titulos']
};
</script>
