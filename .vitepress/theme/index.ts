// https://vitepress.dev/guide/custom-theme
import 'viewerjs/dist/viewer.min.css'; // 引入 viewerjs 的样式[1](@ref)
import { useRoute } from 'vitepress';
import imageViewer from 'vitepress-plugin-image-viewer';
import DefaultTheme from 'vitepress/theme'; // 建议保留基础主题
import Layout from './Layout.vue';
import './style.css';
import './styles/custom.css';

export default {
  extends: DefaultTheme, // 继承默认主题
  Layout,
  setup() {
    const route = useRoute();
    // 使用插件，自动为图片添加预览功能[1](@ref)
    imageViewer(route);
  },
};
