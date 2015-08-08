#/bin/sh

if [ $# -ne 1 ]; then
    exit
fi
sound=$1

/usr/bin/paplay $sound

