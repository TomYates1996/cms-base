<template>
    <form class="new-layout-info form" @submit.prevent="createLayout()" aria-label="Create New Page">
      <fieldset class="form-inner">
        <legend class="form-title">Create New Layout</legend>
  
        <div class="form-page-title form-field">
          <label for="title">Layout Name</label>
          <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true"/>
        </div>
        <div class="form-page-description form-field">
          <label for="description">Description</label>
          <input id="description" name="description" type="text" v-model="form.description" />
        </div>
  
  
        <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Layout
        </button>
        <button type="button" class="btn-default" tabindex="6" :disabled="form.processing" @click.prevent="cancelNew()" >
          Cancel
        </button>
      </fieldset>
    </form>
  </template>
  
  <script>
  import { useForm } from '@inertiajs/vue3';
  import { LoaderCircle } from 'lucide-vue-next';
  
  export default {
    setup(){
        const form = useForm({
            'title' : '',
            'description' : '',
        });
  
        return { form } 
    },
    props: {
      parent: Object,
      section: String,
    },
    data () {
      return {
        manualSlugChange: false, 
      }
    },
    components: {
        LoaderCircle,
    },
    methods: {
        createLayout () {
          this.form.post(route('api.layout.store'), {
          onSuccess: () => {
              this.$inertia.visit(`/cms/layouts`);  
          },
          onError: (errors) => {
            console.log('Form submission error:', errors); 
          },
        });
        },
        cancelNew() {
            this.form.reset();
            this.$emit('cancelNew');
        },
    },
  
  }
  </script>
  
  <style scoped>
  form.new-layout-info {
      flex-direction: column;
  }
  .new-layout-info input {
      color: var(--black);
  }
  
  .page-list {
      .page-item {
          display: grid;
          grid-template-columns: repeat( 5, 1fr);
          padding: 8px 20px;
          .options-section {
              display: flex;
              gap: 20px;
          }
      }
      .table-head {
          border-bottom: 1px solid var(--black);
      }
  }
  
  .form-inner {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: flex-start;
      gap: 10px;
      padding: 20px 0px;
      .form-title {
          font-size: 22px;
          font-weight: 600;
      }
      .form-field {
          display: flex;
          gap: 10px;
          align-items: center;
          label {
              font-size: 18px;
          }
      }
      input {
          border: 1px solid var(--black);
          padding: 4px;
          border-radius: 2px;
      }
  }
  </style>