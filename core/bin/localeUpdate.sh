#!/bin/bash

msgmerge "${1}" "${2}" -o /tmp/$$
mv /tmp/$$ "${1}"
