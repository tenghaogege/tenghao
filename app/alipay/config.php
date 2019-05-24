<?php
$config = array (	
		//应用ID,您的APPID。
    'app_id' => "2016100100638339",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCTNJ1CYAJ+1oDsLz9GOR3LX3hlGOoO/FgwpokXJxwgAJTenQA/Sn+syoRVC0JbAVc7PZnWosRypicugU/Yd3AkNqORDXhNZziYrYcmvivogZ+Nud6izpnmMYWvc0So61++GLCWHDjxpxVOvQdwnnoSgswqIs/kapTPZRl24Buy5YIKBaqYjoTmKydyTftikI7gOnt/Ain+M/iWRhAiytT4gwFPf5shnAx3uat3SFLcpNjgH/Y2Lt349BAHHI5/CsOfFUsRehioPU9pgxwz9unPug6ROdplkbp48jqTvpv8Dpi27fTG8oHxiCwKz9jJja2Ezq7Vpzw32bGYifJeFMkXAgMBAAECggEAGc5Sd01fgp7WERGjVQs0Ru/gqlB4z9G7ICRByjZH/vA7KN97KJ1FzqPeGQ6VPmD4yuDctDzqlXK9UbDHr5oYpbCSch5bZa5fxJw/IjKaffMjZnQcLIxtfGZYGk7pzXcd3LR1WItwDCUPXjNTN5IAsngNlKFvsopgtg7yLN3S3USLNSUnr07GtUrpb6FYvdUXt4aDFSNbMF9FIXp4WqMF/6Q8tX0xHvVqyxxoaf0mUeQ9H9Gjg3CCy61lLUoZYMVAnGnBkEWv9Z1wm/Jti0Caxbp2yE2UNxEJHxxwiO+oNbk+y09HAHzQnPhXmyv+ftqdVq2SpwiBDvK6zUZqEpUSCQKBgQDv11Fwnid/59tjAXNI8dgnnGNB2lGrezxB8fsIMZ1QQ5pynYPfpYR5pm5Q/4zYAAmHj+21tRDA2NB20JlLCgLINDNlWMU4BiqaeJcsN4ICJ4BjnSqrKVerKaN3qa2ofIqGpZvgzLvswVv95ZasfcT88/uBl/Td+XgYshWCar22uwKBgQCdH443hEvsjF51FPen1Hs5/wGGhk8dH0oI73tozOTdQ3buyDkg4zls+Hv2H0gj9dcIlTtV8CYybFFNcDH2liaW3Y+FSJlrsIDGmi0fyd9ve12vyzVA0uSxTEl9B24K+Zy0AJAVGMQjpJkVLYsiYkOjdRAzRBaXYyyBIAFAwcgHVQKBgELQV+aomFZgb9Rv5f7PKKk4PspsmE3OIeY5E/afY7SlcJQ3XzJheLsu6xJzbngJ4p26Mb1p+IXQoJpI2Dt4SXgeWqcECMTnqtyndW2i9PvGp1SYxvcxqDONgPVn9nG2aBzEa90/C6cT4p2jZMseqk7RZSIgA94GjjdK/QT82WbbAoGAUqBliYxo4NxwT+HW2stCgh04ECZRNF9f75MAsvwM3s67nJZudH0JleSWS7fnmAGQM1NHAMppS9rqZm6515LeeDfLYavsAlWvYsFxOWZPubCGdAvI1W4SFaqoereNhvf9ECu7mVyMyLGmTxzELjC+oRbOjicgOjxNCxppjkRZjIECgYEAtY7t+ogVo0Qh5HPwesO4TBohKYup4zjL7P+aLGxQM1F3UdpteBE2+woGWnyGLGNlTqZLK0tmPRcPk4ieKvRuhQxhIB/WKhm3aT7vpudOhk+fp7dkrWK+4Zc45MRxDHEhDgFcHoGLNiT69h63EosAkiwt8suElr9hcItATazXECY=",

    //异步通知地址
    'notify_url' => "http://wxshop.com/alipay/notify",

    //同步跳转
    'return_url' => "http://www.blog1.com/",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlX3SEyXwTtsyN8b+r2cpj7p8fjQmrTll0gbLODfH6iPPoa7zhC0Hrdf4YRpzJi4Z3Gtwcha3EJbaB9p7zpAPbNmFL4Iar57T53YeOYgsqUtnnIZwYiqPpk14h1O05WSvuYF9gw/H9hb/CMWps4zHQNuL+RvLFLwHv4A7oDXSweBJ3oPf6rhC73F3SNoVzB9xT91EUNTwkOcGOkVSzH+p7Zob7OPUbCiC51+r5OmqlGqtcm4RsaJyiHtGxfBiZVV4W0vNrlqxY+UTAjVIsax15g1SjLD7zQpadlqEh05RRSopo+83a/Xag0TAXvlwXzXH27YzUVb9Cj5om5Ti+lBtiwIDAQAB",

);