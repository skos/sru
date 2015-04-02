#!/bin/bash

find . -wholename '*.php' -exec php -l '{}' \;
