<template>
    <Slider :widget="widget" :aspectRatios="aspectRatios" :swiperConfig="swiperConfig"/>
</template>

<script>
import { Autoplay, Pagination, Navigation } from 'swiper/modules';
import Slider from '../components/Slider.vue';

export default {
  components: {
    Slider,
  },
  props: {
    widget: Object,
  },
  data() {
    return {
      aspectRatios: [
        { width: 320, height: 220, at: 640 },
        { width: 612, height: 400, at: 1024 },
        { width: 1536, height: 960, at: 1440 },
      ],
      swiperConfig: {
        slidesPerView: 2,
        spaceBetween: 10,
        breakpoints: {
          640: {
            slidesPerView: 2,
            spaceBetween: 20,
          },
          1024: {
            slidesPerView: 2,
            spaceBetween: 20,
          },
        },
        autoplay: false,
        loop: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        modules: [Autoplay, Navigation], 
      },
    };
  },
};
</script>
