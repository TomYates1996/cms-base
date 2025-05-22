<template>
    <div class="media-gallery">
        <Swiper
          v-bind="swiperConfig"
          class="my-swiper content"
        >
          <SwiperSlide :lazy="true" v-for="(image, index) in images" :key="index">
            <div class="image-section">
              <ResponsiveImage :slide="formatImageToFit(image)" :aspectRatios="aspectRatios" />
            </div>
          </SwiperSlide>
          <div class="swiper-arrows">
              <div class="swiper-button-prev slider-arrow"></div>
              <div class="swiper-button-next slider-arrow"></div>
          </div>
          <div v-if="swiperConfig.modules.pagination" class="swiper-pagination"></div>
        </Swiper>
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
    created() {
        console.log(this.images);
        
    },
    data() {
        return {
            aspectRatios: [
                { width: 600, height: 320, at: 640 },
                { width: 482, height: 315, at: 1024 },
                { width: 620, height: 388, at: 1440 },
            ],
            swiperConfig: {
                slidesPerView: 1,
                spaceBetween: 10,
                breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 1,
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
        }
    },
    methods: {
        formatImageToFit(image) {
            const slide = {
                image: {
                    image_path: '/' + image
                }
            };
            return slide;
        }
    },
}
</script>

<style>


.swiper {
  margin-left: 0;
  margin-right: 0;
}
.swiper-arrows {
    display: flex;
    justify-content: space-between;
    position: static;
    z-index: 2;
    width: 100%;
    left: 0px;
    max-width: 160px;
    margin: 0px auto;
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
        &&::after {
            font-size: var(--text-2xl);
            font-weight: 900;
        }
        @media (hover:hover) {
            &&:hover {
                background-color: var(--primary-colour);
                border-color: var(--white);
                color: var(--text-primary);
            }
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
    
}    
@media screen and (min-width: 40em) {
    .swiper-arrows {
        position: absolute;
        top: 40%;
        max-width: unset;
        margin: 0px;
        padding: 0px 10px;
        .slider-arrow {
            margin-top: calc(0px - (var(--swiper-navigation-size) / 2));
        }
    }
}
</style>
