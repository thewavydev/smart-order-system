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
</main>
    </div>
</template>

<script>
export default {
    data () {
        return {
            statCards: [
                { title: 'Queue Depth', value: '1,029' },
                { title: 'Orders Processed', value: '5,432' },
                { title: 'Pending Orders', value: '1,234' },
                { title: 'Completed Orders', value: '4,198' },
            ]   
        }
    }
}
</script>

<style lang="scss" scoped>

</style>