<template>
  <nav class="nav" role="navigation" :class="'menu-'+header.menu_type">
    <a class="logo" :href="link" v-if="logo">
      <img :src="'/' + logo.image_path" :alt="logo.image_alt">
    </a>
    <ul class="page-list navigation-secondary">
      <li class="page-item level-1" v-for="page in header.pages" :key="page.id">
        <a :href="'/' + page.slug">{{ page.title }}</a>

        <!-- Level 2 Dropdown -->
        <ul class="dropdown level-2" v-if="page.children && page.children.length && header.menu_type === 'dropdown'">
          <li class="page-item level-2" v-for="child in page.children" :key="child.id">
            <a :href="'/' + child.slug">{{ child.title }}</a>

            <!-- Level 3 Dropdown -->
            <ul class="dropdown level-3" v-if="child.children && child.children.length">
              <li class="page-item level-3" v-for="childo in child.children" :key="childo.id">
                <a :href="'/' + childo.slug">{{ childo.title }}</a>
              </li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
    <div class="widgets">
      <BasketIcon v-if="this.$page.url !== '/basket' && $page.props.cms.products" />
      <DropdownNav :pages="header.menu_type === 'dropdown' ? header.pages : header.hamburger_pages"/>
    </div>
  </nav>
  
</template>

<script>
import BasketIcon from '../product/basket/BasketIcon.vue';
import DropdownNav from './DropdownNav.vue';

export default {
    props: {
        pages: Array,
        allPages: Object,
        link: String,
        logo: Object,
        header: Object,
    },
    components: {
      DropdownNav,
      BasketIcon,
    },
    created() {
    },
}
</script>

<style scoped>
.logo img {
    max-width: 160px;
}
.nav {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    align-items: center;
    padding: 20px;
    max-width: var(--width-max);
    margin: 0px auto;
    width: 100%;
}
.navigation-secondary {
  list-style: none;
  padding: 0;
  margin: 0;
  display: none; 
  justify-content: center; 
  gap: 5px;
}

@media screen and (min-width: 1024px) {
  nav.menu-dropdown ::v-deep(.toggle-btn) {
    display: none;
  }
}
  
.page-item {
  position: relative;
}

.page-item a {
  display: block;
  padding: 4px 10px;
  text-align: center;
  white-space: nowrap;
}

.widgets {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 20px;
}

.dropdown {
  display: none;
  flex-direction: column;
  position: absolute;
  top: 100%; 
  left: 50%;
  transform: translateX(-50%);
  background-color: var(--white);
  list-style: none;
  padding: 0;
  margin: 0;
}
.dropdown.level-3 {
  transform: none;
  top: 0px;
}

.page-item:hover > .dropdown {
  display: flex;
}

.page-item .dropdown .page-item a {
  padding: 8px 10px;
}

.page-item.level-1:hover > .dropdown {
  display: flex; 
}

.page-item.level-2:hover > .dropdown {
  display: flex; 
}

.page-item .dropdown.level-3 {
  left: 100%; 
}

@media screen and (min-width: 64em) {
    .nav {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        align-items: center;
        padding: 20px;
    }
    .navigation-secondary {
      display: flex;
    }
}
</style>