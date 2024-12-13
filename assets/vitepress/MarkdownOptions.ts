interface MarkdownOptions extends MarkdownIt.Options {
  // Custom theme for syntax highlighting.
  // You can use an existing theme.
  // See: https://github.com/shikijs/shiki/blob/main/docs/themes.md#all-themes
  // Or add your own theme.
  // See: https://github.com/shikijs/shiki/blob/main/docs/themes.md#loading-theme
  theme?: Shiki.IThemeRegistration | { light: Shiki.IThemeRegistration; dark: Shiki.IThemeRegistration };

  // Enable line numbers in code block.
  lineNumbers?: boolean;

  // Add support for your own languages.
  // https://github.com/shikijs/shiki/blob/main/docs/languages.md#supporting-your-own-languages-with-shiki
  languages?: Shiki.ILanguageRegistration;

  // markdown-it-anchor plugin options.
  // See: https://github.com/valeriangalliat/markdown-it-anchor#usage
  anchor?: anchorPlugin.AnchorOptions;

  // markdown-it-attrs plugin options.
  // See: https://github.com/arve0/markdown-it-attrs
  attrs?: {
    leftDelimiter?: string;
    rightDelimiter?: string;
    allowedAttributes?: string[];
    disable?: boolean;
  };

  // specify default language for syntax highlighter
  defaultHighlightLang?: string;

  // @mdit-vue/plugin-frontmatter plugin options.
  // See: https://github.com/mdit-vue/mdit-vue/tree/main/packages/plugin-frontmatter#options
  frontmatter?: FrontmatterPluginOptions;

  // @mdit-vue/plugin-headers plugin options.
  // See: https://github.com/mdit-vue/mdit-vue/tree/main/packages/plugin-headers#options
  headers?: HeadersPluginOptions;

  // @mdit-vue/plugin-sfc plugin options.
  // See: https://github.com/mdit-vue/mdit-vue/tree/main/packages/plugin-sfc#options
  sfc?: SfcPluginOptions;

  // @mdit-vue/plugin-toc plugin options.
  // See: https://github.com/mdit-vue/mdit-vue/tree/main/packages/plugin-toc#options
  toc?: TocPluginOptions;

  // @mdit-vue/plugin-component plugin options.
  // See: https://github.com/mdit-vue/mdit-vue/tree/main/packages/plugin-component#options
  component?: ComponentPluginOptions;

  // Configure the Markdown-it instance.
  config?: (md: MarkdownIt) => void;

  // Same as `config` but will be applied before all other plugins.
  preConfig?: (md: MarkdownIt) => void;

  // Disable cache (experimental)
  cache?: boolean;

  // Math support (experimental)
  // You need to install `markdown-it-mathjax3` and set `math` to `true` to enable it.
  // You can also pass options to `markdown-it-mathjax3` here.
  // See: https://github.com/tani/markdown-it-mathjax3#customization
  math?: any;
}
