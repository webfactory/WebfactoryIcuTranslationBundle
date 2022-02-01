#!/bin/bash

cat <<< $(jq --arg version $VERSION '.require  |= with_entries(if ((.key|test("^symfony/translation-contracts")|not) and (.key|test("^symfony/"))) then .value=$version else . end)' < composer.json) > composer.json
