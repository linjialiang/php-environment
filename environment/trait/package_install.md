## 手动安装依赖

通常有两种情况需要手动安装依赖项：

1. 在较新的系统发行版上安装较旧的软件包；
2. 在较旧的系统发行版上安装较新的软件包。

::: warning :warning: 警告
安装发行版不受支持的软件包版本，可能会对系统依赖造成混乱，需要引起警惕。

对于正式环境有以下几点建议：

::: code-group

```[发行版建议]
1. 发行版必须为正式发行版；
2. 发行版尽可能是长期支持，可以延长漏洞修复周期；
3. 对应软件版本在此发行版做过权威测试，如：官方机构。
```

```[软件建议]
1. 使用长期支持的稳定版；
2. 上线存在中型及以上项目时，坚决不做版本升级；
3. 软件包不追求最新，以对项目框架友好为优先。
```

```[框架建议]
1. 新项目使用官方还在维护的长期支持版；
2. 新旧项目使用的框架要相似，最好只做版本迭代，可减少维护成本；
3. 使用开发人员熟悉的框架，再好的框架不会用也白瞎。
```

:::

### 编译安装 OpenSSL {#build-install-openssl}

如果对 openssl 依赖库版本有特殊需求，需自行编译安装特定版本

::: code-group

```bash{5-11} [1.1.1w构建选项]
# 作为公共依赖库，推荐以root用户安装它
mkdir /server/openssl-1.1.1w
cd /root/openssl-1.1.1w/

./config --prefix=/server/openssl-1.1.1w \
--openssldir=/server/openssl-1.1.1w \
no-shared \
zlib
```

```bash{5-11} [3.0.17构建选项]
# 作为公共依赖库，推荐以root用户安装它
mkdir /server/openssl-3.0.17
cd /root/openssl-3.0.17/

./config --prefix=/server/openssl-3.0.17 \
--openssldir=/server/openssl-3.0.17 \
no-shared \
zlib
```

```bash [编译安装]
make -j4 > make.log
make test > make-test.log
make install
```

```bash{2,8-10} [1.1.1w配置]
# 设置新的 PKG_CONFIG_PATH，排除系统默认的 OpenSSL 库路径
export PKG_CONFIG_PATH=/server/openssl-1.1.1w/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，是否正确替换
pkg-config --path openssl,libssl,libcrypto

# 成功替换展示：
/server/openssl-1.1.1w/lib/pkgconfig/openssl.pc
/server/openssl-1.1.1w/lib/pkgconfig/libssl.pc
/server/openssl-1.1.1w/lib/pkgconfig/libcrypto.pc

# 未成功替换展示：
/usr/lib/x86_64-linux-gnu/pkgconfig/openssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libcrypto.pc
```

```bash{2,8-10} [3.0.17配置]
# 设置新的 PKG_CONFIG_PATH，排除系统默认的 OpenSSL 库路径
export PKG_CONFIG_PATH=/server/openssl-3.0.17/lib64/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，是否正确替换
pkg-config --path openssl,libssl,libcrypto

# 成功替换展示：
/server/openssl-3.0.17/lib/pkgconfig/openssl.pc
/server/openssl-3.0.17/lib/pkgconfig/libssl.pc
/server/openssl-3.0.17/lib/pkgconfig/libcrypto.pc

# 未成功替换展示：
/usr/lib/x86_64-linux-gnu/pkgconfig/openssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libssl.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/libcrypto.pc
```

:::

## 编译安装 ICU4C {#bulid-install-icu4c}

Debian/Ubuntu 系统仓库中对 `ICU4C` 库的命名约定为 `libicu`。

系统自带的 libicu 版本如果不能满足编译环境需求，就需要通过手动编译特定版本来满足需求。

::: code-group

```bash [构建选项]
mkdir /server/icu4c-72_1
wget https://github.com/unicode-org/icu/releases/download/release-72-1/icu4c-72_1-src.tgz
tar - xzf icu4c-72_1-src.tgz
cd icu/source/
./configure --prefix=/server/icu4c-72.1 \
--enable-static
```

```bash [编译安装]
make -j4 > make.log
make check > make-check.log
make install
```

```bash [配置]
# 设置新的 PKG_CONFIG_PATH，排除系统默认的 icu 库路径
export PKG_CONFIG_PATH=/server/icu4c-72.1/lib/pkgconfig:$PKG_CONFIG_PATH

# 使用下面指令检查，是否正确替换
pkg-config --path icu-i18n, icu-io, icu-uc

# 成功替换展示：
/server/icu4c-72.1/lib/pkgconfig/icu-i18n.pc
/server/icu4c-72.1/lib/pkgconfig/icu-io.pc
/server/icu4c-72.1/lib/pkgconfig/icu-uc.pc

# 未成功替换展示：
/usr/lib/x86_64-linux-gnu/pkgconfig/icu-i18n.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/icu-io.pc
/usr/lib/x86_64-linux-gnu/pkgconfig/icu-uc.pc
```
