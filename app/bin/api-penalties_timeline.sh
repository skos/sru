#!/bin/bash

# wysyła do administratorów osiedlowych maila z listą założoncyh
# i zmodyfikowanych kar

BASE=$(dirname "$0")
. $BASE/api.sh

get "penalties/sendTimeline"
