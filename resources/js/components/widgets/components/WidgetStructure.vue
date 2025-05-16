<template>
    <div :class="'core-widget ' + widget.type + ' ' + widget.variant">
        <WidgetHeader :title="widget.title" :subtitle="widget.subtitle" :description="widget.description"/>
        <div v-if="widget.type !== 'mosaic'" class="content">
            <SlideContent :slides="widget.slides" :aspectRatios="aspectRatios" />
        </div>
        <div v-else class="content">
            <MosaicContent :slides="widget.slides" :aspectRatios="aspectRatios" />
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
    },
}
</script>

<style>
.core-widget {
    max-width: var(--width-max);
    margin: 0px auto;
    .content {
        padding: 0px 20px;
    }
}

/* Mosaic */
.mosaic .content {
        display: grid;
        grid-template-columns: 2fr 1fr; 
        grid-template-rows: auto; 
        gap: 16px; 
}

.side_by_side_full {
    max-width: unset;
    width: 100%;
    margin: 0px;
    .content {
        padding: 0px;
    }
}
    /* Side By Side */
    .side_by_side {
        .content .item {
            display: flex;
            align-items: center;
            gap: 20px;
            .text-section {
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
        } 
    }

    /* Cards */
    .cards{
        margin: 0px auto;
        max-width: var(--width-base);
        padding: 0px 20px;
        .content {
            gap: 10px;
            display: grid;
            .image-section {
                display: flex;
                img {
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