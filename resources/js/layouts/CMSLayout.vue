<template>
    <div class="layout">
    <aside class="sidebar">
        <OptionsBar />
      </aside>
      <main class="main-content">
        <slot />
      </main>
    </div>
</template>
  
<script>
import OptionsBar from '@/components/cms/structure/OptionsBar.vue';

export default {
    components: {
        OptionsBar
    },
};

</script>
  
<style scoped>
.layout {
    display: flex;
    min-height: 100vh;
}
  
.sidebar {
    width: 25%; 
    background-color: #f8f9fa;
}
  
.main-content {
    width: 75%;
    padding: 2rem;
    position: relative;
}
</style>