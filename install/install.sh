#!/usr/bin/env bash

# The part of you must do.
# 1. Make image
cd image && bash make.sh && cd ..

# The part of you could do.
# 1. Run kafka container
cd container && bash kafka.sh && cd ..
