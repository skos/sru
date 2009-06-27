#!/bin/bash

# zmienia maksymalny czas waznosci rejestracji komputera
# parametr to data w formacie zrozumialym przez php-owe strtotime()
# http://php.net/manual/en/function.strtotime.php

BASE=$(dirname "$0")
. $BASE/api.sh

get "computers/available/${1}"
