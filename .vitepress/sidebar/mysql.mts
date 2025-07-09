const path = {
  main: '/mysql/',
};

const sidebar = [
  {
    text: 'MySQL主从复制',
    collapsed: true,
    items: [
      { text: '概述', link: `${path.main}master-slave-replication/` },
      { text: '主从复制-linux', link: `${path.main}master-slave-replication/linux` },
      { text: '主从复制-windows', link: `${path.main}master-slave-replication/windows` },
    ],
  },
];

export { sidebar as mysql };
