#!/usr/bin/env bash

php artisan ide-helper:generate
php artisan ide-helper:meta
php artisan ide-helper:models --dir="app/Models" -N