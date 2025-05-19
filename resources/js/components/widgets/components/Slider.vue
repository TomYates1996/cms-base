<template>
    <div :class="'core-widget ' + widget.type + ' ' + widget.variant">
        <WidgetHeader v-if="widget.title || widget.subtitle || widget.description" :widget="widget"/>
        <Swiper
          v-bind="swiperConfig"
          class="my-swiper content"
        >
          <SwiperSlide :lazy="true" v-for="(slide, index) in widget.slides" :key="index">
            <div class="image-section">
              <ResponsiveImage :slide="slide" :aspectRatios="aspectRatios" />
            </div>
            <TextContent :slide="slide" :widget="widget" />
          </SwiperSlide>
          <div class="swiper-arrows" :class="{ 'in-footer': isMobile }">
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

import ResponsiveImage from './ResponsiveImage.vue';
import TextContent from './TextContent.vue';
import WidgetHeader from './WidgetHeader.vue';

export default {
  components: {
    Swiper,
    SwiperSlide,
    ResponsiveImage,
    TextContent,
    WidgetHeader,
  },
  props: {
    widget: Object,
    aspectRatios: Array,
    swiperConfig: {
      type: Object,
      required: true,
    },
  },
}
</script>

<style>
.core-widget {
    max-width: var(--width-max);
    margin: 0px auto var(--widget-bottom);
    width: 100%;
    .content {
        margin: 0px 20px;
    }
}
.core-widget.slider_2_across_full,
.core-widget.slider_3_across_full,
.core-widget.slider_4_across_full {
    max-width: unset;
    margin-left: 0px;
    margin-right: 0px;
    .content {
        margin: 0px 0px;
        .text-section {
            padding: 10px 20px;
        }
    }
}

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
