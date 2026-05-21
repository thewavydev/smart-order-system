    <template>
  <header class="sticky top-0 w-full h-14 bg-surface flex items-center justify-between px-6 border-b border-outline-variant z-40">
    <div class="flex items-center gap-4">
      <span class="text-xl font-extrabold text-on-surface">SmartOrder-Sys Dashboard</span>
      
      <div class="relative ml-4">
        <Search :size="14" class="absolute left-2 top-1/2 -translate-y-1/2 text-outline-variant" />
        <input
          type="text"
          class="bg-surface-container-low border-none rounded-lg pl-8 pr-4 py-1 text-xs focus:ring-2 focus:ring-primary w-64 h-8 outline-none"
          placeholder="Search orders..."
        />
      </div>
    </div>

    <div class="flex items-center gap-6">
      <!-- <div class="flex items-center gap-2 px-3 py-1 bg-surface-container-high rounded-full">
        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
        <span class="font-data-mono text-[11px] text-on-surface-variant uppercase">API: Healthy</span>
      </div> -->

      <div class="flex items-center gap-4">
        <Bell :size="18" class="text-outline cursor-pointer hover:text-primary transition-colors" />
        <img
          src="https://lh3.googleusercontent.com/a/default-user=s88-c"
          alt="User Profile"
          class="w-8 h-8 rounded-full border border-outline-variant object-cover"
        />
      </div>
    </div>
  </header>
</template>

<script>
import { Search, Bell } from '@lucide/vue';

export default {
  name: 'TopBar',
  components: {
    Search,
    Bell
  }
};
</script>
