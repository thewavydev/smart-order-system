import {  createApp } from 'vue';
import Orders from './Components/Orders.vue';
import Dashboard from './Components/Dashboard.vue';
import OrderTable from './Components/OrderTable.vue';
import Sidebar from './Components/SideBar.vue';
import ChartTable from './Components/ChartTable.vue';
import StatCard from './Components/StatCard.vue';
import TopBar from './Components/TopBar.vue';

const apiUrl = `${window.location.origin}/api`;


const app = createApp({});

app.component('orders', Orders)
app.component('order-table', OrderTable)
app.component('chart-table', ChartTable)
app.component('dashboard', Dashboard)
app.component('sidebar', Sidebar)
app.component('stat-card', StatCard)
app.component('top-bar', TopBar)


app.config.globalProperties.$apiUrl = apiUrl;
app.mount('#app');
