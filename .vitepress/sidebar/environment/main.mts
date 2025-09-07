const path = {
  main: '/environment/',
  mainOld: '/environment/',
};

const sidebar = [
  { text: '概述', link: `${path.main}` },
  { text: 'SQLite3', link: `${path.main}sqlite3` },
  {
    text: 'Redis',
    collapsed: true,
    items: [
      { text: '8.2', link: `${path.main}redis` },
      { text: '7.4', link: `${path.main}redis-74` },
      { text: 'Redis配置文件', link: `${path.main}redis-conf` },
    ],
  },
  { text: 'PostgreSQL', link: `${path.main}pgsql_compile` },
  { text: 'MySQL', link: `${path.main}mysql_compile` },
  { text: 'PHP', link: `${path.main}php` },
  { text: 'Nginx', link: `${path.main}nginx` },
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
          { text: 'Redis配置文件', link: `${path.mainOld}redis-conf` },
        ],
      },
      { text: 'PostgreSQL', link: `${path.mainOld}pgsql_compile` },
      { text: 'MySQL', link: `${path.mainOld}mysql_compile` },
      { text: 'PHP', link: `${path.mainOld}php` },
      { text: 'Nginx', link: `${path.mainOld}nginx` },
    ],
  },
  {
    text: '存档(不再积极维护)',
    collapsed: true,
    items: [
      { text: 'PostgreSQL(apt)', link: `${path.mainOld}archive/pgsql` },
      { text: 'MySQL(apt)', link: `${path.mainOld}archive/mysql` },
      { text: 'MongoDB', link: `${path.mainOld}archive/mongodb` },
      { text: 'PHP旧版', link: `${path.mainOld}archive/php_old` },
    ],
  },
];

export { sidebar as environment };
