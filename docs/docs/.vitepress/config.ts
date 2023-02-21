import {defineConfig} from 'vitepress';

export default defineConfig({
  title: 'Recipe Plugin',
  description: 'Documentation for the Recipe plugin',
  base: '/docs/recipe/',
  lang: 'en-US',
  head: [
    ['meta', {content: 'https://github.com/nystudio107', property: 'og:see_also',}],
    ['meta', {content: 'https://twitter.com/nystudio107', property: 'og:see_also',}],
    ['meta', {content: 'https://youtube.com/nystudio107', property: 'og:see_also',}],
    ['meta', {content: 'https://www.facebook.com/newyorkstudio107', property: 'og:see_also',}],
  ],
  themeConfig: {
    socialLinks: [
      {icon: 'github', link: 'https://github.com/nystudio107'},
      {icon: 'twitter', link: 'https://twitter.com/nystudio107'},
    ],
    logo: '/img/plugin-logo.svg',
    editLink: {
      pattern: 'https://github.com/nystudio107/craft-recipe/edit/develop/docs/docs/:path',
      text: 'Edit this page on GitHub'
    },
    algolia: {
      appId: 'ANVOBU7GYX',
      apiKey: '66d1888afb505fa3d1b0342a487706ff',
      indexName: 'recipe'
    },
    lastUpdatedText: 'Last Updated',
    sidebar: [],
    nav: [
      {text: 'Home', link: 'https://nystudio107.com/plugins/recipe'},
      {text: 'Store', link: 'https://plugins.craftcms.com/recipe'},
      {text: 'Changelog', link: 'https://nystudio107.com/plugins/recipe/changelog'},
      {text: 'Issues', link: 'https://github.com/nystudio107/craft-recipe/issues'},
    ]
  },
});
