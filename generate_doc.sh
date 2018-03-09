#!/usr/bin/env bash
# bash
# 生成md格式的api文档
php artisan api:docs --name "容易住文档" --use-version v1 --output-file ./document/document.md -vvv