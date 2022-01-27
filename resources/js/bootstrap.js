const { default: axios } = require('axios');

window._ = require('lodash');

try {
    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

// interceptar os requests da aplicação feitas pelo axios antes deles serem enviados e atuar sobre as configurações da requisição
//primeiro método de callback define as configurações da requisição antes que ela seja feita
axios.interceptors.request.use(
    config => {

        //definindo para todas as requisições os cabeçalhos de accept e authorization

        config.headers['Accept'] = 'application/json'

        let token = document.cookie.split(';').find(indice =>{
            return indice.includes('token=')
        })
        token = token.split('=')[1]
        token = 'Bearer ' + token
       

        config.headers.Authorization = token

        console.log('Interceptando o request antes do envio', config)
        return config
    },

    error => {
        console.log('Erro na requisição: ', error)
        return Promise.reject(error)
    }
)

//interceptar todos os responses dados pelo axios, trantando e aplicando lógicas antes que a response seja de fato absorvida pela aplicação
//primeiro método trata a resposta recebida para a requisição
axios.interceptors.response.use(
    response =>{

        console.log('Interceptando o response antes da absorção', response)
        return response
    },

    error => {
        if (error.response.status == 401 && error.response.data.message == 'Token has expired') {
            axios.post('http://localhost:8000/api/refresh')
                .then(response => {
                    console.log('Refresh com sucesso ', response)

                    document.cookie = 'token='+response.data.token+';SameSite=Lax'
                    window.location.reload() 
                })
        }
        
        return Promise.reject(error)
    }
)