<template>
    <input type="checkbox" class="toggle">
   <form class="edit-page-info" @submit.prevent="updatePage(form)">
        <div class="page-title">
            <label for="title">Page Title</label>
            <input id="title" autofocus type="text" required v-model="form.title" >
        </div>
        <div class="page-slug">
            <label for="slug">Slug</label>
            <input id="slug" required type="text" v-model="form.slug" >
        </div>
        <label>
            <input type="checkbox" v-model="form.show_in_nav"/> 
        </label>
        <button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Save Changes
        </button>
    </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

export default {
    setup(){
        const form = useForm({
            'title' : '',
            'slug' : '',
            'show_in_nav' : false,
            'created_by' : '',
        });

        return { form } 
    },
    components: {
        LoaderCircle,
    },
    props: {
        page: Object,
    },
    created() {
        this.form.title = this.page.title,
        this.form.slug = this.page.slug,
        this.form.created_by = this.page.created_by,
        this.form.show_in_nav = this.page.show_in_nav ? this.page.show_in_nav : true
    },
    methods: {
        updatePage (form) {
            form.put(route('api.pages.update', this.page.id), {
            onSuccess: () => {
            },
            onError: (errors) => {
            console.log('Form submission error:', errors);
            },
        });
        },
    },

}
</script>

<style scoped>
form.edit-page-info {
    display: none;
    flex-direction: column;
}
.edit-page-info input {
    color: var(--black);
}
.toggle {
    
}
.toggle:checked ~ form.edit-page-info {
    display: flex;
}
</style>