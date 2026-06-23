<template>
  <div class="bg-on-surface text-white rounded-xl p-6 shadow-sm">
    <h2 class="text-lg font-semibold mb-4">Most Bought Items</h2>
    <table class="w-full text-left">
      <thead class="text-white/60 text-sm border-b border-white/10">
        <tr>
          <th class="py-2">Product</th>
          <th class="py-2">Total Sold</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="item in mostBoughtItems"
          :key="item.id"
          class="border-b border-white/5 hover:bg-white/5 transition"
        >
          <td class="py-3">{{ item.name }}</td>
          <td class="py-3 font-bold">{{ item.total_sold }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
    props: {
        mostBoughtItems: {
            type: Array,
        },
    },
    data () {
        return {
            
        }
    }
}
</script>

<style lang="scss" scoped>

</style>