// https://vitepress.dev/guide/custom-theme
import 'viewerjs/dist/viewer.min.css'; // 引入 viewerjs 的样式[1](@ref)
import { useRoute } from 'vitepress';
import imageViewer from 'vitepress-plugin-image-viewer';
import Theme from 'vitepress/theme';
import Layout from './Layout.vue';
import './style.css';
import './styles/custom.css';

export default {
  extends: Theme,
  Layout,
  setup() {
    const route = useRoute();
    // 使用插件，自动为图片添加预览功能[1](@ref)
    imageViewer(route, 'main', {
      rotatable: true, // 启用旋转功能
      // 详细配置工具栏，确保旋转按钮可见
      toolbar: {
        zoomIn: 1, // 放大按钮
        zoomOut: 1, // 缩小按钮
        oneToOne: 1, // 1:1 尺寸按钮
        reset: 1, // 重置按钮
        prev: 1, // 上一张图片
        play: 1, // 播放幻灯片（如果有多张图）
        next: 1, // 下一张图片
        rotateLeft: 1, // 【关键】向左旋转按钮 (90度逆时针)
        rotateRight: 1, // 【关键】向右旋转按钮 (90度顺时针)
        flipHorizontal: 1, // 水平翻转
        flipVertical: 1, // 垂直翻转
      },
      // 其他常用配置
      navbar: true, // 显示底部缩略图导航
      title: true, // 可根据需要决定是否显示图片标题（通常VitePress文档中不需要）
      keyboard: true, // 启用键盘支持
      loop: true, // 是否允许循环查看​​
    });
  },
};
