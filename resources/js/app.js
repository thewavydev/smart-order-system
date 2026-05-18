import {  createApp } from 'vue';
// import App from './components/App.vue';
// import './bootstrap';
import Hello  from './Components/Hello.vue';
import CreateOrder from './Components/CreateOrder.vue';
import Dashboard from './Components/Dashboard.vue';
import OrderProgress from './Components/OrderProgress.vue';
import OrderTable from './Components/OrderTable.vue';
import Sidebar from './Components/SideBar.vue';
import StatCard from './Components/StatCard.vue';
import TopBar from './Components/TopBar.vue';


const app = createApp({});
app.component('create-order', CreateOrder)
app.component('dashboard', Dashboard)
app.component('order-progress', OrderProgress)
app.component('order-table', OrderTable)
app.component('sidebar', Sidebar)
app.component('stat-card', StatCard)
app.component('top-bar', TopBar)

app.component('hello', Hello)

app.mount('#app');
