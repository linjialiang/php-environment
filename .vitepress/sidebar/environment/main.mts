const path = {
  main: '/environment/',
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
    ],
  },
  { text: 'PostgreSQL', link: `${path.main}pgsql_compile` },
  { text: 'MySQL', link: `${path.main}mysql_compile` },
  { text: 'PHP', link: `${path.main}php` },
  { text: 'Nginx', link: `${path.main}nginx` },
  {
    text: '存档(不再积极维护)',
    collapsed: true,
    items: [
      { text: 'PostgreSQL(apt)', link: `${path.main}archive/pgsql` },
      { text: 'MySQL(apt)', link: `${path.main}archive/mysql` },
      { text: 'MongoDB', link: `${path.main}archive/mongodb` },
      { text: 'PHP旧版', link: `${path.main}archive/php_old` },
    ],
  },
];

export { sidebar as environment };
