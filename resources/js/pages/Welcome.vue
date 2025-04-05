
<template>
    <Head title="Welcome">
    </Head>

    <hamburgerHeader :pages="this.pages"/>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import hamburgerHeader from '@/components/nav/hamburgerHeader.vue';

export default {
    components: {
        hamburgerHeader,
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