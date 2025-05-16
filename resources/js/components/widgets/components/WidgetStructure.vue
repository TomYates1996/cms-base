<template>
    <div :class="'core-widget ' + widget.type + ' ' + widget.variant">
        <WidgetHeader v-if="widget.title || widget.subtitle || widget.description" :title="widget.title" :subtitle="widget.subtitle" :description="widget.description"/>
        <div v-if="widget.type !== 'mosaic'" class="content">
            <SlideContent :slides="widget.slides" :aspectRatios="aspectRatios" />
        </div>
        <div v-else class="content">
            <MosaicContent :slides="widget.slides" :aspectRatios="aspectRatios" :mosaicRatios="mosaicRatios" :imageHovers="imageHovers"/>
        </div>
    </div>
</template>

<script>
import MosaicContent from './MosaicContent.vue';
import SlideContent from './SlideContent.vue';
import WidgetHeader from './WidgetHeader.vue';

export default {
    components: {
        WidgetHeader,
        SlideContent,
        MosaicContent,
    },
    props: {
        widget: Object,
        aspectRatios: Array,
        mosaicRatios: {
            type: Array,
            default: [],
        },
        imageHovers: {
            type: Boolean,
            default: false,
        },
    },
}
</script>

<style>
.core-widget {
    max-width: var(--width-max);
    margin: 0px auto var(--widget-bottom);
    .content {
        padding: 0px 20px;
        .image-wrapper {
            display: block;
            overflow: hidden;
        }

        .responsive-picture {
            display: block;
            width: 100%;
            height: 100%;
        }

        .image-hover {
            width: 100%;
            height: auto;
            object-fit: cover;
            transition: var(--transition-image-hover);
        }

        @media (hover:hover) {
            .image-wrapper:hover .image-hover {
                transform: var(--transform-image-hover);
            }
        } 
    }
}

/* Mosaic */
.mosaic .content {
    display: flex;
    flex-direction: column;
    gap: 16px; 
    .big-image {
        width: 100%; 
        @media (hover:hover) {
            .image-wrapper:hover .image-hover {
                transform: none;
            }
        } 
    }
    .small-images {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 16px;
         .item {
            display: flex;
            gap: 16px;
            .image-section {
                flex-basis: 25%;
            }
        }
        .text-section {
            flex-basis: 75%;
            justify-content: center;
            .slide-title {
                font: var(--slide-title-small);
                width: 100%;
            }
        }
    }
    @media screen and (min-width: 64em) {
        flex-direction: row;
        .big-image {
            width: 55%;
        }
        .small-images {
            width: 45%;
        }
    }
}

.side_by_side_full {
    max-width: unset;
    width: 100%;
    margin-left: 0px;
    margin-right: 0px;
    .content {
        padding: 0px;
        padding-right: 20px;
    }
}
    /* Side By Side */
    .side_by_side {
        .content .item {
            display: flex;
            align-items: center;
            gap: 20px;
            .text-section {
                flex-basis: 40%;
                background-color: var(--primary-colour);
                color: var(--text-primary);
                padding: 40px;
                @media (hover:hover) {
                    a:hover {
                        color: var(--white);
                        text-decoration: underline;
                    }
                }
            }
            .image-wrapper {
                overflow: visible;
            }
            .responsive-picture {
                position: relative;
                img {
                    z-index: 2;
                    position: relative;
                }
                &::before {
                    position: absolute;
                    content: '';
                    top: -10px;
                    left: -10px;
                    background-color: var(--secondary-colour);
                    height: 100%;
                    width: 100%;
                    z-index: 1;
                }
            }
        } 
    }

    /* Cards */
    .cards{
        margin: 0px auto;
        max-width: var(--width-base);
        .content {
            gap: 10px;
            display: grid;
            .image-section {
                display: flex;
                overflow: hidden;
                border-radius: var(--border-radius-cards);
            }
        }
    }

    /* Hero Image */
    .hero {
        max-width: unset;
        margin: 0px 0px var(--widget-bottom);
        overflow: hidden;
        .content {
            padding: 0px;
            .item {
                position: relative;
                .text-section {
                    position: absolute;
                }
            }
        }
    }
    .hero_image {
        border-radius: var(--border-radius-hero);
        .content {
            .item {
                .text-section {
                    bottom: 0px;
                    left: 0px;
                    background-color: var(--white);
                    padding: 20px;
                }
            }
        }
    }
    /* Hero Image Alt */
    .hero_image_alt {
        border-radius: var(--border-radius-hero-alt);
        .content {
            .item {
                .text-section {
                    bottom: 50%;
                    left: 50%;
                    transform: translate(-50%, 50%);
                    padding: 20px;
                    .slide-title {
                        font: var(--hero-alt-title);
                        color: var(--hero-text-alt-colour);
                        text-shadow: 2px 2px 4px var(--black);
                        @media (hover:hover) {
                            a:hover {
                                color: var(--hero-text-alt-colour);
                            }
                        }
                    }
                    .slide-desc {
                        display: none;
                    }
                }
            }
        }
    }

    @media screen and (min-width: 40em) {        
        .cards_2_across .content {
            grid-template-columns: repeat(2, 1fr);
        }
        .cards_3_across .content {
            grid-template-columns: repeat(3, 1fr);
        }
        .cards_4_across .content {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>