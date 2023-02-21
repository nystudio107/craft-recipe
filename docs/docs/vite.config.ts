import {defineConfig} from 'vite';
import SitemapPlugin from 'rollup-plugin-sitemap';
import VitePressConfig from './.vitepress/config';

const docsSiteBaseUrl = 'https://nystudio107.com';
const docsBaseUrl = new URL(VitePressConfig.base!, docsSiteBaseUrl).href.replace(/\/$/, '') + '/';
const siteMapRoutes = [{
  path: '',
  name: VitePressConfig.title
}];

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    SitemapPlugin({
      baseUrl: docsBaseUrl,
      contentBase: './docs/.vitepress/dist',
      routes: siteMapRoutes,
    })
  ],
  server: {
    host: '0.0.0.0',
    port: parseInt(process.env.DOCS_DEV_PORT ?? '4000'),
    strictPort: true,
  }
});
