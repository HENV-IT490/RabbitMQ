#!/bin/bash
ping 25.2.97.87 -w1 -c2 &>/dev/null

if [ $? -eq 0 ]
then
    sed -i 's/master/slave/g' /home/nickdb/.bashrc
    export STATUS="slave"
else
    sed -i 's/slave/master/g' /home/nickdb/.bashrc
    export STATUS="master"
fi
