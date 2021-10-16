module.exports = {
    title: 'Recipe Plugin Documentation',
    description: 'Documentation for the Recipe plugin',
    base: '/docs/recipe/',
    lang: 'en-US',
    head: [
        ['meta', { content: 'https://github.com/nystudio107', property: 'og:see_also', }],
        ['meta', { content: 'https://twitter.com/nystudio107', property: 'og:see_also', }],
        ['meta', { content: 'https://youtube.com/nystudio107', property: 'og:see_also', }],
        ['meta', { content: 'https://www.facebook.com/newyorkstudio107', property: 'og:see_also', }],
    ],
    themeConfig: {
        repo: 'nystudio107/craft-recipe',
        docsDir: 'docs/docs',
        docsBranch: 'v1',
        algolia: {
            apiKey: 'd9aa4fc31d67fba1d38915de67311070',
            indexName: 'recipe'
        },
        editLinks: true,
        editLinkText: 'Edit this page on GitHub',
        lastUpdated: 'Last Updated',
        sidebar: 'auto',
    },
};
