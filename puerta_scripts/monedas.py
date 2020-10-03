#!/usr/bin/python
# Made by: Saul Gonzalez (saulgonzalez76@gmail.com)
# Copyright (c) 2019.
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <https://www.gnu.org/licenses/>.

#Raspberry pi modules to be used with usb port on the qr sensor

import RPi.GPIO as GPIO
from time import sleep
import subprocess
GPIO.setmode(GPIO.BCM)
pin = 19
GPIO.setup(pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
try:
        while True:
                GPIO.wait_for_edge(pin, GPIO.FALLING)
                subprocess.call(["php","-f","/var/www/html/puerta_scripts/registro.php", "00001111"])
                sleep(3)
except KeyboardInterrupt:
        GPIO.cleanup()
