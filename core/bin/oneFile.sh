#!/bin/bash

FILE='/tmp/ufra.$$'
DIRS='conf ex lib map dao tpl bean box view act ctl'

rm "${FILE}"
echo '' > "${FILE}"
for dir in ${DIRS}; do
	find "${dir}" -name '*.php' -exec cat '{}' \; >> "${FILE}"
done;
cat ufra.php >> "${FILE}"

echo '<?'
cat "${FILE}" | sed 's/<?//'
