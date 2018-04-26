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
    'alipayKey'=>'MIICXgIBAAKBgQDohgUoiDUgN1O0m76eXJ0dtuLM4ENlB3lbksmTtgQxOvSK1MX0ElBNK8uUEpTYbsV8vXPo8U7+KTR7slGyHmK5NBP1K1/pYEEj9EVeaW4aCQh+UBpR1vReQ15Q7QypkOW6WupjA58lKI+cvutTn8KBKPSaVVEsG3iWjbp98BznVQIDAQABAoGAdiMJYeN6ImkZxSxP98OUK/GsX20dsKnQdb8pXTvf+2c5sYOTI4NeeybiItQh/aih+9OBnXtkp+sleCdMLJQVkY0y4i4puM0PSNwL9NOJHcSeTYXhrFMDeE8Mov6HRR3yJfPChKDbhLz7NWf5Z0nqrz5OwyI6sv0C3S2W3199NE0CQQD5Wtyf4Lnr3AB3reqjYE4M0VrLhJTr0ttBRFgmPB9k0iqqcYwk8rR3HKQ0QNntBs84IIw7S6pUOC5y/5/hZZzHAkEA7rhU8cil6z3MPISf1vQRtJryvczimA1GAS3kXNnwCqM6Ji1xsBasdXMLdhvQwkl3s4lZ9Iy0l9K3D4Mgf2hnAwJBALrv7268nyW/bWpLrBiHXnwlh5gD8VFKZq1re1pOwIppNStKrPIWMk0J3+B8reQQstE1NWxOsYcqB0iXc1kbQIkCQQCbZ5Sk8iJRSiGzJAOKQ8li8Zwkw0SgB8QS8HVBnP8PbcNlJyBjqd8LdqF3ehQk5v7t+fR9pzvJuntBtfNp/eVRAkEAiKrPgrPCIxfdf53R3SXlHvnr/zXOG7AnR1oAqEaH0lk/wDtBwtxioaGCU8vpOluN1a1eNcm7oS0Y/iowaendiQ==',
    'alipaySecret'=>'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB'
];