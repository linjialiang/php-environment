interface VitePressData<T = any> {
  /**
   * Site-level metadata
   */
  site: Ref<SiteData<T>>;
  /**
   * themeConfig from .vitepress/config.js
   */
  theme: Ref<T>;
  /**
   * Page-level metadata
   */
  page: Ref<PageData>;
  /**
   * Page frontmatter
   */
  frontmatter: Ref<PageData['frontmatter']>;
  /**
   * Dynamic route params
   */
  params: Ref<PageData['params']>;
  title: Ref<string>;
  description: Ref<string>;
  lang: Ref<string>;
  isDark: Ref<boolean>;
  dir: Ref<string>;
  localeIndex: Ref<string>;
}

interface PageData {
  title: string;
  titleTemplate?: string | boolean;
  description: string;
  relativePath: string;
  filePath: string;
  headers: Header[];
  frontmatter: Record<string, any>;
  params?: Record<string, any>;
  isNotFound?: boolean;
  lastUpdated?: number;
}
