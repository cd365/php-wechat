<?php

include './pay/SignV3.php';
include './pay/Native.php';

use xooooooox\wechat\pay\SignV3;
use xooooooox\wechat\pay\Native;

$pk = <<<YR
-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDfY/lt6eCy0eqT
xuKrnwXSKlgl9fQj4eLVHwfHuJzlVzUYj951phOZl4XmZG75U5gAa1P1nKut+S9D
4hc7Uc+xT00teo8cq5P/eD61uXenaLuXK0ygfboeEfXix9yZjJsEsQvr4Gc6CK3m
otVd0Sw4Q4WOW1lhyesII0+cexgIRK5otx/OzvU69TYGorubOzHG2ZClgXw4T+du
c24t019un6crZtL6z/hDVS2UzU3MIbFaVQFrbF2HVq3axEojhz9h64tevMvrnaZ2
gnpHcaNKIKMr24ewKNhQ+PxEZ5FirK0+zh1jE64xoVs2W089YJH5u9h75HI5UNCX
MSCmz5ltAgMBAAECggEAfcAtfO4XCxKX1jAzESEnibNEg7n8gOZ7ZjVdj9QgngmF
Xho+xEOE7QUa3yLSRZAFFNdWIADds8V+EdyH72CSZeGaNGu1fBIp8bmis3GAJcET
OHmrXPzpdFvv6oVPbTB1YyK270UXVtfj7Nzk9zb0iYeY+xX5Ls8XezFFc3Tnhl71
+xKCiwR7AYcoE114u5hv+ndA0i8uGxVNY8auQzacqOc6howj5NQCsWQn2sUJIvta
BY2K/EzYr1aYMdmrQkR/okN44ObXqGDrgK8dPbdcOFSiMn6o0ltsJ3PrkP6kzKBO
aT8k9Ofwu+aT9SWnoDxDvNAdnHs9OaLgr0PFART4kQKBgQDypOcHe+dPbaorS6rw
z5b8FbJ6zE0OawzyWq9+kBLdyAqLlZBrRBs4oMTzh5xcsOvx2N5m2QBxkXDH8iGS
TdO5uSW3GzAijyn5vtjeK2NM+9q07sldt2Mgy4AX1JJlsLA2Ip9kx7765RdaX0ev
MNvINqlOxzKyagudpgrfEuWsNwKBgQDrr8Tl2gDkaDdLuBIMoH/Z9rdendVOATLM
tiQ80nvPO+oMFbGxn4CKo0PqOxEbBM6RyL7ZyfbA/ey6vnXT7MvoNEGhRbBMGqMA
K7Hzgnlaf8kCCP5SmbtRhfipx9V2q9EIYLsSSc2f+z6W3fL6Wyd4Zk+ApyvBfynt
AgzURBl9ewKBgFbekNPOGT8HjP/ZJEb4mx5/ChoKoJ/D1avCqcfO/uIl5xiYIE82
3+QDMt/ZMjLBKIe6U81QbWc7YbxDxJ8je+SnE4idlDsbNDT0jaHkuLVsCZ3zS2Zg
7H1mPeLKOOttOXj4JkaneIlMkXLKX9ipzlW8tBq/GDhl3OjA9G1uz4k7AoGAQfrs
CVj1hPvz9vup+eT4xeE+xnszGupU+WBIVsqqJILma8mq/Enl52n7elhc2o6G8eMc
IsZakP7FRiZJwDF7iB8Q/IAQ8c1HMqYI5F3zcTVy5WH1KNSmzxTNX2J1TQqes1S5
Kk6FBTOF+yBZhGL+csNZoG+sXTgnWZWIV+hUpn8CgYBGKmaxpgUodpJ2ZSnyXm3H
Lf5FJSLeEjjJcFJyiNYlPIF9ylAXXRw0vIN+dbAyl741MkPU4X766kdYJQZT6ASi
abs1nypvOjJd/L0KmFAAhgXl2jvxf+gNrXucY4JPqCPtLE/xsdWdY2ru53aOP24V
6DJRTSHu0omY4avJ1mETJw==
-----END PRIVATE KEY-----
YR;
$result = SignV3::CountAuthorization('POST',Native::$UrlPlace,(string)time(),'12345678901234567890123456789012','{}','123456',$pk);
var_dump($result);