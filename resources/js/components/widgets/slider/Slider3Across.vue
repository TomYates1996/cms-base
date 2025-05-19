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
        { width: 600, height: 320, at: 640 },
        { width: 315, height: 206, at: 1024 },
        { width: 407, height: 254, at: 1440 },
      ],
      swiperConfig: {
        slidesPerView: 1,
        spaceBetween: 10,
        breakpoints: {
          640: {
            slidesPerView: 3,
            spaceBetween: 20,
          },
          1024: {
            slidesPerView: 3,
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
