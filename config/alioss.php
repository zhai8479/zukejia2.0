<?php
/**
 * oss配置读取
 * Created by PhpStorm.
 * User: cdria
 * Date: 2018/4/20
 * Time: 11:36
 */

return [
    'ossServer' => env('ALIOSS_SERVER', null),                      // 外网
    'ossServerInternal' => env('ALIOSS_SERVERINTERNAL', null),      // 内网
    'AccessKeyId' => env('ALIOSS_KEYID', null),                     // key
    'AccessKeySecret' => env('ALIOSS_KEYSECRET', null),             // secret
    'alipayKey'=>'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCrjMI8ZFNkGi/QvKCIU25ZVhQP3Yccsyj7qABEF+9MQAFhbGG4ykr42e4+dJXcjc4UI6WRo2ZLajuNWVajP146ha/vHYxXfYcI8SY23WyBGDnK1GwrraYt54PzBf8aJcftF7CDUpuQAwNngiiF1A+7z9DQJc5z3wOl8H2BEqoXniPqorjZxYqhrZGikCiiSsOz24tqeEweuVKBOB9l5a3W2TktiictDJ5JLlYS9bis2m2DF7e6QV7WLiuEDmWjHsuTrQjLiz2cdJdKvW1HA1zECcILK1S/PFz3du7qA8OF6+zkIYzgWNybSIcHBWtHEEYiiMWJfPJNUvBfBBIElBvjAgMBAAECggEAUXGwPY+774hnLwh+hnplp1Awkh+wJ7X/PQrTpYBfganZffFAq8SOt3pvm4MqKt2/+tu5nV0gEanLwB2r/jD1gX86uaEjod3coCgs5ltVSizAM0WKXWpBvQVvJY+xPy60riFTEeXEKSjlEt0+c9rr5VSSZF+ulmBIkR2N2BuOWUtZMaMvpnJN/fPrS3rAn8PB+myoqkWbNF93xq1vAkpGJSlQITyQv8TN3xa+PDlCt8Kd2D4X12HPBeCl/dHArG4myl3qm6vhl2qKMy8NEq1QO/v6JvxTUC31kCtaO7gqOTxmCXsdbmVIOls416AIjZw/u7iSc2eTpWSbNZ8vriSnYQKBgQDkkSJxpVsBEhqIMGJwib7THv8CdaJToyoH+UoBTOg1ADk+IjbEKLb2krLM2PQULcWVawIpv99yfyd7UaAIyfU28GgJN7Qpgb886aCpsyChu5tAs6/XbwmNLD9yzAO6+EqzcEIik/sa6V+54Z0RIwxctDXAKTcA4qpchrdS2HiB2QKBgQDAI7zKkqyelshb+dJjvXpmc5lz84zQMxE0/UgZdZu0pInaijWRv4VYsZ+DecJ2Msg/GWJ74ZY3P5zMJreA+7qYCjGOZjAxFqSOFc0ZqMPA7gj9S0B+YPTPHjpP179yElKfNxOBlkdr1SEA/aTGkdVdQyRq0ztesL2rLVbSxsV6GwKBgCg1Y/LMHQNGNQaqRFb51Gj9dezv1ruR4439i8hIyDyyql9E04+UfgNIcU3aCK4YEz8i0QGZMGzK854dEOMa7BlbF2Ivu3Dl00ea7dmMmnv5Lemm8pMahHqiQxMl0q4sia+hWvEMSUtFrMJdby0Zlk4koaQJXBS6yy91v4BCdoiZAoGAeoTPV+UbiazgajXtQIiMSlQgX93HxuMpb76qgQGLni7lcKN88UlNAHeTHcVAiRpssj+/mvsITIaVt2Bg1zCtlVG6s/DZfhPQLoIcXOZOnPGsbcfUgkHVGVVPHaaXf2fLo5b9Kz7moA4xk24p6i0H/wZXbH7xdroA1+x7VsBFIE0CgYEAhMnTCpxDa42UcLQbxrNL0rgah1vYbV3fPf5lagNZ5dKeBSaspGtXT31cd74tkrcnc0itKlcRpMNWfrl+RwbXSAFDO2FMzCpEtMkCOFqXH2QwnYwZ56JiCyjYUvZcdQcbZGkbqir3RWhT6yzVH8ev07+Mabp2y0Tzv2BihNNM7ns=',
    'alipaySecret'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAp/o21E4zheqD/+42VKR0MEN/iOyIZF6sXUSsKy0ywdpXfzyBKfWTAg/stQam1Cv2f+x3lRTg0F/gc/CU5b6gOc7jen3ZIdQm851cta9BEscY0QXg0nmwWIqNn4rV3YE/D9U+LDna05EnoRujC8LF0GZQF1gaPEQbfeCeOeyX18eckRsjMOMQoAYtjtkBfz8jQSEXNxxM9dSnCMJynRw2Cz7kGDeqZr34+HDoal6rUcY87DVUezEkZ4CY8ZI1IHrYPs1WYvuhY/iwySbiyyaXc3ZRj/ZlmvLtagxcmuLBwXmS6MB9zb+mRM6W1DMZ7EzqQOMQkXhXYd88UG3K0kqB3wIDAQAB'
];