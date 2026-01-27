// 1. 定义配置的类型
export interface SiteConfigInterface {
  tutorialApiUrl: string;
  apiUrl: string;
  emadBilibiliUrl: string;
  githubUrl: string;
  giteeUrl: string;
}

// 2. 按照类型定义数据
const siteConfig: SiteConfigInterface = {
  tutorialApiUrl: 'https://tutorial.e8so.com/',
  apiUrl: 'https://php-environment.e8so.com/',
  emadBilibiliUrl: 'https://space.bilibili.com/473623415',
  githubUrl: 'https://github.com/linjialiang/php-environment',
  giteeUrl: 'https://gitee.com/linjialiang/php-environment',
};

export { siteConfig };
