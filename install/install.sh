#!/usr/bin/env bash

# Check params.
if [ ! -n "$1" ] ;then
    echo -e "Please input the localIP param."
    exit 1
else
    export ESUPDATER_LOCAL_IP=$1
    systemVariables="ESUPDATER_LOCAL_IP=$1\n"
    echo -e $systemVariables > ../.env
fi

# The part of you must do.
# 1. Make image
cd image && bash make.sh && cd ..

# The part of you could do.
# 1. Run kafka container
cd container && bash kafka.sh && cd ..
