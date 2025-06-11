<template>
  <div :class="'core-widget core-item-grid ' + widget.type + ' ' + widget.variant">
    <WidgetHeader v-if="widget.title || widget.subtitle || widget.description" :widget="widget" />
    <!-- Subcategory Filters -->
    <div class="filters px-4 mb-4">
      <div v-for="cat in widget.categories" :key="cat.id" class="mb-2">
        <strong>{{ cat.name }}</strong>
        <div v-for="subcat in cat.subcategories" :key="subcat.id" class="ml-4">
          <label>
            <input
              type="checkbox"
              :value="subcat.id"
              v-model="selectedSubcategories"
            />
            {{ subcat.name }}
          </label>
        </div>
      </div>
    </div>

    <!-- Filtered Slide Content -->
    <div class="content">
      <SlideContent :widget="{ ...modifiedWidget, slides: filteredSlides }" :aspectRatios="aspectRatios" />
    </div>

    <!-- Pagination Controls -->
    <div class="pagination-controls" style="text-align:center; margin-top:1rem;">
      <button
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage === 1 || isLoading"
        class="px-3 py-1 border rounded mr-2"
      >
        Prev
      </button>

      <button
        v-for="page in totalPages"
        :key="page"
        @click="goToPage(page)"
        :class="['px-3 py-1 border rounded mx-1', { 'bg-blue-500 text-white': page === currentPage }]"
        :disabled="isLoading"
      >
        {{ page }}
      </button>

      <button
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage === totalPages || isLoading"
        class="px-3 py-1 border rounded ml-2"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import WidgetHeader from '../components/WidgetHeader.vue';
import SlideContent from '../components/SlideContent.vue';

export default {
  components: {
    WidgetHeader,
    SlideContent,
  },
  props: {
    widget: Object,
  },
  data() {
    return {
      modifiedWidget: {
        ...this.widget,
        slides: [],
      },
      aspectRatios: [
        { width: 640, height: 400, at: 640 },
        { width: 466, height: 350, at: 1024 },
        { width: 800, height: 600, at: 1440 },
      ],
      selectedSubcategories: [],
      currentPage: 1,
      totalPages: 1,
      isLoading: false,
    };
  },
  computed: {
    filteredSlides() {
      if (this.selectedSubcategories.length === 0) {
        return this.modifiedWidget.slides;
      }

      return this.modifiedWidget.slides.filter(slide =>
        this.selectedSubcategories.includes(slide.sub_category_id)
      );
    },
  },
  methods: {
    goToPage(page) {
      if (page < 1 || page > this.totalPages || this.isLoading) return;
      this.currentPage = page;
      this.loadPage(page);
    },
    loadPage(page) {
      this.isLoading = true;
      const params = { page };

      let categories = this.widget.categories;
      if (categories) {
        if (!Array.isArray(categories)) {
          categories = [categories];
        }
        params.category = categories.map(c => (typeof c === 'object' ? c.id : c)).join(',');
      }

      if (this.widget.tags && this.widget.tags.length > 0) {
        params.tag = this.widget.tags.join(',');
      }

      axios
        .get(`/api/${this.widget.type}/grid`, { params })
        .then((response) => {
          const slides = response.data.items || [];
          const totalCount = response.data.total_count ?? null;
          const perPage = 6;

          this.modifiedWidget.slides = slides;

          this.totalPages =
            totalCount !== null
              ? Math.ceil(totalCount / perPage)
              : slides.length < perPage
              ? page
              : page + 1;
        })
        .catch((error) => {
          console.error('Error loading items:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
  created() {
    this.loadPage(this.currentPage);
  },
};
</script>

<style>
.core-item-grid .content {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  padding: 0px 20px;
}
.pagination-controls button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.pagination-controls button.bg-blue-500 {
  background-color: #3b82f6;
  color: white;
}
</style>
