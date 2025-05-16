import { defineAsyncComponent } from 'vue';
import { widgetOptions } from './widgetOptions';

const modules = import.meta.glob('../Components/Widgets/**/*.vue');

export const asyncWidgets = {};

widgetOptions.forEach(widget => {
  const path = `../Components/Widgets/${widget.path}.vue`;

  if (modules[path]) {
    asyncWidgets[widget.variant] = defineAsyncComponent(modules[path]);
  } else {
    console.warn(`Component not found: ${path}`);
  }
});