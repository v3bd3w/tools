#!/bin/sh

### Functions
#{{{

error ()
{
	/usr/bin/echo -e -n "\nERROR: ${BASENAME} - ${1}\n" >&2
	return 255
}

notice ()
{
	/usr/bin/echo -e -n "\nNOTICE: ${BASENAME} - ${1}\n"
	return 0
}

run ()
{
	COMMAND="${*}"
	echo "\n> ${COMMAND}"
	${COMMAND}
	if test ${?} -ne 0
	then
		error "Running the command failed with an error"
		exit 255
	fi
	return 0
}

#}}}

### Initialization
# {{{

FILE=`/usr/bin/realpath $0`
DIR=`/usr/bin/dirname ${FILE}`
BASENAME=`/usr/bin/basename ${FILE}`
ERROR=0

HELP="
Description: Copy basic config files and next installation step scripts
Usage: ${BASENAME} <config_file/path>
"
if test "${1}" = "-h" -o "${1}" = "--help"
then
	/usr/bin/echo "${HELP}"
	exit 0
fi

# }}}

if test -z "${1}"
	then
	error "Path to config file is not set in command line"
	exit 255
fi
CONFIG_FILE="${1}"
. ${CONFIG_FILE}

/usr/bin/findmnt -c --mountpoint ${MOUNT_POINT}
if test ${?} -eq 0
	then
	error "${MOUNT_POINT} already mounted"
	exit 255
fi

/usr/bin/findmnt -c ${ROOT_DEVICE}
if test ${?} -eq 0
	then
	error "${ROOT_DEVICE} already mounted"
	exit 255
fi

run /usr/bin/mount ${ROOT_DEVICE} ${MOUNT_POINT}

run /usr/bin/apt update
PACKAGE_NAMES="
	debootstrap
"
for PACKAGE_NAME in ${PACKAGE_NAMES}
do
	dpkg-query --status ${PACKAGE_NAME} 1> /dev/null 2> /dev/null
	if test ${?} -ne 0
	then
		run /usr/bin/apt -y install ${PACKAGE_NAME}
	fi
done

run /usr/sbin/debootstrap ${RELEASE_NAME} ${MOUNT_POINT} http://ftp.ru.debian.org/debian/

exit 0

