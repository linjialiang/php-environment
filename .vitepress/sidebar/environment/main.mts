const path = {
  main: '/environment/',
  mainOld: '/environment-old/',
  mainOld2: '/environment-lnmpp/',
  redisConfUrl: 'http://tutorial.e8so.com/nosql/redis/redis-conf.html',
};

const sidebar = [
  { text: '概述', link: `${path.main}` },
  { text: 'PostgreSQL', link: `${path.main}pgsql` },
  { text: 'PHP', link: `${path.main}php` },
  { text: 'Nginx', link: `${path.main}nginx` },
  {
    text: 'LNMPP存档',
    collapsed: true,
    items: [
      { text: '概述', link: `${path.mainOld2}` },
      { text: 'SQLite3', link: `${path.mainOld2}sqlite3` },
      {
        text: 'Redis',
        collapsed: true,
        items: [
          { text: '8.2', link: `${path.mainOld2}redis` },
          { text: 'Redis配置文件', link: `${path.redisConfUrl}` },
        ],
      },
      { text: 'PostgreSQL', link: `${path.mainOld2}pgsql` },
      { text: 'MySQL', link: `${path.mainOld2}mysql` },
      { text: 'PHP', link: `${path.mainOld2}php` },
      { text: 'Nginx', link: `${path.mainOld2}nginx` },
    ],
  },
  {
    text: '2025/9/7存档',
    collapsed: true,
    items: [
      { text: '概述', link: `${path.mainOld}` },
      { text: 'SQLite3', link: `${path.mainOld}sqlite3` },
      {
        text: 'Redis',
        collapsed: true,
        items: [
          { text: '8.2', link: `${path.mainOld}redis` },
          { text: '7.4', link: `${path.mainOld}redis-74` },
          { text: 'Redis配置文件', link: `${path.redisConfUrl}` },
        ],
      },
      { text: 'PostgreSQL', link: `${path.mainOld}pgsql_compile` },
      { text: 'MySQL', link: `${path.mainOld}mysql_compile` },
      { text: 'PHP', link: `${path.mainOld}php` },
      { text: 'Nginx', link: `${path.mainOld}nginx` },
      {
        text: '不再积极维护',
        collapsed: true,
        items: [
          { text: 'PostgreSQL(apt)', link: `${path.mainOld}archive/pgsql` },
          { text: 'MySQL(apt)', link: `${path.mainOld}archive/mysql` },
          { text: 'MongoDB', link: `${path.mainOld}archive/mongodb` },
          { text: 'PHP旧版', link: `${path.mainOld}archive/php_old` },
        ],
      },
    ],
  },
];

export { sidebar as environment };
