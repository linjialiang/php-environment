const path = {
  main: '/mysql/',
};

const sidebar = [
  {
    text: 'MySQL主从复制',
    collapsed: true,
    items: [
      { text: '主从复制-linux', link: `${path.main}linux-master-slave-replication` },
      { text: '主从复制-windows', link: `${path.main}windows-master-slave-replication` },
    ],
  },
];

export { sidebar as mysql };
