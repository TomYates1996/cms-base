<template>
    <form class="new-link form" @submit.prevent="createLink()" aria-label="Create New Page">
        <label for="title">Title
            <input id="title" v-model="form.title" type="title">
        </label>
        <select id="page-to-link">
            <option disabled value="">-- Select a page --</option>
            <option v-for="page in filteredPages" :value="page.title" :key="page.id" @click="selectPage(page)">
                {{ page.title }}
            </option>
        </select>
        <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Page
        </button>
    </form>
</template>

<script>
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

export default {
    components: {
        LoaderCircle,
    },
    setup(){
        const form = useForm({
            'id' : '',
            'title' : '',
            'section' : '',
        });
        return { form } 
    },
    props: {
        parent: Object,
        section: String,
    },
    data() {
        return {
            pages: [],
        }
    },
    created() {
        this.getPages();
    },
    computed: {
        filteredPages() {
            return this.pages.filter(page => !page.is_link);
        }
    },
    methods: {
        getPages() {
            axios.get('/api/pages/all')
            .then((response) => {
                this.pages = response.data.pages; 
            })
        },
        selectPage(page) {
            this.form.id = page.id;
            
        },
        createLink () {        
            this.form.section = this.section;
            this.form.post(route('api.pages.link.store'), {
            onSuccess: () => {
            if (this.parent) {
                this.$inertia.visit(`/cms/pages/children/${this.parent.slug}`);  
            } else {
                this.$inertia.visit(`/cms/pages/${this.section}`);  
            }
            },
            onError: (errors) => {
            console.log('Form submission error:', errors); 
            },
        });
      },
    },
}
</script>

<style>

</style>