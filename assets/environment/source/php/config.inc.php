<?php
# /server/default/pma/config.inc.php

declare(strict_types=1);

$cfg['blowfish_secret'] = 'qWgP3sMqaGff5VyFHWWX5NkD7swtTx6x'; // 新版本，只能是32位

$i = 0;
$i++;

$cfg['Servers'][$i]['auth_type']       = 'cookie';
$cfg['Servers'][$i]['host']            = '127.0.0.1';
$cfg['Servers'][$i]['compress']        = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;
$cfg['UploadDir']                      = '';
$cfg['SaveDir']                        = '';
$cfg['TempDir']                        = '/tmp/';
$cfg['DefaultLang']                    = 'zh_CN';
$cfg['ThemeDefault']                   = 'original';
