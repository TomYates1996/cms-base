<template>
    <div class="top-row-dashboard">
        <PageStats :top-pages="topPages" :totalViews="totalViews" />
        <ProductStats />
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import Pages from './cms/pages/Pages.vue';
import Slides from './cms/Slides.vue';
import OptionsBar from '@/components/cms/structure/OptionsBar.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import PageStats from '@/components/cms/dashboard/PageStats.vue';
import ProductStats from '@/components/cms/dashboard/ProductStats.vue';


export default {
    components: {
        PageStats,
        ProductStats,
      Link,
      Pages,
      Slides,
      OptionsBar,
    },
    layout: CMSLayout,
    props: {
        totalViews: String,
        topPages: Array,
    },
}
</script>

<style>
    .top-row-dashboard {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }
</style>