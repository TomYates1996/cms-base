<template>
    <form @submit.prevent="submitForm">
      <div v-for="(value, key) in form" :key="key" class="form-group">
        <label :for="key">{{ key }}</label>
  
        <input
          v-if="typeof value === 'boolean'"
          type="checkbox"
          :id="key"
          v-model="form[key]"
        />
  
        <select
          v-else-if="key === 'icon'"
          :id="key"
          v-model="form[key]"
          required
        >
          <option v-for="(option, index) in options[key]" :key="index" :value="option.value">
            {{ option.label }}
          </option>
        </select>
  
        <input
          v-else-if="key === 'link'"
          type="url"
          :id="key"
          v-model="form[key]"
        />
  
        <input
          v-else
          type="text"
          :id="key"
          v-model="form[key]"
        />
      </div>
  
      <button type="submit">Submit</button>
    </form>
  </template>
  
  <script>
  import axios from 'axios';
  import { reactive } from 'vue';
  
  export default {
    props: {
      type: String,
    },
    data() {
      return {
        form: {},
        options: {
          icon: [
            { label: 'Facebook Square', value: 'square-facebook' },
            { label: 'Facebook', value: 'facebook' },
            { label: 'Facebook F', value: 'facebook-f' },
            { label: 'Twitter', value: 'twitter' },
            { label: 'X', value: 'x-twitter' },
            { label: 'Square X', value: 'square-x-twitter' },
            { label: 'Instagram', value: 'instagram' },
            { label: 'Instagram Square', value: 'square-instagram' },
          ],
        },
      };
    },
    created() {
      if (this.type === 'social') {
        this.form = reactive({
          link: '',
          icon: '',
          label: '',
        });
      } else if (this.type === 'page') {
        this.form = reactive({
          title: '',
          slug: '',
          show_in_nav: true,
          created_by: '',
          parent: {},
          section: '',
        });
      }
    },
    methods: {
      async submitForm() {
        try {
          let url = '';
          if (this.type === 'social') {
            url = route('api.social.store');
          }
  
          const response = await axios.post(url, this.form);
          this.$emit('created', response.data);
        } catch (error) {
          if (error.response && error.response.data && error.response.data.errors) {
            console.error('Validation errors:', error.response.data.errors);
          } else {
            console.error('Submission error:', error);
          }
        }
      },
    },
  };
  </script>
  