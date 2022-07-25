#!/bin/bash

function createGrossStatisticsFor {
  find . -name "*.$1" | xargs wc -l | sort -nr
  echo
  echo
}

function createNetStatisticsFor {
  find . -name "*.$1" | xargs grep -v ^$ | wc -l
  echo
  echo
}

echo "--------------------------------------"
echo
echo "Übersicht hinsichtlich der Code-Metric"
echo
echo "         LoC -- Lines of Code"
echo
echo "--------------------------------------"

echo
echo "Betrachtung INKLUSIVE Leerzeilen"
echo

components=(php mustache css html)

for component in ${components[@]}
do
  echo "$component Quelltexte"
  createGrossStatisticsFor $component
done

echo
echo "*********************************"
echo

echo
echo "Betrachtung EXKLUSIVE Leerzeilen"
echo

for component in ${components[@]}
do
  echo "$component Quelltexte"
  createNetStatisticsFor $component
done
