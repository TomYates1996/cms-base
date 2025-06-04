<template>
  <div class="media-gallery">
    <!-- Main Swiper -->
    <Swiper
      :key="images.length + '-' + images[0]"
      v-bind="swiperConfig"
      @swiper="onMainSwiper"
      class="my-swiper content"
    >
      <SwiperSlide v-for="(image, index) in images" :key="index">
          <ResponsiveImage
            :slide="formatImageToFit(image)"
            :aspectRatios="aspectRatios"
          />
      </SwiperSlide>

      <div class="swiper-arrows">
        <div class="swiper-button-prev slider-arrow"></div>
        <div class="swiper-button-next slider-arrow"></div>
      </div>

      <div v-if="swiperConfig.modules.pagination" class="swiper-pagination"></div>
    </Swiper>

    <!-- Thumbnails Row -->
    <div class="thumbnails-wrapper">
      <div
        v-for="image in images"
        :key="'thumb-' + image"       
        :class="['thumbnail', { active: activeIndex === images.indexOf(image) }]"
        @click="goToSlide(images.indexOf(image))"
      >
        <ResponsiveImage :slide="formatImageToFit(image)" :aspectRatios="aspectRatiosThumbs" />
      </div>
    </div>
  </div>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import ResponsiveImage from '../components/ResponsiveImage.vue';
import { Autoplay, Pagination, Navigation } from 'swiper/modules';

export default {
  components: {
    Swiper,
    SwiperSlide,
    ResponsiveImage,
  },
  props: {
    images: Array,
  },
  data() {
    return {
      swiperInstance: null,
      activeIndex: 0,
      aspectRatios: [
        { width: 600, height: 320, at: 640 },
        { width: 1024, height: 600, at: 1024 },
        { width: 1440, height: 708, at: 1440 },
      ],
      aspectRatiosThumbs: [
        { width: 80, height: 50, at: 640 },
      ],
    };
  },
  computed: {
    swiperConfig() {
      return {
        slidesPerView: 1,
        spaceBetween: 10,
        loop: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        modules: [Autoplay, Navigation, Pagination],
        // centeredSlides: true,
      };
    },
  },
  methods: {
    formatImageToFit(image) {
      const slide = {
        image: {
          image_path: '/' + image,
        },
      };
      return slide;
    },
    onMainSwiper(swiper) {
      this.swiperInstance = swiper;

      swiper.on('slideChange', () => {
        this.activeIndex = swiper.realIndex;
      });
    },
    goToSlide(index) {
      if (this.swiperInstance) {
        this.swiperInstance.slideToLoop(index); 
      }
    },
  },
  watch: {
  images: {
    immediate: true,
    deep: true,
    handler(newImages) {
      if (this.swiperInstance) {
        this.swiperInstance.update();
        this.activeIndex = 0;
      }
    },
  },
},
};
</script>

<style scoped>

.swiper {
    margin-left: 0;
    margin-right: 0;
    width: 100% !important; 
    max-width: 100%;
    box-sizing: border-box;
}

.swiper-arrows {
    display: flex;
    justify-content: space-between;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    width: 100%;
    left: 0px;
    margin: 0px auto;
    padding: 0px 10px;
}

.slider-arrow {
    background-color: var(--white);
    border: 2px solid var(--primary-colour);
    border-radius: var(--border-radius-slider-arrows);
    height: 45px;
    width: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: var(--text-2xl);
    cursor: pointer;
    color: var(--primary-colour);
    transition: all 0.3s;
    position: static;
    margin-top: 0px;
}

.slider-arrow::after {
  font-size: var(--text-2xl);
  font-weight: 900;
}

@media (hover: hover) {
  .slider-arrow:hover {
    background-color: var(--primary-colour);
    border-color: var(--white);
    color: var(--text-primary);
  }
}

.swiper-button-prev {
  padding-right: 3px;
  left: 0px;
}

.swiper-button-next {
    padding-left: 3px;
  right: 0px;
}

@media screen and (min-width: 40em) {
  .swiper-arrows {
    position: absolute;
    top: 50%;
    max-width: unset;
    margin: 0px;
}

}

/* Thumbnail Styles */
.thumbnails-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 20px;
}

.thumbnail {
  opacity: 0.6;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: opacity 0.3s ease, border 0.3s ease;
  display: flex;
  border-bottom: 3px solid transparent;
}

.thumbnail.active {
  opacity: 1;
  border-bottom: 3px solid var(--primary-colour);
}

.media-gallery {
    margin: 0px auto var(--widget-bottom);
    width: 100%;      
    max-width: 100%;  
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
}
</style>
