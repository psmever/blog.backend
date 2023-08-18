#!/bin/bash

line=$(head -n 1 /etc/hosts)
line2=$(echo $line | awk '{print $2}')
#echo "$line $line2.localdomain" >> /etc/hosts


sed -i 's/\;date\.timezone \=/date\.timezone \= Asia\/Seoul/g' /etc/php/8.2/cli/php.ini
