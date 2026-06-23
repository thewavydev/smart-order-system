<template>
    <div class="flex min-h-screen bg-surface">    
<main class="ml-[240px] flex-1 min-h-screen p-6">
  <div class="flex flex-wrap gap-4">
    
    <div
      v-for="statCard in statCards"
      :key="statCard.title"
      class="flex-1 min-w-[250px] p-6 bg-on-surface rounded-xl text-white shadow-sm"
    >
      <stat-card :stat-card="statCard" />
    </div>
</div>
<div class="flex-1 min-w-[250px] mt-12 p-6 bg-on-surface rounded-xl text-white shadow-sm">
  <chart-table :most-bought-items="mostBoughtItems" />
</div>
</main>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data () {
        return {
            statCards: [],
            mostBoughtItems: [],  
        }
    },

    methods: {
        getStats(){
            axios.get(`${this.$apiUrl}/dashboard/stats`)
            .then(response => {
                this.statCards = [
                    { title: 'Total Orders', value: response.data.stats.total_orders },
                    { title: 'Total Revenue', value: response.data.stats.total_revenue },
                    { title: 'Pending Orders', value: response.data.stats.pending_orders },
                    { title: 'Completed Orders', value: response.data.stats.completed_orders }
                ];
                this.mostBoughtItems = response.data.most_bought_items;
                console.log('Most bought items fetched successfully:', this.mostBoughtItems);
            })
            .catch(error => {
                console.error('Error fetching most bought items:', error);
            });
        },
    },
    created() {
        this.getStats();
    }
}
</script>

<style lang="scss" scoped>

</style>