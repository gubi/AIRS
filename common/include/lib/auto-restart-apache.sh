#!/bin/bash
# Apache Process Monitor
# Restart Apache Web Server When It Goes Down
# -------------------------------------------------------------------------
# Copyright (c) 2003 nixCraft project <http://cyberciti.biz/fb/>
# This script is licensed under GNU GPL version 2.0 or above
# -------------------------------------------------------------------------
# This script is part of nixCraft shell script collection (NSSC)
# Visit http://bash.cyberciti.biz/ for more information.
# -------------------------------------------------------------------------
# RHEL / CentOS / Fedora Linux restart command
#RESTART="/sbin/service httpd restart"

# uncomment if you are using Debian / Ubuntu Linux
RESTART="/etc/init.d/apache2 start"

#path to pgrep command
PGREP="/usr/bin/pgrep"

# Httpd daemon name,
# Under RHEL/CentOS/Fedora it is httpd
# Under Debian 4.x it is apache2
HTTPD="httpd"

# find httpd pid
$PGREP ${HTTPD}

if [ $? -ne 0 ] # if apache not running
then
# restart apache
$RESTART
fi
