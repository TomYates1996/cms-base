
<template>
    <Head title="Welcome">
    </Head>

    <HamburgerHeader :header="header" :allPages="pages" :pages="header.pages" :link="header.link" :logo="header.logo"/>

    <component v-for="widget in widgets" :key="widget.id" 
        :is="widget.variant"
        :widget="widget"
    />

    <Footer v-if="footer" :footer="footer" :pages="pages" />


</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';
import axios from 'axios';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import Cards4 from '@/components/cms/pages/collections/cards/Cards4.vue';
import Footer from '@/components/nav/Footer.vue';
import { asyncWidgets } from '@/utils/asyncWidgets';

export default {
    components: {
        Head,
        Footer,
        HamburgerHeader,
        Cards4,
        defineAsyncComponent,
        ...asyncWidgets,
    },
    props: {
        widgets: Array,
        header: Object,
        footer: Object,
        pages: Object,
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