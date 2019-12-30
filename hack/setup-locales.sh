#!/bin/bash

set -e

echo en_US ISO-8859-1 | sudo tee -a /etc/locale.gen
echo en_US.UTF-8 UTF-8 | sudo tee -a /etc/locale.gen
echo en_CA ISO-8859-1 | sudo tee -a /etc/locale.gen
echo en_CA.UTF-8 UTF-8 | sudo tee -a /etc/locale.gen
echo es_ES ISO-8859-1 | sudo tee -a /etc/locale.gen
echo es_ES.UTF-8 UTF-8 | sudo tee -a /etc/locale.gen
echo ru_RU ISO-8859-5 | sudo tee -a /etc/locale.gen
echo ru_RU.UTF-8 UTF-8 | sudo tee -a /etc/locale.gen
sudo locale-gen
sudo update-locale
