<template>
  <nav class="nav" role="navigation">
    <a class="logo" :href="'/' + link" v-if="logo">
      <img :src="'/' + logo.image_path" :alt="logo.image_alt">
    </a>
    <ul class="page-list">
      <li class="page-item level-1" v-for="page in pages" :key="page.id">
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
    <DropdownNav v-if="header.menu_type === 'hamburger'" :pages="pages"/>
  </nav>
  
</template>

<script>
import DropdownNav from './DropdownNav.vue';

export default {
    props: {
        pages: Array,
        link: String,
        logo: Object,
        header: Object,
    },
    components: {
      DropdownNav,
    },
}
</script>

<style scoped>
  .logo img {
    max-width: 160px;
  }
  .nav {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: center;
    padding: 20px;
  }
  .page-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex; 
  justify-content: center; 
  gap: 5px;
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

/* Level 2 and Level 3 Dropdowns */
.dropdown {
  display: none;
  flex-direction: column;
  position: absolute;
  top: 100%; /* Position below the parent item */
  left: 50%;
  transform: translateX(-50%);
  background-color: var(--white);
  list-style: none;
  padding: 0;
  margin: 0;
}
.dropdown.level-3 {
  transform: none;
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
</style>