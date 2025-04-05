<template>
  <input type="checkbox" class="toggle">
 <form class="edit-page-info" @submit.prevent="createPage()">
      <div class="page-title">
          <label for="title">Page Title</label>
          <input id="title" autofocus type="text" required v-model="form.title" >
      </div>
      <div class="page-slug">
          <label for="slug">Slug</label>
          <input id="slug" required type="text" v-model="form.slug" >
      </div>
      <label>
          Show In Nav
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
          'show_in_nav' : true,
          'created_by' : '',
      });

      return { form } 
  },
  components: {
      LoaderCircle,
  },
  methods: {
      createPage () {
        this.form.post(route('api.pages.store'), {
        onSuccess: () => {
          this.$inertia.visit('/dashboard');  
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