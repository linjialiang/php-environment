import { siteConfig } from '../../siteConfig.mts';

const path = {
  main: '/linux/',
  redisConfUrl: `${siteConfig.tutorialApiUrl}nosql/redis/redis-conf.html`,
};

const sidebar = [
  {
    text: '概述',
    collapsed: true,
    items: [
      { text: '准备工作', link: `${path.main}` },
      { text: '内核管理与优化', link: `${path.main}include/kernel` },
      { text: '用户与进程', link: `${path.main}include/userOrProcess` },
    ],
  },
  { text: 'SQLite3', link: `${path.main}sqlite3` },
  {
    text: 'Redis',
    collapsed: true,
    items: [
      { text: '8.6', link: `${path.main}redis` },
      { text: 'Redis配置文件', link: `${path.redisConfUrl}` },
    ],
  },
  { text: 'PostgreSQL', link: `${path.main}pgsql` },
  {
    text: 'PHP',
    collapsed: true,
    items: [
      { text: 'PHP 8.5', link: `${path.main}php` },
      { text: '安装和配置共享扩展', link: `${path.main}include/phpExtension` },
    ],
  },
  { text: 'Nginx', link: `${path.main}nginx` },
  { text: 'MySQL', link: `${path.main}mysql` },
];

export { sidebar as linux };
