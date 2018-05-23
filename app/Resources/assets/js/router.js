import Vue from 'vue';
import VueRouter from 'vue-router'
Vue.use(VueRouter)

const routes = [
    { path: '/bae', component: Foo },
]

export default new VueRouter({
    routes
})
