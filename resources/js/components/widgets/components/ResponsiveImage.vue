<template>
  <component
    :is="slide.link ? 'a' : 'div'"
    v-bind="slide.link ? { href: slide.link, 'aria-label': 'Link to related content' } : {}"
    class="image-wrapper"
  >
  <div v-if="slide.startDate" class="event-dates">
    <p class="date-from">{{ getDay(slide.startDate).dayNumber + '&nbsp;&nbsp;' + getMonth(slide.startDate).monthNumber }}</p>
    <p>-</p>
    <p class="date-to">{{ getDay(slide.endDate).dayNumber + '&nbsp;&nbsp;' + getMonth(slide.startDate).monthNumber }}</p>
  </div>

    <picture class="responsive-picture">
      <source
        v-for="(source, index) in sources"
        :key="index"
        :media="source.media"
        :srcset="source.src"
      />
      <img
        :src="fallbackSrc"
        :alt="slide.image.image_alt"
        :class="imageHovers ? 'image-hover' : ''"
      />
    </picture>
  </component>
</template>
  
  <script>
  export default {
    props: {
      slide: Object,
      aspectRatios: Array,
      imageHovers: Boolean,
    },
    data() {
      return {
        sources: [],
        fallbackSrc: '',
        months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
      };
    },
    created() {
        const urlPattern = /^\/?((?:[\w-]+\/)*)([^\/]+\.(jpg|jpeg|png|webp|gif))$/i;
        const match = this.slide.image.image_path.match(urlPattern);
  
        if (!match) {
          console.error('Invalid image URL format:', this.slide.image.image_path);
          return;
        }
  
        const folder = match[1];
        const fullFilename = match[2]; 
  
        const sortedRatios = [...this.aspectRatios].sort((a, b) => a.at - b.at);
  
        this.sources = sortedRatios.map(ratio => ({
          media: `(max-width: ${ratio.at}px)`,
          src: `/resize/${folder}${fullFilename}?w=${ratio.width}&h=${ratio.height}`,
        }));
  
        const fallback = sortedRatios[sortedRatios.length - 1];
        this.fallbackSrc = `/resize/${folder}${fullFilename}?w=${fallback.width}&h=${fallback.height}`;
    },
    methods: {
      getDay(inputDate) {
        let dayNumber = inputDate.slice(8, 10);
        return {
          dayNumber : dayNumber
        };
      },
      getMonth(inputDate) {
        let monthNumber = Number(inputDate.slice(5, 7)) - 1;
        let monthName = this.months[monthNumber]
        return {
          monthNumber : monthName
        };
      }
    },
  };
</script>
  
<style>
.image-wrapper {
  position: relative;
  display: flex;
}
.event-dates {
  position: absolute;
  bottom: 0px;
  left: 0px;
  background-color: var(--white);
  color: var(--black);
  display: flex;
  gap: 6px;
  padding: 4px 8px;
  p {
    font-weight: 600;
    display: flex;
  }
}
</style>