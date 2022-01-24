<template>
    <div :class="estilo" role="alert">
        Mensagem de feedback
    </div>
</template>

<script>
    export default {
        props: ['tipo'],
        computed: {
            estilo(){
                return 'alert alert-'+this.tipo
            }
        }

    }
</script>
