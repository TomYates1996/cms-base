
<template>
    <Head title="Welcome">
    </Head>

    <HamburgerHeader :pages="this.pages"/>
    <!-- <Cards4/> -->

</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import Cards4 from '@/components/cms/pages/collections/cards/Cards4.vue';

export default {
    components: {
        HamburgerHeader,
        Cards4,
    },
    data() {
        return {
            pages: []
        }
    },
    mounted() {
        this.fetchPages();
    },
    methods: {
        // Get the list of pages
        fetchPages() {
            axios.get('/api/pages')
            .then((response) => {
                this.pages = response.data.pages; 
            })
            .catch((error) => {
                console.error('Error fetching pages:', error);
            });
         }
    }
}
</script>