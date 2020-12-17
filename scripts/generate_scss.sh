#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
sass --no-source-map $DIR/../resources/scss/materialize.scss $DIR/../public/materialize/css/materialize.css