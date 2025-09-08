// https://vitepress.dev/guide/custom-theme
// import Theme from 'vitepress/theme';
import Layout from './Layout.vue';
import './style.css';
import './styles/custom.css';

export default {
  // extends: Theme,
  Layout,
  enhanceApp({ app, router, siteData }) {
    // ...
  },
};
