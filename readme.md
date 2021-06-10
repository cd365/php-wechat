## Installation
```bash
composer require xooooooox/wechat
``` 

## For example
```php
<?php

use xooooooox\wechat\pay\Native;

// Native支付下单
$result = Native::Place(
    '商户号',
    '商户私钥内容',
    '商户证书序列号',
    '应用ID',
    '平台订单号',
    '订单描述',
    '回调通知地址',
    '支付总金额, 单位:分',
    '订单附加信息',
    'CNY'
);
var_dump($result);

```
