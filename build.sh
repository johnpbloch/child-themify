#!/bin/bash

VER=$(git tag | sort -Vr | head -n1)
REBUILD="yes"

while [[ $# -gt 1 ]]
do
key="$1"

case $key in
    -v|--version)
    VER="$2"
    shift
    ;;
    -s|--short)
    REBUILD="no"
    ;;
esac
shift
done

if [ ! -e build/svn ]; then
    svn co https://plugins.svn.wordpress.org/child-themify/ build/svn
fi

if [ "$REBUILD" == "yes" ]; then
    npm run clean
    npm run setup
fi
npm run build:react

mkdir -p build/plugin/assets/js
mkdir -p build/plugin/assets/css

sed -e "s/{{VERSION}}/$VER/g" < child-themify.php > build/plugin/child-themify.php
sed -e "s/{{VERSION}}/$VER/g" < readme.txt > build/plugin/readme.txt

cp -r includes build/plugin/

cp assets/css/child-themify.css build/plugin/assets/css/
cp assets/js/child-themify.js build/plugin/assets/js/

rm -r build/svn/trunk

cp -r build/plugin build/svn/trunk

if [ -e "build/svn/tags/$VER" ]; then
    rm -r "build/svn/tags/$VER"
fi

cp -r build/svn/trunk "build/svn/tags/$VER"
