<template>
  <button @click="toggle()" class="toggle-btn">
    <span :class="toggled ? 'toggle-yes toggle' : 'toggle-no toggle'"></span>
    <span :class="toggled ? 'toggle-yes toggle' : 'toggle-no toggle'"></span>
    <span :class="toggled ? 'toggle-yes toggle' : 'toggle-no toggle'"></span>
  </button>
  <nav class="hamburger-nav" v-if="toggled">
    <div class="hamburger-inner">
        <ul class="col-1">
            <li v-for="page in pages" :key="page.id">
                <a :href="'/' + page.slug">{{ page.title }}</a>
                <button class="more-btn" @click="showChildDropdown(page)" v-if="page.children"><font-awesome-icon :icon="['fas', 'plus']" /></button>
            </li>
        </ul>
        <ul v-if="openChild" class="col-2 dropdown level-2">
            <li v-for="child in openDropdown" :key="child.id">
                <a :href="'/' + child.slug">{{ child.title }}</a>
                <button class="more-btn" @click="showGrandchildDropdown(child)" v-if="child.children"><font-awesome-icon :icon="['fas', 'plus']" /></button>
            </li>
        </ul>
        <ul v-if="openGrandchild" class="col-3 dropdown level-3">
            <li v-for="grandchild in openCol3Dropdown" :key="grandchild.id">
                <a :href="grandchild.slug">{{ grandchild.title }}</a>
            </li>
        </ul>
    </div>
  </nav>
</template>

<script>
export default {
    props: {
        pages: Array,
    },
    data() {
        return {
            toggled: false,
            openChild: false,
            openDropdown: null,
            openGrandchild: false,
            openCol3Dropdown: null,
        }
    },
    methods: {
        toggle() {
           this.toggled = !this.toggled;
        },
        showChildDropdown(page) {
            if (this.openDropdown === page.children) {
                this.openDropdown = null;
                this.openChild = false;
                this.openCol3Dropdown = null;
                this.openGrandchild = false;
            } else {
                this.openGrandchild = false;
                this.openDropdown = page.children;
                this.openChild = true;
            }
        },
        showGrandchildDropdown(page) {
            if (this.openCol3Dropdown === page.children) {
                this.openCol3Dropdown = null;
                this.openGrandchild = false;
            } else {
                this.openCol3Dropdown = page.children;
                this.openGrandchild = true;
            }
        },
    },
}
</script>

<style scoped>
    .toggle-btn {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        cursor: pointer;
        flex-direction: column;
        gap: 4px;
        padding: 10px;
        z-index: 1000;
    }
    .toggle {
        transition: all 0.2s;
        position: relative;
        height: 2px;
        width: 18px;
        background-color: var(--black);
    }
    .toggle-yes {
        background-color: var(--white);
    }
    .toggle-yes:first-of-type {
        transform: rotate(45deg) translate(4px, 4px);
    }
    .toggle-yes:nth-of-type(2) {
        width: 0px;
        background-color: var(--white);
    }
    .toggle-yes:nth-of-type(3) {
        transform: rotate(-45deg) translate(4px, -4px);
    }
    .hamburger-nav {
        position: fixed;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 100vw;
        background-color: var(--primary-colour);
        .hamburger-inner {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            padding: 40px;
            gap: 10px;
            max-width: 1000px;
            ul li {
                display: flex;
                width: 100%;
                background-color: var(--secondary-colour);
                border: 1px solid var(--white);
                border-bottom: none;
                @media (hover:hover) {
                    &:hover {
                        background-color: var(--tertiary-colour); 
                        a {
                            color: var(--text-tertiary);
                            text-decoration: none;
                        }
                        .more-btn {
                            color: var(--text-tertiary);
                        }
                    }
                }
                a {
                    padding: 10px 20px;
                    width: 100%;
                }
                .more-btn {
                    padding: 0px 10px;
                }
            }
            li:last-of-type {
                border-bottom: 1px solid var(--white);
            }
        }
    }
</style>