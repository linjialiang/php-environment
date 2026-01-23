const path = {
  main: '/linux/',
  redisConfUrl: 'https://tutorial.e8so.com/nosql/redis/redis-conf.html',
};

const sidebar = [
  { text: '概述', link: `${path.main}` },
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
