import {  createApp } from 'vue';
// import App from './components/App.vue';
// import './bootstrap';
import Hello  from './Components/Hello.vue';

const app = createApp({});
app.component('hello', Hello)

app.mount('#app');
