<template>
    <EditPage :page="page"/>
</template>
  
<script>
import EditPage from '@/components/cms/pages/EditPage.vue';

  
export default {
    components: {
        EditPage
    },
    props:  {
        page: Object,
    },

}
</script>  
  
<style scoped>
</style>