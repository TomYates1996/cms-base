
<template>
    <Head title="Welcome">
    </Head>

    <HamburgerHeader v-for="header in headers" :key="header.id" :pages="header.pages" :logo="header.logo"/>
    <component v-for="widget in widgets" :key="widget.id" 
        :is="widget.type"
        :widget="widget"
    />

</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';
import axios from 'axios';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import Cards4 from '@/components/cms/pages/collections/cards/Cards4.vue';

export default {
    components: {
        HamburgerHeader,
        Cards4,
        defineAsyncComponent,
        cards_2_across : defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards2Across.vue')),
        cards_3_across : defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards3Across.vue')),
        cards_4_across : defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards4Across.vue')),
        imagebox_with_caption : defineAsyncComponent(() => import('@/Components/Widgets/imagebox/ImageBoxWithCaption.vue')),
    },
    props: {
        widgets: Array,
        headers: Array,
        pages: Array,
    },
    data() {
        return {
            
        }
    },
    mounted() {
    },
    methods: {
        // Get the list of pages
        // fetchPages() {
        //     axios.get('/api/pages')
        //     .then((response) => {
        //         this.pages = response.data.pages; 
        //     })
        //     .catch((error) => {
        //         console.error('Error fetching pages:', error);
        //     });
        //  }
    }
}
</script>