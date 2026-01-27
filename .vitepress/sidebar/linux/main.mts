import { siteConfig } from '@config/siteConfig.mts';

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
      { text: '用户与进程', link: `${path.main}include/userOrProcess` },
    ],
  },
  { text: 'SQLite3', link: `${path.main}sqlite3` },
  {
    text: 'Redis',
    collapsed: true,
    items: [
      { text: '8.4', link: `${path.main}redis` },
      { text: 'Redis配置文件', link: `${path.redisConfUrl}` },
    ],
  },
  { text: 'PostgreSQL', link: `${path.main}pgsql` },
  { text: 'MySQL', link: `${path.main}mysql` },
  { text: 'PHP', link: `${path.main}php` },
  { text: 'Nginx', link: `${path.main}nginx` },
];

export { sidebar as linux };
